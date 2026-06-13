<?php
/**
 * Template Name: 助成金マップ（東京）
 * 表示用テンプレート: page-grant-k.php
 * スラッグ「grant-k」の固定ページに適用
 */

get_header();

$data    = get_option( 'keichan_grant_tokyo', [] );
$updated = get_option( 'keichan_grant_tokyo_updated', '' );
$cities  = function_exists( 'keichan_grant_tokyo_cities' )
    ? keichan_grant_tokyo_cities()
    : array_keys( $data );

$total  = count( $cities );
$filled = 0;
foreach ( $cities as $c ) {
    $r = $data[ $c ] ?? [];
    if ( ! empty( $r['grant_name'] ) || ! empty( $r['period'] ) || ! empty( $r['slots'] ) ) $filled++;
}

// -------------------------------------------------------
// 白地図上のピン座標（画像に対する % 位置）
// -------------------------------------------------------
$pin_coords = [
    // 23区
    '千代田区'   => [ 'x' => 75.5, 'y' => 54.5 ],
    '中央区'     => [ 'x' => 77.5, 'y' => 57.0 ],
    '港区'       => [ 'x' => 74.0, 'y' => 61.0 ],
    '新宿区'     => [ 'x' => 71.0, 'y' => 52.5 ],
    '文京区'     => [ 'x' => 74.0, 'y' => 49.5 ],
    '台東区'     => [ 'x' => 78.0, 'y' => 49.0 ],
    '墨田区'     => [ 'x' => 80.5, 'y' => 52.5 ],
    '江東区'     => [ 'x' => 82.5, 'y' => 58.0 ],
    '品川区'     => [ 'x' => 76.0, 'y' => 65.5 ],
    '目黒区'     => [ 'x' => 72.5, 'y' => 65.0 ],
    '大田区'     => [ 'x' => 73.5, 'y' => 72.5 ],
    '世田谷区'   => [ 'x' => 68.0, 'y' => 65.0 ],
    '渋谷区'     => [ 'x' => 71.0, 'y' => 59.0 ],
    '中野区'     => [ 'x' => 68.0, 'y' => 53.5 ],
    '杉並区'     => [ 'x' => 65.0, 'y' => 57.0 ],
    '豊島区'     => [ 'x' => 71.5, 'y' => 46.5 ],
    '北区'       => [ 'x' => 73.5, 'y' => 43.5 ],
    '荒川区'     => [ 'x' => 77.5, 'y' => 45.5 ],
    '板橋区'     => [ 'x' => 69.0, 'y' => 43.0 ],
    '練馬区'     => [ 'x' => 65.0, 'y' => 46.0 ],
    '足立区'     => [ 'x' => 79.5, 'y' => 41.0 ],
    '葛飾区'     => [ 'x' => 84.0, 'y' => 44.5 ],
    '江戸川区'   => [ 'x' => 88.0, 'y' => 52.0 ],
    // 多摩地区
    '八王子市'   => [ 'x' => 31.0, 'y' => 66.5 ],
    '立川市'     => [ 'x' => 44.0, 'y' => 52.5 ],
    '武蔵野市'   => [ 'x' => 57.5, 'y' => 50.0 ],
    '三鷹市'     => [ 'x' => 56.5, 'y' => 56.5 ],
    '青梅市'     => [ 'x' => 24.0, 'y' => 33.5 ],
    '府中市'     => [ 'x' => 48.5, 'y' => 60.0 ],
    '昭島市'     => [ 'x' => 40.0, 'y' => 56.5 ],
    '調布市'     => [ 'x' => 53.5, 'y' => 62.5 ],
    '町田市'     => [ 'x' => 40.5, 'y' => 79.0 ],
    '小金井市'   => [ 'x' => 53.5, 'y' => 53.0 ],
    '小平市'     => [ 'x' => 54.5, 'y' => 46.0 ],
    '日野市'     => [ 'x' => 44.5, 'y' => 66.0 ],
    '東村山市'   => [ 'x' => 57.0, 'y' => 41.5 ],
    '国分寺市'   => [ 'x' => 50.5, 'y' => 54.5 ],
    '国立市'     => [ 'x' => 47.0, 'y' => 60.5 ],
    '福生市'     => [ 'x' => 34.5, 'y' => 46.5 ],
    '狛江市'     => [ 'x' => 59.0, 'y' => 63.5 ],
    '東大和市'   => [ 'x' => 49.5, 'y' => 42.5 ],
    '清瀬市'     => [ 'x' => 60.0, 'y' => 36.5 ],
    '東久留米市' => [ 'x' => 61.5, 'y' => 42.0 ],
    '武蔵村山市' => [ 'x' => 44.0, 'y' => 42.5 ],
    '多摩市'     => [ 'x' => 48.5, 'y' => 72.5 ],
    '稲城市'     => [ 'x' => 52.5, 'y' => 71.5 ],
    '羽村市'     => [ 'x' => 30.5, 'y' => 44.5 ],
    'あきる野市' => [ 'x' => 26.0, 'y' => 52.0 ],
    '西東京市'   => [ 'x' => 62.5, 'y' => 46.5 ],
];
?>

