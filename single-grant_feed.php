<?php
/**
 * Template Name: 助成金情報ページ（関東）
 * 表示用テンプレート: single-grant_feed.php
 * ─────────────────────────────────────────
 * wp_options の keichan_grant_status を読み取り
 * 都道府県タブ＋市区町村カード一覧を表示する。
 */

get_header();

// -------------------------------------------------------
// データ取得
// -------------------------------------------------------
$grant_data  = get_option( 'keichan_grant_status', [] );
$last_update = get_option( 'keichan_grant_last_updated', '' );

$prefs = [ '東京都', '神奈川県', '埼玉県', '千葉県' ];

// ステータス表示用設定
$status_config = [
    'available'   => [ 'label' => '受付中',     'class' => 'grant-available',    'icon' => '✅' ],
    'nearly_full' => [ 'label' => '残りわずか',  'class' => 'grant-nearly-full',  'icon' => '⚠️' ],
    'full'        => [ 'label' => '締め切り',    'class' => 'grant-full',          'icon' => '❌' ],
    'unknown'     => [ 'label' => '情報なし',    'class' => 'grant-unknown',       'icon' => '❓' ],
];

// 都道府県別に仕分け
$by_pref = [];
foreach ( $grant_data as $key => $row ) {
    $by_pref[ $row['pref'] ][] = $row;
}

// サマリー集計
function keichan_grant_summary( array $rows, array $config ): array {
    $counts = array_fill_keys( array_keys( $config ), 0 );
    foreach ( $rows as $r ) {
        $s = $r['status'] ?? 'unknown';
        if ( isset( $counts[ $s ] ) ) $counts[ $s ]++;
    }
    return $counts;
}
?>

<!-- ===== スタイル ===== -->
<style>
:root {
    --color-available:   #198754;
    --color-nearly:      #fd7e14;
    --color-full:        #dc3545;
    --color-unknown:     #6c757d;
    --color-bg:          #f8f9fa;
    --radius:            8px;
}

#grant-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem 4rem;
    font-family: 'Noto Sans JP', sans-serif;
}

#grant-page h1 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: .25rem;
}

.grant-meta {
    color: #666;
    font-size: .85rem;
    margin-bottom: 2rem;
}

/* ── タブ ── */
.grant-tabs {
    display: flex;
    gap: .5rem;
    border-bottom: 2px solid #dee2e6;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.grant-tab-btn {
    padding: .5rem 1.25rem;
    border: none;
    background: none;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    color: #555;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: color .2s, border-color .2s;
}
.grant-tab-btn.active,
.grant-tab-btn:hover {
    color: #e44d00;
    border-bottom-color: #e44d00;
}

/* ── サマリーバー ── */
.grant-summary {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}
.grant-summary-item {
    display: flex;
    align-items: center;
    gap: .4rem;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 99px;
    padding: .3rem .9rem;
    font-size: .85rem;
    font-weight: 600;
}
.dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}
.dot-available   { background: var(--color-available); }
.dot-nearly-full { background: var(--color-nearly); }
.dot-full        { background: var(--color-full); }
.dot-unknown     { background: var(--color-unknown); }

/* ── カードグリッド ── */
.grant-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: .75rem;
}

.grant-card {
    background: #fff;
    border-radius: var(--radius);
    border: 1px solid #dee2e6;
    padding: .9rem .8rem;
    text-align: center;
    transition: transform .15s, box-shadow .15s;
    position: relative;
    overflow: hidden;
}
.grant-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
}

.grant-card::before {
    content: '';
    display: block;
    height: 4px;
    position: absolute;
    top: 0; left: 0; right: 0;
    border-radius: var(--radius) var(--radius) 0 0;
}
.grant-available::before   { background: var(--color-available); }
.grant-nearly-full::before { background: var(--color-nearly); }
.grant-full::before        { background: var(--color-full); }
.grant-unknown::before     { background: var(--color-unknown); }

.grant-card-city {
    font-weight: 700;
    font-size: .95rem;
    margin-bottom: .4rem;
}
.grant-card-status {
    font-size: .8rem;
    font-weight: 600;
}
.grant-available   .grant-card-status { color: var(--color-available); }
.grant-nearly-full .grant-card-status { color: var(--color-nearly); }
.grant-full        .grant-card-status { color: var(--color-full); }
.grant-unknown     .grant-card-status { color: var(--color-unknown); }

.grant-card-link {
    display: block;
    margin-top: .5rem;
    font-size: .72rem;
    color: #888;
    text-decoration: none;
    word-break: break-all;
}
.grant-card-link:hover { color: #e44d00; }

/* ── タブパネル非表示 ── */
.grant-panel { display: none; }
.grant-panel.active { display: block; }

/* ── 凡例 ── */
.grant-legend {
    margin-top: 2rem;
    padding: 1rem;
    background: var(--color-bg);
    border-radius: var(--radius);
    font-size: .82rem;
    color: #555;
}
.grant-legend ul { margin: .4rem 0 0; padding-left: 1.2rem; }
.grant-legend li { margin-bottom: .2rem; }

@media (max-width: 600px) {
    .grant-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); }
}
</style>

