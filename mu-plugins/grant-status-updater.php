<?php
/**
 * Plugin Name: 助成金ステータス管理（東京）
 * Description: 東京都の市区町村ごとに助成金名・残り期間・残り枠を管理する
 * Version: 3.1.0
 * 設置場所: wp-content/mu-plugins/grant-status-updater.php
 */

// GitHubから取り込む既定のRaw URL（管理画面で変更可）
const KEICHAN_GRANT_GITHUB_DEFAULT_URL = 'https://raw.githubusercontent.com/masakikamata/keichanpaint-theme/main/tama_11shi_josei_copy.html';

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
        $initial[ $city ] = [ 'grant_name' => '', 'period' => '', 'slots' => '', 'url' => '' ];
    }
    add_option( 'keichan_grant_tokyo', $initial, '', false );
}
add_action( 'init', 'keichan_grant_maybe_init' );

// -------------------------------------------------------
// URLスクレイピング関数
// -------------------------------------------------------
function keichan_scrape_url( string $url ): array {
    $result = [ 'grant_name' => '', 'period' => '', 'slots' => '' ];

    if ( empty( $url ) ) return $result;

    $response = wp_remote_get( $url, [
        'timeout'    => 20,
        'user-agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1)',
    ] );

    if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
        return $result;
    }

    $html = wp_remote_retrieve_body( $response );
    $html = mb_convert_encoding( $html, 'UTF-8', 'auto' );

    // ── 助成金名：ページタイトル or h1 ──
    if ( preg_match( '/<title[^>]*>([^<]+)<\/title>/iu', $html, $m ) ) {
        $title = trim( html_entity_decode( $m[1], ENT_QUOTES, 'UTF-8' ) );
        // サイト名部分を除去（| や - 以降）
        $title = preg_replace( '/[｜|・\-―─]\s*.+$/u', '', $title );
        $result['grant_name'] = mb_substr( trim( $title ), 0, 40 );
    }
    if ( empty( $result['grant_name'] ) ) {
        if ( preg_match( '/<h1[^>]*>([^<]+)<\/h1>/iu', $html, $m ) ) {
            $result['grant_name'] = mb_substr( trim( strip_tags( $m[1] ) ), 0, 40 );
        }
    }

    // ── 残り期間：日付パターンを検索 ──
    // 令和X年X月X日 ～ 令和X年X月X日
    if ( preg_match( '/((令和|R)\s*\d+\s*年\s*\d+\s*月\s*\d+\s*日)\s*[〜～~\-―]\s*((令和|R)\s*\d+\s*年\s*\d+\s*月\s*\d+\s*日)/u', $html, $m ) ) {
        $result['period'] = $m[1] . '〜' . $m[3];
    }
    // 令和X年X月X日まで
    elseif ( preg_match( '/(令和\s*\d+\s*年\s*\d+\s*月\s*\d+\s*日)\s*(まで|締切|締め切り)/u', $html, $m ) ) {
        $result['period'] = $m[1] . 'まで';
    }
    // 20XX年X月X日 ～ 20XX年X月X日
    elseif ( preg_match( '/(20\d{2}年\d{1,2}月\d{1,2}日)\s*[〜～~\-―]\s*(20\d{2}年\d{1,2}月\d{1,2}日)/u', $html, $m ) ) {
        $result['period'] = $m[1] . '〜' . $m[2];
    }
    // 令和X年度
    elseif ( preg_match( '/(令和\s*\d+\s*年度)/u', $html, $m ) ) {
        $result['period'] = $m[1];
    }

    // ── 残り枠：件数・戸数パターン ──
    if ( preg_match( '/残り\s*(\d+)\s*(件|戸|枠|世帯)/u', $html, $m ) ) {
        $result['slots'] = '残り' . $m[1] . $m[2];
    }
    elseif ( preg_match( '/受付\s*残り\s*(\d+)/u', $html, $m ) ) {
        $result['slots'] = '残り' . $m[1] . '件';
    }
    elseif ( preg_match( '/予算\s*(\d+)\s*(件|戸|世帯)/u', $html, $m ) ) {
        $result['slots'] = '定員' . $m[1] . $m[2];
    }
    elseif ( preg_match( '/(\d+)\s*(件|戸|世帯)\s*(限り|まで|を?上限)/u', $html, $m ) ) {
        $result['slots'] = $m[1] . $m[2] . '上限';
    }
    // 締め切り済みチェック
    elseif ( preg_match( '/受付(終了|締切|締め切り|中止)|予算.*?終了|上限に?達し/u', $html ) ) {
        $result['slots'] = '受付終了';
    }

    return $result;
}

