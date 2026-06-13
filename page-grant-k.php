<?php
/**
 * Template Name: 助成金マップ（東京）
 * 表示用テンプレート: page-grant-k.php
 *
 * 使い方:
 *   1. このファイルを使用中テーマフォルダ直下に置く
 *   2. 固定ページを新規作成し、スラッグを「grant-k」に
 *   3. ページ属性 > テンプレート で「助成金マップ（東京）」を選択
 */

get_header();

$data    = get_option( 'keichan_grant_tokyo', [] );
$updated = get_option( 'keichan_grant_tokyo_updated', '' );
$cities  = function_exists( 'keichan_grant_tokyo_cities' )
    ? keichan_grant_tokyo_cities()
    : array_keys( $data );

// 集計
$total   = count( $cities );
$filled  = 0;
foreach ( $cities as $c ) {
    $r = $data[ $c ] ?? [];
    if ( ! empty( $r['grant_name'] ) || ! empty( $r['period'] ) || ! empty( $r['slots'] ) ) {
        $filled++;
    }
}
?>

<style>
#grant-tokyo {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem 4rem;
    font-family: 'Noto Sans JP', 'Hiragino Kaku Gothic ProN', sans-serif;
    color: #333;
}

#grant-tokyo .page-header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 3px solid #e44d00;
}
#grant-tokyo h1 {
    font-size: 2rem;
    font-weight: 800;
    margin: 0 0 .5rem;
    color: #222;
}
#grant-tokyo .lead {
    color: #666;
    font-size: .95rem;
    margin: 0;
}
.grant-meta {
    display: inline-flex;
    gap: 1rem;
    margin-top: .75rem;
    font-size: .8rem;
    color: #888;
    flex-wrap: wrap;
    justify-content: center;
}
.grant-meta span {
    background: #f8f9fa;
    padding: .25rem .8rem;
    border-radius: 99px;
}

/* 白地図エリア */
.grant-map {
    text-align: center;
    margin: 1.5rem 0 2.5rem;
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1rem;
}
.grant-map img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}
.grant-map-empty {
    padding: 2rem 1rem;
    color: #999;
    font-size: .85rem;
    line-height: 1.7;
}
.grant-map-empty code {
    background: #fff;
    padding: .15rem .4rem;
    border-radius: 3px;
    border: 1px solid #ddd;
}

/* セクション区切り */
.section-title {
    font-size: 1.3rem;
    font-weight: 700;
    margin: 2rem 0 1rem;
    padding-left: .8rem;
    border-left: 5px solid #e44d00;
}

/* カードグリッド */
.grant-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}

.grant-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 1.1rem 1rem 1rem;
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    position: relative;
}
.grant-card:not(.empty):hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0,0,0,.08);
    border-color: #e44d00;
}
.grant-card.empty {
    background: #fafafa;
    opacity: .55;
}

.grant-card-city {
    font-size: 1.1rem;
    font-weight: 700;
    color: #222;
    margin-bottom: .7rem;
    padding-bottom: .5rem;
    border-bottom: 1px dashed #e5e7eb;
    display: flex;
    align-items: center;
    gap: .4rem;
}
.grant-card-city::before {
    content: '📍';
    font-size: .95rem;
}