<!-- ===== コンテンツ ===== -->
<div id="grant-page">

    <h1>関東エリア 外壁塗装助成金マップ</h1>
    <p class="grant-meta">
        対象エリア: 東京都・神奈川県・埼玉県・千葉県　／
        <?php if ( $last_update ) : ?>
            最終更新日: <?php echo esc_html( date_i18n( 'Y年n月j日', strtotime( $last_update ) ) ); ?>（毎月1日更新）
        <?php else : ?>
            データ準備中
        <?php endif; ?>
    </p>

    <!-- タブ -->
    <div class="grant-tabs" role="tablist">
        <?php foreach ( $prefs as $i => $pref ) : ?>
        <button class="grant-tab-btn <?php echo $i === 0 ? 'active' : ''; ?>"
                role="tab"
                aria-controls="panel-<?php echo esc_attr( $i ); ?>"
                data-panel="<?php echo esc_attr( $i ); ?>">
            <?php echo esc_html( $pref ); ?>
        </button>
        <?php endforeach; ?>
    </div>

    <!-- タブパネル -->
    <?php foreach ( $prefs as $i => $pref ) :
        $rows    = $by_pref[ $pref ] ?? [];
        $summary = keichan_grant_summary( $rows, $status_config );
    ?>
    <div class="grant-panel <?php echo $i === 0 ? 'active' : ''; ?>"
         id="panel-<?php echo esc_attr( $i ); ?>"
         role="tabpanel">

        <!-- サマリーバー -->
        <div class="grant-summary">
            <div class="grant-summary-item"><span class="dot dot-available"></span>受付中: <?php echo (int) $summary['available']; ?>市区町村</div>
            <div class="grant-summary-item"><span class="dot dot-nearly-full"></span>残りわずか: <?php echo (int) $summary['nearly_full']; ?>市区町村</div>
            <div class="grant-summary-item"><span class="dot dot-full"></span>締め切り: <?php echo (int) $summary['full']; ?>市区町村</div>
            <div class="grant-summary-item"><span class="dot dot-unknown"></span>情報なし: <?php echo (int) $summary['unknown']; ?>市区町村</div>
        </div>

        <!-- カードグリッド -->
        <div class="grant-grid">
        <?php if ( empty( $rows ) ) : ?>
            <p>データがありません。管理画面から更新してください。</p>
        <?php else :
            // ステータス順にソート（available → nearly_full → unknown → full）
            $order = [ 'available' => 0, 'nearly_full' => 1, 'unknown' => 2, 'full' => 3 ];
            usort( $rows, fn( $a, $b ) =>
                ( $order[ $a['status'] ] ?? 2 ) <=> ( $order[ $b['status'] ] ?? 2 )
            );
            foreach ( $rows as $row ) :
                $st  = $row['status'] ?? 'unknown';
                $cfg = $status_config[ $st ] ?? $status_config['unknown'];
        ?>
            <div class="grant-card <?php echo esc_attr( $cfg['class'] ); ?>">
                <div class="grant-card-city"><?php echo esc_html( $row['city'] ); ?></div>
                <div class="grant-card-status"><?php echo esc_html( $cfg['icon'] . ' ' . $cfg['label'] ); ?></div>
                <?php if ( ! empty( $row['url'] ) ) : ?>
                    <a class="grant-card-link" href="<?php echo esc_url( $row['url'] ); ?>" target="_blank" rel="noopener">
                        公式ページ
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; endif; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- 凡例 -->
    <div class="grant-legend">
        <strong>注意事項</strong>
        <ul>
            <li>本ページの情報は各自治体の公式情報をもとに毎月1回更新しています。</li>
            <li>受付状況は随時変動します。申請前に必ず各市区町村の公式ページをご確認ください。</li>
            <li>助成金の名称・条件・金額は自治体によって異なります。</li>
        </ul>
    </div>

</div><!-- /#grant-page -->

<!-- タブ切替スクリプト -->
<script>
(function () {
    var btns   = document.querySelectorAll('.grant-tab-btn');
    var panels = document.querySelectorAll('.grant-panel');

    btns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var idx = this.dataset.panel;
            btns.forEach(function (b) { b.classList.remove('active'); });
            panels.forEach(function (p) { p.classList.remove('active'); });
            this.classList.add('active');
            document.getElementById('panel-' + idx).classList.add('active');
        });
    });
}());
</script>

<?php get_footer(); ?>
