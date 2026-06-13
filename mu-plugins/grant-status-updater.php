<?php
/**
 * Plugin Name: 助成金ステータス管理（東京）
 * Description: 東京都の市区町村ごとに助成金名・残り期間・残り枠を管理する
 * Version: 3.0.0
 * 設置場所: wp-content/mu-plugins/grant-status-updater.php
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
        echo '<div class="notice notice-success"><p>保存しました。</p></div>';
    }

    $data    = get_option( 'keichan_grant_tokyo', [] );
    $updated = get_option( 'keichan_grant_tokyo_updated', '未保存' );
    $nonce   = wp_create_nonce( 'keichan_fetch_nonce' );
    ?>
    <div class="wrap">
        <h1>助成金管理（東京都）</h1>
        <p>最終更新: <strong><?php echo esc_html( $updated ); ?></strong></p>

        <div style="background:#fff3cd;border:1px solid #ffc107;padding:.7rem 1rem;border-radius:4px;margin-bottom:1rem;">
            <strong>使い方：</strong>
            URLを入力 → 「<strong>自動取得</strong>」ボタンでページから助成金名・残り期間・残り枠を自動入力 →
            内容を確認・修正 → 最下部の「<strong>保存</strong>」ボタンで一括保存
        </div>

        <p>
            <button type="button" id="fetch-all-btn" class="button button-secondary">
                ✦ URLが入力済みの行を全て自動取得
            </button>
            <span id="fetch-all-status" style="margin-left:1em;color:#666;"></span>
        </p>

        <form method="post" id="grant-form">
            <?php wp_nonce_field( 'keichan_grant_save' ); ?>
            <input type="hidden" name="keichan_save" value="1">

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
        var ajaxUrl = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';
        var nonce   = '<?php echo esc_js( $nonce ); ?>';

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