// -------------------------------------------------------
// GitHub 同期：URL設定
// -------------------------------------------------------
function keichan_grant_github_url(): string {
    $url = get_option( 'keichan_grant_github_url', '' );
    return $url ?: KEICHAN_GRANT_GITHUB_DEFAULT_URL;
}

// -------------------------------------------------------
// HTMLから市別データを抽出（tama_11shi_josei_copy.html 形式）
// 期待構造: <tr data-city="市名"><td>市名</td><td>助成金名</td>
//          <td>受付期間</td><td>残り枠</td><td>公式URL</td><td>PDF URL</td></tr>
// -------------------------------------------------------
function keichan_grant_strip_tags_clean( string $s ): string {
    $s = preg_replace( '/<[^>]+>/u', ' ', $s );
    $s = html_entity_decode( $s, ENT_QUOTES, 'UTF-8' );
    return trim( preg_replace( '/\s+/u', ' ', $s ) );
}

function keichan_grant_parse_html( string $html ): array {
    $result = [];
    if ( ! $html ) return $result;

    $html = mb_convert_encoding( $html, 'UTF-8', 'UTF-8, SJIS, EUC-JP, ISO-2022-JP, ASCII' );

    if ( ! preg_match_all( '/<tr\s+data-city="([^"]+)"[^>]*>(.*?)<\/tr>/su', $html, $rows, PREG_SET_ORDER ) ) {
        return $result;
    }

    foreach ( $rows as $row ) {
        $city = trim( html_entity_decode( $row[1], ENT_QUOTES, 'UTF-8' ) );
        if ( $city === '' || $city === '全市共通' ) continue;
        // 同じ市が複数行ある場合は最初の行を採用
        if ( isset( $result[ $city ] ) ) continue;

        if ( ! preg_match_all( '/<td[^>]*>(.*?)<\/td>/su', $row[2], $tds ) ) continue;
        $cells = $tds[1];
        if ( count( $cells ) < 5 ) continue;

        // セル順：0=市名, 1=助成金名, 2=受付期間, 3=残り枠, 4=公式URL, 5=PDF
        $url = '';
        if ( preg_match( '/https?:\/\/[^\s"<>]+/u', $cells[4], $m ) ) {
            $url = $m[0];
        }

        $result[ $city ] = [
            'grant_name' => mb_substr( keichan_grant_strip_tags_clean( $cells[1] ), 0, 200 ),
            'period'     => mb_substr( keichan_grant_strip_tags_clean( $cells[2] ), 0, 100 ),
            'slots'      => mb_substr( keichan_grant_strip_tags_clean( $cells[3] ), 0, 100 ),
            'url'        => $url,
        ];
    }

    return $result;
}

// -------------------------------------------------------
// GitHub から同期（HTML 取得 → 差分があれば反映）
// 戻り値: ['status' => 'updated|nochange|error', 'message' => '...', 'updated_cities' => [...]]
// -------------------------------------------------------
function keichan_grant_sync_from_github( bool $force = false ): array {
    $url = keichan_grant_github_url();
    if ( empty( $url ) ) {
        return [ 'status' => 'error', 'message' => 'GitHub URLが未設定です' ];
    }

    $response = wp_remote_get( $url, [
        'timeout'    => 30,
        'user-agent' => 'Mozilla/5.0 (compatible; KeichanGrantSync/1.0)',
    ] );
    if ( is_wp_error( $response ) ) {
        return [ 'status' => 'error', 'message' => '取得失敗: ' . $response->get_error_message() ];
    }
    $code = wp_remote_retrieve_response_code( $response );
    if ( $code !== 200 ) {
        return [ 'status' => 'error', 'message' => 'HTTPステータス: ' . $code ];
    }

    $body = wp_remote_retrieve_body( $response );
    $hash = md5( $body );

    $last_hash = get_option( 'keichan_grant_github_hash', '' );
    update_option( 'keichan_grant_github_checked', current_time( 'Y-m-d H:i:s' ), false );

    if ( ! $force && $hash === $last_hash ) {
        return [ 'status' => 'nochange', 'message' => '変更なし（前回と同じ内容）' ];
    }

    $parsed = keichan_grant_parse_html( $body );
    if ( empty( $parsed ) ) {
        return [ 'status' => 'error', 'message' => 'HTMLからデータを抽出できませんでした' ];
    }

    $data    = get_option( 'keichan_grant_tokyo', [] );
    $updated = [];
    foreach ( $parsed as $city => $row ) {
        if ( ! isset( $data[ $city ] ) ) continue; // 市区町村マスタにない市はスキップ
        if ( ( $data[ $city ] ?? [] ) !== $row ) {
            $data[ $city ] = $row;
            $updated[]     = $city;
        }
    }

    update_option( 'keichan_grant_tokyo', $data, false );
    update_option( 'keichan_grant_github_hash', $hash, false );
    update_option( 'keichan_grant_github_synced', current_time( 'Y-m-d H:i:s' ), false );
    update_option( 'keichan_grant_tokyo_updated', current_time( 'Y-m-d H:i:s' ), false );

    return [
        'status'         => 'updated',
        'message'        => '✓ ' . count( $updated ) . '件の市を更新',
        'updated_cities' => $updated,
    ];
}

