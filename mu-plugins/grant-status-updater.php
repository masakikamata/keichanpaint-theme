<?php
/**
 * Plugin Name: 助成金ステータス自動更新（関東）
 * Description: 東京・神奈川・埼玉・千葉の市区町村ごとに助成金の枠の埋まり具合を月1回スクレイピング/API取得して保存する
 * Version: 1.0.0
 * 設置場所: wp-content/mu-plugins/grant-status-updater.php
 *
 * データ保存先: wp_options テーブル  キー: keichan_grant_status
 * 手動更新:     WP管理画面 > ツール > 助成金ステータス更新
 */

// -------------------------------------------------------
// 対象市区町村マスタ
// -------------------------------------------------------
function keichan_grant_cities(): array {
    return [
        '東京都' => [
            '千代田区' => [ 'url' => '', 'label' => '千代田区' ],
            '中央区'   => [ 'url' => '', 'label' => '中央区' ],
            '港区'     => [ 'url' => '', 'label' => '港区' ],
            '新宿区'   => [ 'url' => '', 'label' => '新宿区' ],
            '文京区'   => [ 'url' => '', 'label' => '文京区' ],
            '台東区'   => [ 'url' => '', 'label' => '台東区' ],
            '墨田区'   => [ 'url' => '', 'label' => '墨田区' ],
            '江東区'   => [ 'url' => '', 'label' => '江東区' ],
            '品川区'   => [ 'url' => '', 'label' => '品川区' ],
            '目黒区'   => [ 'url' => '', 'label' => '目黒区' ],
            '大田区'   => [ 'url' => '', 'label' => '大田区' ],
            '世田谷区' => [ 'url' => '', 'label' => '世田谷区' ],
            '渋谷区'   => [ 'url' => '', 'label' => '渋谷区' ],
            '中野区'   => [ 'url' => '', 'label' => '中野区' ],
            '杉並区'   => [ 'url' => '', 'label' => '杉並区' ],
            '豊島区'   => [ 'url' => '', 'label' => '豊島区' ],
            '北区'     => [ 'url' => '', 'label' => '北区' ],
            '荒川区'   => [ 'url' => '', 'label' => '荒川区' ],
            '板橋区'   => [ 'url' => '', 'label' => '板橋区' ],
            '練馬区'   => [ 'url' => '', 'label' => '練馬区' ],
            '足立区'   => [ 'url' => '', 'label' => '足立区' ],
            '葛飾区'   => [ 'url' => '', 'label' => '葛飾区' ],
            '江戸川区' => [ 'url' => '', 'label' => '江戸川区' ],
            '八王子市' => [ 'url' => '', 'label' => '八王子市' ],
            '立川市'   => [ 'url' => '', 'label' => '立川市' ],
            '武蔵野市' => [ 'url' => '', 'label' => '武蔵野市' ],
            '三鷹市'   => [ 'url' => '', 'label' => '三鷹市' ],
            '青梅市'   => [ 'url' => '', 'label' => '青梅市' ],
            '府中市'   => [ 'url' => '', 'label' => '府中市' ],
            '昭島市'   => [ 'url' => '', 'label' => '昭島市' ],
            '調布市'   => [ 'url' => '', 'label' => '調布市' ],
            '町田市'   => [ 'url' => '', 'label' => '町田市' ],
            '小金井市' => [ 'url' => '', 'label' => '小金井市' ],
            '小平市'   => [ 'url' => '', 'label' => '小平市' ],
            '日野市'   => [ 'url' => '', 'label' => '日野市' ],
            '東村山市' => [ 'url' => '', 'label' => '東村山市' ],
            '国分寺市' => [ 'url' => '', 'label' => '国分寺市' ],
            '国立市'   => [ 'url' => '', 'label' => '国立市' ],
            '福生市'   => [ 'url' => '', 'label' => '福生市' ],
            '狛江市'   => [ 'url' => '', 'label' => '狛江市' ],
            '東大和市' => [ 'url' => '', 'label' => '東大和市' ],
            '清瀬市'   => [ 'url' => '', 'label' => '清瀬市' ],
            '東久留米市' => [ 'url' => '', 'label' => '東久留米市' ],
            '武蔵村山市' => [ 'url' => '', 'label' => '武蔵村山市' ],
            '多摩市'   => [ 'url' => '', 'label' => '多摩市' ],
            '稲城市'   => [ 'url' => '', 'label' => '稲城市' ],
            '羽村市'   => [ 'url' => '', 'label' => '羽村市' ],
            'あきる野市' => [ 'url' => '', 'label' => 'あきる野市' ],
            '西東京市' => [ 'url' => '', 'label' => '西東京市' ],
        ],
        '神奈川県' => [
            '横浜市'   => [ 'url' => '', 'label' => '横浜市' ],
            '川崎市'   => [ 'url' => '', 'label' => '川崎市' ],
            '相模原市' => [ 'url' => '', 'label' => '相模原市' ],
            '横須賀市' => [ 'url' => '', 'label' => '横須賀市' ],
            '平塚市'   => [ 'url' => '', 'label' => '平塚市' ],
            '鎌倉市'   => [ 'url' => '', 'label' => '鎌倉市' ],
            '藤沢市'   => [ 'url' => '', 'label' => '藤沢市' ],
            '小田原市' => [ 'url' => '', 'label' => '小田原市' ],
            '茅ヶ崎市' => [ 'url' => '', 'label' => '茅ヶ崎市' ],
            '逗子市'   => [ 'url' => '', 'label' => '逗子市' ],
            '三浦市'   => [ 'url' => '', 'label' => '三浦市' ],
            '秦野市'   => [ 'url' => '', 'label' => '秦野市' ],
            '厚木市'   => [ 'url' => '', 'label' => '厚木市' ],
            '大和市'   => [ 'url' => '', 'label' => '大和市' ],
            '伊勢原市' => [ 'url' => '', 'label' => '伊勢原市' ],
            '海老名市' => [ 'url' => '', 'label' => '海老名市' ],
            '座間市'   => [ 'url' => '', 'label' => '座間市' ],
            '南足柄市' => [ 'url' => '', 'label' => '南足柄市' ],
            '綾瀬市'   => [ 'url' => '', 'label' => '綾瀬市' ],
        ],
        '埼玉県' => [
            'さいたま市' => [ 'url' => '', 'label' => 'さいたま市' ],
            '川越市'   => [ 'url' => '', 'label' => '川越市' ],
            '熊谷市'   => [ 'url' => '', 'label' => '熊谷市' ],
            '川口市'   => [ 'url' => '', 'label' => '川口市' ],
            '行田市'   => [ 'url' => '', 'label' => '行田市' ],
            '秩父市'   => [ 'url' => '', 'label' => '秩父市' ],
            '所沢市'   => [ 'url' => '', 'label' => '所沢市' ],
            '飯能市'   => [ 'url' => '', 'label' => '飯能市' ],
            '加須市'   => [ 'url' => '', 'label' => '加須市' ],
            '本庄市'   => [ 'url' => '', 'label' => '本庄市' ],
            '東松山市' => [ 'url' => '', 'label' => '東松山市' ],
            '春日部市' => [ 'url' => '', 'label' => '春日部市' ],
            '狭山市'   => [ 'url' => '', 'label' => '狭山市' ],
            '羽生市'   => [ 'url' => '', 'label' => '羽生市' ],
            '鴻巣市'   => [ 'url' => '', 'label' => '鴻巣市' ],
            '深谷市'   => [ 'url' => '', 'label' => '深谷市' ],
            '上尾市'   => [ 'url' => '', 'label' => '上尾市' ],
            '草加市'   => [ 'url' => '', 'label' => '草加市' ],
            '越谷市'   => [ 'url' => '', 'label' => '越谷市' ],
            '蕨市'     => [ 'url' => '', 'label' => '蕨市' ],
            '戸田市'   => [ 'url' => '', 'label' => '戸田市' ],
            '入間市'   => [ 'url' => '', 'label' => '入間市' ],
            '朝霞市'   => [ 'url' => '', 'label' => '朝霞市' ],
            '志木市'   => [ 'url' => '', 'label' => '志木市' ],
            '和光市'   => [ 'url' => '', 'label' => '和光市' ],
            '新座市'   => [ 'url' => '', 'label' => '新座市' ],
            '桶川市'   => [ 'url' => '', 'label' => '桶川市' ],
            '久喜市'   => [ 'url' => '', 'label' => '久喜市' ],
            '北本市'   => [ 'url' => '', 'label' => '北本市' ],
            '八潮市'   => [ 'url' => '', 'label' => '八潮市' ],
            '富士見市' => [ 'url' => '', 'label' => '富士見市' ],
            '三郷市'   => [ 'url' => '', 'label' => '三郷市' ],
            '蓮田市'   => [ 'url' => '', 'label' => '蓮田市' ],
            '坂戸市'   => [ 'url' => '', 'label' => '坂戸市' ],
            '幸手市'   => [ 'url' => '', 'label' => '幸手市' ],
            '鶴ヶ島市' => [ 'url' => '', 'label' => '鶴ヶ島市' ],
            '日高市'   => [ 'url' => '', 'label' => '日高市' ],
            '吉川市'   => [ 'url' => '', 'label' => '吉川市' ],
            'ふじみ野市' => [ 'url' => '', 'label' => 'ふじみ野市' ],
            '白岡市'   => [ 'url' => '', 'label' => '白岡市' ],
        ],
        '千葉県' => [
            '千葉市'   => [ 'url' => '', 'label' => '千葉市' ],
            '銚子市'   => [ 'url' => '', 'label' => '銚子市' ],
            '市川市'   => [ 'url' => '', 'label' => '市川市' ],
            '船橋市'   => [ 'url' => '', 'label' => '船橋市' ],
            '館山市'   => [ 'url' => '', 'label' => '館山市' ],
            '木更津市' => [ 'url' => '', 'label' => '木更津市' ],
            '松戸市'   => [ 'url' => '', 'label' => '松戸市' ],
            '野田市'   => [ 'url' => '', 'label' => '野田市' ],
            '茂原市'   => [ 'url' => '', 'label' => '茂原市' ],
            '成田市'   => [ 'url' => '', 'label' => '成田市' ],
            '佐倉市'   => [ 'url' => '', 'label' => '佐倉市' ],
            '東金市'   => [ 'url' => '', 'label' => '東金市' ],
            '旭市'     => [ 'url' => '', 'label' => '旭市' ],
            '習志野市' => [ 'url' => '', 'label' => '習志野市' ],
            '柏市'     => [ 'url' => '', 'label' => '柏市' ],
            '勝浦市'   => [ 'url' => '', 'label' => '勝浦市' ],
            '市原市'   => [ 'url' => '', 'label' => '市原市' ],
            '流山市'   => [ 'url' => '', 'label' => '流山市' ],
            '八千代市' => [ 'url' => '', 'label' => '八千代市' ],
            '我孫子市' => [ 'url' => '', 'label' => '我孫子市' ],
            '鴨川市'   => [ 'url' => '', 'label' => '鴨川市' ],
            '鎌ケ谷市' => [ 'url' => '', 'label' => '鎌ケ谷市' ],
            '君津市'   => [ 'url' => '', 'label' => '君津市' ],
            '富津市'   => [ 'url' => '', 'label' => '富津市' ],
            '浦安市'   => [ 'url' => '', 'label' => '浦安市' ],
            '四街道市' => [ 'url' => '', 'label' => '四街道市' ],
            '袖ケ浦市' => [ 'url' => '', 'label' => '袖ケ浦市' ],
            '八街市'   => [ 'url' => '', 'label' => '八街市' ],
            '印西市'   => [ 'url' => '', 'label' => '印西市' ],
            '白井市'   => [ 'url' => '', 'label' => '白井市' ],
            '富里市'   => [ 'url' => '', 'label' => '富里市' ],
            '南房総市' => [ 'url' => '', 'label' => '南房総市' ],
            '匝瑳市'   => [ 'url' => '', 'label' => '匝瑳市' ],
            '香取市'   => [ 'url' => '', 'label' => '香取市' ],
            '山武市'   => [ 'url' => '', 'label' => '山武市' ],
            'いすみ市' => [ 'url' => '', 'label' => 'いすみ市' ],
            '大網白里市' => [ 'url' => '', 'label' => '大網白里市' ],
        ],
    ];
}