.grant-row {
    display: flex;
    font-size: .85rem;
    margin-bottom: .45rem;
    line-height: 1.45;
}
.grant-label {
    flex: 0 0 78px;
    color: #888;
    font-weight: 600;
    font-size: .78rem;
}
.grant-value {
    flex: 1;
    color: #222;
    word-break: break-word;
    font-weight: 500;
}
.grant-value.empty {
    color: #ccc;
    font-weight: 400;
}
.grant-value.slots-warn  { color: #fd7e14; font-weight: 700; }
.grant-value.slots-full  { color: #dc3545; font-weight: 700; }
.grant-value.slots-open  { color: #198754; font-weight: 700; }

.grant-card-link {
    display: inline-block;
    margin-top: .7rem;
    padding: .35rem .8rem;
    font-size: .8rem;
    font-weight: 600;
    color: #fff;
    background: #e44d00;
    border-radius: 4px;
    text-decoration: none;
    transition: background .15s;
}
.grant-card-link:hover {
    background: #b03d00;
    color: #fff;
}

/* 注意事項 */
.grant-notice {
    margin-top: 3rem;
    padding: 1.2rem 1.4rem;
    background: #fff8e1;
    border-left: 4px solid #ffc107;
    border-radius: 4px;
    font-size: .85rem;
    color: #555;
    line-height: 1.7;
}
.grant-notice strong { color: #b8860b; }
.grant-notice ul { margin: .5rem 0 0 1.3rem; padding: 0; }
.grant-notice li { margin-bottom: .2rem; }

@media (max-width: 600px) {
    #grant-tokyo h1 { font-size: 1.5rem; }
    .grant-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: .7rem; }
    .grant-card { padding: .9rem .7rem; }
    .grant-card-city { font-size: 1rem; }
    .grant-row { font-size: .8rem; }
    .grant-label { flex-basis: 64px; }
}
</style>

<div id="grant-tokyo">

    <header class="page-header">
        <h1>東京都 外壁塗装 助成金マップ</h1>
        <p class="lead">区市町村別の最新の助成金情報をまとめています</p>
        <div class="grant-meta">
            <?php if ( $updated ) : ?>
                <span>📅 最終更新: <?php echo esc_html( date_i18n( 'Y年n月j日', strtotime( $updated ) ) ); ?></span>
            <?php else : ?>
                <span>📅 データ準備中</span>
            <?php endif; ?>
            <span>📍 掲載: <?php echo (int) $filled; ?> / <?php echo (int) $total; ?> 市区町村</span>
        </div>
    </header>

    <!-- 白地図 -->
    <?php
    $map_path = get_stylesheet_directory() . '/images/tokyo-map.png';
    $map_url  = get_stylesheet_directory_uri() . '/images/tokyo-map.png';
    ?>
    <div class="grant-map">
        <?php if ( file_exists( $map_path ) ) : ?>
            <img src="<?php echo esc_url( $map_url ); ?>" alt="東京都 区市町村地図">
        <?php else : ?>
            <div class="grant-map-empty">
                白地図画像は <code><?php echo esc_html( get_stylesheet() ); ?>/images/tokyo-map.png</code> にアップロードしてください。<br>
                ダウンロード元: <a href="https://freemap.jp/" target="_blank" rel="noopener">つなぐ白地図 (freemap.jp)</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- 23区 -->
    <h2 class="section-title">東京23区</h2>
    <div class="grant-grid">
    <?php
    $wards = array_slice( $cities, 0, 23 );
    foreach ( $wards as $city ) :
        keichan_grant_render_card( $city, $data[ $city ] ?? [] );
    endforeach;
    ?>
    </div>

    <!-- 多摩地区 -->
    <h2 class="section-title">多摩地区（市部）</h2>
    <div class="grant-grid">
    <?php
    $towns = array_slice( $cities, 23 );
    foreach ( $towns as $city ) :
        keichan_grant_render_card( $city, $data[ $city ] ?? [] );
    endforeach;
    ?>
    </div>

    <!-- 注意事項 -->
    <div class="grant-notice">
        <strong>⚠ ご注意</strong>
        <ul>
            <li>掲載情報は各自治体の公式情報をもとに月1回更新しています。</li>
            <li>受付状況は随時変動します。申請前に必ず各市区町村の公式ページをご確認ください。</li>
            <li>助成金の名称・条件・金額は自治体によって異なります。</li>
        </ul>
    </div>

</div>

<?php
// -------------------------------------------------------
// カード描画ヘルパー
// -------------------------------------------------------
function keichan_grant_render_card( string $city, array $row ): void {
    $row = wp_parse_args( $row, [ 'grant_name' => '', 'period' => '', 'slots' => '', 'url' => '' ] );
    $has = ! empty( $row['grant_name'] ) || ! empty( $row['period'] ) || ! empty( $row['slots'] );

    // 残り枠のカラー判定
    $slots_class = '';
    if ( $row['slots'] ) {
        if ( preg_match( '/(終了|締切|締め切り|0)/u', $row['slots'] ) ) {
            $slots_class = 'slots-full';
        } elseif ( preg_match( '/(残りわずか|わずか|1\D|2\D|3\D|4\D|5\D)/u', $row['slots'] ) ) {
            $slots_class = 'slots-warn';
        } else {
            $slots_class = 'slots-open';
        }
    }
    ?>
    <article class="grant-card <?php echo $has ? '' : 'empty'; ?>">
        <div class="grant-card-city"><?php echo esc_html( $city ); ?></div>

        <div class="grant-row">
            <span class="grant-label">助成金名</span>
            <span class="grant-value <?php echo empty( $row['grant_name'] ) ? 'empty' : ''; ?>">
                <?php echo $row['grant_name'] ? esc_html( $row['grant_name'] ) : '—'; ?>
            </span>
        </div>

        <div class="grant-row">
            <span class="grant-label">残り期間</span>
            <span class="grant-value <?php echo empty( $row['period'] ) ? 'empty' : ''; ?>">
                <?php echo $row['period'] ? esc_html( $row['period'] ) : '—'; ?>
            </span>
        </div>

        <div class="grant-row">
            <span class="grant-label">残り枠</span>
            <span class="grant-value <?php echo $row['slots'] ? esc_attr( $slots_class ) : 'empty'; ?>">
                <?php echo $row['slots'] ? esc_html( $row['slots'] ) : '—'; ?>
            </span>
        </div>

        <?php if ( ! empty( $row['url'] ) ) : ?>
            <a class="grant-card-link" href="<?php echo esc_url( $row['url'] ); ?>" target="_blank" rel="noopener">
                公式ページを見る →
            </a>
        <?php endif; ?>
    </article>
    <?php
}

get_footer();