// -------------------------------------------------------
// AJAX：GitHubから手動同期
// -------------------------------------------------------
add_action( 'wp_ajax_keichan_github_sync', function () {
    check_ajax_referer( 'keichan_github_sync', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( '権限がありません' );
    }
    $force  = ! empty( $_POST['force'] );
    $result = keichan_grant_sync_from_github( $force );
    if ( $result['status'] === 'error' ) {
        wp_send_json_error( $result['message'] );
    }
    wp_send_json_success( $result );
} );

// -------------------------------------------------------
// WP-Cron：月次自動同期
// -------------------------------------------------------
add_filter( 'cron_schedules', function ( $schedules ) {
    if ( ! isset( $schedules['keichan_monthly'] ) ) {
        $schedules['keichan_monthly'] = [
            'interval' => 30 * DAY_IN_SECONDS,
            'display'  => '毎月（30日ごと）',
        ];
    }
    return $schedules;
} );

add_action( 'init', function () {
    if ( ! wp_next_scheduled( 'keichan_grant_github_cron' ) ) {
        wp_schedule_event( time() + HOUR_IN_SECONDS, 'keichan_monthly', 'keichan_grant_github_cron' );
    }
} );

add_action( 'keichan_grant_github_cron', function () {
    keichan_grant_sync_from_github( false );
} );