// -------------------------------------------------------
// ステータス定数
// -------------------------------------------------------
define( 'KEICHAN_GRANT_AVAILABLE',   'available' );   // 受付中（空きあり）
define( 'KEICHAN_GRANT_NEARLY_FULL', 'nearly_full' ); // 残りわずか
define( 'KEICHAN_GRANT_FULL',        'full' );         // 締め切り/上限到達
define( 'KEICHAN_GRANT_UNKNOWN',     'unknown' );      // 情報なし/未確認

// -------------------------------------------------------
// WP-Cron スケジュール登録（毎月1日 午前2時）
// -------------------------------------------------------
add_action( 'wp', 'keichan_grant_schedule_monthly' );
function keichan_grant_schedule_monthly(): void {
    if ( ! wp_next_scheduled( 'keichan_grant_monthly_update' ) ) {
        // 次の月初（1日 02:00 JST = UTC-9h → 17:00 UTC）
        $next = strtotime( 'first day of next month 02:00:00 +0900' );
        wp_schedule_event( $next, 'monthly', 'keichan_grant_monthly_update' );
    }
}

// 月次カスタムスケジュール追加
add_filter( 'cron_schedules', function ( $schedules ) {
    $schedules['monthly'] = [
        'interval' => 30 * DAY_IN_SECONDS,
        'display'  => '月1回',
    ];
    return $schedules;
} );

