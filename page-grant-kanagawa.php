<?php
/**
 * Template Name: 助成金マップ（神奈川）
 * 表示用テンプレート: page-grant-kanagawa.php
 *
 * 使い方:
 *   1. このファイルを使用中テーマフォルダ直下に置く
 *   2. 固定ページを新規作成し、スラッグを「grant-kanagawa」に
 *   3. ページ属性 > テンプレート で「助成金マップ（神奈川）」を選択
 */

get_header();

$data    = get_option( 'keichan_grant_kanagawa', [] );
$updated = get_option( 'keichan_grant_kanagawa_updated', '' );
$cities  = function_exists( 'keichan_grant_kanagawa_cities' )
    ? keichan_grant_kanagawa_cities()
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

// -------------------------------------------------------
// 白地図上のピン座標（神奈川県、画像に対する % 位置）
// 神奈川県の白地図を images/kanagawa-map.png に配置した際の
// 各市町村のおおよその位置。実際の地図画像に合わせて微調整可。
// -------------------------------------------------------
$pin_coords = [
    // ── 政令指定都市 ──
    '横浜市'     => [ 'x' => 76.0, 'y' => 55.0 ],
    '川崎市'     => [ 'x' => 82.0, 'y' => 40.0 ],
    '相模原市'   => [ 'x' => 45.0, 'y' => 30.0 ],
    // ── 一般市（16） ──
    '横須賀市'   => [ 'x' => 82.0, 'y' => 72.0 ],
    '平塚市'     => [ 'x' => 50.0, 'y' => 66.0 ],
    '鎌倉市'     => [ 'x' => 72.0, 'y' => 68.0 ],
    '藤沢市'     => [ 'x' => 63.0, 'y' => 64.0 ],
    '小田原市'   => [ 'x' => 26.0, 'y' => 72.0 ],
    '茅ヶ崎市'   => [ 'x' => 58.0, 'y' => 67.0 ],
    '逗子市'     => [ 'x' => 76.0, 'y' => 71.0 ],
    '三浦市'     => [ 'x' => 84.0, 'y' => 83.0 ],
    '秦野市'     => [ 'x' => 38.0, 'y' => 55.0 ],
    '厚木市'     => [ 'x' => 47.0, 'y' => 47.0 ],
    '大和市'     => [ 'x' => 60.0, 'y' => 48.0 ],
    '伊勢原市'   => [ 'x' => 44.0, 'y' => 53.0 ],
    '海老名市'   => [ 'x' => 55.0, 'y' => 47.0 ],
    '座間市'     => [ 'x' => 55.0, 'y' => 43.0 ],
    '南足柄市'   => [ 'x' => 24.0, 'y' => 60.0 ],
    '綾瀬市'     => [ 'x' => 58.0, 'y' => 52.0 ],
    // ── 町村（14） ──
    '葉山町'     => [ 'x' => 79.0, 'y' => 72.0 ],
    '寒川町'     => [ 'x' => 55.0, 'y' => 60.0 ],
    '大磯町'     => [ 'x' => 47.0, 'y' => 71.0 ],
    '二宮町'     => [ 'x' => 42.0, 'y' => 71.0 ],
    '中井町'     => [ 'x' => 34.0, 'y' => 62.0 ],
    '大井町'     => [ 'x' => 30.0, 'y' => 63.0 ],
    '松田町'     => [ 'x' => 27.0, 'y' => 56.0 ],
    '山北町'     => [ 'x' => 15.0, 'y' => 52.0 ],
    '開成町'     => [ 'x' => 26.0, 'y' => 59.0 ],
    '箱根町'     => [ 'x' => 18.0, 'y' => 72.0 ],
    '真鶴町'     => [ 'x' => 21.0, 'y' => 80.0 ],
    '湯河原町'   => [ 'x' => 17.0, 'y' => 82.0 ],
    '愛川町'     => [ 'x' => 44.0, 'y' => 37.0 ],
    '清川村'     => [ 'x' => 40.0, 'y' => 44.0 ],
];
?>

<style>
#grant-kanagawa {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem 4rem;
    font-family: 'Noto Sans JP', 'Hiragino Kaku Gothic ProN', sans-serif;
    color: #333;
}

#grant-kanagawa .page-header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 3px solid #e44d00;
}
#grant-kanagawa h1 {
    font-size: 2rem;
    font-weight: 800;
    margin: 0 0 .5rem;
    color: #222;
}
#grant-kanagawa .lead {
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

/* 白地図＋ピン */
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

/* ピン */
.map-pin {
    position: absolute;
    transform: translate(-50%, -100%);
    cursor: pointer;
    z-index: 10;
    animation: pin-drop-kanagawa .5s cubic-bezier(.36,.07,.19,.97) both;
    text-decoration: none;
}
@keyframes pin-drop-kanagawa {
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
.map-pin:hover svg { transform: scale(1.3); }

/* ツールチップ */
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

/* ピンクリック時のカードハイライト */
.grant-card.highlight {
    border-color: #e44d00;
    box-shadow: 0 0 0 3px rgba(228,77,0,.25);
}

/* 既存のgrant-map(未使用) */
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
    #grant-kanagawa h1 { font-size: 1.5rem; }
    .grant-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: .7rem; }
    .grant-card { padding: .9rem .7rem; }
    .grant-card-city { font-size: 1rem; }
    .grant-row { font-size: .8rem; }
    .grant-label { flex-basis: 64px; }
}
</style>

