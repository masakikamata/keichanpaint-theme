<?php
/**
 * Plugin Name: Claude Auto Poster
 * Description: 市の公式サイトから助成金情報を読み取り、テンプレート記事のデザインをそのまま引き継いだ新記事を自動生成・投稿します。
 * Version: 4.5.1
 * Author: Claude
 *
 * v4.4 変更点:
 *   ・記事本文1500文字以上を保証するプロンプト改修
 *   ・画像を2カラムレイアウト（wp:columns / wp:column）で自動挿入
 *   ・タイトル色: ブルーグレー + パールホワイトアクセント（CSS自動注入）
 *
 * v4.4.1 修正:
 *   ・「今すぐ生成」ボタン押下時に設定が保存されない問題を修正
 *   ・投稿元URLを変更したら処理済みリストを自動リセット
 *
 * v4.5.1 変更:
 *   ・GitHub取り込み機能は「助成金管理（東京）」画面に集約。Auto Poster側からは削除
 *   ・投稿元URLは手動貼り付けで使用してください
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'CAP_OPTION', 'cap_settings' );
define( 'CAP_CRON',   'cap_auto_post_event' );

// ──────────────────────────────────────────────
// 有効化 / 無効化
// ──────────────────────────────────────────────

register_activation_hook( __FILE__, 'cap_activate' );
register_deactivation_hook( __FILE__, 'cap_deactivate' );

function cap_activate() { cap_schedule_cron(); }
function cap_deactivate() {
    $ts = wp_next_scheduled( CAP_CRON );
    if ( $ts ) wp_unschedule_event( $ts, CAP_CRON );
}

add_filter( 'cron_schedules', function( $s ) {
    $s['cap_monthly'] = [ 'interval' => 2592000, 'display' => '毎月' ];
    return $s;
} );

function cap_schedule_cron() {
    if ( wp_next_scheduled( CAP_CRON ) ) return;
    $interval = get_option( CAP_OPTION, [] )['interval'] ?? 'daily';
    wp_schedule_event( time(), $interval, CAP_CRON );
}
function cap_reschedule_cron() {
    $ts = wp_next_scheduled( CAP_CRON );
    if ( $ts ) wp_unschedule_event( $ts, CAP_CRON );
    cap_schedule_cron();
}

// mu-plugins設置時の保険：init時にスケジュール
add_action( 'init', 'cap_schedule_cron' );

add_action( CAP_CRON, 'cap_run_auto_post' );

// ──────────────────────────────────────────────
// FAQ JSON-LD を wp_head で出力（wp_kses_post 対策）
// ──────────────────────────────────────────────

add_action( 'wp_head', function() {
    if ( ! is_singular() ) return;
    $schema = get_post_meta( get_the_ID(), '_cap_faq_schema', true );
    if ( empty( $schema ) ) return;
    echo '<script type="application/ld+json">' . json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
} );

// ──────────────────────────────────────────────
// [v4.4] タイトル装飾CSS — ブルーグレー＋パールホワイト
// ──────────────────────────────────────────────

add_action( 'wp_head', function() {
    if ( ! is_singular() ) return;
    if ( ! get_post_meta( get_the_ID(), '_cap_generated', true ) ) return;
    ?>
    <style id="cap-title-style">
    .cap-styled-title .entry-title,
    .cap-styled-title .article-title,
    .cap-styled-title h1.entry-title {
        color: #7a8fa6;
        text-shadow: 0 1px 2px rgba(255,255,255,0.7);
        position: relative;
        padding-bottom: 12px;
    }
    .cap-styled-title .entry-title::after,
    .cap-styled-title .article-title::after,
    .cap-styled-title h1.entry-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 3px;
        margin-top: 10px;
        border-radius: 2px;
        background: linear-gradient(90deg, #7a8fa6, #f0ece4);
    }
    .cap-styled-title h2 {
        color: #6b7f96;
        border-left: 4px solid #e8e4dc;
        padding-left: 12px;
    }
    .cap-2col-image {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin: 20px 0;
        align-items: start;
    }
    .cap-2col-image figure {
        margin: 0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .cap-2col-image img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 0.3s ease;
    }
    .cap-2col-image figure:hover img {
        transform: scale(1.03);
    }
    .cap-2col-image figcaption {
        font-size: 0.82em;
        color: #8a96a3;
        text-align: center;
        padding: 6px 8px;
        background: linear-gradient(180deg, #fafafa, #f4f2ee);
    }
    @media (max-width: 600px) {
        .cap-2col-image { grid-template-columns: 1fr; }
    }
    </style>
    <?php
} );

// ──────────────────────────────────────────────
// URLキュー管理
// ──────────────────────────────────────────────

function cap_next_url() {
    $opts  = get_option( CAP_OPTION, [] );
    $urls  = array_values( array_filter( array_map( 'trim', explode( "\n", $opts['source_urls'] ?? '' ) ) ) );
    if ( empty( $urls ) ) return null;
    $done    = get_option( 'cap_done_urls', [] );
    $pending = array_diff( $urls, $done );
    if ( empty( $pending ) ) {
        update_option( 'cap_done_urls', [] );
        $pending = $urls;
    }
    $url    = reset( $pending );
    $done[] = $url;
    update_option( 'cap_done_urls', array_values( $done ) );
    return $url;
}

// ──────────────────────────────────────────────
// 情報源ページのテキスト取得
// ──────────────────────────────────────────────

function cap_fetch_page_text( $url ) {
    $resp = wp_remote_get( $url, [ 'timeout' => 30, 'user-agent' => 'Mozilla/5.0' ] );
    if ( is_wp_error( $resp ) ) return null;
    $text = wp_strip_all_tags( wp_remote_retrieve_body( $resp ) );
    return mb_substr( preg_replace( '/\s{3,}/', "\n\n", $text ), 0, 8000 );
}

// ──────────────────────────────────────────────
// テンプレート投稿の raw post_content を取得
// ──────────────────────────────────────────────

function cap_get_template_content( $template_post_id ) {
    if ( ! $template_post_id ) return '';
    $post = get_post( intval( $template_post_id ) );
    return $post ? $post->post_content : '';
}

// ──────────────────────────────────────────────
// テンプレートから特定H2セクションを抽出
// ──────────────────────────────────────────────

function cap_extract_h2_section( $content, $keyword ) {
    $kw = preg_quote( $keyword, '/' );

    if ( preg_match(
        '/(<!-- wp:heading[\s\S]*?<h2[^>]*>[^<]*' . $kw . '[^<]*<\/h2>[\s\S]*?<!-- \/wp:heading -->[\s\S]*?)(?=<!-- wp:heading|$)/iu',
        $content, $m
    ) ) {
        return trim( $m[1] );
    }

    if ( preg_match(
        '/(<!-- wp:heading[\s\S]*?' . $kw . '[\s\S]*?<!-- \/wp:heading -->[\s\S]*?)(?=<!-- wp:heading|$)/iu',
        $content, $m
    ) ) {
        return trim( $m[1] );
    }

    if ( preg_match(
        '/(<h2[^>]*>[^<]*' . $kw . '[^<]*<\/h2>[\s\S]*?)(?=<h2[^>]*>|$)/iu',
        $content, $m
    ) ) {
        return trim( $m[1] );
    }

    return null;
}

// ──────────────────────────────────────────────
// 生成コンテンツ内の同系H2セクションをテンプレート版で置換
// ──────────────────────────────────────────────

function cap_replace_h2_section( $content, $keyword, $template_section ) {
    $kw = preg_quote( $keyword, '/' );

    $pat_gb = '/(<!-- wp:heading[\s\S]*?' . $kw . '[\s\S]*?<!-- \/wp:heading -->[\s\S]*?)(?=<!-- wp:heading|$)/iu';
    if ( preg_match( $pat_gb, $content ) ) {
        return preg_replace( $pat_gb, $template_section, $content, 1 );
    }

    $pat_cl = '/(<h2[^>]*>[^<]*' . $kw . '[^<]*<\/h2>[\s\S]*?)(?=<h2[^>]*>|$)/iu';
    if ( preg_match( $pat_cl, $content ) ) {
        return preg_replace( $pat_cl, $template_section, $content, 1 );
    }

    cap_log( "「{$keyword}」セクションが生成コンテンツに見つからないため末尾に追加しました。" );
    return $content . "\n" . $template_section;
}

// ──────────────────────────────────────────────
// 画像生成：Imagen4 → Gemini Flash Image フォールバック
// ──────────────────────────────────────────────

function cap_generate_image( $prompt, $gemini_key ) {
    if ( empty( $gemini_key ) ) return null;

    $imagen_models = [
        'imagen-4.0-generate-001',
        'imagen-4.0-ultra-generate-001',
    ];

    foreach ( $imagen_models as $model ) {
        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/'
                    . $model . ':predict?key=' . $gemini_key;

        $resp = wp_remote_post( $endpoint, [
            'timeout' => 90,
            'headers' => [ 'Content-Type' => 'application/json' ],
            'body'    => json_encode( [
                'instances'  => [ [ 'prompt' => $prompt ] ],
                'parameters' => [ 'sampleCount' => 1, 'aspectRatio' => '16:9' ],
            ] ),
        ] );

        if ( is_wp_error( $resp ) ) { cap_log( "画像エラー({$model}): " . $resp->get_error_message() ); continue; }

        $http_code = wp_remote_retrieve_response_code( $resp );
        $body_raw  = wp_remote_retrieve_body( $resp );
        $data      = json_decode( $body_raw, true );

        if ( $http_code === 400 && strpos( $body_raw, 'paid plans' ) !== false ) {
            cap_log( '⚠️ Imagenは有料プランが必要です。Geminiフォールバックに切り替えます。' );
            break;
        }

        $prediction = $data['predictions'][0] ?? null;
        $b64        = $prediction['bytesBase64Encoded'] ?? null;
        $mime       = $prediction['mimeType'] ?? 'image/png';

        if ( ! $b64 ) { cap_log( "Imagen NG({$model}) HTTP{$http_code}: " . substr( $body_raw, 0, 300 ) ); continue; }

        return cap_save_image_from_b64( $b64, $mime, $model );
    }

    $generatecontent_models = [
        'gemini-2.0-flash-exp',
        'gemini-3.1-flash-image',
    ];

    foreach ( $generatecontent_models as $gc_model ) {
        cap_log( "画像生成試行: {$gc_model}" );
        $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/'
                    . $gc_model . ':generateContent?key=' . $gemini_key;

        $body_json = json_encode( [
            'contents'         => [ [ 'parts' => [ [ 'text' => $prompt ] ] ] ],
            'generationConfig' => [ 'responseModalities' => [ 'IMAGE', 'TEXT' ] ],
        ] );

        $max_retry = 2;
        for ( $try = 1; $try <= $max_retry; $try++ ) {
            $resp = wp_remote_post( $endpoint, [
                'timeout' => 90,
                'headers' => [ 'Content-Type' => 'application/json' ],
                'body'    => $body_json,
            ] );

            if ( is_wp_error( $resp ) ) {
                cap_log( "画像エラー({$gc_model}): " . $resp->get_error_message() );
                break;
            }

            $http_code = wp_remote_retrieve_response_code( $resp );
            $body_raw  = wp_remote_retrieve_body( $resp );

            if ( $http_code === 429 ) {
                cap_log( "429 レート制限({$gc_model}) {$try}回目 — 3秒後にリトライ..." );
                sleep( 3 );
                continue;
            }

            $data  = json_decode( $body_raw, true );
            $parts = $data['candidates'][0]['content']['parts'] ?? [];

            foreach ( $parts as $part ) {
                if ( ! empty( $part['inlineData'] ) ) {
                    $b64  = $part['inlineData']['data']     ?? null;
                    $mime = $part['inlineData']['mimeType'] ?? 'image/png';
                    if ( $b64 ) return cap_save_image_from_b64( $b64, $mime, $gc_model );
                }
            }

            cap_log( "NG({$gc_model}) HTTP{$http_code}: " . substr( $body_raw, 0, 300 ) );
            break;
        }
    }

    cap_log( '⚠️ 全モデルで画像生成失敗。画像なしで続行します。' );
    return null;
}

// ──────────────────────────────────────────────
// 画像をメディアライブラリに保存するヘルパー
// ──────────────────────────────────────────────

function cap_save_image_from_b64( $b64, $mime, $label ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $ext = ( $mime === 'image/jpeg' ) ? 'jpg' : 'png';
    $tmp = get_temp_dir() . 'cap-img-' . time() . '.' . $ext;
    file_put_contents( $tmp, base64_decode( $b64 ) );

    $attach_id = media_handle_sideload(
        [ 'name' => 'cap-' . time() . '.' . $ext, 'tmp_name' => $tmp ],
        0, null,
        [ 'post_mime_type' => $mime ]
    );
    @unlink( $tmp );

    if ( is_wp_error( $attach_id ) ) { cap_log( "メディア登録失敗({$label}): " . $attach_id->get_error_message() ); return null; }

    cap_log( "画像生成OK({$label}) attach_id:{$attach_id}" );
    return $attach_id;
}

// ──────────────────────────────────────────────
// [v4.4] 2カラムおしゃれ画像レイアウトで挿入
// ──────────────────────────────────────────────

function cap_inject_section_images( $content, $article_title, $gemini_key, $image_style ) {
    if ( empty( $gemini_key ) ) return $content;

    preg_match_all( '/<h2([^>]*)>(.*?)<\/h2>/si', $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );
    if ( empty( $matches ) ) return $content;

    $images = [];
    foreach ( $matches as $m ) {
        $heading = wp_strip_all_tags( $m[2][0] );
        $style_map = [
            'photo' => 'Realistic photo, Japanese residential house, clean exterior, professional painter, bright trustworthy atmosphere, no text, no watermark, 16:9 ratio.',
            'flat'  => 'Flat design illustration, simple modern, blue and white tones, Japanese house exterior, no text, 16:9 ratio.',
            'manga' => 'Japanese manga style illustration, friendly character, explaining home improvement, no text, 16:9 ratio.',
        ];
        $style_hint = $style_map[ $image_style ] ?? $style_map['photo'];
        $prompt = "Japanese home improvement website. Article: \"{$article_title}\". Section: \"{$heading}\". {$style_hint}";

        $id = cap_generate_image( $prompt, $gemini_key );
        if ( $id ) {
            $images[] = [
                'attach_id' => $id,
                'url'       => wp_get_attachment_url( $id ),
                'alt'       => $heading,
                'offset'    => $m[0][1] + strlen( $m[0][0] ),
            ];
        }
    }

    if ( empty( $images ) ) return $content;

    $insertions = [];

    for ( $i = 0; $i < count( $images ); $i += 2 ) {
        $img1 = $images[ $i ];
        $img2 = $images[ $i + 1 ] ?? null;

        if ( $img2 ) {
            $html = "\n"
                . '<!-- wp:columns {"className":"cap-2col-image"} -->' . "\n"
                . '<div class="wp-block-columns cap-2col-image">' . "\n"
                . '<!-- wp:column -->' . "\n"
                . '<div class="wp-block-column">' . "\n"
                . '<!-- wp:image {"sizeSlug":"large"} -->' . "\n"
                . '<figure class="wp-block-image size-large">'
                .   '<img src="' . esc_url( $img1['url'] ) . '" alt="' . esc_attr( $img1['alt'] ) . '" loading="lazy"/>'
                .   '<figcaption class="wp-element-caption">' . esc_html( $img1['alt'] ) . '</figcaption>'
                . '</figure>' . "\n"
                . '<!-- /wp:image -->' . "\n"
                . '</div>' . "\n"
                . '<!-- /wp:column -->' . "\n"
                . '<!-- wp:column -->' . "\n"
                . '<div class="wp-block-column">' . "\n"
                . '<!-- wp:image {"sizeSlug":"large"} -->' . "\n"
                . '<figure class="wp-block-image size-large">'
                .   '<img src="' . esc_url( $img2['url'] ) . '" alt="' . esc_attr( $img2['alt'] ) . '" loading="lazy"/>'
                .   '<figcaption class="wp-element-caption">' . esc_html( $img2['alt'] ) . '</figcaption>'
                . '</figure>' . "\n"
                . '<!-- /wp:image -->' . "\n"
                . '</div>' . "\n"
                . '<!-- /wp:column -->' . "\n"
                . '</div>' . "\n"
                . '<!-- /wp:columns -->' . "\n";
        } else {
            $html = "\n"
                . '<!-- wp:image {"sizeSlug":"large","align":"wide"} -->' . "\n"
                . '<figure class="wp-block-image alignwide size-large">'
                .   '<img src="' . esc_url( $img1['url'] ) . '" alt="' . esc_attr( $img1['alt'] ) . '" loading="lazy"/>'
                .   '<figcaption class="wp-element-caption">' . esc_html( $img1['alt'] ) . '</figcaption>'
                . '</figure>' . "\n"
                . '<!-- /wp:image -->' . "\n";
        }

        $insertions[ $img1['offset'] ] = $html;
    }

    krsort( $insertions );
    foreach ( $insertions as $offset => $html ) {
        $content = substr_replace( $content, $html, $offset, 0 );
    }

    return $content;
}

// ──────────────────────────────────────────────
// wp_insert_post 時に kses フィルターを一時解除
// ──────────────────────────────────────────────

function cap_insert_post_raw( $post_data ) {
    remove_filter( 'content_save_pre', 'wp_filter_post_kses' );
    remove_filter( 'content_filtered_save_pre', 'wp_filter_post_kses' );
    $post_id = wp_insert_post( wp_slash( $post_data ) );
    add_filter( 'content_save_pre', 'wp_filter_post_kses' );
    add_filter( 'content_filtered_save_pre', 'wp_filter_post_kses' );
    return $post_id;
}

// ──────────────────────────────────────────────
// [v4.4.1] フォームからの設定保存を共通化
//   「設定を保存」と「今すぐ生成」の両方で使う
// ──────────────────────────────────────────────

function cap_save_form_settings() {
    $tmpl_url = esc_url_raw( trim( $_POST['template_page_url'] ?? '' ) );
    $tmpl_id  = 0;
    if ( $tmpl_url ) {
        $found = url_to_postid( $tmpl_url );
        if ( $found ) {
            $tmpl_id = $found;
        } else {
            $slug = basename( rtrim( parse_url( $tmpl_url, PHP_URL_PATH ), '/' ) );
            $q    = get_posts( [ 'name' => $slug, 'post_type' => 'any', 'numberposts' => 1, 'post_status' => 'any' ] );
            if ( $q ) $tmpl_id = $q[0]->ID;
        }
        if ( ! $tmpl_id ) cap_log( "⚠️ テンプレートURL解決失敗: {$tmpl_url}" );
    }

    $new = [
        'api_key'           => sanitize_text_field( $_POST['api_key']    ?? '' ),
        'gemini_key'        => sanitize_text_field( $_POST['gemini_key'] ?? '' ),
        'source_urls'       => sanitize_textarea_field( $_POST['source_urls'] ?? '' ),
        'template_page_url' => $tmpl_url,
        'template_page_id'  => $tmpl_id,
        'preserve_sections' => sanitize_textarea_field( $_POST['preserve_sections'] ?? '申請手続き' ),
        'chara_name'        => sanitize_text_field( $_POST['chara_name']  ?? 'けいちゃん' ),
        'chara_img_id'      => intval( $_POST['cap_chara_img_id']         ?? 0 ),
        'company'           => sanitize_text_field( $_POST['company']     ?? '' ),
        'model'             => sanitize_text_field( $_POST['model']       ?? 'claude-opus-4-8' ),
        'post_status'       => in_array( $_POST['post_status'], ['draft','publish'] ) ? $_POST['post_status'] : 'draft',
        'category'          => intval( $_POST['category'] ?? 0 ),
        'interval'          => in_array( $_POST['interval'], ['hourly','twicedaily','daily','cap_monthly'] ) ? $_POST['interval'] : 'daily',
        'gen_images'        => isset( $_POST['gen_images'] ) ? 1 : 0,
        'image_style'       => in_array( $_POST['image_style'], ['photo','flat','manga'] ) ? $_POST['image_style'] : 'photo',
    ];

    // [v4.4.1] 投稿元URLが変わったら処理済みリストを自動リセット
    $old = get_option( CAP_OPTION, [] );
    if ( ( $old['source_urls'] ?? '' ) !== $new['source_urls'] ) {
        update_option( 'cap_done_urls', [] );
        cap_log( '🔄 投稿元URLが変更されたため処理済みリストをリセットしました。' );
    }

    update_option( CAP_OPTION, $new );
    cap_reschedule_cron();
    return $new;
}

// ──────────────────────────────────────────────
// メイン処理
// ──────────────────────────────────────────────

function cap_run_auto_post() {
    $opts = get_option( CAP_OPTION, [] );

    $claude_key       = $opts['api_key']              ?? '';
    $gemini_key       = $opts['gemini_key']            ?? '';
    $model            = $opts['model']                 ?? 'claude-opus-4-8';
    $status           = $opts['post_status']           ?? 'draft';
    $category         = intval( $opts['category']      ?? 0 );
    $chara_name       = $opts['chara_name']            ?? 'けいちゃん';
    $chara_img        = intval( $opts['chara_img_id']  ?? 0 );
    $company          = $opts['company']               ?? '';
    $template_id      = $opts['template_page_id']      ?? '';
    $image_style      = $opts['image_style']           ?? 'photo';
    $gen_images       = ! empty( $opts['gen_images'] ) && ! empty( $gemini_key );
    $preserve_keywords = array_values( array_filter( array_map( 'trim',
        explode( "\n", $opts['preserve_sections'] ?? '申請手続き' )
    ) ) );

    if ( empty( $claude_key ) ) { cap_log( 'Claude APIキーが未設定です。' ); return; }

    $url = cap_next_url();
    if ( ! $url ) { cap_log( '投稿元URLが未設定です。' ); return; }

    cap_log( "処理開始: {$url}" );

    $source_text = cap_fetch_page_text( $url );
    if ( ! $source_text ) { cap_log( "ページ取得失敗: {$url}" ); return; }

    $template_raw   = cap_get_template_content( $template_id );
    $template_post  = $template_id ? get_post( intval( $template_id ) ) : null;
    $template_title = $template_post ? $template_post->post_title : '';

    // ── プロンプト構築（v4.4: 1500文字以上を明示指示）──
    if ( $template_raw ) {
        $prompt = "あなたはWordPress記事の編集者です。\n"
            . "以下の「テンプレート記事」のHTML・Cocoonショートコード・CSSクラスを完全に維持したまま、\n"
            . "「情報源」の内容に合わせて変わるべきテキストだけを書き換えてください。\n\n"
            . "=== テンプレート記事タイトル ===\n{$template_title}\n\n"
            . "=== テンプレート記事 post_content ===\n{$template_raw}\n\n"
            . "=== 情報源URL ===\n{$url}\n\n"
            . "=== 情報源の内容 ===\n{$source_text}\n\n"
            . "=== 書き換えルール ===\n"
            . "【文字数】\n"
            . "・本文は必ず1500文字以上にしてください（HTMLタグ除くテキスト量）。\n"
            . "・各H2セクション内に十分な解説文を入れ、薄い内容にしないこと。\n"
            . "・具体的な金額・条件・手順の説明に加え、読者向けのアドバイスや補足を入れて\n"
            . "  情報量を確保してください。\n\n"
            . "【変更する】\n"
            . "・記事タイトル（H1）→ 新しい市区町村・制度名\n"
            . "・リード文・概要 → 新しい制度の内容\n"
            . "・助成金の名称・金額・上限額 → 情報源から正確に転記\n"
            . "・申請できる条件・対象者 → 情報源から正確に転記\n"
            . "・期限・注意事項 → 情報源から正確に転記\n"
            . "・{$chara_name}のセリフ → 新しい制度に合わせて書き換え\n"
            . "・よくある質問 → 新しい制度に合わせて5問書き換え\n"
            . "・「この記事でわかること」箇条書き → 新しい内容に変更\n"
            . "・H2・H3の見出しテキスト → 質問形式（〜できますか？〜はいくらですか？）に\n\n"
            . "【絶対に変更しない】\n"
            . "・申請手続きの流れセクション（ステップ・必要書類・手順）→ 一字一句そのままコピー\n"
            . "・公式LINEのCTAセクション → そのままコピー\n"
            . "・HTMLタグ・CSSクラス・Cocoonショートコード → 一切変更しない\n"
            . "・imgタグのsrc属性 → 変更しない\n"
            . "・テーブル・リスト構造 → そのまま維持\n\n"
            . "【AEO対応】\n"
            . "・各セクション冒頭1〜2文に結論・直接回答を書く\n"
            . "・不明な情報は「公式サイトでご確認ください」と書く\n"
            . ( $company ? "・{$company}\n" : "" ) . "\n"
            . "出力はJSON形式のみ（コードブロック不要）:\n"
            . '{"title":"新タイトル","content":"書き換え後のpost_content全文（1500文字以上）","excerpt":"概要120文字","h1_image_prompt":"eyecatch prompt in English","faq_schema":[{"question":"Q1","answer":"A1"},{"question":"Q2","answer":"A2"},{"question":"Q3","answer":"A3"},{"question":"Q4","answer":"A4"},{"question":"Q5","answer":"A5"}]}';
    } else {
        $prompt = "外壁塗装専門ライターとして助成金記事を作成してください。\n"
            . "【ソースURL】{$url}\n【内容】{$source_text}\n"
            . "AEO最適化・正確性最優先・H2は質問形式。\n"
            . "★重要：本文は必ず1500文字以上で書いてください。各セクションに具体的な金額・条件の解説に加え、\n"
            . "読者向けのアドバイスや補足情報を入れて情報量を確保してください。\n"
            . ( $company ? $company . "\n" : "" )
            . '出力はJSON: {"title":"...","content":"HTML本文（1500文字以上）","excerpt":"120文字","h1_image_prompt":"English","faq_schema":[{"question":"Q1","answer":"A1"},{"question":"Q2","answer":"A2"},{"question":"Q3","answer":"A3"},{"question":"Q4","answer":"A4"},{"question":"Q5","answer":"A5"}]}';
    }

    // ── Claude API 呼び出し ──
    $response = wp_remote_post( 'https://api.anthropic.com/v1/messages', [
        'timeout' => 180,
        'headers' => [
            'x-api-key'         => $claude_key,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ],
        'body' => json_encode( [
            'model'      => $model,
            'max_tokens' => 16000,
            'messages'   => [ [ 'role' => 'user', 'content' => $prompt ] ],
        ] ),
    ] );

    if ( is_wp_error( $response ) ) { cap_log( 'Claude APIエラー: ' . $response->get_error_message() ); return; }

    $http_code = wp_remote_retrieve_response_code( $response );
    $body      = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( ( $body['stop_reason'] ?? '' ) === 'max_tokens' ) {
        cap_log( '⚠️ Claude応答がmax_tokensで打ち切られました。テンプレートが長すぎる可能性があります。' );
    }

    $raw = $body['content'][0]['text'] ?? '';
    if ( empty( $raw ) ) { cap_log( "Claude応答が空です。HTTP{$http_code}: " . substr( wp_remote_retrieve_body( $response ), 0, 300 ) ); return; }

    if ( preg_match( '/```(?:json)?\s*([\s\S]+?)\s*```/s', $raw, $m ) ) $raw = $m[1];
    if ( preg_match( '/(\{[\s\S]*\})/s', $raw, $m2 ) ) $raw = $m2[1];

    $article = json_decode( $raw, true );

    if ( json_last_error() !== JSON_ERROR_NONE ) { cap_log( 'JSONパースエラー(' . json_last_error_msg() . '): ' . substr( $raw, 0, 400 ) ); return; }
    if ( empty( $article['title'] ) || empty( $article['content'] ) ) { cap_log( 'title/content が空です: ' . substr( $raw, 0, 300 ) ); return; }

    $content = $article['content'];

    // 文字数チェック＆ログ
    $text_only   = wp_strip_all_tags( $content );
    $char_count  = mb_strlen( $text_only );
    cap_log( "生成文字数: {$char_count}文字" );
    if ( $char_count < 1500 ) {
        cap_log( "⚠️ 文字数が1500文字未満です（{$char_count}文字）。投稿は続行しますが内容追記を推奨します。" );
    }

    // ── 強制コピーセクションをテンプレートから上書き ──
    if ( $template_raw && ! empty( $preserve_keywords ) ) {
        foreach ( $preserve_keywords as $keyword ) {
            $tmpl_section = cap_extract_h2_section( $template_raw, $keyword );
            if ( $tmpl_section ) {
                $content = cap_replace_h2_section( $content, $keyword, $tmpl_section );
                cap_log( "✅ セクション強制コピー完了:「{$keyword}」" );
            } else {
                cap_log( "⚠️ テンプレート内に「{$keyword}」セクションが見つかりませんでした。" );
            }
        }
    }

    // 漫画キャラ画像の差し替え
    if ( $chara_img ) {
        $chara_url = wp_get_attachment_url( $chara_img );
        $content   = preg_replace(
            '/(<img[^>]*src=")([^"]*(?:keipaintadd|page-1-1|chara)[^"]*)(")/i',
            '$1' . esc_url( $chara_url ) . '$3',
            $content
        );
    }

    // 各H2に2カラムおしゃれ画像挿入
    if ( $gen_images ) {
        cap_log( '画像生成中（2カラムレイアウト）...' );
        $content = cap_inject_section_images( $content, $article['title'], $gemini_key, $image_style );
    }

    $source_link  = "\n" . '<p style="font-size:0.85em;color:#666;">出典: <a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . esc_html( $url ) . '</a></p>';
    $full_content = $content . $source_link;

    // WordPress に投稿（kses フィルター一時解除）
    $post_data = [
        'post_title'   => sanitize_text_field( $article['title'] ),
        'post_content' => $full_content,
        'post_excerpt' => sanitize_text_field( $article['excerpt'] ?? '' ),
        'post_status'  => $status,
        'post_author'  => 1,
        'post_type'    => $template_post ? $template_post->post_type : 'post',
    ];
    if ( $category > 0 ) $post_data['post_category'] = [ $category ];

    $post_id = cap_insert_post_raw( $post_data );
    if ( is_wp_error( $post_id ) ) { cap_log( '投稿エラー: ' . $post_id->get_error_message() ); return; }

    // 自動生成フラグ（CSS適用判定用）
    update_post_meta( $post_id, '_cap_generated', 1 );

    // テンプレートのカスタムフィールドをコピー（プライベートキー除く）
    if ( $template_post ) {
        foreach ( get_post_meta( $template_post->ID ) as $key => $values ) {
            if ( strpos( $key, '_' ) !== 0 ) {
                foreach ( $values as $v ) add_post_meta( $post_id, $key, maybe_unserialize( $v ) );
            }
        }
    }

    // FAQ JSON-LD を post_meta に保存
    if ( ! empty( $article['faq_schema'] ) ) {
        $schema = [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array_map( function( $q ) {
                return [
                    '@type'          => 'Question',
                    'name'           => $q['question'],
                    'acceptedAnswer' => [ '@type' => 'Answer', 'text' => $q['answer'] ],
                ];
            }, $article['faq_schema'] ),
        ];
        update_post_meta( $post_id, '_cap_faq_schema', $schema );
    }

    // アイキャッチ画像生成
    if ( $gen_images && ! empty( $article['h1_image_prompt'] ) ) {
        cap_log( 'アイキャッチ生成中...' );
        $eid = cap_generate_image( $article['h1_image_prompt'] . ' No text. For Japanese home improvement website.', $gemini_key );
        if ( $eid ) { set_post_thumbnail( $post_id, $eid ); cap_log( "アイキャッチ設定 ID:{$eid}" ); }
    }

    cap_log( "✅ 完了 ID:{$post_id}「{$article['title']}」({$char_count}文字)" );
}

// ──────────────────────────────────────────────
// body_class に cap-styled-title を追加
// ──────────────────────────────────────────────

add_filter( 'body_class', function( $classes ) {
    if ( is_singular() && get_post_meta( get_the_ID(), '_cap_generated', true ) ) {
        $classes[] = 'cap-styled-title';
    }
    return $classes;
} );

// ──────────────────────────────────────────────
// ログ
// ──────────────────────────────────────────────

function cap_log( $msg ) {
    $logs   = get_option( 'cap_logs', [] );
    $logs[] = '[' . current_time( 'Y-m-d H:i:s' ) . '] ' . $msg;
    if ( count( $logs ) > 100 ) $logs = array_slice( $logs, -100 );
    update_option( 'cap_logs', $logs );
}

// ──────────────────────────────────────────────
// メディアライブラリ選択JS
// ──────────────────────────────────────────────

add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( strpos( $hook, 'claude-auto-poster' ) === false ) return;
    wp_enqueue_media();
    wp_add_inline_script( 'jquery', "
        jQuery(function($){
            $('#cap-select-chara').on('click', function(e){
                e.preventDefault();
                var frame = wp.media({ title: 'キャラ画像を選択', button: { text: '選択' }, multiple: false });
                frame.on('select', function(){
                    var att = frame.state().get('selection').first().toJSON();
                    $('#cap_chara_img_id').val(att.id);
                    $('#cap-chara-preview').html('<img src=\"'+att.url+'\" style=\"max-height:80px;margin-top:8px;border-radius:4px;\">');
                });
                frame.open();
            });
        });
    " );
} );

// ──────────────────────────────────────────────
// 管理画面
// ──────────────────────────────────────────────

add_action( 'admin_menu', function() {
    add_menu_page( 'Claude Auto Poster', 'Auto Poster', 'manage_options',
        'claude-auto-poster', 'cap_settings_page', 'dashicons-edit-large', 30 );
} );

function cap_settings_page() {
    $opts = get_option( CAP_OPTION, [] );

    // ──────────────────────────────────────────
    // [v4.4.1] 「設定を保存」ハンドラ
    // ──────────────────────────────────────────
    if ( isset( $_POST['cap_save'] ) && check_admin_referer( 'cap_save' ) ) {
        $opts = cap_save_form_settings();
        echo '<div class="notice notice-success"><p>保存しました。</p></div>';
    }

    // ──────────────────────────────────────────
    // [v4.4.1 fix] 「今すぐ生成」ハンドラ
    //   旧版: 設定を保存せずに実行 → 古いURLで処理されていた
    //   新版: 必ず設定を保存してから実行する
    // ──────────────────────────────────────────
    if ( isset( $_POST['cap_run_now'] ) && check_admin_referer( 'cap_save' ) ) {
        $opts = cap_save_form_settings();
        cap_run_auto_post();
        echo '<div class="notice notice-info"><p>設定を保存して手動実行しました。ログを確認してください。</p></div>';
    }

    if ( isset( $_POST['cap_clear_done'] ) && check_admin_referer( 'cap_save' ) ) {
        update_option( 'cap_done_urls', [] );
        echo '<div class="notice notice-info"><p>処理済みURLをリセットしました。</p></div>';
    }

    $categories    = get_categories( ['hide_empty' => false] );
    $logs          = array_reverse( get_option( 'cap_logs', [] ) );
    $done_urls     = get_option( 'cap_done_urls', [] );
    $next_run      = wp_next_scheduled( CAP_CRON );
    $chara_img_id  = intval( $opts['chara_img_id'] ?? 0 );
    $chara_preview = $chara_img_id ? wp_get_attachment_image( $chara_img_id, [80,80] ) : '';
    $tmpl_id       = intval( $opts['template_page_id'] ?? 0 );
    $tmpl_post     = $tmpl_id ? get_post( $tmpl_id ) : null;
    $tmpl_label    = $tmpl_post ? '✅ ' . $tmpl_post->post_title . ' (ID:' . $tmpl_id . ')' : '';
    ?>
    <div class="wrap">
        <h1>🤖 Claude Auto Poster <small style="font-size:13px;color:#7a8fa6;">v4.5.1</small></h1>
        <p>次回自動実行: <strong><?= $next_run ? date_i18n('Y-m-d H:i:s',$next_run) : '未設定' ?></strong>
           &nbsp;|&nbsp; 処理済みURL: <strong><?= count($done_urls) ?>件</strong></p>

        <form method="post">
            <?php wp_nonce_field('cap_save'); ?>

            <h2 style="border-left:4px solid #7a8fa6;padding-left:10px;">🔑 API設定</h2>
            <table class="form-table">
                <tr>
                    <th>Claude APIキー</th>
                    <td><input type="password" name="api_key" value="<?= esc_attr($opts['api_key']??'') ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>Google Gemini APIキー<br><small>（画像生成用）</small></th>
                    <td>
                        <input type="password" name="gemini_key" value="<?= esc_attr($opts['gemini_key']??'') ?>" class="regular-text">
                        <p class="description"><a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio で取得</a> — Imagen 4使用</p>
                    </td>
                </tr>
            </table>

            <h2 style="border-left:4px solid #7a8fa6;padding-left:10px;">📄 テンプレート・ソース設定</h2>
            <table class="form-table">
                <tr>
                    <th>テンプレート記事URL</th>
                    <td>
                        <input type="url" name="template_page_url"
                               value="<?= esc_attr($opts['template_page_url']??'') ?>"
                               class="large-text"
                               placeholder="https://keichanpaint-main.group/東京都/kokudokoutuugrant/">
                        <input type="hidden" name="template_page_id" value="<?= esc_attr($tmpl_id) ?>">
                        <?php if($tmpl_label): ?>
                        <p class="description" style="color:#7a8fa6;"><?= esc_html($tmpl_label) ?></p>
                        <?php endif; ?>
                        <p class="description">
                            ⭐ このURLの記事のHTMLをそのままコピーし、変わる部分だけ書き換えます。
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>投稿元URL<br><small>（1行1URL）</small></th>
                    <td>
                        <textarea name="source_urls" rows="8" class="large-text"><?= esc_textarea($opts['source_urls']??'') ?></textarea>
                        <p class="description">市区町村の助成金公式ページ。上から順番に1件ずつ処理します。<br>
                        ※ URLを変更すると処理済みリストは自動リセットされます。<br>
                        ※ GitHubからの自動取り込みは「ツール → 助成金管理（東京）」画面で行ってください。</p>
                    </td>
                </tr>
            </table>

            <h2 style="border-left:4px solid #7a8fa6;padding-left:10px;">🔒 常にコピーするセクション</h2>
            <table class="form-table">
                <tr>
                    <th>強制コピーするH2キーワード<br><small>（1行1キーワード）</small></th>
                    <td>
                        <textarea name="preserve_sections" rows="4" class="large-text"><?= esc_textarea($opts['preserve_sections']??'申請手続き') ?></textarea>
                        <p class="description">
                            ここに入力したキーワードを含むH2セクションは、<strong>AIの書き換えを上書きして</strong>テンプレートの内容をそのままコピーします。
                        </p>
                    </td>
                </tr>
            </table>

            <h2 style="border-left:4px solid #7a8fa6;padding-left:10px;">🎨 画像設定</h2>
            <table class="form-table">
                <tr>
                    <th>画像を自動生成する</th>
                    <td>
                        <label>
                            <input type="checkbox" name="gen_images" value="1" <?= checked($opts['gen_images']??0,1,false) ?>>
                            アイキャッチ・各H2の画像をGemini（Imagen 4）で自動生成する
                        </label>
                        <p class="description">画像は2カラムレイアウトで自動配置されます。</p>
                    </td>
                </tr>
                <tr>
                    <th>画像スタイル</th>
                    <td>
                        <select name="image_style">
                            <option value="photo" <?= selected($opts['image_style']??'photo','photo',false) ?>>写真風（リアル）</option>
                            <option value="flat"  <?= selected($opts['image_style']??'photo','flat', false) ?>>フラットイラスト</option>
                            <option value="manga" <?= selected($opts['image_style']??'photo','manga',false) ?>>漫画風イラスト</option>
                        </select>
                    </td>
                </tr>
            </table>

            <h2 style="border-left:4px solid #7a8fa6;padding-left:10px;">🦸 漫画キャラクター設定</h2>
            <table class="form-table">
                <tr>
                    <th>キャラクター名</th>
                    <td><input type="text" name="chara_name" value="<?= esc_attr($opts['chara_name']??'けいちゃん') ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th>固定キャラ画像</th>
                    <td>
                        <input type="hidden" name="cap_chara_img_id" id="cap_chara_img_id" value="<?= esc_attr($chara_img_id?:'') ?>">
                        <button type="button" id="cap-select-chara" class="button">画像を選択（メディアライブラリ）</button>
                        <div id="cap-chara-preview" style="margin-top:8px;"><?= $chara_preview ?></div>
                    </td>
                </tr>
                <tr>
                    <th>会社名・PR文</th>
                    <td>
                        <input type="text" name="company" value="<?= esc_attr($opts['company']??'') ?>" class="large-text">
                        <p class="description">例: けいちゃんペイント（東京・神奈川の外壁塗装専門店）</p>
                    </td>
                </tr>
            </table>

            <h2 style="border-left:4px solid #7a8fa6;padding-left:10px;">⚙️ 投稿設定</h2>
            <table class="form-table">
                <tr>
                    <th>Claudeモデル</th>
                    <td>
                        <select name="model">
                            <option value="claude-opus-4-8"           <?= selected($opts['model']??'claude-opus-4-8','claude-opus-4-8',          false) ?>>Opus 4（最高品質・推奨）</option>
                            <option value="claude-sonnet-4-6"         <?= selected($opts['model']??'claude-opus-4-8','claude-sonnet-4-6',        false) ?>>Sonnet 4（バランス）</option>
                            <option value="claude-haiku-4-5-20251001" <?= selected($opts['model']??'claude-opus-4-8','claude-haiku-4-5-20251001',false) ?>>Haiku 4（高速・安価）</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>投稿ステータス</th>
                    <td>
                        <select name="post_status">
                            <option value="draft"   <?= selected($opts['post_status']??'draft','draft',  false) ?>>下書き</option>
                            <option value="publish" <?= selected($opts['post_status']??'draft','publish',false) ?>>即公開</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>カテゴリー</th>
                    <td>
                        <select name="category">
                            <option value="0">なし</option>
                            <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat->term_id ?>" <?= selected($opts['category']??0,$cat->term_id,false) ?>><?= esc_html($cat->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>投稿間隔</th>
                    <td>
                        <select name="interval">
                            <option value="hourly"     <?= selected($opts['interval']??'daily','hourly',     false) ?>>毎時</option>
                            <option value="twicedaily" <?= selected($opts['interval']??'daily','twicedaily', false) ?>>1日2回</option>
                            <option value="daily"      <?= selected($opts['interval']??'daily','daily',      false) ?>>毎日</option>
                            <option value="cap_monthly"<?= selected($opts['interval']??'daily','cap_monthly',false) ?>>毎月</option>
                        </select>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="cap_save"       class="button button-primary"   value="設定を保存">
                &nbsp;
                <input type="submit" name="cap_run_now"    class="button button-secondary" value="▶ 今すぐ1記事生成・投稿"
                       onclick="return confirm('設定を保存して次のURLから記事を生成します。よろしいですか？')">
                &nbsp;
                <input type="submit" name="cap_clear_done" class="button"                  value="処理済みURLをリセット">
            </p>
        </form>

        <?php if(!empty($done_urls)): ?>
        <details style="margin-bottom:20px;">
            <summary>処理済みURL（<?= count($done_urls) ?>件）</summary>
            <ul><?php foreach($done_urls as $u): ?><li><?= esc_html($u) ?></li><?php endforeach; ?></ul>
        </details>
        <?php endif; ?>

        <hr>
        <h2>ログ（直近100件）</h2>
        <?php if($logs): ?>
        <textarea readonly style="width:100%;height:260px;font-family:monospace;font-size:12px;"><?= esc_textarea(implode("\n",$logs)) ?></textarea>
        <?php else: ?>
        <p>ログはまだありません。</p>
        <?php endif; ?>
    </div>
    <?php
}