// -------------------------------------------------------
// AJAX：URLから情報を自動取得（管理画面用）
// -------------------------------------------------------
add_action( 'wp_ajax_keichan_fetch_url', function () {
    check_ajax_referer( 'keichan_fetch_nonce', 'nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( '権限がありません' );
    }

    $url = sanitize_text_field( wp_unslash( $_POST['url'] ?? '' ) );
    if ( empty( $url ) ) {
        wp_send_json_error( 'URLが空です' );
    }

    $result = keichan_scrape_url( $url );
    wp_send_json_success( $result );
} );

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
        $data   = get_option( 'keichan_grant_tokyo', [] );
        $posted = $_POST['grants'] ?? [];

        foreach ( $posted as $city => $fields ) {
            $city = wp_unslash( $city );
            if ( ! isset( $data[ $city ] ) ) continue;
            $data[ $city ] = [
                'grant_name' => sanitize_text_field( wp_unslash( $fields['grant_name'] ?? '' ) ),
                'period'     => sanitize_text_field( wp_unslash( $fields['period']     ?? '' ) ),
                'slots'      => sanitize_text_field( wp_unslash( $fields['slots']      ?? '' ) ),
                'url'        => sanitize_text_field( wp_unslash( $fields['url']        ?? '' ) ),
            ];
        }
        update_option( 'keichan_grant_tokyo', $data, false );
        update_option( 'keichan_grant_tokyo_updated', current_time( 'Y-m-d H:i:s' ), false );

        // GitHub取り込みURLも同フォームで保存
        $github_url = esc_url_raw( wp_unslash( $_POST['github_url'] ?? '' ) );
        update_option( 'keichan_grant_github_url', $github_url, false );

        echo '<div class="notice notice-success"><p>保存しました。</p></div>';
    }

    $data         = get_option( 'keichan_grant_tokyo', [] );
    $updated      = get_option( 'keichan_grant_tokyo_updated', '未保存' );
    $nonce        = wp_create_nonce( 'keichan_fetch_nonce' );
    $sync_nonce   = wp_create_nonce( 'keichan_github_sync' );
    $github_url   = keichan_grant_github_url();
    $github_sync  = get_option( 'keichan_grant_github_synced', '未取得' );
    $github_check = get_option( 'keichan_grant_github_checked', '未取得' );
    $next_cron    = wp_next_scheduled( 'keichan_grant_github_cron' );
    $next_cron_s  = $next_cron ? wp_date( 'Y-m-d H:i:s', $next_cron ) : '未予約';
    ?>
    <div class="wrap">
        <h1>助成金管理（東京都）</h1>
        <p>最終更新: <strong><?php echo esc_html( $updated ); ?></strong></p>

        <div style="background:#fff3cd;border:1px solid #ffc107;padding:.7rem 1rem;border-radius:4px;margin-bottom:1rem;">
            <strong>使い方：</strong>
            URLを入力 → 「<strong>自動取得</strong>」ボタンでページから助成金名・残り期間・残り枠を自動入力 →
            内容を確認・修正 → 最下部の「<strong>保存</strong>」ボタンで一括保存
        </div>

        <form method="post" id="grant-form">
            <?php wp_nonce_field( 'keichan_grant_save' ); ?>
            <input type="hidden" name="keichan_save" value="1">

            <div style="background:#e8f4fd;border:1px solid #5b9dd9;padding:.8rem 1rem;border-radius:4px;margin-bottom:1rem;">
                <h2 style="margin:0 0 .5rem;font-size:14px;">📥 GitHubから取り込む</h2>
                <p style="margin:.3rem 0;font-size:12px;color:#555;">
                    指定したGitHub Raw URL（HTMLファイル）から、各市の助成金データを自動で取り込みます。
                    月次のWP-Cronで自動チェックし、内容が変わっていれば反映します。
                </p>
                <p style="margin:.5rem 0;">
                    <label style="display:block;font-weight:600;margin-bottom:.2rem;">GitHub Raw URL:</label>
                    <input type="url" name="github_url" id="github_url" style="width:80%;font-family:monospace;font-size:12px;"
                           value="<?php echo esc_attr( $github_url ); ?>"
                           placeholder="https://raw.githubusercontent.com/.../...html">
                </p>
                <p style="margin:.5rem 0;font-size:12px;color:#555;">
                    最終取り込み: <strong><?php echo esc_html( $github_sync ); ?></strong>
                    ／ 最終チェック: <strong><?php echo esc_html( $github_check ); ?></strong>
                    ／ 次回自動チェック: <strong><?php echo esc_html( $next_cron_s ); ?></strong>
                </p>
                <p style="margin:.5rem 0;">
                    <button type="button" id="github-sync-btn" class="button button-primary">📥 今すぐGitHubから取り込む</button>
                    <button type="button" id="github-force-btn" class="button">強制更新（変更検知を無視）</button>
                    <span id="github-sync-status" style="margin-left:1em;color:#666;"></span>
                </p>
                <p style="margin:.3rem 0 0;font-size:11px;color:#888;">
                    ※ URLを変更した場合は、下の「保存」ボタンで先にURLを保存してから取り込んでください。
                </p>
            </div>

            <p>
                <button type="button" id="fetch-all-btn" class="button button-secondary">
                    ✦ URLが入力済みの行を全て自動取得（個別スクレイピング）
                </button>
                <span id="fetch-all-status" style="margin-left:1em;color:#666;"></span>
            </p>

            <table class="widefat striped" style="table-layout:fixed;">
                <colgroup>
                    <col style="width:90px;">
                    <col>
                    <col style="width:160px;">
                    <col style="width:100px;">
                    <col style="width:280px;">
                    <col style="width:80px;">
                </colgroup>
                <thead>
                    <tr>
                        <th>市区町村</th>
                        <th>助成金名</th>
                        <th>残り期間</th>
                        <th>残り枠</th>
                        <th>参照URL</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( keichan_grant_tokyo_cities() as $city ) :
                    $row = $data[ $city ] ?? [ 'grant_name' => '', 'period' => '', 'slots' => '', 'url' => '' ];
                    $safe = esc_attr( $city );
                ?>
                    <tr data-city="<?php echo $safe; ?>">
                        <td><strong><?php echo esc_html( $city ); ?></strong></td>
                        <td>
                            <input type="text" class="field-grant-name" style="width:100%;"
                                   name="grants[<?php echo $safe; ?>][grant_name]"
                                   value="<?php echo esc_attr( $row['grant_name'] ); ?>"
                                   placeholder="助成金名">
                        </td>
                        <td>
                            <input type="text" class="field-period" style="width:100%;"
                                   name="grants[<?php echo $safe; ?>][period]"
                                   value="<?php echo esc_attr( $row['period'] ); ?>"
                                   placeholder="例：2026/3/31まで">
                        </td>
                        <td>
                            <input type="text" class="field-slots" style="width:100%;"
                                   name="grants[<?php echo $safe; ?>][slots]"
                                   value="<?php echo esc_attr( $row['slots'] ); ?>"
                                   placeholder="例：残り20件">
                        </td>
                        <td>
                            <input type="text" class="field-url" style="width:100%;"
                                   name="grants[<?php echo $safe; ?>][url]"
                                   value="<?php echo esc_attr( $row['url'] ); ?>"
                                   placeholder="https://...">
                        </td>
                        <td>
                            <button type="button" class="button button-small fetch-btn">取得</button>
                            <span class="fetch-status" style="font-size:11px;display:block;color:#666;"></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <p style="margin-top:1.5em;">
                <?php submit_button( '保存', 'primary', 'submit', false ); ?>
            </p>
        </form>
    </div>

    <script>
    (function($){
        var ajaxUrl   = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';
        var nonce     = '<?php echo esc_js( $nonce ); ?>';
        var syncNonce = '<?php echo esc_js( $sync_nonce ); ?>';

        // GitHub から取り込み
        function githubSync( force ) {
            var $status = $('#github-sync-status');
            $status.css('color', '#666').text( '取得中…' );
            $('#github-sync-btn, #github-force-btn').prop('disabled', true);

            $.post( ajaxUrl, {
                action: 'keichan_github_sync',
                nonce:  syncNonce,
                force:  force ? 1 : 0
            }, function( res ) {
                if ( res.success ) {
                    var d = res.data || {};
                    $status.css('color', '#0a7f3f').text( d.message || '取り込み完了' );
                    if ( d.status === 'updated' ) {
                        // データ反映のためページを再読込
                        setTimeout(function(){ location.reload(); }, 1200);
                    }
                } else {
                    $status.css('color', '#c00').text( '✗ ' + ( res.data || 'エラー' ) );
                }
            }).fail(function(){
                $status.css('color', '#c00').text( '✗ 通信エラー' );
            }).always(function(){
                $('#github-sync-btn, #github-force-btn').prop('disabled', false);
            });
        }
        $('#github-sync-btn').on('click', function(){ githubSync( false ); });
        $('#github-force-btn').on('click', function(){
            if ( confirm('変更検知を無視して強制的に再取り込みします。よろしいですか？') ) {
                githubSync( true );
            }
        });

        // 1行ずつ取得
        function fetchRow( $tr, cb ) {
            var url = $tr.find('.field-url').val().trim();
            if ( ! url ) {
                $tr.find('.fetch-status').text('URLなし');
                if ( cb ) cb();
                return;
            }
            $tr.find('.fetch-status').text('取得中…');
            $tr.find('.fetch-btn').prop('disabled', true);

            $.post( ajaxUrl, {
                action: 'keichan_fetch_url',
                nonce:  nonce,
                url:    url
            }, function( res ) {
                if ( res.success ) {
                    var d = res.data;
                    if ( d.grant_name ) $tr.find('.field-grant-name').val( d.grant_name );
                    if ( d.period )     $tr.find('.field-period').val( d.period );
                    if ( d.slots )      $tr.find('.field-slots').val( d.slots );
                    $tr.find('.fetch-status').text('✓ 取得済');
                } else {
                    $tr.find('.fetch-status').text('✗ 失敗');
                }
            }).fail(function(){
                $tr.find('.fetch-status').text('✗ エラー');
            }).always(function(){
                $tr.find('.fetch-btn').prop('disabled', false);
                if ( cb ) cb();
            });
        }

        // 個別ボタン
        $(document).on('click', '.fetch-btn', function(){
            fetchRow( $(this).closest('tr') );
        });

        // 全行一括取得
        $('#fetch-all-btn').on('click', function(){
            var $rows = $('tr[data-city]').filter(function(){
                return $(this).find('.field-url').val().trim() !== '';
            });
            if ( ! $rows.length ) {
                $('#fetch-all-status').text('URLが入力されている行がありません');
                return;
            }
            $('#fetch-all-btn').prop('disabled', true);
            var total = $rows.length, done = 0;
            $('#fetch-all-status').text('0 / ' + total + ' 取得中…');

            // 3件ずつ並列で処理
            var queue = $rows.toArray();
            function next() {
                if ( ! queue.length ) {
                    $('#fetch-all-btn').prop('disabled', false);
                    $('#fetch-all-status').text('✓ 全 ' + total + ' 件 取得完了');
                    return;
                }
                var batch = queue.splice(0, 3);
                var pending = batch.length;
                batch.forEach(function(tr){
                    fetchRow( $(tr), function(){
                        done++;
                        $('#fetch-all-status').text( done + ' / ' + total + ' 取得中…');
                        pending--;
                        if ( pending === 0 ) next();
                    });
                });
            }
            next();
        });
    })(jQuery);
    </script>
    <?php
}
