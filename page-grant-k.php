<?php
/**
 * Template Name: 助成金マップ（東京）
 * 表示用テンプレート: page-grant-k.php
 * 固定ページのスラッグを「grant-k」にして使用
 */

get_header();

$data    = get_option( 'keichan_grant_tokyo', [] );
$updated = get_option( 'keichan_grant_tokyo_updated', '' );
$cities  = function_exists( 'keichan_grant_tokyo_cities' ) ? keichan_grant_tokyo_cities() : array_keys( $data );
?>

<style>
#grant-tokyo {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem 4rem;
    font-family: 'Noto Sans JP', sans-serif;
}
#grant-tokyo h1 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: .25rem;
}
.grant-meta {
    color: #666;
    font-size: .85rem;
    margin-bottom: 2rem;
}
.grant-map {
    text-align: center;
    margin: 1rem 0 2rem;
}
.grant-map img {
    max-width: 100%;
    height: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
}
.grant-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1rem;
}
.grant-card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-left: 4px solid #e44d00;
    border-radius: 8px;
    padding: 1rem;
    transition: transform .15s, box-shadow .15s;
}
.grant-card.empty {
    border-left-color: #ccc;
    opacity: .5;
}
.grant-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
}
.grant-card-city {
    font-size: 1.05rem;
    font-weight: 700;
    color: #333;
    margin-bottom: .6rem;
    border-bottom: 1px dashed #dee2e6;
    padding-bottom: .4rem;
}
.grant-row {
    display: flex;
    font-size: .85rem;
    margin-bottom: .35rem;
    line-height: 1.4;
}
.grant-label {
    flex: 0 0 70px;
    color: #888;
    font-weight: 600;
}
.grant-value {
    flex: 1;
    color: #222;
    word-break: break-all;
}
.grant-value.empty {
    color: #bbb;
}
.grant-card a {
    display: inline-block;
    margin-top: .5rem;
    font-size: .8rem;
    color: #e44d00;
    text-decoration: none;
}
.grant-card a:hover { text-decoration: underline; }
</style>

<div id="grant-tokyo">
    <h1>東京都 助成金情報</h1>
    <p class="grant-meta">
        <?php if ( $updated ) : ?>
            最終更新日: <?php echo esc_html( date_i18n( 'Y年n月j日', strtotime( $updated ) ) ); ?>
        <?php else : ?>
            データ準備中
        <?php endif; ?>
    </p>

    <!-- 白地図 -->
    <div class="grant-map">
        <?php
        $map_url = get_stylesheet_directory_uri() . '/images/tokyo-map.png';
        $map_path = get_stylesheet_directory() . '/images/tokyo-map.png';
        if ( file_exists( $map_path ) ) : ?>
            <img src="<?php echo esc_url( $map_url ); ?>" alt="東京都白地図">
        <?php else : ?>
            <p style="color:#999;font-size:.85rem;">
                白地図画像は <code>テーマフォルダ/images/tokyo-map.png</code> にアップロードしてください。<br>
                ダウンロード元: <a href="https://freemap.jp/" target="_blank" rel="noopener">つなぐ白地図</a>
            </p>
        <?php endif; ?>
    </div>

    <!-- 助成金カード一覧 -->
    <div class="grant-grid">
    <?php foreach ( $cities as $city ) :
        $row = $data[ $city ] ?? [];
        $has_data = ! empty( $row['grant_name'] ) || ! empty( $row['period'] ) || ! empty( $row['slots'] );
    ?>
        <div class="grant-card <?php echo $has_data ? '' : 'empty'; ?>">
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
                <span class="grant-value <?php echo empty( $row['slots'] ) ? 'empty' : ''; ?>">
                    <?php echo $row['slots'] ? esc_html( $row['slots'] ) : '—'; ?>
                </span>
            </div>

            <?php if ( ! empty( $row['url'] ) ) : ?>
                <a href="<?php echo esc_url( $row['url'] ); ?>" target="_blank" rel="noopener">公式ページ →</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>
</div>

<?php get_footer(); ?>
