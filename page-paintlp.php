<?php
/**
 * Template Name: LP（塗装／助成金訴求）
 * 表示用テンプレート: page-paintlp.php
 *
 * 使い方:
 *   1. 固定ページを新規作成、スラッグを「paint-lp」等に
 *   2. ページ属性 > テンプレート で「LP（塗装／助成金訴求）」を選択
 *
 * ※ Cocoonの装飾を全て外したGoogle広告用LP専用テンプレート
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="東京49自治体・神奈川33市町村の助成金完全対応。遮熱・断熱塗料で光熱費削減。代表が最初から最後まで一貫対応する外壁塗装専門店。">
<meta property="og:title" content="知らないと損する助成金で、外壁塗装を賢く。 | けいちゃんペイント">
<meta property="og:description" content="令和8年度・東京都49自治体・神奈川県33市町村の助成金を全網羅。書類代行から施工まで一貫サポート。">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>">
<link rel="canonical" href="<?php echo esc_url( get_permalink() ); ?>">
<?php wp_head(); ?>
<style>
/* Cocoon装飾を完全非表示 */
body.lp-page #header, body.lp-page .header-container, body.lp-page #navi,
body.lp-page #footer, body.lp-page .footer-container, body.lp-page #sidebar,
body.lp-page .breadcrumb, body.lp-page .article-header, body.lp-page .article-footer,
body.lp-page #comment-area, body.lp-page .comment-btn-wrap, body.lp-page .cocoon-widget-area,
body.lp-page .go-to-top, body.lp-page .related-entry-heading, body.lp-page .a-wrap { display: none !important; }
body.lp-page #wrap, body.lp-page #container, body.lp-page #main,
body.lp-page .article, body.lp-page .entry-content { max-width: none !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }
body.lp-page { padding-bottom: 76px !important; margin: 0 !important; background: #FAF6EE; }
</style>
<title>けいちゃんペイント LP</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Zen+Old+Mincho:wght@500;700;900&family=Noto+Sans+JP:wght@400;500;700&family=Inter:wght@600;800;900&display=swap">
<style>
:root {
  --ground: #FAF6EE;
  --paper: #FFFDF7;
  --ink: #1A2B2E;
  --ink-mid: #3E4F52;
  --ink-soft: #6B7A7D;
  --line: #E5DFD1;
  --accent: #D2461B;
  --accent-deep: #A6371A;
  --sage: #6B8E7F;
  --gold: #E8A93B;
  --pale-sage: #E9EFEA;
  --pale-clay: #F5E8DC;
  --stat-red: #C0392B;
  --stat-green: #588A6F;
  --stat-gold: #C08F1F;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body {
  background: var(--ground);
  color: var(--ink);
  font-family: "Noto Sans JP", "Hiragino Kaku Gothic ProN", sans-serif;
  font-size: 16px;
  line-height: 1.7;
  -webkit-font-smoothing: antialiased;
  padding-bottom: 76px;
}
img { max-width: 100%; display: block; }

.mincho { font-family: "Zen Old Mincho", "Yu Mincho", serif; }
.mono { font-family: "Inter", sans-serif; font-feature-settings: "tnum"; }

.container { max-width: 1080px; margin: 0 auto; padding: 0 20px; }
.container-narrow { max-width: 780px; margin: 0 auto; padding: 0 20px; }

.eyebrow {
  display: inline-block;
  font-size: 11px;
  letter-spacing: .28em;
  font-weight: 700;
  color: var(--accent);
  text-transform: uppercase;
  padding: 5px 12px;
  border: 1px solid var(--accent);
  border-radius: 999px;
  background: var(--paper);
}

/* ---- Top notice bar ---- */
.notice {
  background: var(--ink);
  color: var(--ground);
  padding: 8px 20px;
  text-align: center;
  font-size: 12.5px;
  letter-spacing: .04em;
}
.notice strong { color: var(--gold); font-weight: 700; }

/* ---- Header ---- */
.header {
  background: var(--paper);
  border-bottom: 1px solid var(--line);
  padding: 14px 0;
  position: sticky;
  top: 0;
  z-index: 30;
  backdrop-filter: blur(6px);
}
.header-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}
.brand {
  font-family: "Zen Old Mincho", serif;
  font-weight: 700;
  font-size: 20px;
  color: var(--ink);
  letter-spacing: .04em;
  display: flex; align-items: baseline; gap: 8px;
}
.brand-mark {
  width: 26px; height: 26px;
  background: var(--accent);
  border-radius: 4px;
  display: inline-block;
  transform: rotate(-4deg);
  align-self: center;
}
.brand-sub { font-family: "Noto Sans JP"; font-size: 11px; color: var(--ink-soft); font-weight: 500; letter-spacing: .12em; }
.header-nav { display: flex; gap: 20px; font-size: 13.5px; color: var(--ink-mid); }
.header-nav a { color: inherit; text-decoration: none; font-weight: 500; transition: color .15s; }
.header-nav a:hover { color: var(--accent); }
.header-cta {
  background: var(--accent);
  color: #fff;
  padding: 9px 18px;
  border-radius: 4px;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  transition: background .15s;
}
.header-cta:hover { background: var(--accent-deep); }
@media (max-width: 720px) {
  .header-nav { display: none; }
}

