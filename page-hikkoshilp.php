<?php
/**
 * Template Name: LP（引越し／LINE集客）
 * 表示用テンプレート: page-hikkoshilp.php
 *
 * 使い方:
 *   1. 固定ページを新規作成、スラッグを「hikkoshi-lp」等に
 *   2. ページ属性 > テンプレート で「LP（引越し／LINE集客）」を選択
 *
 * ※ Cocoonの装飾を全て外したGoogle広告用LP専用テンプレート(引越し事業向け)
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex,nofollow">
<meta name="googlebot" content="noindex,nofollow">
<meta name="description" content="LINE で写真を送るだけ、60秒で概算見積もり。神奈川・東京の引越しは、けいちゃん引越し便へ。大手より2〜3割安く、追加料金なし、しつこい営業なし。">
<meta property="og:title" content="LINEで写真を送るだけ、60秒で見積もり | けいちゃん引越し便">
<meta property="og:description" content="神奈川・東京の引越し。大手より2〜3割安く、追加料金なし。LINE友だち追加で段ボール10枚無料プレゼント。">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>">
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
body.lp-page { padding-bottom: 76px !important; margin: 0 !important; background: #F4F7F2; }
</style>
<title>けいちゃん引越し便 LP</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Zen+Kaku+Gothic+New:wght@500;700;900&family=Noto+Sans+JP:wght@400;500;700&family=Inter:wght@600;800;900&display=swap">
<style>
:root {
  --ground: #F4F7F2;
  --paper: #FFFFFF;
  --mist: #E7EDE5;
  --ink: #23342E;
  --ink-mid: #4A5A54;
  --ink-soft: #7D8B85;
  --line: #D6DFD2;
  --moss: #5B9B7C;
  --moss-deep: #3F7B5D;
  --line-green: #06C755;
  --line-green-deep: #05a447;
  --warm: #F0A93B;
  --sun: #FDF3D6;
  --coral: #E67C50;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body {
  background: var(--ground);
  color: var(--ink);
  font-family: "Noto Sans JP", "Hiragino Kaku Gothic ProN", sans-serif;
  font-size: 16px;
  line-height: 1.75;
  -webkit-font-smoothing: antialiased;
  padding-bottom: 76px;
}
img { max-width: 100%; display: block; }
.display { font-family: "Zen Kaku Gothic New", "Noto Sans JP", sans-serif; }
.mono { font-family: "Inter", sans-serif; font-feature-settings: "tnum"; }

.container { max-width: 1040px; margin: 0 auto; padding: 0 20px; }
.container-narrow { max-width: 720px; margin: 0 auto; padding: 0 20px; }

/* ---- Top strip ---- */
.strip {
  background: var(--line-green);
  color: #fff;
  padding: 9px 20px;
  text-align: center;
  font-size: 12.5px;
  font-weight: 700;
  letter-spacing: .04em;
}
.strip .em { background: rgba(255,255,255,.2); padding: 2px 8px; border-radius: 3px; margin: 0 4px; }

/* ---- Header ---- */
.header {
  background: var(--paper);
  border-bottom: 1px solid var(--line);
  padding: 12px 0;
  position: sticky; top: 0; z-index: 30;
  backdrop-filter: blur(6px);
}
.header-inner {
  display: flex; align-items: center; justify-content: space-between; gap: 20px;
}
.brand {
  font-family: "Zen Kaku Gothic New", sans-serif;
  font-weight: 900; font-size: 20px;
  color: var(--ink);
  display: flex; align-items: center; gap: 10px;
}
.brand-mark {
  width: 28px; height: 28px;
  background: var(--moss);
  border-radius: 8px;
  color: #fff;
  display: flex; align-items: center; justify-content: center;
  font-family: "Inter"; font-weight: 900; font-size: 15px;
}
.brand-sub { font-family: "Noto Sans JP"; font-size: 11px; color: var(--ink-soft); font-weight: 500; letter-spacing: .1em; }
.header-cta {
  background: var(--line-green);
  color: #fff;
  padding: 10px 18px;
  border-radius: 6px;
  font-size: 13.5px; font-weight: 700;
  text-decoration: none;
  display: inline-flex; align-items: center; gap: 6px;
  transition: background .15s;
}
.header-cta:hover { background: var(--line-green-deep); }

