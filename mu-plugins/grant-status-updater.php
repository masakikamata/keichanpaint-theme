<?php
/**
 * Plugin Name: 助成金ステータス管理（東京）
 * Description: 東京都の市区町村ごとに助成金名・残り期間・残り枠を管理する
 * Version: 2.0.0
 * 設置場所: wp-content/mu-plugins/grant-status-updater.php
 *
 * データ保存先: wp_options  キー: keichan_grant_tokyo
 * 管理画面:     WP管理画面 > ツール > 助成金管理（東京）
 */

// -------------------------------------------------------
// 東京都の市区町村マスタ
// -------------------------------------------------------
function keichan_grant_tokyo_cities(): array {
    return [
        '千代田区', '中央区', '港区', '新宿区', '文京区', '台東区', '墨田区',
        '江東区', '品川区', '目黒区', '大田区', '世田谷区', '渋谷区', '中野区',
        '杉並区', '豊島区', '北区', '荒川区', '板橋区', '練馬区', '足立区',
        '葛飾区', '江戸川区',
        '八王子市', '立川市', '武蔵野市', '三鷹市', '青梅市', '府中市', '昭島市',
        '調布市', '町田市', '小金井市', '小平市', '日野市', '東村山市', '国分寺市',
        '国立市', '福生市', '狛江市', '東大和市', '清瀬市', '東久留米市', '武蔵村山市',
        '多摩市', '稲城市', '羽村市', 'あきる野市', '西東京市',
    ];
}

// -------------------------------------------------------
// 初期データ作成
// -------------------------------------------------------
function keichan_grant_maybe_init(): void {
    if ( get_option( 'keichan_grant_tokyo' ) !== false ) {
        return;
    }
    $initial = [];
    foreach ( keichan_grant_tokyo_cities() as $city ) {
        $initial[ $city ] = [
            'grant_name' => '',
            'period'     => '',
            'slots'      => '',
            'url'        => '',
        ];
    }
    add_option( 'keichan_grant_tokyo', $initial, '', false );
}
add_action( 'init', 'keichan_grant_maybe_init' );

// -------------------------------------------------------
// 管理画面メニュー
// -------------------------------------------------------
add_action( 'admin_menu', function () {
    add_management_page(
        '助成金管理（東京）',
        '助成金管理（東京）',
        'manage_options',
        'keichan-grant-tokyo',
        'keichan_grant_admin_page'
    );
} );

function keichan_grant_admin_page(): void {
    if ( ! current_user_can( 'manage_options' ) ) return;

    // 保存処理
    if ( isset( $_POST['keichan_save'] ) && check_admin_referer( 'keichan_grant_save' ) ) {
        $data = get_option( 'keichan_grant_tokyo', [] );
        $posted = $_POST['grants'] ?? [];

        foreach ( $posted as $city => $fields ) {
            $city = wp_unslash( $city );
            if ( ! isset( $data[ $city ] ) ) continue;
            $data[ $city ] = [
                'grant_name' => sanitize_text_field( wp_unslash( $fields['grant_name'] ?? '' ) ),
                'period'     => sanitize_text_field( wp_unslash( $fields['period'] ?? '' ) ),
                'slots'      => sanitize_text_field( wp_unslash( $fields['slots'] ?? '' ) ),
                'url'        => sanitize_text_field( wp_unslash( $fields['url'] ?? '' ) ),
            ];
        }
        update_option( 'keichan_grant_tokyo', $data, false );
        update_option( 'keichan_grant_tokyo_updated', current_time( 'Y-m-d H:i:s' ), false );
        echo '<div class="notice notice-success"><p>保存しました。</p></div>';
    }

    $data    = get_option( 'keichan_grant_tokyo', [] );
    $updated = get_option( 'keichan_grant_tokyo_updated', '未保存' );
    ?>
    <div class="wrap">
        <h1>助成金管理（東京都）</h1>
        <p>最終更新: <strong><?php echo esc_html( $updated ); ?></strong></p>
        <p>各市区町村の <strong>助成金名・残り期間・残り枠・URL</strong> を入力して、最下部の「保存」ボタンを押してください。</p>

        <form method="post">
            <?php wp_nonce_field( 'keichan_grant_save' ); ?>
            <input type="hidden" name="keichan_save" value="1">

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th style="width:100px;">市区町村</th>
                        <th>助成金名</th>
                        <th style="width:180px;">残り期間</th>
                        <th style="width:120px;">残り枠</th>
                        <th>参照URL</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( keichan_grant_tokyo_cities() as $city ) :
                    $row = $data[ $city ] ?? [ 'grant_name' => '', 'period' => '', 'slots' => '', 'url' => '' ];
                ?>
                    <tr>
                        <td><strong><?php echo esc_html( $city ); ?></strong></td>
                        <td>
                            <input type="text" style="width:100%;"
                                   name="grants[<?php echo esc_attr( $city ); ?>][grant_name]"
                                   value="<?php echo esc_attr( $row['grant_name'] ); ?>"
                                   placeholder="例：住宅リフォーム助成金">
                        </td>
                        <td>
                            <input type="text" style="width:100%;"
                                   name="grants[<?php echo esc_attr( $city ); ?>][period]"
                                   value="<?php echo esc_attr( $row['period'] ); ?>"
                                   placeholder="例：2026/3/31まで">
                        </td>
                        <td>
                            <input type="text" style="width:100%;"
                                   name="grants[<?php echo esc_attr( $city ); ?>][slots]"
                                   value="<?php echo esc_attr( $row['slots'] ); ?>"
                                   placeholder="例：残り20件">
                        </td>
                        <td>
                            <input type="text" style="width:100%;"
                                   name="grants[<?php echo esc_attr( $city ); ?>][url]"
                                   value="<?php echo esc_attr( $row['url'] ); ?>"
                                   placeholder="https://...">
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <p style="margin-top:1em;">
                <?php submit_button( '保存', 'primary', 'submit', false ); ?>
            </p>
        </form>
    </div>
    <?php
}
