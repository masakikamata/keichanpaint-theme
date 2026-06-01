<?php
/**
 * Instagram 動画を magazine カスタム投稿に自動投稿
 * 設置場所: wp-content/mu-plugins/instagram-to-magazine.php
 *
 * 使い方:
 *   1. INSTAGRAM_ACCESS_TOKEN に取得したアクセストークンを入力
 *   2. INSTAGRAM_USER_ID に Instagram ビジネスアカウントID（数字）を入力
 *   3. このファイルを wp-content/mu-plugins/ にアップロード
 */

define( 'INSTAGRAM_ACCESS_TOKEN', 'ここにアクセストークンを貼る' );
define( 'INSTAGRAM_USER_ID',      'ここにInstagramユーザーID（数字）を貼る' );

// ------------------------------------
// Instagram から動画を取得して投稿する
// ------------------------------------
function instagram_fetch_and_post() {

    $url = add_query_arg( [
        'fields'       => 'id,media_type,media_url,thumbnail_url,caption,timestamp',
        'access_token' => INSTAGRAM_ACCESS_TOKEN,
    ], 'https://graph.instagram.com/' . INSTAGRAM_USER_ID . '/media' );

    $response = wp_remote_get( $url, [ 'timeout' => 15 ] );

    if ( is_wp_error( $response ) ) {
        error_log( '[Instagram Auto Post] API取得エラー: ' . $response->get_error_message() );
        return;
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( empty( $data['data'] ) ) {
        error_log( '[Instagram Auto Post] データなし' );
        return;
    }

    foreach ( $data['data'] as $item ) {

        // 動画のみ対象（REELSも含む）
        if ( ! in_array( $item['media_type'], [ 'VIDEO', 'REELS' ], true ) ) {
            continue;
        }

        // すでに投稿済みならスキップ（instagram_id で重複チェック）
        $existing = get_posts( [
            'post_type'  => 'magazine',
            'meta_key'   => '_instagram_media_id',
            'meta_value' => $item['id'],
            'numberposts' => 1,
        ] );
        if ( ! empty( $existing ) ) {
            continue;
        }

        // キャプションをタイトルと本文に使用
        $caption   = $item['caption'] ?? '';
        $title     = mb_substr( $caption, 0, 50 ) ?: 'Instagram動画';
        $video_url = $item['media_url']      ?? '';
        $thumb_url = $item['thumbnail_url']  ?? '';

        // 本文：動画タグ＋キャプション
        $content  = '<figure class="instagram-video">';
        $content .= '<video src="' . esc_url( $video_url ) . '" controls playsinline';
        if ( $thumb_url ) {
            $content .= ' poster="' . esc_url( $thumb_url ) . '"';
        }
        $content .= '></video>';
        $content .= '</figure>';
        if ( $caption ) {
            $content .= '<p class="instagram-caption">' . nl2br( esc_html( $caption ) ) . '</p>';
        }

        // magazine カスタム投稿として作成
        $post_id = wp_insert_post( [
            'post_type'    => 'magazine',
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_date'    => date( 'Y-m-d H:i:s', strtotime( $item['timestamp'] ) ),
        ] );

        if ( is_wp_error( $post_id ) ) {
            error_log( '[Instagram Auto Post] 投稿作成エラー: ' . $post_id->get_error_message() );
            continue;
        }

        // Instagram メディアIDを保存（重複防止用）
        update_post_meta( $post_id, '_instagram_media_id', $item['id'] );
        update_post_meta( $post_id, '_instagram_video_url', $video_url );
        update_post_meta( $post_id, '_instagram_timestamp', $item['timestamp'] );

        error_log( '[Instagram Auto Post] 投稿作成: ' . $title . ' (post_id=' . $post_id . ')' );
    }
}

// ------------------------------------
// WordPress Cron で1時間ごとに実行
// ------------------------------------
add_action( 'instagram_auto_post_cron', 'instagram_fetch_and_post' );

if ( ! wp_next_scheduled( 'instagram_auto_post_cron' ) ) {
    wp_schedule_event( time(), 'hourly', 'instagram_auto_post_cron' );
}

// ------------------------------------
// 管理画面から手動実行ボタン（テスト用）
// ------------------------------------
add_action( 'admin_notices', function () {
    if ( ! current_user_can( 'manage_options' ) ) return;

    if ( isset( $_GET['instagram_run_now'] ) && $_GET['instagram_run_now'] === '1' ) {
        instagram_fetch_and_post();
        echo '<div class="notice notice-success"><p>Instagramから動画を取得しました。</p></div>';
    }

    $run_url = add_query_arg( 'instagram_run_now', '1', admin_url() );
    echo '<div class="notice notice-info"><p>';
    echo '<strong>Instagram自動投稿:</strong> ';
    echo '<a href="' . esc_url( $run_url ) . '">今すぐ取得して投稿する（テスト実行）</a>';
    echo '</p></div>';
} );