<div id="grant-kanagawa">

    <header class="page-header">
        <h1>神奈川県 外壁塗装 助成金マップ</h1>
        <p class="lead">市町村別の最新の助成金情報をまとめています</p>
        <div class="grant-meta">
            <?php if ( $updated ) : ?>
                <span>📅 最終更新: <?php echo esc_html( date_i18n( 'Y年n月j日', strtotime( $updated ) ) ); ?></span>
            <?php else : ?>
                <span>📅 データ準備中</span>
            <?php endif; ?>
            <span>📍 掲載: <?php echo (int) $filled; ?> / <?php echo (int) $total; ?> 市区町村</span>
        </div>
    </header>

    <!-- 白地図＋色分けピン -->
    <?php
    $map_path = get_stylesheet_directory() . '/images/kanagawa-map.png';
    $map_url  = get_stylesheet_directory_uri() . '/images/kanagawa-map.png';
    ?>
    <div class="grant-map-wrap">
        <?php if ( file_exists( $map_path ) ) : ?>
            <img id="kanagawa-map-img" src="<?php echo esc_url( $map_url ); ?>" alt="神奈川県 市町村地図">

            <?php
            $delay = 0;
            foreach ( $pin_coords as $city => $pos ) :
                $row     = $data[ $city ] ?? [];
                $has     = ! empty( $row['grant_name'] ) || ! empty( $row['period'] ) || ! empty( $row['slots'] );

                // ピン色をステータス別に判定（受付中=緑 / 要確認=橙 / 受付終了=赤 / データなし=グレー）
                $pin_fill = '#bbb';
                $cls      = 'no-data';
                if ( $has ) {
                    if ( ! empty( $row['slots'] ) && preg_match( '/(終了|締切|締め切り)/u', $row['slots'] ) ) {
                        $pin_fill = '#dc3545';
                        $cls      = 'status-full';
                    } elseif ( ! empty( $row['slots'] ) && preg_match( '/(要確認|要最新|わずか|残り\s*[1-5]\D)/u', $row['slots'] ) ) {
                        $pin_fill = '#fd7e14';
                        $cls      = 'status-warn';
                    } else {
                        $pin_fill = '#198754';
                        $cls      = 'status-open';
                    }
                }

                $tip = esc_html( $city );
                if ( $has && ! empty( $row['slots'] ) ) $tip .= ' ／ ' . esc_html( $row['slots'] );
                $anchor = 'kcity-' . esc_attr( rawurlencode( $city ) );
                $delay += 30;
            ?>
            <a class="map-pin <?php echo esc_attr( $cls ); ?>"
               href="#<?php echo $anchor; ?>"
               style="left:<?php echo esc_attr( $pos['x'] ); ?>%;top:<?php echo esc_attr( $pos['y'] ); ?>%;animation-delay:<?php echo (int) $delay; ?>ms;"
               title="<?php echo esc_attr( $city ); ?>">
                <svg viewBox="0 0 24 34" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 0C7.03 0 3 4.03 3 9c0 6.75 9 21 9 21s9-14.25 9-21c0-4.97-4.03-9-9-9z"
                          fill="<?php echo $pin_fill; ?>"/>
                    <circle cx="12" cy="9" r="4" fill="#fff" opacity=".9"/>
                </svg>
                <span class="pin-tip"><?php echo $tip; ?></span>
            </a>
            <?php endforeach; ?>

        <?php else : ?>
            <div class="map-empty">
                白地図を <code><?php echo esc_html( get_stylesheet() ); ?>/images/kanagawa-map.png</code> に配置するとピン付き地図が表示されます。<br>
                ダウンロード: <a href="https://freemap.jp/" target="_blank" rel="noopener">つなぐ白地図（freemap.jp）</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- 政令指定都市 -->
    <h2 class="section-title">政令指定都市</h2>
    <div class="grant-grid">
    <?php foreach ( array_slice( $cities, 0, 3 ) as $city ) :
        keichan_grant_kanagawa_render_card( $city, $data[ $city ] ?? [] );
    endforeach; ?>
    </div>

    <!-- 一般市 -->
    <h2 class="section-title">一般市</h2>
    <div class="grant-grid">
    <?php foreach ( array_slice( $cities, 3, 16 ) as $city ) :
        keichan_grant_kanagawa_render_card( $city, $data[ $city ] ?? [] );
    endforeach; ?>
    </div>

    <!-- 町村 -->
    <h2 class="section-title">町村</h2>
    <div class="grant-grid">
    <?php foreach ( array_slice( $cities, 19 ) as $city ) :
        keichan_grant_kanagawa_render_card( $city, $data[ $city ] ?? [] );
    endforeach; ?>
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

<script>
// ピンクリック → カードへスクロール＋ハイライト
document.querySelectorAll('#grant-kanagawa .map-pin').forEach(function(pin){
    pin.addEventListener('click', function(e){
        e.preventDefault();
        var id = this.getAttribute('href').slice(1);
        var card = document.getElementById(id);
        if (!card) return;
        document.querySelectorAll('#grant-kanagawa .grant-card').forEach(function(c){ c.classList.remove('highlight'); });
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
function keichan_grant_kanagawa_render_card( string $city, array $row ): void {
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
    <article class="grant-card <?php echo $has ? '' : 'empty'; ?>" id="<?php echo 'kcity-' . esc_attr( rawurlencode( $city ) ); ?>">
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