/* ---- Hero ---- */
.hero {
  background: linear-gradient(180deg, var(--paper) 0%, var(--ground) 100%);
  padding: 60px 0 70px;
  border-bottom: 1px solid var(--line);
  position: relative;
  overflow: hidden;
}
.hero-inner {
  display: grid;
  grid-template-columns: 1.15fr 1fr;
  gap: 60px;
  align-items: center;
}
.hero-eyebrow {
  display: inline-block;
  font-size: 12px;
  letter-spacing: .3em;
  font-weight: 700;
  color: var(--accent);
  margin-bottom: 22px;
}
.hero-title {
  font-family: "Zen Old Mincho", serif;
  font-weight: 900;
  font-size: clamp(32px, 5vw, 46px);
  line-height: 1.28;
  letter-spacing: .01em;
  text-wrap: balance;
  margin-bottom: 24px;
  color: var(--ink);
}
.hero-title .accent { color: var(--accent); }
.hero-title .highlight {
  background: linear-gradient(transparent 60%, rgba(232, 169, 59, .35) 60%);
  padding: 0 4px;
}
.hero-lead {
  font-size: 16.5px;
  line-height: 1.85;
  color: var(--ink-mid);
  margin-bottom: 32px;
  max-width: 520px;
}
.hero-cta-row { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 32px; }
.btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 15px 26px;
  font-size: 15px;
  font-weight: 700;
  text-decoration: none;
  border-radius: 6px;
  transition: all .18s;
  border: 2px solid transparent;
  letter-spacing: .04em;
}
.btn-primary { background: var(--accent); color: #fff; }
.btn-primary:hover { background: var(--accent-deep); transform: translateY(-1px); box-shadow: 0 6px 18px rgba(210,70,27,.28); }
.btn-line { background: #06C755; color: #fff; }
.btn-line:hover { background: #05a447; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(6,199,85,.28); }
.btn-ghost { border-color: var(--ink); color: var(--ink); }
.btn-ghost:hover { background: var(--ink); color: #fff; }
.hero-trust {
  display: flex; gap: 26px; flex-wrap: wrap;
  font-size: 12.5px; color: var(--ink-soft);
  padding-top: 22px;
  border-top: 1px solid var(--line);
}
.hero-trust span { display: inline-flex; align-items: center; gap: 6px; }
.hero-trust .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--sage); }

/* Hero right visual (big number + kanji stamp) */
.hero-visual {
  position: relative;
  background: var(--ink);
  border-radius: 10px;
  padding: 44px 40px;
  color: var(--ground);
  overflow: hidden;
}
.hero-visual::after {
  content: "";
  position: absolute;
  bottom: -60px; right: -60px;
  width: 220px; height: 220px;
  border-radius: 50%;
  background: var(--accent);
  opacity: .18;
}
.hv-label {
  font-size: 11.5px;
  letter-spacing: .3em;
  color: var(--gold);
  font-weight: 700;
  margin-bottom: 10px;
}
.hv-title {
  font-family: "Zen Old Mincho", serif;
  font-size: 22px;
  font-weight: 700;
  line-height: 1.5;
  margin-bottom: 22px;
}
.hv-amount {
  font-family: "Inter", sans-serif;
  font-weight: 900;
  font-size: 74px;
  line-height: 1;
  color: var(--gold);
  letter-spacing: -.02em;
  margin-bottom: 6px;
}
.hv-amount .yen { font-size: 30px; color: var(--ground); margin-left: 8px; letter-spacing: 0; }
.hv-note { font-size: 12.5px; color: rgba(250,246,238,.6); line-height: 1.65; position: relative; z-index: 1; }
.hv-stamp {
  position: absolute;
  top: 20px; right: 20px;
  font-family: "Zen Old Mincho", serif;
  font-size: 11px;
  letter-spacing: .3em;
  color: var(--accent);
  border: 1.5px solid var(--accent);
  padding: 6px 10px;
  border-radius: 3px;
  writing-mode: vertical-rl;
  font-weight: 700;
}
@media (max-width: 860px) {
  .hero-inner { grid-template-columns: 1fr; gap: 40px; }
  .hero-visual { padding: 36px 28px; }
  .hv-amount { font-size: 58px; }
}

/* ---- Section framework ---- */
.section { padding: 80px 0; }
.section-tight { padding: 60px 0; }
.section-heading {
  text-align: center;
  margin-bottom: 50px;
}
.section-heading .eyebrow { margin-bottom: 18px; }
.section-heading h2 {
  font-family: "Zen Old Mincho", serif;
  font-weight: 900;
  font-size: clamp(26px, 3.6vw, 34px);
  line-height: 1.35;
  color: var(--ink);
  text-wrap: balance;
  letter-spacing: .01em;
}
.section-heading p {
  max-width: 620px;
  margin: 16px auto 0;
  font-size: 15px;
  color: var(--ink-mid);
  line-height: 1.85;
}

/* ---- Why grants section ---- */
.why {
  background: var(--paper);
  border-top: 1px solid var(--line);
  border-bottom: 1px solid var(--line);
}
.why-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  margin-top: 40px;
}
.why-card {
  background: var(--ground);
  padding: 32px 26px;
  border-radius: 8px;
  border: 1px solid var(--line);
}
.why-num {
  font-family: "Inter", sans-serif;
  font-weight: 900;
  font-size: 34px;
  color: var(--accent);
  letter-spacing: -.02em;
  margin-bottom: 12px;
}
.why-card h3 {
  font-family: "Zen Old Mincho", serif;
  font-size: 18px;
  font-weight: 700;
  color: var(--ink);
  margin-bottom: 10px;
}
.why-card p {
  font-size: 14px;
  color: var(--ink-mid);
  line-height: 1.75;
}
@media (max-width: 720px) {
  .why-grid { grid-template-columns: 1fr; }
}

/* ---- Grants table ---- */
.grants-panel {
  background: var(--paper);
  border-radius: 10px;
  border: 1px solid var(--line);
  overflow: hidden;
}
.grants-tabs {
  display: flex;
  border-bottom: 1px solid var(--line);
  background: var(--pale-sage);
}
.grants-tab {
  flex: 1;
  padding: 16px 20px;
  text-align: center;
  font-family: "Zen Old Mincho", serif;
  font-weight: 700;
  font-size: 15px;
  color: var(--ink-mid);
  cursor: pointer;
  border: none;
  background: transparent;
  border-bottom: 3px solid transparent;
  transition: all .15s;
}
.grants-tab.active { color: var(--accent); border-bottom-color: var(--accent); background: var(--paper); }
.grants-tab:hover:not(.active) { color: var(--ink); background: rgba(255,255,255,.4); }
.grants-body { padding: 8px 0; }
.grants-list { display: none; }
.grants-list.active { display: block; }
.grants-row {
  display: grid;
  grid-template-columns: 140px 1fr 100px;
  gap: 20px;
  padding: 16px 24px;
  border-bottom: 1px solid var(--line);
  align-items: center;
  font-size: 14px;
}
.grants-row:last-child { border-bottom: none; }
.grants-city {
  font-family: "Zen Old Mincho", serif;
  font-weight: 700;
  font-size: 15.5px;
  color: var(--ink);
}
.grants-desc { color: var(--ink-mid); line-height: 1.6; font-size: 13.5px; }
.grants-amount {
  font-family: "Inter", sans-serif;
  font-weight: 800;
  font-size: 17px;
  color: var(--accent);
  text-align: right;
  letter-spacing: -.01em;
}
.grants-amount small { display: block; font-size: 10px; color: var(--ink-soft); font-weight: 500; letter-spacing: .08em; margin-top: 2px; }
.grants-footer {
  padding: 20px 24px;
  background: var(--pale-clay);
  border-top: 1px solid var(--line);
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  font-size: 13px;
  color: var(--ink-mid);
}
.grants-footer a { color: var(--accent); text-decoration: none; font-weight: 700; }
@media (max-width: 720px) {
  .grants-row { grid-template-columns: 1fr; gap: 6px; padding: 14px 18px; }
  .grants-amount { text-align: left; font-size: 20px; }
}

/* ---- Reasons ---- */
.reasons {
  background: var(--ground);
}
.reasons-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
}
.reason {
  padding: 8px;
}
.reason-icon {
  width: 52px; height: 52px;
  background: var(--pale-clay);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 20px;
  font-size: 24px;
  color: var(--accent);
}
.reason-num {
  font-family: "Inter", sans-serif;
  font-size: 11.5px;
  letter-spacing: .3em;
  color: var(--sage);
  font-weight: 700;
  margin-bottom: 8px;
}
.reason h3 {
  font-family: "Zen Old Mincho", serif;
  font-size: 21px;
  font-weight: 700;
  line-height: 1.45;
  color: var(--ink);
  margin-bottom: 14px;
  text-wrap: balance;
}
.reason p {
  font-size: 14px;
  color: var(--ink-mid);
  line-height: 1.8;
}
@media (max-width: 720px) {
  .reasons-grid { grid-template-columns: 1fr; gap: 40px; }
}