/* ---- Hero ---- */
.hero {
  background:
    radial-gradient(circle at 85% 20%, var(--sun) 0%, transparent 50%),
    linear-gradient(180deg, var(--paper) 0%, var(--ground) 100%);
  padding: 56px 0 60px;
  border-bottom: 1px solid var(--line);
  position: relative;
  overflow: hidden;
}
.hero-inner {
  display: grid;
  grid-template-columns: 1.2fr 1fr;
  gap: 40px;
  align-items: center;
}
.hero-eyebrow {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 12px;
  letter-spacing: .16em;
  font-weight: 700;
  color: var(--moss-deep);
  background: var(--mist);
  padding: 6px 14px;
  border-radius: 999px;
  margin-bottom: 20px;
}
.hero-eyebrow .pulse {
  width: 8px; height: 8px; border-radius: 50%; background: var(--line-green);
  box-shadow: 0 0 0 0 rgba(6,199,85,.6);
  animation: pulse 1.8s infinite;
}
@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(6,199,85,.6); }
  100% { box-shadow: 0 0 0 10px rgba(6,199,85,0); }
}
.hero h1 {
  font-family: "Zen Kaku Gothic New", sans-serif;
  font-weight: 900;
  font-size: clamp(32px, 5vw, 44px);
  line-height: 1.32;
  letter-spacing: .01em;
  text-wrap: balance;
  color: var(--ink);
  margin-bottom: 22px;
}
.hero h1 .hl {
  background: linear-gradient(transparent 62%, rgba(240,169,59,.4) 62%);
  padding: 0 4px;
}
.hero h1 .green { color: var(--line-green-deep); }
.hero-lead {
  font-size: 16px;
  color: var(--ink-mid);
  line-height: 1.9;
  margin-bottom: 30px;
  max-width: 520px;
}
.hero-cta { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
.btn {
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  padding: 16px 24px;
  font-size: 15.5px; font-weight: 700;
  text-decoration: none; border-radius: 8px;
  transition: all .18s;
  border: 2px solid transparent;
  letter-spacing: .04em;
}
.btn-line {
  background: var(--line-green); color: #fff;
  box-shadow: 0 4px 0 var(--line-green-deep);
}
.btn-line:hover { transform: translateY(-2px); box-shadow: 0 6px 0 var(--line-green-deep); }
.btn-line:active { transform: translateY(2px); box-shadow: 0 0 0 var(--line-green-deep); }
.btn-ghost {
  border-color: var(--ink); color: var(--ink);
}
.btn-ghost:hover { background: var(--ink); color: #fff; }
.hero-note {
  font-size: 12px; color: var(--ink-soft);
  display: flex; gap: 20px; flex-wrap: wrap;
  padding-top: 18px; border-top: 1px dashed var(--line);
}
.hero-note span::before { content: "✓ "; color: var(--moss); font-weight: 700; }

/* Hero right — phone mockup */
.phone-mock {
  background: var(--ink);
  border-radius: 32px;
  padding: 18px 12px;
  max-width: 300px;
  margin: 0 auto;
  box-shadow: 0 30px 60px -20px rgba(35,52,46,.4);
  position: relative;
}
.phone-mock::before {
  content: "";
  position: absolute; top: 8px; left: 50%; transform: translateX(-50%);
  width: 60px; height: 6px; background: #000; border-radius: 3px;
}
.phone-screen {
  background: #EDEDED;
  border-radius: 22px;
  padding: 20px 14px 14px;
  min-height: 380px;
}
.chat-header {
  display: flex; align-items: center; gap: 10px;
  padding-bottom: 12px; border-bottom: 1px solid #DADADA;
  margin-bottom: 14px;
}
.chat-avatar {
  width: 34px; height: 34px; border-radius: 50%;
  background: var(--moss); color: #fff;
  font-family: "Inter"; font-weight: 900; font-size: 13px;
  display: flex; align-items: center; justify-content: center;
}
.chat-name { font-size: 13px; font-weight: 700; color: #333; }
.chat-status { font-size: 10.5px; color: #888; }
.chat-status .online { color: var(--line-green); font-weight: 700; }
.chat-bubble {
  padding: 9px 12px;
  border-radius: 14px;
  font-size: 12.5px; line-height: 1.55;
  max-width: 78%;
  margin-bottom: 8px;
  color: #222;
}
.chat-them { background: #fff; border-top-left-radius: 4px; }
.chat-me {
  background: #8BE87A;
  margin-left: auto;
  border-top-right-radius: 4px;
  color: #1A1A1A;
}
.chat-me.quote {
  background: #fff;
  border: 1.5px solid var(--warm);
  color: #333;
  font-weight: 700;
  font-size: 13.5px;
}
.chat-me.quote .price {
  color: var(--coral); font-size: 20px; font-family: "Inter"; font-weight: 900;
  display: block; margin-top: 4px;
}
.chat-time { font-size: 10px; color: #999; text-align: center; margin: 8px 0; }

@media (max-width: 800px) {
  .hero-inner { grid-template-columns: 1fr; gap: 40px; }
  .phone-mock { max-width: 260px; }
}

/* ---- Section base ---- */
.section { padding: 72px 0; }
.section-heading {
  text-align: center;
  margin-bottom: 44px;
}
.section-heading .eyebrow {
  display: inline-block;
  font-size: 11px; letter-spacing: .28em;
  font-weight: 700; color: var(--moss);
  margin-bottom: 14px;
}
.section-heading h2 {
  font-family: "Zen Kaku Gothic New", sans-serif;
  font-weight: 900;
  font-size: clamp(24px, 3.4vw, 32px);
  line-height: 1.4;
  text-wrap: balance;
  color: var(--ink);
}
.section-heading p {
  max-width: 580px; margin: 14px auto 0;
  font-size: 14.5px; color: var(--ink-mid); line-height: 1.85;
}

/* ---- 60秒フロー ---- */
.flow { background: var(--paper); border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); }
.flow-steps {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
  margin-top: 40px;
  position: relative;
}
.flow-step {
  background: var(--mist);
  padding: 26px 22px;
  border-radius: 12px;
  text-align: center;
  position: relative;
}
.flow-step::after {
  content: "→";
  position: absolute;
  right: -22px; top: 50%; transform: translateY(-50%);
  color: var(--moss);
  font-size: 22px; font-weight: 700;
}
.flow-step:last-child::after { display: none; }
.flow-time {
  display: inline-block;
  font-family: "Inter"; font-weight: 800;
  font-size: 11.5px; letter-spacing: .1em;
  color: #fff;
  background: var(--moss);
  padding: 3px 10px;
  border-radius: 3px;
  margin-bottom: 14px;
}
.flow-step h3 {
  font-family: "Zen Kaku Gothic New", sans-serif;
  font-size: 17px; font-weight: 700;
  color: var(--ink);
  margin-bottom: 10px;
}
.flow-step p { font-size: 13px; color: var(--ink-mid); line-height: 1.7; }
@media (max-width: 720px) {
  .flow-steps { grid-template-columns: 1fr; gap: 16px; }
  .flow-step::after { content: "↓"; right: 50%; top: auto; bottom: -18px; transform: translateX(50%); }
}

/* ---- Plans / pricing ---- */
.plans { background: var(--ground); }
.plans-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}
.plan {
  background: var(--paper);
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 30px 26px;
  display: flex; flex-direction: column;
  position: relative;
}
.plan.featured {
  border-color: var(--moss);
  border-width: 2px;
  box-shadow: 0 10px 28px -12px rgba(91,155,124,.35);
}
.plan.featured::before {
  content: "人気NO.1";
  position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
  background: var(--warm);
  color: #fff;
  padding: 5px 14px;
  font-size: 11px; font-weight: 700; letter-spacing: .15em;
  border-radius: 999px;
}
.plan-title {
  font-family: "Zen Kaku Gothic New", sans-serif;
  font-size: 20px; font-weight: 900;
  color: var(--ink);
  margin-bottom: 6px;
}
.plan-sub {
  font-size: 12px; color: var(--ink-soft);
  margin-bottom: 24px;
}
.plan-price {
  font-family: "Inter";
  font-size: 42px; font-weight: 900;
  color: var(--coral);
  line-height: 1;
  letter-spacing: -.02em;
  margin-bottom: 4px;
}
.plan-price .from { font-size: 12px; color: var(--ink-soft); font-weight: 700; margin-right: 4px; }
.plan-price .yen { font-size: 16px; color: var(--ink-mid); margin-left: 4px; }
.plan-note { font-size: 11px; color: var(--ink-soft); margin-bottom: 22px; }
.plan-list {
  list-style: none;
  padding-top: 20px;
  border-top: 1px solid var(--line);
  margin-bottom: 22px;
  flex: 1;
}
.plan-list li {
  padding: 8px 0 8px 22px;
  font-size: 13px;
  position: relative;
  color: var(--ink-mid);
}
.plan-list li::before {
  content: "✓";
  position: absolute; left: 0;
  color: var(--moss); font-weight: 900;
}
.plan-cta {
  display: block;
  padding: 13px;
  background: var(--line-green);
  color: #fff;
  text-align: center;
  text-decoration: none;
  border-radius: 6px;
  font-weight: 700;
  font-size: 14px;
  transition: background .15s;
}
.plan-cta:hover { background: var(--line-green-deep); }
@media (max-width: 800px) {
  .plans-grid { grid-template-columns: 1fr; }
}

/* ---- Reasons ---- */
.reasons { background: var(--paper); border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); }
.reasons-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
  margin-top: 20px;
}
.reason {
  padding: 4px;
}
.reason-icon {
  width: 56px; height: 56px;
  background: var(--sun);
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 18px;
  font-size: 26px;
}
.reason h3 {
  font-family: "Zen Kaku Gothic New", sans-serif;
  font-size: 16px; font-weight: 700;
  line-height: 1.5;
  color: var(--ink);
  margin-bottom: 10px;
}
.reason p {
  font-size: 13px; color: var(--ink-mid); line-height: 1.75;
}
@media (max-width: 800px) {
  .reasons-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) {
  .reasons-grid { grid-template-columns: 1fr; }
}

/* ---- Trust bar ---- */
.trust {
  background: var(--ink);
  color: var(--ground);
  padding: 44px 0;
}
.trust-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  text-align: center;
}
.trust-num {
  font-family: "Inter"; font-weight: 900;
  font-size: 42px;
  color: var(--warm);
  line-height: 1;
  letter-spacing: -.02em;
  margin-bottom: 6px;
}
.trust-num small { font-size: 14px; color: var(--ground); margin-left: 3px; }
.trust-label { font-size: 12.5px; color: rgba(244,247,242,.75); letter-spacing: .05em; }
@media (max-width: 720px) {
  .trust-grid { grid-template-columns: repeat(2, 1fr); gap: 30px 20px; }
}

/* ---- LINE section (main CTA) ---- */
.line-section {
  background:
    linear-gradient(135deg, var(--line-green) 0%, #04A249 100%);
  color: #fff;
  padding: 70px 0 76px;
  text-align: center;
  position: relative;
  overflow: hidden;
}
.line-section::before {
  content: "";
  position: absolute; inset: 0;
  background:
    radial-gradient(circle at 15% 20%, rgba(255,255,255,.15), transparent 30%),
    radial-gradient(circle at 85% 80%, rgba(255,255,255,.1), transparent 30%);
}
.line-section-inner { position: relative; z-index: 1; }
.line-badge {
  display: inline-block;
  background: rgba(255,255,255,.2);
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 12px; font-weight: 700; letter-spacing: .18em;
  margin-bottom: 20px;
}
.line-section h2 {
  font-family: "Zen Kaku Gothic New", sans-serif;
  font-weight: 900;
  font-size: clamp(28px, 4vw, 36px);
  line-height: 1.4;
  text-wrap: balance;
  margin-bottom: 20px;
}
.line-section h2 .em {
  background: var(--warm);
  color: var(--ink);
  padding: 2px 12px;
  border-radius: 4px;
  display: inline-block;
}
.line-section p {
  max-width: 520px;
  margin: 0 auto 32px;
  font-size: 15px;
  color: rgba(255,255,255,.9);
  line-height: 1.85;
}
.line-qr-block {
  display: inline-flex; align-items: center; gap: 24px;
  background: #fff;
  padding: 20px 26px;
  border-radius: 14px;
  color: var(--ink);
  margin-bottom: 24px;
}
.line-qr {
  width: 100px; height: 100px;
  background:
    linear-gradient(90deg, transparent 46%, var(--ink) 46%, var(--ink) 54%, transparent 54%),
    linear-gradient(0deg, transparent 46%, var(--ink) 46%, var(--ink) 54%, transparent 54%);
  background-size: 20px 20px, 20px 20px;
  background-color: #EEE;
  border: 4px solid var(--ink);
  border-radius: 8px;
  position: relative;
  flex-shrink: 0;
}
.line-qr::before {
  content: "QR";
  position: absolute; inset: 0;
  display: flex; align-items: center; justify-content: center;
  font-family: "Inter"; font-weight: 900; font-size: 22px;
  color: var(--ink);
  background: #fff;
  margin: 12px;
  border-radius: 4px;
}
.line-qr-copy { text-align: left; }
.line-qr-copy .b { font-family: "Zen Kaku Gothic New"; font-weight: 900; font-size: 16px; }
.line-qr-copy .s { font-size: 12px; color: var(--ink-soft); }
.line-btn {
  display: inline-flex; align-items: center; gap: 10px;
  background: #fff;
  color: var(--line-green-deep);
  padding: 16px 32px;
  border-radius: 999px;
  font-weight: 900;
  font-size: 16px;
  text-decoration: none;
  box-shadow: 0 6px 0 rgba(0,0,0,.12);
  transition: transform .15s;
}
.line-btn:hover { transform: translateY(-2px); }
@media (max-width: 720px) {
  .line-qr-block { flex-direction: column; gap: 12px; padding: 18px; }
}

/* ---- Voice ---- */
.voice { background: var(--mist); }
.voice-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.voice-card {
  background: var(--paper);
  padding: 26px 24px;
  border-radius: 12px;
  border: 1px solid var(--line);
}
.voice-rate {
  color: var(--warm);
  font-size: 15px;
  letter-spacing: 2px;
  margin-bottom: 10px;
}
.voice-title {
  font-family: "Zen Kaku Gothic New", sans-serif;
  font-size: 15px; font-weight: 700;
  color: var(--ink);
  margin-bottom: 10px;
  line-height: 1.5;
}
.voice-body {
  font-size: 13px; color: var(--ink-mid); line-height: 1.75;
  margin-bottom: 16px;
}
.voice-meta {
  font-size: 11.5px; color: var(--ink-soft);
  padding-top: 12px; border-top: 1px solid var(--line);
}
@media (max-width: 800px) { .voice-grid { grid-template-columns: 1fr; } }

/* ---- FAQ ---- */
.faq { background: var(--paper); }
.faq-list { max-width: 720px; margin: 0 auto; }
.faq-item { border-bottom: 1px solid var(--line); }
.faq-item:first-child { border-top: 1px solid var(--line); }
.faq-q {
  width: 100%;
  padding: 22px 44px 22px 40px;
  font-family: "Zen Kaku Gothic New", sans-serif;
  font-size: 15.5px; font-weight: 700;
  color: var(--ink);
  background: transparent; border: none;
  text-align: left; cursor: pointer;
  position: relative;
}
.faq-q::before {
  content: "Q"; position: absolute; left: 12px;
  color: var(--line-green); font-weight: 900;
  font-family: "Inter"; font-size: 14px;
  opacity: 0;              /* Qラベルは非表示にして+と場所を交代 */
}
.faq-q::after {
  content: "＋";
  position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
  color: var(--moss); font-weight: 700;
}
.faq-item.open .faq-q::after { content: "−"; }
.faq-item.open .faq-q { color: var(--moss-deep); }
.faq-a { max-height: 0; overflow: hidden; transition: max-height .3s ease; }
.faq-a-inner {
  padding: 0 20px 22px 40px;
  font-size: 13.5px; color: var(--ink-mid);
  line-height: 1.85;
  position: relative;
}
.faq-a-inner::before {
  content: "A"; position: absolute; left: 12px; top: 0;
  color: var(--moss); font-weight: 900;
  font-family: "Inter"; font-size: 14px;
}
.faq-item.open .faq-a { max-height: 400px; }

/* ---- Final CTA ---- */
.final {
  background: var(--ink);
  color: var(--ground);
  padding: 60px 0 70px;
  text-align: center;
}
.final h2 {
  font-family: "Zen Kaku Gothic New", sans-serif;
  font-size: clamp(24px, 3.5vw, 32px);
  font-weight: 900;
  line-height: 1.4;
  margin-bottom: 18px;
  color: var(--ground);
}
.final p {
  max-width: 500px; margin: 0 auto 32px;
  font-size: 14.5px; color: rgba(244,247,242,.75);
  line-height: 1.85;
}
.final-cta { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.btn-final-line {
  background: var(--line-green); color: #fff;
  padding: 16px 32px;
  border-radius: 8px; font-weight: 900; font-size: 15px;
  text-decoration: none;
  box-shadow: 0 4px 0 var(--line-green-deep);
}
.btn-final-line:hover { transform: translateY(-2px); box-shadow: 0 6px 0 var(--line-green-deep); }
.btn-final-tel {
  background: transparent;
  color: var(--ground);
  border: 2px solid var(--ground);
  padding: 14px 32px;
  border-radius: 8px; font-weight: 700; font-size: 15px;
  text-decoration: none;
}
.btn-final-tel:hover { background: var(--ground); color: var(--ink); }

/* ---- Footer ---- */
.footer {
  background: var(--ink);
  color: rgba(244,247,242,.55);
  padding: 24px 0;
  border-top: 1px solid rgba(255,255,255,.08);
  font-size: 11.5px;
}
.footer-inner {
  display: flex; justify-content: space-between; align-items: center;
  flex-wrap: wrap; gap: 10px;
}
.footer a { color: rgba(244,247,242,.75); text-decoration: none; margin-right: 16px; }

/* ---- Sticky mobile CTA ---- */
.sticky-cta {
  display: none;
  position: fixed; bottom: 0; left: 0; right: 0;
  background: var(--paper);
  border-top: 1px solid var(--line);
  padding: 10px 12px;
  z-index: 40;
  box-shadow: 0 -4px 20px rgba(0,0,0,.08);
}
.sticky-cta-row { display: flex; gap: 8px; }
.sticky-cta a {
  flex: 1;
  text-align: center;
  padding: 13px 8px;
  font-size: 13px; font-weight: 700;
  text-decoration: none; border-radius: 6px;
}
.sticky-cta .sc-tel { background: var(--ink); color: #fff; flex: 0.6; }
.sticky-cta .sc-line { background: var(--line-green); color: #fff; box-shadow: 0 3px 0 var(--line-green-deep); }
@media (max-width: 720px) { .sticky-cta { display: block; } }
</style>
</head>
<body <?php body_class("lp-page"); ?>>
<div class="strip">
  🚚 LINE友だち追加で <span class="em">段ボール10枚無料</span> ／ 60秒でカンタン見積もり
</div>

<header class="header">
  <div class="container header-inner">
    <div class="brand">
      <span class="brand-mark">引</span>
      <span>けいちゃん引越し便<br><span class="brand-sub">by KEICHAN PAINT / 神奈川・東京</span></span>
    </div>
    <a href="#line-cta" class="header-cta">💬 LINEで見積もり</a>
  </div>
</header>

<!-- HERO -->
<section class="hero">
  <div class="container hero-inner">
    <div>
      <div class="hero-eyebrow">
        <span class="pulse"></span>
        LINE 60秒 概算見積もり
      </div>
      <h1>
        LINEで<span class="green">写真を送るだけ</span>。<br>
        引越し料金、<span class="hl">60秒</span>で分かる。
      </h1>
      <p class="hero-lead">
        塗装業で培った「現場対応の丁寧さ」を、そのまま引越しに。
        大手より<strong>2〜3割安く</strong>、しつこい営業は一切なし。
        まずはLINEでお部屋の写真を送るだけ、簡単見積もりから。
      </p>
      <div class="hero-cta">
        <a href="#line-cta" class="btn btn-line">💬 LINEで見積もり(無料)</a>
        <a href="#plans" class="btn btn-ghost">料金プランを見る</a>
      </div>
      <div class="hero-note">
        <span>追加料金なし</span>
        <span>キャンセル料無料</span>
        <span>神奈川・東京 即日対応</span>
      </div>
    </div>
    <div class="phone-mock" aria-label="LINEチャットのイメージ">
      <div class="phone-screen">
        <div class="chat-header">
          <div class="chat-avatar">引</div>
          <div>
            <div class="chat-name">けいちゃん引越し便</div>
            <div class="chat-status"><span class="online">●</span> オンライン</div>
          </div>
        </div>
        <div class="chat-time">今日 14:32</div>
        <div class="chat-bubble chat-them">こんにちは!<br>お部屋のお写真、荷物量を教えてください 📸</div>
        <div class="chat-bubble chat-me">単身・1K<br>横浜→川崎 / 2月20日希望です</div>
        <div class="chat-bubble chat-them">ありがとうございます!<br>1分で概算お出しします 🚚</div>
        <div class="chat-bubble chat-me quote">
          概算お見積もり<br>
          <span class="price">¥28,000〜</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TRUST BAR -->
<section class="trust">
  <div class="container trust-grid">
    <div>
      <div class="trust-num">60<small>秒</small></div>
      <div class="trust-label">概算見積もり</div>
    </div>
    <div>
      <div class="trust-num">2,300<small>件+</small></div>
      <div class="trust-label">累計対応件数</div>
    </div>
    <div>
      <div class="trust-num">98<small>%</small></div>
      <div class="trust-label">お客様満足度</div>
    </div>
    <div>
      <div class="trust-num">0<small>円</small></div>
      <div class="trust-label">追加料金・営業なし</div>
    </div>
  </div>
</section>

<!-- 60秒フロー -->
<section class="section flow">
  <div class="container">
    <div class="section-heading">
      <span class="eyebrow">60 SECONDS</span>
      <h2>LINEで、たった3ステップ。</h2>
      <p>電話が苦手な方も安心。すべてLINEのチャットで完結します。</p>
    </div>
    <div class="flow-steps">
      <div class="flow-step">
        <span class="flow-time">STEP 01 / 10秒</span>
        <h3>友だち追加</h3>
        <p>QRコード読み取りまたはボタンから追加するだけ。</p>
      </div>
      <div class="flow-step">
        <span class="flow-time">STEP 02 / 30秒</span>
        <h3>お部屋の写真を送信</h3>
        <p>お部屋の全景を撮影して送るだけ。日時と住所も教えてください。</p>
      </div>
      <div class="flow-step">
        <span class="flow-time">STEP 03 / 20秒</span>
        <h3>概算料金が届く</h3>
        <p>スタッフがすぐに概算をご返信。気に入ればそのまま予約OK。</p>
      </div>
    </div>
  </div>
</section>

<!-- 料金プラン -->
<section class="section plans" id="plans">
  <div class="container">
    <div class="section-heading">
      <span class="eyebrow">Simple Pricing</span>
      <h2>明朗会計。追加料金なし。</h2>
      <p>大手業者との料金比較の一例。同じ荷物量・距離で、平均2〜3割お得です。</p>
    </div>
    <div class="plans-grid">
      <div class="plan">
        <div class="plan-title">単身プラン</div>
        <div class="plan-sub">1R / 1K / 荷物少なめ</div>
        <div class="plan-price"><span class="from">¥</span>25,000<span class="yen">〜</span></div>
        <div class="plan-note">近距離(市内〜隣接市)の場合</div>
        <ul class="plan-list">
          <li>作業員1〜2名</li>
          <li>2t車 or 軽トラ</li>
          <li>基本梱包資材付き</li>
          <li>養生・搬入まで一貫</li>
        </ul>
        <a href="#line-cta" class="plan-cta">LINEで詳細を聞く</a>
      </div>
      <div class="plan featured">
        <div class="plan-title">ご家族プラン</div>
        <div class="plan-sub">2LDK 〜 3LDK / ファミリー</div>
        <div class="plan-price"><span class="from">¥</span>55,000<span class="yen">〜</span></div>
        <div class="plan-note">近距離+養生+家具運搬込み</div>
        <ul class="plan-list">
          <li>作業員2〜3名</li>
          <li>4t車 対応可</li>
          <li>ダンボール20枚無料</li>
          <li>大型家具の分解・組立</li>
          <li>不用品引き取り(有料)</li>
        </ul>
        <a href="#line-cta" class="plan-cta">LINEで詳細を聞く</a>
      </div>
      <div class="plan">
        <div class="plan-title">オフィスライト</div>
        <div class="plan-sub">小規模事務所 / SOHO</div>
        <div class="plan-price"><span class="from">¥</span>45,000<span class="yen">〜</span></div>
        <div class="plan-note">土日祝の作業も対応</div>
        <ul class="plan-list">
          <li>デスク10台まで</li>
          <li>OA機器の丁寧梱包</li>
          <li>養生・原状回復サポート</li>
          <li>塗装業と兼業で内装補修可</li>
        </ul>
        <a href="#line-cta" class="plan-cta">LINEで詳細を聞く</a>
      </div>
    </div>
  </div>
</section>

<!-- 選ばれる理由 -->
<section class="section reasons">
  <div class="container">
    <div class="section-heading">
      <span class="eyebrow">Why Us</span>
      <h2>大手にはない、地域密着の強み。</h2>
    </div>
    <div class="reasons-grid">
      <div class="reason">
        <div class="reason-icon">💬</div>
        <h3>LINEで完結</h3>
        <p>見積もりから予約まで、電話一切不要。写真を送るだけで簡単。</p>
      </div>
      <div class="reason">
        <div class="reason-icon">💴</div>
        <h3>大手より2〜3割安い</h3>
        <p>中間マージンなしの代表直営。追加請求も一切なしの明朗会計。</p>
      </div>
      <div class="reason">
        <div class="reason-icon">🎨</div>
        <h3>塗装業との相乗効果</h3>
        <p>壁や床の傷防止に慣れた養生技術。原状回復の内装補修まで対応可。</p>
      </div>
      <div class="reason">
        <div class="reason-icon">⚡</div>
        <h3>神奈川・東京 即日OK</h3>
        <p>横浜を拠点に近距離即日対応。急な引越しも柔軟にご相談ください。</p>
      </div>
    </div>
  </div>
</section>

<!-- LINE 集客本命 -->
<section class="line-section" id="line-cta">
  <div class="container line-section-inner">
    <div class="line-badge">LINE FRIEND</div>
    <h2>
      いま友だち追加で<br>
      <span class="em">段ボール10枚 無料</span>プレゼント。
    </h2>
    <p>LINEで簡単に見積もり・予約が可能。友だち追加特典として、引越しに必須の段ボール10枚を無料でお送りします(ご成約後にお届け)。</p>

    <div class="line-qr-block">
      <div class="line-qr" aria-label="LINE友だち追加 QRコード"></div>
      <div class="line-qr-copy">
        <div class="b">スマホでQRを読み取り</div>
        <div class="s">またはボタンから直接追加</div>
      </div>
    </div>

    <div>
      <a href="#" class="line-btn">
        💬 LINE友だち追加で見積もり
      </a>
    </div>
  </div>
</section>

<!-- お客様の声 -->
<section class="section voice">
  <div class="container">
    <div class="section-heading">
      <span class="eyebrow">Voice</span>
      <h2>お客様の声。</h2>
    </div>
    <div class="voice-grid">
      <div class="voice-card">
        <div class="voice-rate">★★★★★</div>
        <div class="voice-title">大手の半額でびっくり。</div>
        <div class="voice-body">大手2社の見積もりが8万円台だったのに、こちらは4.5万円。当日の作業も丁寧で全く不満なし。もっと早く知っておけば良かった。</div>
        <div class="voice-meta">A様 / 2LDK / 横浜市 → 川崎市</div>
      </div>
      <div class="voice-card">
        <div class="voice-rate">★★★★★</div>
        <div class="voice-title">LINEで完結、電話ゼロで楽。</div>
        <div class="voice-body">仕事中に電話に出られないので、LINEで全部進められて本当に助かりました。写真送っただけで正確な見積もりが来て安心。</div>
        <div class="voice-meta">K様 / 1K / 都内 → 横浜</div>
      </div>
      <div class="voice-card">
        <div class="voice-rate">★★★★★</div>
        <div class="voice-title">壁の傷、その場で直してもらえた。</div>
        <div class="voice-body">搬出中に旧居の壁を傷つけてしまったのですが、塗装業もされているとのことで、その場でパテ補修と塗装まで対応。追加料金もなく、本当に感謝しています。</div>
        <div class="voice-meta">M様 / ご家族4人 / 藤沢市内</div>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="section faq">
  <div class="container">
    <div class="section-heading">
      <span class="eyebrow">FAQ</span>
      <h2>よくあるご質問。</h2>
    </div>
    <div class="faq-list">
      <div class="faq-item">
        <button class="faq-q">見積もりだけでも大丈夫ですか?</button>
        <div class="faq-a"><div class="faq-a-inner">はい、もちろん大丈夫です。LINEで概算を確認して、しっくりこなければお断りいただいて全く問題ありません。しつこい営業や勧誘は一切いたしません。</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q">対応エリアはどこまでですか?</button>
        <div class="faq-a"><div class="faq-a-inner">神奈川県全域(横浜市・川崎市を中心に県内全域)と、東京都(23区・多摩地区)まで対応しています。県外への引越しもご相談可能です。まずはお伝えください。</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q">当日追加料金は本当にありませんか?</button>
        <div class="faq-a"><div class="faq-a-inner">はい、事前にお伝えいただいた荷物・階数・距離であれば追加料金は発生しません。仮に当日荷物が大幅に増えていた場合のみ、その場でご説明のうえ調整させていただきます(強引な追加請求はしません)。</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q">急な引越しでも対応可能ですか?</button>
        <div class="faq-a"><div class="faq-a-inner">神奈川・東京の近距離であれば、最短翌日〜3日以内に対応可能です。ご予約状況により変動しますので、まずはLINEで日程をお伝えください。</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q">不用品の引き取りもお願いできますか?</button>
        <div class="faq-a"><div class="faq-a-inner">はい、家電・家具の有料引き取りに対応しています。粗大ゴミの分別が難しい方も、まとめて引き取り可能です(有料 / 品目により料金変動)。</div></div>
      </div>
      <div class="faq-item">
        <button class="faq-q">支払い方法は?</button>
        <div class="faq-a"><div class="faq-a-inner">現金・銀行振込・PayPay等キャッシュレス決済に対応しています。原則、作業完了後のお支払いです。</div></div>
      </div>
    </div>
  </div>
</section>

<!-- 最終 -->
<section class="final">
  <div class="container">
    <h2>まずはLINE友だち追加から、<br>お気軽にどうぞ。</h2>
    <p>営業ゼロ・見積もり無料・キャンセル料なし。<br>気になったら、写真を送るだけで簡単スタートです。</p>
    <div class="final-cta">
      <a href="#" class="btn-final-line">💬 LINEで見積もりを取る</a>
      <a href="tel:0000000000" class="btn-final-tel">📞 電話でも受付中</a>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="container footer-inner">
    <div>© けいちゃん引越し便 (KEICHAN PAINT 副業事業)</div>
    <div>
      <a href="/">ホーム</a>
      <a href="/privacy">プライバシーポリシー</a>
      <a href="/tokushou">特定商取引法</a>
    </div>
  </div>
</footer>

<div class="sticky-cta">
  <div class="sticky-cta-row">
    <a href="tel:0000000000" class="sc-tel">📞</a>
    <a href="#line-cta" class="sc-line">💬 LINEで60秒見積もり →</a>
  </div>
</div>

<script>
document.querySelectorAll('.faq-q').forEach(btn => {
  btn.addEventListener('click', () => btn.parentElement.classList.toggle('open'));
});
</script>
<?php wp_footer(); ?>
</body>
</html>