add_action( 'keichan_grant_monthly_update', 'keichan_grant_run_update' );

// -------------------------------------------------------
// メイン更新処理
// -------------------------------------------------------
function keichan_grant_run_update(): void {
    $cities  = keichan_grant_cities();
    $current = get_option( 'keichan_grant_status', [] );

    foreach ( $cities as $pref => $city_list ) {
        foreach ( $city_list as $city_name => $meta ) {
            $key    = $pref . '_' . $city_name;
            $status = keichan_grant_fetch_status( $pref, $city_name, $meta['url'] );

            $current[ $key ] = [
                'pref'       => $pref,
                'city'       => $city_name,
                'status'     => $status,
                'updated_at' => current_time( 'Y-m-d H:i:s' ),
                'url'        => $meta['url'],
            ];
        }
    }

    update_option( 'keichan_grant_status', $current, false );
    update_option( 'keichan_grant_last_updated', current_time( 'Y-m-d H:i:s' ), false );
}

// -------------------------------------------------------
// 個別市区町村のステータス取得
// ※ 各自治体のURLが設定されていれば簡易スクレイピング
//   URLが空の場合は unknown を返す
// -------------------------------------------------------
function keichan_grant_fetch_status( string $pref, string $city, string $url ): string {
    if ( empty( $url ) ) {
        return KEICHAN_GRANT_UNKNOWN;
    }

    $response = wp_remote_get( $url, [
        'timeout'    => 20,
        'user-agent' => 'Mozilla/5.0 (compatible; KeichanGrantBot/1.0)',
    ] );

    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
        return KEICHAN_GRANT_UNKNOWN;
    }

    $body = wp_remote_retrieve_body( $response );

    // キーワードマッチで状況を判定（各自治体ページに合わせて調整）
    if ( preg_match( '/受付(終了|締め?切|中止|終了)/u', $body ) ||
         preg_match( '/上限に?達し/u', $body ) ||
         preg_match( '/予算.*?終了/u', $body ) ) {
        return KEICHAN_GRANT_FULL;
    }

    if ( preg_match( '/残りわずか|残りわずかです|定員(に?近づ|間近)/u', $body ) ||
         preg_match( '/受付(残り|件数が少な)/u', $body ) ) {
        return KEICHAN_GRANT_NEARLY_FULL;
    }

    if ( preg_match( '/受付(中|しています)|申請(受付|可能)|募集(中|しています)/u', $body ) ) {
        return KEICHAN_GRANT_AVAILABLE;
    }

    return KEICHAN_GRANT_UNKNOWN;
}

