<?php
/**
 * Plugin Name: けいちゃんペイント 助成金管理（東京）
 * Plugin URI:  https://keichanpaint-main.group
 * Description: 東京都 区市町村別 助成金情報を管理画面から編集し、フロントの助成金マップに反映します。
 * Version:     1.4.0
 * Author:      けいちゃんペイント
 *
 * ★ v1.2 からの修正点:
 *   sanitize_key() が日本語文字をすべて除去するため
 *   フォームの name 属性が全市区町村で同一（空）になり保存不能だった。
 *   → name="cities[市区町村名][フィールド]" の配列形式に変更して修正。
 *
 * v1.4.0 追加:
 *   ・GitHub Raw URL（HTMLファイル）から助成金データを自動取り込み
 *   ・月次WP-Cronで自動チェック（内容ハッシュで差分検知）
 *   ・対応フォーマット: <tr data-city="…"> の表形式
 *   ・noteフィールドは取り込み時に保持（手動入力が上書きされません）
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* GitHubから取り込む既定のRaw URL（管理画面で変更可） */
const KEICHAN_GRANT_GITHUB_DEFAULT_URL = 'https://raw.githubusercontent.com/masakikamata/keichanpaint-theme/main/tama_11shi_josei_copy.html';

/* ============================================================
   1. 市区町村マスター
   ============================================================ */
function keichan_grant_tokyo_cities(): array {
    return [
        // ── 23区 ──
        '千代田区', '中央区', '港区', '新宿区', '文京区',
        '台東区', '墨田区', '江東区', '品川区', '目黒区',
        '大田区', '世田谷区', '渋谷区', '中野区', '杉並区',
        '豊島区', '北区', '荒川区', '板橋区', '練馬区',
        '足立区', '葛飾区', '江戸川区',
        // ── 多摩地区 ──
        '八王子市', '立川市', '武蔵野市', '三鷹市', '青梅市',
        '府中市', '昭島市', '調布市', '町田市', '小金井市',
        '小平市', '日野市', '東村山市', '国分寺市', '国立市',
        '福生市', '狛江市', '東大和市', '清瀬市', '東久留米市',
        '武蔵村山市', '多摩市', '稲城市', '羽村市', 'あきる野市', '西東京市',
    ];
}

/* ============================================================
   1.5 GitHub連携：HTMLパース＆同期
   ============================================================ */

function keichan_grant_github_url(): string {
    $url = get_option( 'keichan_grant_github_url', '' );
    return $url ?: KEICHAN_GRANT_GITHUB_DEFAULT_URL;
}

function keichan_grant_strip_tags_clean( string $s ): string {
    $s = preg_replace( '/<[^>]+>/u', ' ', $s );
    $s = html_entity_decode( $s, ENT_QUOTES, 'UTF-8' );
    return trim( preg_replace( '/\s+/u', ' ', $s ) );
}

/**
 * HTMLから市別データを抽出。
 * 想定構造:
 *   <tr data-city="市区町村名">
 *     <td>市名</td>
 *     <td>助成金名</td>
 *     <td>受付期間</td>
 *     <td>残り枠</td>
 *     <td>公式URL</td>
 *     <td>PDF URL</td>
 *   </tr>
 *
 * @return array  ['市区町村名' => ['grant_name'=>..., 'period'=>..., 'slots'=>..., 'url'=>..., 'pdf_url'=>...]]
 */
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
        if ( isset( $result[ $city ] ) ) continue; // 同市複数行は最初の行を採用

        if ( ! preg_match_all( '/<td[^>]*>(.*?)<\/td>/su', $row[2], $tds ) ) continue;
        $cells = $tds[1];
        if ( count( $cells ) < 5 ) continue;

        $url = '';
        if ( preg_match( '/https?:\/\/[^\s"<>]+/u', $cells[4], $m ) ) {
            $url = $m[0];
        }

        $pdf_url = '';
        if ( isset( $cells[5] ) && preg_match( '/https?:\/\/[^\s"<>]+/u', $cells[5], $m ) ) {
            $pdf_url = $m[0];
        }

        $result[ $city ] = [
            'grant_name' => mb_substr( keichan_grant_strip_tags_clean( $cells[1] ), 0, 200 ),
            'period'     => mb_substr( keichan_grant_strip_tags_clean( $cells[2] ), 0, 100 ),
            'slots'      => mb_substr( keichan_grant_strip_tags_clean( $cells[3] ), 0, 100 ),
            'url'        => $url,
            'pdf_url'    => $pdf_url,
        ];
    }

    return $result;
}