<style>
/* ── ベース ── */
#grant-tokyo {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem 4rem;
    font-family: 'Noto Sans JP', 'Hiragino Kaku Gothic ProN', sans-serif;
    color: #333;
}
.page-header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 3px solid #e44d00;
}
.page-header h1 {
    font-size: 2rem;
    font-weight: 800;
    margin: 0 0 .4rem;
}
.page-header .lead { color: #666; font-size: .95rem; margin: 0; }
.grant-meta {
    display: inline-flex;
    gap: .8rem;
    margin-top: .75rem;
    font-size: .8rem;
    color: #888;
    flex-wrap: wrap;
    justify-content: center;
}
.grant-meta span {
    background: #f8f9fa;
    padding: .2rem .75rem;
    border-radius: 99px;
    border: 1px solid #e5e7eb;
}

/* ── 白地図＋ピン ── */
.grant-map-wrap {
    position: relative;
    display: inline-block;
    width: 100%;
    margin: 1.5rem 0 2.5rem;
    border-radius: 12px;
    overflow: visible;
    background: #f8f9fa;
    padding: .5rem;
}
.grant-map-wrap img {
    display: block;
    width: 100%;
    height: auto;
    border-radius: 8px;
}
.map-empty {
    padding: 2.5rem 1rem;
    text-align: center;
    color: #999;
    font-size: .85rem;
    line-height: 1.8;
}
.map-empty code {
    background: #fff;
    padding: .15rem .4rem;
    border-radius: 3px;
    border: 1px solid #ddd;
}

/* ── ピン ── */
.map-pin {
    position: absolute;
    transform: translate(-50%, -100%);
    cursor: pointer;
    z-index: 10;
    animation: pin-drop .5s cubic-bezier(.36,.07,.19,.97) both;
}

/* 各ピンのアニメーション遅延を JS で設定 */
@keyframes pin-drop {
    0%   { transform: translate(-50%, calc(-100% - 60px)); opacity: 0; }
    60%  { transform: translate(-50%, calc(-100% + 8px));  opacity: 1; }
    80%  { transform: translate(-50%, calc(-100% - 4px)); }
    100% { transform: translate(-50%, -100%); opacity: 1; }
}

.map-pin svg {
    width: 24px;
    height: 34px;
    filter: drop-shadow(0 3px 4px rgba(0,0,0,.35));
    transition: transform .15s ease;
}
.map-pin:hover svg {
    transform: scale(1.3);
}
/* データなし → グレーピン */
.map-pin.no-data svg path { fill: #bbb; }
.map-pin.no-data svg circle { fill: #fff; }

/* ── ツールチップ ── */
.map-pin .pin-tip {
    display: none;
    position: absolute;
    bottom: calc(100% + 6px);
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0,0,0,.82);
    color: #fff;
    font-size: .75rem;
    white-space: nowrap;
    padding: .3rem .65rem;
    border-radius: 5px;
    pointer-events: none;
    z-index: 20;
}
.map-pin .pin-tip::after {
    content: '';
    position: absolute;
    top: 100%; left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: rgba(0,0,0,.82);
}
.map-pin:hover .pin-tip { display: block; }

/* ── セクション ── */
.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 2.5rem 0 1rem;
    padding-left: .8rem;
    border-left: 5px solid #e44d00;
}

/* ── カードグリッド ── */
.grant-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1rem;
}
.grant-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 1.1rem 1rem 1rem;
    transition: transform .15s, box-shadow .15s, border-color .15s;
    scroll-margin-top: 80px;
}
.grant-card:not(.empty):hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0,0,0,.08);
    border-color: #e44d00;
}
.grant-card.empty {
    background: #fafafa;
    opacity: .5;
}
/* ピンクリック時のハイライト */
.grant-card.highlight {
    border-color: #e44d00;
    box-shadow: 0 0 0 3px rgba(228,77,0,.25);
}
.grant-card-city {
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: .7rem;
    padding-bottom: .5rem;
    border-bottom: 1px dashed #e5e7eb;
}
.grant-row {
    display: flex;
    font-size: .85rem;
    margin-bottom: .4rem;
    line-height: 1.45;
}
.grant-label {
    flex: 0 0 78px;
    color: #888;
    font-weight: 600;
    font-size: .78rem;
}
.grant-value { flex: 1; word-break: break-word; font-weight: 500; }
.grant-value.empty      { color: #ccc; font-weight: 400; }
.grant-value.slots-open { color: #198754; font-weight: 700; }
.grant-value.slots-warn { color: #fd7e14; font-weight: 700; }
.grant-value.slots-full { color: #dc3545; font-weight: 700; }
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
.grant-card-link:hover { background: #b03d00; color: #fff; }

/* ── 注意事項 ── */
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
.grant-notice ul { margin: .4rem 0 0 1.3rem; padding: 0; }
.grant-notice li { margin-bottom: .2rem; }

@media (max-width: 600px) {
    .page-header h1 { font-size: 1.5rem; }
    .grant-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: .7rem; }
    .map-pin svg { width: 18px; height: 26px; }
}
</style>

<div id="grant-tokyo">

    <header class="page-header">
        <h1>東京都 外壁塗装 助成金マップ</h1>
        <p class="lead">区市町村別の最新助成金情報</p>
        <div class="grant-meta">
            <?php if ( $updated ) : ?>
                <span>📅 最終更新: <?php echo esc_html( date_i18n( 'Y年n月j日', strtotime( $updated ) ) ); ?></span>
            <?php else : ?>
                <span>📅 データ準備中</span>
            <?php endif; ?>
            <span>📍 掲載: <?php echo (int) $filled; ?> / <?php echo (int) $total; ?> 市区町村</span>
        </div>
    </header>

    <!-- ── 白地図＋赤ピン ── -->
    <?php
    $map_path = get_stylesheet_directory() . '/images/tokyo-map.png';
    $map_url  = get_stylesheet_directory_uri() . '/images/tokyo-map.png';
    ?>
    <div class="grant-map-wrap">
        <?php if ( file_exists( $map_path ) ) : ?>
            <img id="tokyo-map-img" src="<?php echo esc_url( $map_url ); ?>" alt="東京都 区市町村地図">

            <?php
            $delay = 0;
            foreach ( $pin_coords as $city => $pos ) :
                $row     = $data[ $city ] ?? [];
                $has     = ! empty( $row['grant_name'] ) || ! empty( $row['period'] ) || ! empty( $row['slots'] );
                $cls     = $has ? '' : 'no-data';
                $tip     = esc_html( $city );
                if ( $has && ! empty( $row['slots'] ) ) $tip .= ' ／ ' . esc_html( $row['slots'] );
                $anchor  = 'city-' . esc_attr( rawurlencode( $city ) );
                $delay  += 30;
            ?>
            <a class="map-pin <?php echo esc_attr( $cls ); ?>"
               href="#<?php echo $anchor; ?>"
               style="left:<?php echo esc_attr( $pos['x'] ); ?>%;top:<?php echo esc_attr( $pos['y'] ); ?>%;animation-delay:<?php echo (int) $delay; ?>ms;"
               title="<?php echo esc_attr( $city ); ?>">
                <svg viewBox="0 0 24 34" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 0C7.03 0 3 4.03 3 9c0 6.75 9 21 9 21s9-14.25 9-21c0-4.97-4.03-9-9-9z"
                          fill="<?php echo $has ? '#e44d00' : '#bbb'; ?>"/>
                    <circle cx="12" cy="9" r="4" fill="#fff" opacity=".9"/>
                </svg>
                <span class="pin-tip"><?php echo $tip; ?></span>
            </a>
            <?php endforeach; ?>

        <?php else : ?>
            <div class="map-empty">
                白地図を <code><?php echo esc_html( get_stylesheet() ); ?>/images/tokyo-map.png</code> に配置すると地図が表示されます。<br>
                ダウンロード: <a href="https://freemap.jp/" target="_blank" rel="noopener">つなぐ白地図（freemap.jp）</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- ── 23区 ── -->
    <h2 class="section-title">東京 23区</h2>
    <div class="grant-grid">
    <?php foreach ( array_slice( $cities, 0, 23 ) as $city ) : ?>
        <?php keichan_grant_card( $city, $data[ $city ] ?? [] ); ?>
    <?php endforeach; ?>
    </div>

    <!-- ── 多摩地区 ── -->
    <h2 class="section-title">多摩地区（市部）</h2>
    <div class="grant-grid">
    <?php foreach ( array_slice( $cities, 23 ) as $city ) : ?>
        <?php keichan_grant_card( $city, $data[ $city ] ?? [] ); ?>
    <?php endforeach; ?>
    </div>

    <div class="grant-notice">
        <strong>⚠ ご注意</strong>
        <ul>
            <li>掲載情報は各自治体公式情報をもとに月1回更新しています。</li>
            <li>受付状況は随時変動します。申請前に必ず各市区町村の公式ページをご確認ください。</li>
            <li>助成金の名称・条件・金額は自治体によって異なります。</li>
        </ul>
    </div>

</div>

<script>
// ピンクリック → カードへスクロール＋ハイライト
document.querySelectorAll('.map-pin').forEach(function(pin){
    pin.addEventListener('click', function(e){
        e.preventDefault();
        var id = this.getAttribute('href').slice(1);
        var card = document.getElementById(id);
        if (!card) return;
        document.querySelectorAll('.grant-card').forEach(function(c){ c.classList.remove('highlight'); });
        card.classList.add('highlight');
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(function(){ card.classList.remove('highlight'); }, 2000);
    });
});
</script>

<?php

// -------------------------------------------------------
// カード描画ヘルパー
// -------------------------------------------------------
function keichan_grant_card( string $city, array $row ): void {
    $row     = wp_parse_args( $row, [ 'grant_name' => '', 'period' => '', 'slots' => '', 'url' => '' ] );
    $has     = ! empty( $row['grant_name'] ) || ! empty( $row['period'] ) || ! empty( $row['slots'] );
    $anchor  = 'city-' . rawurlencode( $city );

    $slots_class = '';
    if ( $row['slots'] ) {
        if ( preg_match( '/(終了|締切|締め切り)/u', $row['slots'] ) ) $slots_class = 'slots-full';
        elseif ( preg_match( '/わずか|[1-5]\s*(件|戸|枠)/u', $row['slots'] ) )   $slots_class = 'slots-warn';
        else $slots_class = 'slots-open';
    }
    ?>
    <article class="grant-card <?php echo $has ? '' : 'empty'; ?>" id="<?php echo esc_attr( $anchor ); ?>">
        <div class="grant-card-city">📍 <?php echo esc_html( $city ); ?></div>
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
                公式ページ →
            </a>
        <?php endif; ?>
    </article>
    <?php
}

get_footer();