/* ---- Technique section (deep dark) ---- */
.tech {
  background: var(--ink);
  color: var(--ground);
}
.tech .section-heading h2 { color: var(--ground); }
.tech .section-heading p { color: rgba(250,246,238,.75); }
.tech .eyebrow { background: transparent; color: var(--gold); border-color: var(--gold); }
.tech-grid {
  display: grid;
  grid-template-columns: 1.3fr 1fr;
  gap: 50px;
  align-items: center;
}
.tech-visual {
  background: linear-gradient(135deg, #234043 0%, #12191b 100%);
  border-radius: 8px;
  padding: 34px 30px;
  border: 1px solid rgba(255,255,255,.08);
}
.tech-stat {
  display: flex; align-items: baseline; gap: 12px;
  padding: 20px 0;
  border-bottom: 1px solid rgba(255,255,255,.1);
}
.tech-stat:last-child { border-bottom: none; }
.tech-stat-num {
  font-family: "Inter", sans-serif;
  font-weight: 900;
  font-size: 42px;
  color: var(--gold);
  min-width: 130px;
  letter-spacing: -.02em;
}
.tech-stat-num small { font-size: 16px; color: var(--ground); margin-left: 4px; }
.tech-stat-desc {
  font-size: 13.5px;
  color: rgba(250,246,238,.8);
  line-height: 1.65;
}
.tech-copy h3 {
  font-family: "Zen Old Mincho", serif;
  font-size: 24px;
  font-weight: 700;
  line-height: 1.5;
  margin-bottom: 20px;
  color: var(--ground);
}
.tech-copy p {
  font-size: 15px;
  color: rgba(250,246,238,.8);
  line-height: 1.85;
  margin-bottom: 16px;
}
.tech-list {
  list-style: none;
  margin-top: 24px;
}
.tech-list li {
  padding: 10px 0;
  padding-left: 24px;
  position: relative;
  font-size: 14px;
  color: rgba(250,246,238,.85);
  border-top: 1px solid rgba(255,255,255,.08);
}
.tech-list li:first-child { border-top: none; }
.tech-list li::before {
  content: "";
  position: absolute; left: 0; top: 20px;
  width: 12px; height: 1px;
  background: var(--gold);
}
@media (max-width: 800px) {
  .tech-grid { grid-template-columns: 1fr; gap: 40px; }
  .tech-stat-num { font-size: 32px; min-width: 100px; }
}

/* ---- Process ---- */
.process {
  background: var(--paper);
}
.steps {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 20px;
  position: relative;
  margin-top: 20px;
}
.steps::before {
  content: "";
  position: absolute;
  top: 32px;
  left: 6%; right: 6%;
  height: 1px;
  background: var(--line);
  z-index: 0;
}
.step {
  position: relative;
  z-index: 1;
  text-align: center;
}
.step-num {
  width: 64px; height: 64px;
  border-radius: 50%;
  background: var(--paper);
  border: 2px solid var(--accent);
  color: var(--accent);
  font-family: "Inter", sans-serif;
  font-weight: 900;
  font-size: 22px;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 18px;
}
.step-label {
  font-family: "Zen Old Mincho", serif;
  font-weight: 700;
  font-size: 14.5px;
  color: var(--ink);
  margin-bottom: 8px;
}
.step-desc {
  font-size: 12px;
  color: var(--ink-soft);
  line-height: 1.6;
}
@media (max-width: 720px) {
  .steps { grid-template-columns: 1fr 1fr; }
  .steps::before { display: none; }
}

/* ---- Voice / testimonial ---- */
.voice {
  background: var(--pale-sage);
}
.voice-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
}
.voice-card {
  background: var(--paper);
  padding: 30px 30px 26px;
  border-radius: 8px;
  border: 1px solid var(--line);
}
.voice-quote {
  font-family: "Zen Old Mincho", serif;
  font-size: 17px;
  line-height: 1.75;
  color: var(--ink);
  margin-bottom: 18px;
  position: relative;
  padding-left: 20px;
}
.voice-quote::before {
  content: "「";
  position: absolute;
  left: -8px;
  top: -4px;
  color: var(--accent);
  font-size: 32px;
  font-weight: 700;
}
.voice-meta {
  display: flex; align-items: center; gap: 12px;
  padding-top: 16px;
  border-top: 1px solid var(--line);
}
.voice-avatar {
  width: 40px; height: 40px;
  border-radius: 50%;
  background: var(--pale-clay);
  color: var(--accent);
  display: flex; align-items: center; justify-content: center;
  font-family: "Zen Old Mincho", serif;
  font-weight: 700;
}
.voice-name { font-size: 13.5px; font-weight: 700; color: var(--ink); }
.voice-detail { font-size: 11.5px; color: var(--ink-soft); }
@media (max-width: 720px) { .voice-grid { grid-template-columns: 1fr; } }