/**
 * GitHubから同期。
 * - 内容ハッシュで差分検知（変更なしならスキップ）
 * - 既存のnoteフィールドは保持して上書きしない
 * - マスタにない市区町村はスキップ
 *
 * @return array  ['status'=>'updated|nochange|error', 'message'=>'...', 'updated_cities'=>[...]]
 */
function keichan_grant_sync_from_github( bool $force = false ): array {
    $url = keichan_grant_github_url();
    if ( empty( $url ) ) {
        return [ 'status' => 'error', 'message' => 'GitHub URLが未設定です' ];
    }

    $response = wp_remote_get( $url, [
        'timeout'    => 30,
        'user-agent' => 'Mozilla/5.0 (compatible; KeichanGrantSync/1.4)',
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

    update_option( 'keichan_grant_github_checked', current_time( 'Y-m-d H:i:s' ), false );

    $last_hash = get_option( 'keichan_grant_github_hash', '' );
    if ( ! $force && $hash === $last_hash ) {
        return [ 'status' => 'nochange', 'message' => '変更なし（前回と同じ内容）' ];
    }

    $parsed = keichan_grant_parse_html( $body );
    if ( empty( $parsed ) ) {
        return [ 'status' => 'error', 'message' => 'HTMLからデータを抽出できませんでした' ];
    }

    $cities         = keichan_grant_tokyo_cities();
    $data           = get_option( 'keichan_grant_tokyo', [] );
    $updated_cities = [];

    foreach ( $parsed as $city => $row ) {
        if ( ! in_array( $city, $cities, true ) ) continue; // マスタにない市はスキップ

        $existing = $data[ $city ] ?? [
            'grant_name' => '', 'period' => '', 'slots' => '',
            'url' => '', 'pdf_url' => '', 'note' => '',
        ];

        // noteは保持、それ以外を上書き
        $new_row = [
            'grant_name' => $row['grant_name'],
            'period'     => $row['period'],
            'slots'      => $row['slots'],
            'url'        => $row['url'],
            'pdf_url'    => $row['pdf_url'],
            'note'       => $existing['note'] ?? '',
        ];

        if ( $existing !== $new_row ) {
            $data[ $city ]    = $new_row;
            $updated_cities[] = $city;
        }
    }

    update_option( 'keichan_grant_tokyo',          $data );
    update_option( 'keichan_grant_github_hash',    $hash,                          false );
    update_option( 'keichan_grant_github_synced',  current_time( 'Y-m-d H:i:s' ), false );
    update_option( 'keichan_grant_tokyo_updated',  current_time( 'Y-m-d' ) );

    return [
        'status'         => 'updated',
        'message'        => '✓ ' . count( $updated_cities ) . '件の市区町村を更新しました',
        'updated_cities' => $updated_cities,
    ];
}

/* ============================================================
   1.6 WP-Cron（月次自動同期）
   ============================================================ */

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

/* ============================================================
   2. 管理メニュー登録
   ============================================================ */
add_action( 'admin_menu', function () {
    add_management_page(
        '東京都 助成金管理',
        '🗺 東京助成金管理',
        'manage_options',
        'keichan-grant-tokyo',
        'keichan_grant_tokyo_admin_page'
    );
} );

/* ============================================================
   3. 管理画面
   ============================================================ */
function keichan_grant_tokyo_admin_page(): void {

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( '権限がありません。' );
    }

    $notice = '';

    /* ---- GitHub取り込みハンドラ ---------------------------- */
    if (
        isset( $_POST['kg_github_nonce'] ) &&
        wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kg_github_nonce'] ) ), 'keichan_grant_github' )
    ) {
        // URL設定を更新
        $new_url = esc_url_raw( wp_unslash( $_POST['kg_github_url'] ?? '' ) );
        update_option( 'keichan_grant_github_url', $new_url, false );

        $force  = isset( $_POST['kg_github_force'] );
        $result = keichan_grant_sync_from_github( $force );

        $class = ( $result['status'] === 'error' )
            ? 'notice-error'
            : ( $result['status'] === 'nochange' ? 'notice-info' : 'notice-success' );
        $msg   = $result['message'];
        if ( ! empty( $result['updated_cities'] ) ) {
            $msg .= '（' . implode( '、', $result['updated_cities'] ) . '）';
        }
        $notice .= '<div class="notice ' . esc_attr( $class ) . ' is-dismissible"><p>📥 ' . esc_html( $msg ) . '</p></div>';
    }

    /* ---- 保存処理 ------------------------------------------ */
    if (
        isset( $_POST['keichan_grant_nonce'] ) &&
        wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['keichan_grant_nonce'] ) ), 'keichan_grant_save' )
    ) {
        $cities  = keichan_grant_tokyo_cities();
        // ★ フォームから cities[市区町村名][field] 形式で受け取る
        $posted  = isset( $_POST['cities'] ) && is_array( $_POST['cities'] )
                   ? wp_unslash( $_POST['cities'] )
                   : [];

        $payload = [];
        foreach ( $cities as $city ) {
            $row = isset( $posted[ $city ] ) && is_array( $posted[ $city ] )
                   ? $posted[ $city ]
                   : [];

            $payload[ $city ] = [
                'grant_name' => sanitize_text_field(     $row['grant_name'] ?? '' ),
                'period'     => sanitize_text_field(     $row['period']     ?? '' ),
                'slots'      => sanitize_text_field(     $row['slots']      ?? '' ),
                'url'        => esc_url_raw(              $row['url']        ?? '' ),
                'pdf_url'    => esc_url_raw(              $row['pdf_url']    ?? '' ),
                'note'       => sanitize_textarea_field( $row['note']       ?? '' ),
            ];
        }

        update_option( 'keichan_grant_tokyo',         $payload );
        update_option( 'keichan_grant_tokyo_updated', current_time( 'Y-m-d' ) );
        $notice .= '<div class="notice notice-success is-dismissible"><p>✅ 保存しました（' . count( $cities ) . ' 市区町村）</p></div>';
    }

    /* ---- データ読み込み ------------------------------------- */
    $data    = get_option( 'keichan_grant_tokyo', [] );
    $updated = get_option( 'keichan_grant_tokyo_updated', '' );
    $cities  = keichan_grant_tokyo_cities();

    /* GitHub関連情報 */
    $github_url     = keichan_grant_github_url();
    $github_synced  = get_option( 'keichan_grant_github_synced',  '未取得' );
    $github_checked = get_option( 'keichan_grant_github_checked', '未取得' );
    $next_cron      = wp_next_scheduled( 'keichan_grant_github_cron' );
    $next_cron_s    = $next_cron ? wp_date( 'Y-m-d H:i:s', $next_cron ) : '未予約';

    /* 統計 */
    $cnt_data = $cnt_open = $cnt_end = 0;
    foreach ( $cities as $c ) {
        $r = $data[ $c ] ?? [];
        if ( ! empty( $r['grant_name'] ) ) $cnt_data++;
        if ( ! empty( $r['slots'] ) ) {
            preg_match( '/(終了|締切)/u', $r['slots'] ) ? $cnt_end++ : $cnt_open++;
        }
    }

    /* ---- フロントページURL ---------------------------------- */
    $front_page = get_page_by_path( 'grant-k' );
    $front_url  = $front_page ? get_permalink( $front_page->ID ) : '';

    /* ---- フォーム送信先 ------------------------------------ */
    $action_url = esc_url( admin_url( 'tools.php?page=keichan-grant-tokyo' ) );

    /* ==== HTML 出力 ======================================== */
    echo $notice; // 保存完了メッセージ
    ?>
    <style>
    #kg-wrap{max-width:1160px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;}
    #kg-wrap h1{font-size:1.45rem;margin-bottom:.2rem;display:flex;align-items:center;gap:.5rem;}
    .kg-meta{color:#666;font-size:.83rem;margin-bottom:1.2rem;}
    .kg-meta a{color:#0073aa;}

    /* 統計バー */
    .kg-stats{display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.2rem;}
    .kg-stat{background:#f0f4f8;border-radius:6px;padding:.45rem .9rem;font-size:.82rem;border:1px solid #dde;}
    .kg-stat strong{display:block;font-size:1.15rem;font-weight:800;}
    .kg-stat.open strong{color:#198754;}
    .kg-stat.end  strong{color:#dc3545;}
    .kg-stat.data strong{color:#e44d00;}

    /* GitHub取り込みボックス */
    .kg-github-box{
        background:#e8f4fd;border:1px solid #5b9dd9;
        padding:.75rem 1rem;border-radius:6px;
        margin-bottom:1.2rem;
    }
    .kg-github-box h3{margin:0 0 .4rem;font-size:1rem;color:#1a3a5c;display:flex;align-items:center;gap:.4rem;}
    .kg-github-box .gh-desc{font-size:.8rem;color:#555;margin:.3rem 0;}
    .kg-github-box .gh-desc code{background:#fff;padding:.1rem .35rem;border-radius:3px;border:1px solid #d0d8e3;font-size:.78rem;}
    .kg-github-box .gh-row{display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;margin:.5rem 0;}
    .kg-github-box .gh-row label{font-weight:600;font-size:.82rem;}
    .kg-github-box input[type=url]{
        flex:1;min-width:280px;
        padding:.4rem .55rem;border:1px solid #ccc;border-radius:3px;
        font-family:monospace;font-size:.78rem;
    }
    .kg-github-box .gh-meta{font-size:.78rem;color:#666;margin:.4rem 0 0;}

    /* 絞り込み */
    .kg-toolbar{display:flex;gap:.75rem;align-items:center;margin-bottom:1rem;flex-wrap:wrap;}
    .kg-toolbar input[type=text]{padding:.45rem .8rem;border:1px solid #ccc;border-radius:20px;font-size:.88rem;width:220px;}
    .kg-toolbar input:focus{border-color:#e44d00;outline:none;box-shadow:0 0 0 2px rgba(228,77,0,.18);}
    .kg-help{font-size:.78rem;color:#888;background:#fff8f0;padding:.3rem .75rem;border-radius:4px;border:1px solid #fcd;}

    /* セクション見出し */
    .kg-section{
        font-size:.9rem;font-weight:800;letter-spacing:.06em;
        background:#1a3a5c;color:#fff;
        padding:.5rem 1rem;border-radius:4px;
        margin:2rem 0 .75rem;
    }
    .kg-section.tama{background:#2d6a4f;}

    /* テーブル */
    .kg-table{width:100%;border-collapse:collapse;font-size:.83rem;table-layout:fixed;}
    .kg-table th{
        background:#f1f3f5;color:#333;
        padding:.5rem .6rem;text-align:left;
        border:1px solid #d0d5db;white-space:nowrap;
        position:sticky;top:32px;z-index:5;
        font-size:.78rem;
    }
    .kg-table td{padding:.35rem .45rem;border:1px solid #e0e5eb;vertical-align:middle;}
    .kg-table tr:nth-child(even) td{background:#fafbfc;}
    .kg-table tr:hover td{background:#fff8f0;}

    /* 1列目：市区町村名 */
    .kg-table td:first-child{
        font-weight:700;white-space:nowrap;
        background:#f5f7fa;width:6.5rem;
        border-right:2px solid #bcc;
        font-size:.85rem;
    }

    /* 入力欄 */
    .kg-table input[type=text],
    .kg-table textarea{
        width:100%;box-sizing:border-box;
        padding:.28rem .45rem;
        border:1px solid #ccc;border-radius:3px;
        font-size:.82rem;
        transition:border-color .15s;
    }
    .kg-table input:focus,
    .kg-table textarea:focus{border-color:#e44d00;outline:none;}
    .kg-table textarea{resize:vertical;min-height:42px;}
    .kg-table input.url-f{font-size:.76rem;color:#0055cc;font-family:monospace;}
    .kg-table input.pdf-f{font-size:.76rem;color:#c0392b;font-family:monospace;}

    /* スロット色 */
    .slots-open{color:#198754;font-weight:700;}
    .slots-warn{color:#fd7e14;font-weight:700;}
    .slots-full{color:#dc3545;font-weight:700;}

    /* 保存バー */
    .kg-save-bar{
        position:sticky;bottom:0;z-index:20;
        background:rgba(255,255,255,.97);
        border-top:3px solid #e44d00;
        padding:.7rem 0;margin-top:2rem;
        text-align:center;
        box-shadow:0 -2px 12px rgba(0,0,0,.1);
        backdrop-filter:blur(6px);
    }
    .kg-save-bar .button-primary{font-size:1rem;padding:.55rem 3rem;background:#e44d00;border-color:#c03d00;}
    .kg-save-bar .button-primary:hover{background:#c03d00;}
    .kg-save-bar small{display:block;margin-top:.3rem;font-size:.76rem;color:#888;}

    /* カラム幅 */
    .col-city  {width:7rem;}
    .col-name  {width:20%;}
    .col-period{width:13%;}
    .col-slots {width:11%;}
    .col-url   {width:19%;}
    .col-pdf   {width:19%;}
    .col-note  {width:14%;}
    </style>

    <div id="kg-wrap" class="wrap">

        <h1>🗺 東京都 外壁塗装 助成金管理</h1>
        <p class="kg-meta">
            最終更新: <?php echo $updated
                ? esc_html( date_i18n( 'Y年n月j日', strtotime( $updated ) ) )
                : '<em>未保存</em>'; ?>
            ／ 管理対象: <strong><?php echo count( $cities ); ?></strong> 市区町村
            <?php if ( $front_url ) : ?>
                ／ <a href="<?php echo esc_url( $front_url ); ?>" target="_blank">▶ 助成金マップを表示 →</a>
            <?php endif; ?>
        </p>

        <!-- 統計 -->
        <div class="kg-stats">
            <div class="kg-stat data"><strong><?php echo $cnt_data; ?></strong>情報入力済み</div>
            <div class="kg-stat open"><strong><?php echo $cnt_open; ?></strong>受付中</div>
            <div class="kg-stat end" ><strong><?php echo $cnt_end;  ?></strong>受付終了</div>
            <div class="kg-stat"    ><strong><?php echo count($cities) - $cnt_data; ?></strong>未入力</div>
        </div>

        <!-- GitHub取り込みボックス（独立フォーム） -->
        <form method="post" action="<?php echo $action_url; ?>" class="kg-github-box" id="kg-github-form">
            <?php wp_nonce_field( 'keichan_grant_github', 'kg_github_nonce' ); ?>
            <h3>📥 GitHubから自動取り込み</h3>
            <p class="gh-desc">
                指定したGitHub Raw URL（HTMLファイル）から、各市区町村の助成金データを自動取り込みします。<br>
                対応形式: <code>&lt;tr data-city="市区町村名"&gt;</code> の表形式。
                月次WP-Cronで自動チェックし、内容が変わったときだけ反映します。<br>
                ※ 備考（note）欄は取り込み時に保持されます（手動入力が上書きされません）。
            </p>
            <div class="gh-row">
                <label for="kg_github_url">Raw URL:</label>
                <input type="url" id="kg_github_url" name="kg_github_url"
                       value="<?php echo esc_attr( $github_url ); ?>"
                       placeholder="https://raw.githubusercontent.com/.../*.html">
                <button type="submit" name="kg_github_sync" class="button button-primary">📥 今すぐ取り込む</button>
                <button type="submit" name="kg_github_force" class="button"
                        onclick="return confirm('変更検知を無視して強制的に再取り込みします。よろしいですか？')">強制取り込み</button>
            </div>
            <p class="gh-meta">
                最終取り込み: <strong><?php echo esc_html( $github_synced ); ?></strong>
                ／ 最終チェック: <strong><?php echo esc_html( $github_checked ); ?></strong>
                ／ 次回自動チェック: <strong><?php echo esc_html( $next_cron_s ); ?></strong>
            </p>
        </form>

        <!-- ツールバー -->
        <div class="kg-toolbar">
            <input type="text" id="kg-filter" placeholder="🔍 市区町村を絞り込み…" autocomplete="off">
            <span class="kg-help">💡 入力後、下の「保存する」ボタンを押してください</span>
        </div>

        <!-- フォーム -->
        <!-- action を明示し、method="post" で送信 -->
        <form method="post" action="<?php echo $action_url; ?>" id="kg-form">
            <?php wp_nonce_field( 'keichan_grant_save', 'keichan_grant_nonce' ); ?>

            <?php
            $sections = [
                [ 'label' => '東京 23区',       'class' => '',     'cities' => array_slice( $cities, 0, 23 ) ],
                [ 'label' => '多摩地区（市部）', 'class' => 'tama', 'cities' => array_slice( $cities, 23 )   ],
            ];
            foreach ( $sections as $sec ) :
            ?>
            <div class="kg-section <?php echo esc_attr( $sec['class'] ); ?>">
                <?php echo esc_html( $sec['label'] ); ?>
            </div>
            <table class="kg-table">
                <colgroup>
                    <col class="col-city"><col class="col-name"><col class="col-period">
                    <col class="col-slots"><col class="col-url"><col class="col-pdf"><col class="col-note">
                </colgroup>
                <thead>
                    <tr>
                        <th>市区町村</th>
                        <th>助成金名称</th>
                        <th>受付期間</th>
                        <th>残り枠・状況</th>
                        <th>公式URL</th>
                        <th>PDF URL</th>
                        <th>備考</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( $sec['cities'] as $city ) :

                    /* ★ $city をそのまま配列キーにする（sanitize_key不使用） */
                    $r = wp_parse_args( $data[ $city ] ?? [], [
                        'grant_name' => '', 'period' => '', 'slots' => '',
                        'url' => '', 'pdf_url' => '', 'note' => '',
                    ] );

                    /* スロット色クラス */
                    $sc = '';
                    if ( $r['slots'] ) {
                        if      ( preg_match( '/(終了|締切)/u',               $r['slots'] ) ) $sc = 'slots-full';
                        elseif  ( preg_match( '/わずか|[1-5]\s*(件|戸|枠)/u', $r['slots'] ) ) $sc = 'slots-warn';
                        else $sc = 'slots-open';
                    }

                    /* ★ name 属性: cities[市区町村名][フィールド] */
                    $n = 'cities[' . esc_attr( $city ) . ']';
                ?>
                    <tr class="kg-row" data-city="<?php echo esc_attr( $city ); ?>">
                        <td><?php echo esc_html( $city ); ?></td>

                        <td><input type="text"
                            name="<?php echo $n; ?>[grant_name]"
                            value="<?php echo esc_attr( $r['grant_name'] ); ?>"
                            placeholder="例）ヒートアイランド対策助成"></td>

                        <td><input type="text"
                            name="<?php echo $n; ?>[period]"
                            value="<?php echo esc_attr( $r['period'] ); ?>"
                            placeholder="例）〜令和9年3月31日"></td>

                        <td><input type="text"
                            name="<?php echo $n; ?>[slots]"
                            value="<?php echo esc_attr( $r['slots'] ); ?>"
                            class="<?php echo esc_attr( $sc ); ?>"
                            placeholder="受付中 / 終了 / 残わずか"
                            data-slot-color="1"></td>

                        <td><input type="text"
                            name="<?php echo $n; ?>[url]"
                            value="<?php echo esc_attr( $r['url'] ); ?>"
                            class="url-f"
                            placeholder="https://"></td>

                        <td><input type="text"
                            name="<?php echo $n; ?>[pdf_url]"
                            value="<?php echo esc_attr( $r['pdf_url'] ); ?>"
                            class="pdf-f"
                            placeholder="https://…/panf.pdf"></td>

                        <td><textarea
                            name="<?php echo $n; ?>[note]"
                            placeholder="塗装適用範囲・メモなど"><?php echo esc_textarea( $r['note'] ); ?></textarea></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endforeach; ?>

            <!-- 保存バー（画面下部固定） -->
            <div class="kg-save-bar">
                <button type="submit" class="button button-primary">💾 すべて保存する</button>
                <small>※ 保存すると助成金マップに即時反映されます</small>
            </div>

        </form><!-- /form -->

    </div><!-- #kg-wrap -->

    <script>
    /* 絞り込み */
    document.getElementById('kg-filter').addEventListener('input', function(){
        var v = this.value.trim();
        document.querySelectorAll('.kg-row').forEach(function(tr){
            tr.style.display = (!v || tr.dataset.city.includes(v)) ? '' : 'none';
        });
    });

    /* スロット欄の色リアルタイム更新 */
    document.querySelectorAll('[data-slot-color]').forEach(function(inp){
        function update(){
            var v = inp.value;
            inp.className = '';
            if (!v) return;
            if (/(終了|締切)/.test(v))                inp.className = 'slots-full';
            else if (/わずか|[1-5]\s*(件|戸|枠)/.test(v)) inp.className = 'slots-warn';
            else                                       inp.className = 'slots-open';
        }
        inp.addEventListener('input', update);
    });

    /* フォーム送信前確認（間違い送信防止） */
    document.getElementById('kg-form').addEventListener('submit', function(e){
        // 何も確認せず素直にPOST（confirm削除で快適に）
    });
    </script>
    <?php
}