// -------------------------------------------------------
// 管理画面メニュー：手動更新ページ
// -------------------------------------------------------
add_action( 'admin_menu', function () {
    add_management_page(
        '助成金ステータス更新',
        '助成金ステータス',
        'manage_options',
        'keichan-grant-update',
        'keichan_grant_admin_page'
    );
} );

function keichan_grant_admin_page(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // 手動実行
    if ( isset( $_POST['keichan_grant_run'] ) && check_admin_referer( 'keichan_grant_manual_run' ) ) {
        keichan_grant_run_update();
        echo '<div class="notice notice-success"><p>ステータスを更新しました。</p></div>';
    }

    // 個別ステータス手動設定
    if ( isset( $_POST['keichan_grant_set'] ) && check_admin_referer( 'keichan_grant_set_status' ) ) {
        $key    = sanitize_text_field( $_POST['grant_key'] ?? '' );
        $status = sanitize_text_field( $_POST['grant_status'] ?? KEICHAN_GRANT_UNKNOWN );
        $url    = esc_url_raw( $_POST['grant_url'] ?? '' );

        $current = get_option( 'keichan_grant_status', [] );
        if ( isset( $current[ $key ] ) ) {
            $current[ $key ]['status']     = $status;
            $current[ $key ]['url']        = $url;
            $current[ $key ]['updated_at'] = current_time( 'Y-m-d H:i:s' );
            update_option( 'keichan_grant_status', $current, false );
            echo '<div class="notice notice-success"><p>' . esc_html( $key ) . ' を更新しました。</p></div>';
        }
    }

    $last    = get_option( 'keichan_grant_last_updated', '未実行' );
    $data    = get_option( 'keichan_grant_status', [] );
    $cities  = keichan_grant_cities();
    $statuses = [
        KEICHAN_GRANT_AVAILABLE   => '受付中',
        KEICHAN_GRANT_NEARLY_FULL => '残りわずか',
        KEICHAN_GRANT_FULL        => '締め切り',
        KEICHAN_GRANT_UNKNOWN     => '情報なし',
    ];
    ?>
    <div class="wrap">
        <h1>助成金ステータス管理（関東）</h1>
        <p>最終更新: <strong><?php echo esc_html( $last ); ?></strong></p>

        <form method="post">
            <?php wp_nonce_field( 'keichan_grant_manual_run' ); ?>
            <input type="hidden" name="keichan_grant_run" value="1">
            <?php submit_button( '今すぐ全市区町村を更新', 'primary', 'submit', false ); ?>
        </form>

        <hr>
        <h2>市区町村別ステータス一覧</h2>

        <?php foreach ( $cities as $pref => $city_list ) : ?>
        <h3><?php echo esc_html( $pref ); ?></h3>
        <table class="widefat striped" style="margin-bottom:2em;">
            <thead>
                <tr>
                    <th>市区町村</th>
                    <th>ステータス</th>
                    <th>URL</th>
                    <th>最終更新</th>
                    <th>編集</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $city_list as $city_name => $meta ) :
                $key   = $pref . '_' . $city_name;
                $row   = $data[ $key ] ?? [];
                $st    = $row['status'] ?? KEICHAN_GRANT_UNKNOWN;
                $color = [
                    KEICHAN_GRANT_AVAILABLE   => '#d4edda',
                    KEICHAN_GRANT_NEARLY_FULL => '#fff3cd',
                    KEICHAN_GRANT_FULL        => '#f8d7da',
                    KEICHAN_GRANT_UNKNOWN     => '#e2e3e5',
                ][ $st ] ?? '#e2e3e5';
            ?>
            <tr style="background:<?php echo esc_attr( $color ); ?>">
                <td><?php echo esc_html( $city_name ); ?></td>
                <td><?php echo esc_html( $statuses[ $st ] ?? $st ); ?></td>
                <td style="font-size:11px;word-break:break-all;"><?php echo esc_html( $row['url'] ?? '' ); ?></td>
                <td><?php echo esc_html( $row['updated_at'] ?? '—' ); ?></td>
                <td>
                    <form method="post" style="display:inline-flex;gap:4px;flex-wrap:wrap;">
                        <?php wp_nonce_field( 'keichan_grant_set_status' ); ?>
                        <input type="hidden" name="keichan_grant_set" value="1">
                        <input type="hidden" name="grant_key" value="<?php echo esc_attr( $key ); ?>">
                        <select name="grant_status">
                            <?php foreach ( $statuses as $val => $label ) : ?>
                            <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $st, $val ); ?>><?php echo esc_html( $label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="url" name="grant_url" value="<?php echo esc_attr( $row['url'] ?? '' ); ?>" placeholder="自治体URL" style="width:220px;">
                        <button type="submit" class="button button-small">保存</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endforeach; ?>
    </div>
    <?php
}

// -------------------------------------------------------
// 初期データ投入（プラグイン初回読み込み時に空レコードを作成）
// -------------------------------------------------------
function keichan_grant_maybe_init(): void {
    if ( get_option( 'keichan_grant_status' ) !== false ) {
        return;
    }
    $cities  = keichan_grant_cities();
    $initial = [];
    foreach ( $cities as $pref => $city_list ) {
        foreach ( $city_list as $city_name => $meta ) {
            $key = $pref . '_' . $city_name;
            $initial[ $key ] = [
                'pref'       => $pref,
                'city'       => $city_name,
                'status'     => KEICHAN_GRANT_UNKNOWN,
                'updated_at' => '',
                'url'        => $meta['url'],
            ];
        }
    }
    add_option( 'keichan_grant_status', $initial, '', false );
}
add_action( 'init', 'keichan_grant_maybe_init' );