/* ---- Rep ---- */
.rep {
  background: var(--ground);
}
.rep-inner {
  display: grid;
  grid-template-columns: 220px 1fr;
  gap: 40px;
  align-items: center;
  background: var(--paper);
  padding: 40px;
  border-radius: 10px;
  border: 1px solid var(--line);
}
.rep-portrait {
  aspect-ratio: 1;
  background: linear-gradient(135deg, var(--pale-clay), var(--pale-sage));
  border-radius: 6px;
  display: flex; align-items: center; justify-content: center;
  font-family: "Zen Old Mincho", serif;
  font-size: 68px;
  color: var(--accent);
  font-weight: 900;
}
.rep-name {
  font-size: 12px;
  letter-spacing: .3em;
  color: var(--sage);
  font-weight: 700;
  margin-bottom: 10px;
}
.rep h3 {
  font-family: "Zen Old Mincho", serif;
  font-size: 24px;
  font-weight: 700;
  color: var(--ink);
  margin-bottom: 16px;
  line-height: 1.5;
  text-wrap: balance;
}
.rep p {
  font-size: 14.5px;
  color: var(--ink-mid);
  line-height: 1.9;
  margin-bottom: 12px;
}
.rep-sign {
  margin-top: 18px;
  font-family: "Zen Old Mincho", serif;
  color: var(--ink);
  font-weight: 700;
}
@media (max-width: 720px) {
  .rep-inner { grid-template-columns: 1fr; padding: 28px 22px; text-align: center; }
  .rep-portrait { max-width: 160px; margin: 0 auto; }
}

/* ---- FAQ ---- */
.faq { background: var(--paper); }
.faq-list { max-width: 780px; margin: 0 auto; }
.faq-item {
  border-bottom: 1px solid var(--line);
}
.faq-item:first-child { border-top: 1px solid var(--line); }
.faq-q {
  width: 100%;
  padding: 22px 20px 22px 42px;
  font-family: "Zen Old Mincho", serif;
  font-size: 16px;
  font-weight: 700;
  color: var(--ink);
  background: transparent;
  border: none;
  text-align: left;
  cursor: pointer;
  position: relative;
  transition: color .15s;
}
.faq-q::before {
  content: "Q";
  position: absolute;
  left: 12px;
  color: var(--accent);
  font-weight: 900;
  font-family: "Inter", sans-serif;
  font-size: 15px;
}
.faq-q::after {
  content: "＋";
  position: absolute; right: 20px; top: 50%;
  transform: translateY(-50%);
  color: var(--accent);
  font-weight: 700;
  transition: transform .2s;
}
.faq-item.open .faq-q::after { content: "−"; }
.faq-item.open .faq-q { color: var(--accent); }
.faq-a {
  max-height: 0;
  overflow: hidden;
  transition: max-height .3s ease;
}
.faq-a-inner {
  padding: 0 20px 22px 42px;
  font-size: 14px;
  color: var(--ink-mid);
  line-height: 1.85;
  position: relative;
}
.faq-a-inner::before {
  content: "A";
  position: absolute;
  left: 12px; top: 0;
  color: var(--sage);
  font-weight: 900;
  font-family: "Inter", sans-serif;
  font-size: 15px;
}
.faq-item.open .faq-a { max-height: 400px; }

/* ---- Final CTA ---- */
.final {
  background: var(--ink);
  color: var(--ground);
  padding: 80px 0 90px;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.final::before {
  content: "";
  position: absolute;
  top: -100px; left: 50%; transform: translateX(-50%);
  width: 400px; height: 400px;
  background: radial-gradient(circle, rgba(232,169,59,.18), transparent 60%);
}
.final-inner { position: relative; z-index: 1; }
.final-eyebrow {
  color: var(--gold);
  font-size: 12px;
  letter-spacing: .3em;
  font-weight: 700;
  margin-bottom: 20px;
}
.final h2 {
  font-family: "Zen Old Mincho", serif;
  font-size: clamp(26px, 4vw, 38px);
  font-weight: 900;
  line-height: 1.4;
  margin-bottom: 20px;
  color: var(--ground);
  text-wrap: balance;
}
.final p {
  max-width: 580px;
  margin: 0 auto 36px;
  font-size: 15px;
  color: rgba(250,246,238,.75);
  line-height: 1.85;
}
.final-cta-row {
  display: flex;
  gap: 14px;
  justify-content: center;
  flex-wrap: wrap;
  margin-bottom: 40px;
}
.final-note {
  display: flex; gap: 26px; justify-content: center; flex-wrap: wrap;
  font-size: 12px; color: rgba(250,246,238,.55); letter-spacing: .06em;
}

/* ---- Footer ---- */
.footer {
  background: var(--ink);
  color: rgba(250,246,238,.55);
  padding: 32px 0 20px;
  border-top: 1px solid rgba(255,255,255,.08);
  font-size: 12px;
}
.footer-inner {
  display: flex; justify-content: space-between; align-items: center;
  flex-wrap: wrap; gap: 12px;
}
.footer a { color: rgba(250,246,238,.75); text-decoration: none; margin-right: 18px; }
.footer a:hover { color: var(--gold); }

/* ---- Sticky mobile CTA ---- */
.sticky-cta {
  display: none;
  position: fixed; bottom: 0; left: 0; right: 0;
  background: var(--paper);
  border-top: 1px solid var(--line);
  padding: 10px 14px;
  z-index: 40;
  box-shadow: 0 -4px 20px rgba(0,0,0,.08);
}
.sticky-cta-row { display: flex; gap: 8px; }
.sticky-cta a {
  flex: 1;
  text-align: center;
  padding: 12px 8px;
  font-size: 13px;
  font-weight: 700;
  text-decoration: none;
  border-radius: 5px;
}
.sticky-cta .sc-line { background: #06C755; color: #fff; }
.sticky-cta .sc-form { background: var(--accent); color: #fff; }
.sticky-cta .sc-tel { background: var(--ink); color: #fff; }
@media (max-width: 720px) {
  .sticky-cta { display: block; }
}
</style>
</head>
<body <?php body_class("lp-page"); ?>>
<div class="notice">
  📢 <strong>2026年度</strong>助成金・補助金 東京49自治体 / 神奈川33市町村 全対応 ／ お住まいの地域の最新制度をご案内
</div>

<header class="header">
  <div class="container header-inner">
    <div class="brand">
      <span class="brand-mark" aria-hidden="true"></span>
      けいちゃんペイント
      <span class="brand-sub">SINCE&nbsp;2015 / 横浜市</span>
    </div>
    <nav class="header-nav">
      <a href="#reasons">選ばれる理由</a>
      <a href="#grants">助成金早見表</a>
      <a href="#tech">技術</a>
      <a href="#faq">よくある質問</a>
    </nav>
    <a href="#contact" class="header-cta">無料見積もり</a>
  </div>
</header>

<!-- HERO -->
<section class="hero">
  <div class="container hero-inner">
    <div>
      <span class="hero-eyebrow">助成金・技術・地域密着 — 三つの安心。</span>
      <h1 class="hero-title">
        知らないと<br>
        <span class="highlight">損する</span>助成金で、<br>
        外壁塗装を<span class="accent">賢く</span>。
      </h1>
      <p class="hero-lead">
        東京49自治体・神奈川33市町村の助成金制度を把握し、遮熱・断熱塗料の性能証明書まで一貫サポート。代表自ら現場に立つ、地域密着の外壁塗装専門店です。
      </p>
      <div class="hero-cta-row">
        <a href="#contact" class="btn btn-primary">無料で見積もりを取る →</a>
        <a href="#line" class="btn btn-line">LINEで気軽に相談</a>
      </div>
      <div class="hero-trust">
        <span><span class="dot"></span>創業10年 / 施工実績 1,200件超</span>
        <span><span class="dot"></span>神奈川・東京・千葉・埼玉 対応</span>
        <span><span class="dot"></span>代表一貫対応</span>
      </div>
    </div>
    <aside class="hero-visual" aria-label="助成金の例">
      <div class="hv-stamp">最&thinsp;大</div>
      <div class="hv-label">助成金 例(横浜市)</div>
      <div class="hv-title">既存住宅断熱改修補助制度<br>子育て世代の場合</div>
      <div class="hv-amount">150<span class="yen">万円</span></div>
      <div class="hv-note">
        自治体・工事内容により金額は変動します。<br>
        お住まいの地域の最新制度をお調べします。
      </div>
    </aside>
  </div>
</section>

<!-- WHY GRANTS -->
<section class="section why">
  <div class="container">
    <div class="section-heading">
      <span class="eyebrow">Why Now</span>
      <h2>外壁塗装は「今」が一番お得。</h2>
      <p>令和8年度は、東京都・神奈川県の多くの自治体で遮熱塗装・断熱改修への助成が過去最大級。予算枠は先着順のため、早い者勝ちで受付終了する制度が続出しています。</p>
    </div>
    <div class="why-grid">
      <div class="why-card">
        <div class="why-num">01</div>
        <h3>助成金は先着順</h3>
        <p>令和8年度、青梅市・小金井市・鎌倉市・秦野市などは既に予算上限で受付終了。動くなら「今」が最善。</p>
      </div>
      <div class="why-card">
        <div class="why-num">02</div>
        <h3>着工前申請が必須</h3>
        <p>ほぼ全ての制度で「工事契約前」の申請が交付条件。契約してからでは、もう手遅れです。</p>
      </div>
      <div class="why-card">
        <div class="why-num">03</div>
        <h3>塗料メーカー証明も必要</h3>
        <p>遮熱塗装は日射反射率50%以上など性能要件あり。証明書取得・書類準備まで当店が代行します。</p>
      </div>
    </div>
  </div>
</section>

<!-- GRANTS TABLE -->
<section class="section" id="grants">
  <div class="container">
    <div class="section-heading">
      <span class="eyebrow">Grants Overview</span>
      <h2>お住まいの助成金、いくら?</h2>
      <p>令和8年度に外壁塗装で使える主要な助成制度の一例です。お住まいの自治体をクリックすると、全市区町村の詳細一覧マップに移動します。</p>
    </div>
    <div class="grants-panel">
      <div class="grants-tabs" role="tablist">
        <button class="grants-tab active" data-target="tab-tokyo" role="tab">東京都 23区・多摩</button>
        <button class="grants-tab" data-target="tab-kanagawa" role="tab">神奈川県 33市町村</button>
      </div>
      <div class="grants-body">
        <div class="grants-list active" id="tab-tokyo">
          <div class="grants-row">
            <div class="grants-city">港区</div>
            <div class="grants-desc">高反射率塗料等(屋上・屋根塗装)。区民30万・管理組合100万。</div>
            <div class="grants-amount">30<small>万円 / 上限</small></div>
          </div>
          <div class="grants-row">
            <div class="grants-city">品川区</div>
            <div class="grants-desc">住宅改善工事助成事業(遮熱性塗装含む)。工事費税抜10%。</div>
            <div class="grants-amount">20<small>万円 / 上限</small></div>
          </div>
          <div class="grants-row">
            <div class="grants-city">大田区</div>
            <div class="grants-desc">住宅リフォーム助成事業(屋根/外壁塗装7,000円/㎡)。</div>
            <div class="grants-amount">40<small>万円 / 上限</small></div>
          </div>
          <div class="grants-row">
            <div class="grants-city">杉並区</div>
            <div class="grants-desc">エコ住宅促進助成 高日射反射率塗装。屋根外壁合計。</div>
            <div class="grants-amount">15<small>万円 / 上限</small></div>
          </div>
          <div class="grants-row">
            <div class="grants-city">葛飾区</div>
            <div class="grants-desc">かつしかエコ助成金 高反射率塗装。事前協議必須・新築対象外。</div>
            <div class="grants-amount">10<small>万円 / 一律</small></div>
          </div>
          <div class="grants-row">
            <div class="grants-city">八王子市</div>
            <div class="grants-desc">居住環境整備補助金 省エネ改修20%・長寿命化20%。</div>
            <div class="grants-amount">15<small>万円 / 上限</small></div>
          </div>
        </div>
        <div class="grants-list" id="tab-kanagawa">
          <div class="grants-row">
            <div class="grants-city">横浜市</div>
            <div class="grants-desc">既存住宅断熱改修補助 子育て世代向けに手厚い加算あり。</div>
            <div class="grants-amount">150<small>万円 / 子育て加算</small></div>
          </div>
          <div class="grants-row">
            <div class="grants-city">川崎市</div>
            <div class="grants-desc">たいせつ補助金 太陽光7万/kW・蓄電池10万/kWh・ZEH加算。</div>
            <div class="grants-amount">25<small>万円 / ZEH</small></div>
          </div>
          <div class="grants-row">
            <div class="grants-city">小田原市</div>
            <div class="grants-desc">木造住宅耐震改修費補助金 昭和56年5月以前住宅。</div>
            <div class="grants-amount">115<small>万円 / 上限</small></div>
          </div>
          <div class="grants-row">
            <div class="grants-city">平塚市</div>
            <div class="grants-desc">既存住宅断熱リフォーム 国県補助併用・税抜額1/3。</div>
            <div class="grants-amount">8<small>万円 / 上限</small></div>
          </div>
          <div class="grants-row">
            <div class="grants-city">藤沢市</div>
            <div class="grants-desc">住宅用太陽光(自家消費型)7万/kW+蓄電池1/3+HEMS上限20万。</div>
            <div class="grants-amount">20<small>万円 / HEMS</small></div>
          </div>
          <div class="grants-row">
            <div class="grants-city">厚木市</div>
            <div class="grants-desc">住宅省エネ設備 太陽光1万/kW+断熱窓改修1/5上限20万。</div>
            <div class="grants-amount">20<small>万円 / 上限</small></div>
          </div>
        </div>
      </div>
      <div class="grants-footer">
        <span>※ 令和8年度・当店調べ。予算上限で受付終了する場合があります。最新情報は必ずご確認ください。</span>
        <a href="/tokyopaintgrant/">東京都全域マップ →</a>
        <a href="/kanagawapaintgrant/">神奈川県全域マップ →</a>
      </div>
    </div>
  </div>
</section>

<!-- REASONS -->
<section class="section reasons" id="reasons">
  <div class="container">
    <div class="section-heading">
      <span class="eyebrow">Why Us</span>
      <h2>けいちゃんペイントが<br>選ばれる、三つの理由。</h2>
    </div>
    <div class="reasons-grid">
      <div class="reason">
        <div class="reason-icon">₩</div>
        <div class="reason-num">REASON&nbsp;/&nbsp;01</div>
        <h3>助成金の書類も、当店が代行。</h3>
        <p>塗料の性能証明書、見積書、工事計画書、着工前後写真——助成金申請に必要な書類を全て当店で準備。お客様のご負担はサインだけです。</p>
      </div>
      <div class="reason">
        <div class="reason-icon">◇</div>
        <div class="reason-num">REASON&nbsp;/&nbsp;02</div>
        <h3>遮熱・断熱の性能で選ぶ。</h3>
        <p>日射反射率50%以上・第三者機関の証明書付きの遮熱塗料のみ採用。夏場の室温を最大5〜7℃下げ、冷房代を削減します。</p>
      </div>
      <div class="reason">
        <div class="reason-icon">◎</div>
        <div class="reason-num">REASON&nbsp;/&nbsp;03</div>
        <h3>代表が最初から最後まで。</h3>
        <p>相談・見積もり・現場・完了確認まで代表が一貫対応。「誰に頼んでいるかわからない」不安のない、地域密着の安心感。</p>
      </div>
    </div>
  </div>
</section>

<!-- TECH -->
<section class="section tech" id="tech">
  <div class="container">
    <div class="section-heading">
      <span class="eyebrow">Technique</span>
      <h2>遮熱塗料の実力、<br>数字で語る。</h2>
      <p>助成金の対象要件を満たすだけでなく、住まいの快適性と光熱費削減に本当に効く塗料選びを。</p>
    </div>
    <div class="tech-grid">
      <div class="tech-visual">
        <div class="tech-stat">
          <div class="tech-stat-num">−7<small>℃</small></div>
          <div class="tech-stat-desc">夏場の屋根表面温度を最大7℃低減。<br>冷房負荷を大幅にカット。</div>
        </div>
        <div class="tech-stat">
          <div class="tech-stat-num">−22<small>%</small></div>
          <div class="tech-stat-desc">2階居室のエアコン消費電力を<br>年間で約2割削減した実例あり。</div>
        </div>
        <div class="tech-stat">
          <div class="tech-stat-num">15<small>年</small></div>
          <div class="tech-stat-desc">主力の無機ハイブリッド塗料は<br>塗り替え周期15年以上の耐候性。</div>
        </div>
      </div>
      <div class="tech-copy">
        <h3>助成金の要件をクリアするだけでは、勿体ない。</h3>
        <p>遮熱塗料は「日射反射率50%以上」の要件を満たしていても、色や下地処理で実性能が2〜3倍変わります。当店では第三者機関の証明書付き塗料の中から、お住まいの向き・屋根形状・築年数に合わせて最適な組み合わせをご提案。</p>
        <ul class="tech-list">
          <li>屋根用 高反射率塗料(JIS K5675 適合)</li>
          <li>外壁用 遮熱・断熱ハイブリッド(近赤外域反射率60%以上)</li>
          <li>下地補修 徹底調査(高圧洗浄→ケレン→シーラー3層)</li>
          <li>10年保証・アフター点検制度</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- PROCESS -->
<section class="section process">
  <div class="container">
    <div class="section-heading">
      <span class="eyebrow">Process</span>
      <h2>お問い合わせから完工まで、<br>5つのステップ。</h2>
    </div>
    <div class="steps">
      <div class="step">
        <div class="step-num">1</div>
        <div class="step-label">無料相談</div>
        <div class="step-desc">LINE / フォーム / お電話。<br>助成金の可否も即答。</div>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <div class="step-label">現地調査</div>
        <div class="step-desc">代表が訪問。<br>診断は完全無料。</div>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <div class="step-label">助成金申請</div>
        <div class="step-desc">書類は全て当店が代行。<br>着工前に確実に取得。</div>
      </div>
      <div class="step">
        <div class="step-num">4</div>
        <div class="step-label">塗装工事</div>
        <div class="step-desc">見えない下地から丁寧に。<br>写真で全工程記録。</div>
      </div>
      <div class="step">
        <div class="step-num">5</div>
        <div class="step-label">完了+実績報告</div>
        <div class="step-desc">助成金振込までサポート。<br>10年アフター保証付き。</div>
      </div>
    </div>
  </div>
</section>

<!-- VOICE -->
<section class="section voice">
  <div class="container">
    <div class="section-heading">
      <span class="eyebrow">Voice</span>
      <h2>お客様の声。</h2>
    </div>
    <div class="voice-grid">
      <div class="voice-card">
        <p class="voice-quote">助成金のことは全く知らずに問い合わせたのですが、区の制度を調べてくださって、結果的に20万円も安くなりました。代表さんが最後まで見てくれて、本当に安心できました。</p>
        <div class="voice-meta">
          <div class="voice-avatar">S</div>
          <div>
            <div class="voice-name">S様(品川区・戸建て)</div>
            <div class="voice-detail">遮熱塗装 / 令和7年秋 施工</div>
          </div>
        </div>
      </div>
      <div class="voice-card">
        <p class="voice-quote">大手2社と比較しましたが、価格が明確で、遮熱塗料の説明も一番わかりやすかったです。夏の2階が涼しくなって、エアコンの効きが全然違います。</p>
        <div class="voice-meta">
          <div class="voice-avatar">M</div>
          <div>
            <div class="voice-name">M様(横浜市・二世帯住宅)</div>
            <div class="voice-detail">外壁+屋根 遮熱塗装 / 令和7年夏</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- REP -->
<section class="section rep">
  <div class="container">
    <div class="rep-inner">
      <div class="rep-portrait" aria-label="代表の名字">鎌</div>
      <div>
        <div class="rep-name">REPRESENTATIVE</div>
        <h3>「代表自ら現場に立ちます。<br>——だから、責任を持てる。」</h3>
        <p>創業以来、私が最初のご相談から現場、完了確認まで責任を持って対応してきました。営業マンと職人が別の会社では、伝えたことが伝わらない不安がつきものです。</p>
        <p>横浜を拠点に、東京・千葉・埼玉まで。助成金という「見えないお得」を活かしながら、10年20年先まで安心できる住まいづくりを、一緒に考えさせてください。</p>
        <div class="rep-sign">— 代表 鎌田</div>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="section faq" id="faq">
  <div class="container">
    <div class="section-heading">
      <span class="eyebrow">FAQ</span>
      <h2>よくあるご質問。</h2>
    </div>
    <div class="faq-list">
      <div class="faq-item">
        <button class="faq-q">見積もりは本当に無料ですか?</button>
        <div class="faq-a"><div class="faq-a-inner">はい、現地調査・お見積もり・助成金の可否確認まで完全無料です。契約前に費用が発生することは一切ありません。</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q">助成金の申請は本当に代行してもらえますか?</button>
        <div class="faq-a"><div class="faq-a-inner">はい、塗料の性能証明書取得・見積書・工事計画書・写真撮影など、申請に必要な書類は全て当店で用意します。お客様には申請書へのご記入とご捺印のみお願いしています。</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q">対応エリアはどこまでですか?</button>
        <div class="faq-a"><div class="faq-a-inner">神奈川県・東京都(23区+多摩地区)を中心に、千葉県・埼玉県まで対応しています。まずはお住まいの地域をお伝えください。</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q">相場より高くありませんか?</button>
        <div class="faq-a"><div class="faq-a-inner">中間マージンが発生しない代表直営体制のため、大手ハウスメーカーや訪問販売業者より割安な価格でご提供できます。助成金活用でさらにお得になります。</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q">工事後の保証はありますか?</button>
        <div class="faq-a"><div class="faq-a-inner">主力塗料には最長10年の保証をお付けしています。また、定期点検制度で工事後も継続的にお住まいをサポートします。</div></div>
      </div>
    </div>
  </div>
</section>

<!-- FINAL CTA -->
<section class="section final" id="contact">
  <div class="container final-inner">
    <div class="final-eyebrow">CONTACT</div>
    <h2>まずは、あなたの街の<br>助成金を調べてみませんか?</h2>
    <p>お住まいの自治体・築年数・工事希望時期をお伝えいただければ、使える助成金と概算のお見積もりを最短即日でご返信します。しつこい営業は一切ありません。</p>
    <div class="final-cta-row">
      <a href="#form" class="btn btn-primary">無料見積もりフォーム →</a>
      <a href="#line" class="btn btn-line" id="line">LINEで気軽に相談</a>
      <a href="tel:0000000000" class="btn btn-ghost" style="border-color:var(--ground); color:var(--ground);">📞 お電話でのご相談</a>
    </div>
    <div class="final-note">
      <span>受付 9:00 – 19:00 / 年中無休</span>
      <span>お問い合わせから最短即日ご返信</span>
      <span>強引な営業は一切なし</span>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="container footer-inner">
    <div>© けいちゃんペイント / 代表 鎌田</div>
    <div>
      <a href="/">ホーム</a>
      <a href="/tokyopaintgrant/">東京助成金</a>
      <a href="/kanagawapaintgrant/">神奈川助成金</a>
      <a href="/privacy">プライバシーポリシー</a>
    </div>
  </div>
</footer>

<!-- Sticky mobile CTA -->
<div class="sticky-cta">
  <div class="sticky-cta-row">
    <a href="tel:0000000000" class="sc-tel">📞 電話</a>
    <a href="#line" class="sc-line">💬 LINE</a>
    <a href="#form" class="sc-form">見積もり</a>
  </div>
</div>

<script>
// Tabs
document.querySelectorAll('.grants-tab').forEach(btn => {
  btn.addEventListener('click', () => {
    const target = btn.dataset.target;
    document.querySelectorAll('.grants-tab').forEach(b => b.classList.toggle('active', b === btn));
    document.querySelectorAll('.grants-list').forEach(l => l.classList.toggle('active', l.id === target));
  });
});
// FAQ
document.querySelectorAll('.faq-q').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.parentElement.classList.toggle('open');
  });
});
</script>
<?php wp_footer(); ?>
</body>
</html>
