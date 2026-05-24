<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Monoton&display=swap" rel="stylesheet">
    <title>VinyLog</title>
<style>
/* ============================================================
   RESET
   ============================================================ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { height: 100%; }

/* ============================================================
   DESIGN TOKENS
   ============================================================ */
:root {
  /* Surfaces — warm espresso dark */
  --bg-base:       oklch(16% 0.018 52);
  --bg-card:       oklch(21% 0.014 52);
  --bg-elevated:   oklch(26% 0.013 52);
  --bg-input:      oklch(19% 0.013 52);
  --bg-input-f:    oklch(21% 0.015 52);

  /* Borders */
  --border:        oklch(30% 0.016 52);
  --border-med:    oklch(40% 0.016 52);
  --border-focus:  oklch(62% 0.21 148);

  /* Text */
  --t1: oklch(94% 0.012 82);
  --t2: oklch(70% 0.018 82);
  --t3: oklch(50% 0.016 80);

  /* Accent — vivid forest green */
  --accent:    oklch(62% 0.21 148);
  --accent-h:  oklch(68% 0.22 148);
  --accent-bg: oklch(42% 0.13 148 / 18%);
  --accent-txt:oklch(82% 0.16 148);

  /* CTA — warm amber for primary actions */
  --cta:       oklch(73% 0.17 80);
  --cta-h:     oklch(79% 0.18 80);
  --cta-dark:  oklch(17% 0.018 52);

  /* Danger */
  --danger:    oklch(62% 0.22 22);
  --danger-h:  oklch(67% 0.24 22);
  --danger-bg: oklch(62% 0.22 22 / 14%);

  /* Semantic aliases */
  --red: var(--danger);

  /* Shadows */
  --shadow-sm: 0 1px 3px oklch(7% 0.01 52 / 80%), 0 0 0 1px var(--border);
  --shadow-md: 0 4px 20px oklch(7% 0.01 52 / 75%), 0 0 0 1px var(--border-med);
  --shadow-lg: 0 10px 36px oklch(7% 0.01 52 / 80%), 0 0 0 1px var(--border-med);

  /* Easing */
  --ease-out: cubic-bezier(0.16, 1, 0.3, 1);
}

/* ============================================================
   BASE
   ============================================================ */
body {
  background: var(--bg-base);
  color: var(--t1);
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif;
  font-size: 0.9375rem;
  line-height: 1.6;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
a { color: inherit; text-decoration: none; }
img { display: block; max-width: 100%; }

/* ============================================================
   NAVIGATION
   ============================================================ */
.site-header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: oklch(16% 0.018 52 / 88%);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border-bottom: 1px solid var(--border);
}

.nav-inner {
  max-width: 1440px;
  margin: 0 auto;
  padding: 0 2rem;
  height: 62px;
  display: flex;
  align-items: center;
  gap: 2rem;
}

.nav-logo {
  font-family: 'Monoton', cursive;
  font-size: 1.75rem;
  color: var(--t1);
  letter-spacing: 0.02em;
  flex-shrink: 0;
  transition: color 200ms var(--ease-out);
}
.nav-logo:hover { color: var(--accent); }

.nav-links {
  display: flex;
  align-items: center;
  gap: 0.125rem;
  list-style: none;
}

.nav-link {
  display: inline-flex;
  align-items: center;
  padding: 0.375rem 0.75rem;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--t2);
  transition: color 140ms ease, background 140ms ease;
}
.nav-link:hover { color: var(--t1); background: oklch(100% 0 0 / 5%); }

.nav-right {
  margin-left: auto;
  display: flex;
  align-items: center;
  gap: 0.625rem;
}

.nav-user {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--t2);
}

.badge-admin {
  font-size: 0.6rem;
  font-weight: 700;
  letter-spacing: 0.09em;
  text-transform: uppercase;
  background: var(--accent);
  color: oklch(97% 0.01 148);
  padding: 0.18em 0.55em;
  border-radius: 4px;
  vertical-align: middle;
}

/* ============================================================
   BUTTONS
   ============================================================ */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.375em;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 500;
  font-family: inherit;
  cursor: pointer;
  border: 1px solid transparent;
  line-height: 1;
  white-space: nowrap;
  transition: background 140ms ease, color 140ms ease,
              border-color 140ms ease, transform 80ms ease,
              box-shadow 140ms ease;
}
.btn:active { transform: scale(0.97); }

.btn-primary {
  background: var(--accent);
  color: oklch(97% 0.01 148);
  border-color: var(--accent);
}
.btn-primary:hover { background: var(--accent-h); border-color: var(--accent-h); }

.btn-cta {
  background: var(--cta);
  color: var(--cta-dark);
  border-color: var(--cta);
  font-weight: 600;
}
.btn-cta:hover { background: var(--cta-h); border-color: var(--cta-h); }

/* Alias used in auth views */
.btn-gold { background: var(--cta); color: var(--cta-dark); border-color: var(--cta); font-weight: 600; }
.btn-gold:hover { background: var(--cta-h); border-color: var(--cta-h); }

.btn-ghost {
  background: transparent;
  color: var(--t2);
  border-color: var(--border-med);
}
.btn-ghost:hover { color: var(--t1); border-color: var(--accent); background: var(--accent-bg); }

.btn-danger-outline {
  background: transparent;
  color: var(--danger);
  border-color: oklch(62% 0.22 22 / 35%);
}
.btn-danger-outline:hover { background: var(--danger); color: var(--t1); border-color: var(--danger); }

.btn-admin-edit {
  background: oklch(38% 0.09 148 / 30%);
  color: var(--accent-txt);
  border-color: oklch(62% 0.21 148 / 30%);
  font-size: 0.75rem;
  padding: 0.3rem 0.65rem;
  border-radius: 6px;
}
.btn-admin-edit:hover { background: var(--accent); color: oklch(97% 0.01 148); border-color: var(--accent); }

.btn-admin-delete {
  background: var(--danger-bg);
  color: var(--danger);
  border-color: oklch(62% 0.22 22 / 30%);
  font-size: 0.75rem;
  padding: 0.3rem 0.65rem;
  border-radius: 6px;
}
.btn-admin-delete:hover { background: var(--danger); color: var(--t1); border-color: var(--danger); }

.btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8rem; border-radius: 6px; }
.btn-lg { padding: 0.75rem 1.75rem; font-size: 1rem; border-radius: 10px; }
.btn-full { width: 100%; }

/* ============================================================
   FLASH MESSAGES
   ============================================================ */
.messages { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.75rem; }

.alert {
  padding: 0.8rem 1rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  border: 1px solid;
}
.alert-success {
  background: oklch(42% 0.13 148 / 14%);
  border-color: oklch(62% 0.21 148 / 35%);
  color: var(--accent-txt);
}
.alert-error {
  background: var(--danger-bg);
  border-color: oklch(62% 0.22 22 / 35%);
  color: oklch(80% 0.17 22);
}
.alert-notice {
  background: oklch(73% 0.17 80 / 10%);
  border-color: oklch(73% 0.17 80 / 30%);
  color: oklch(82% 0.13 80);
}

/* ============================================================
   LAYOUT
   ============================================================ */
.site-main {
  flex: 1;
  max-width: 1440px;
  width: 100%;
  margin: 0 auto;
  padding: 2.5rem 2rem 4rem;
}

.page-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
  margin-bottom: 2rem;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--t3);
  margin-bottom: 1.5rem;
  transition: color 140ms ease;
}
.back-link:hover { color: var(--t1); }

/* ============================================================
   CARD GRID
   ============================================================ */
.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(196px, 1fr));
  gap: 1.25rem;
}

/* ============================================================
   VINYL CARD
   ============================================================ */
.vinyl-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  opacity: 0;
  animation: cardIn 420ms var(--ease-out) both;
  transition: transform 240ms var(--ease-out),
              box-shadow 240ms var(--ease-out),
              border-color 240ms ease;
}
.vinyl-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
  border-color: var(--border-med);
}

@keyframes cardIn {
  from { opacity: 0; transform: translateY(14px); }
  to   { opacity: 1; transform: translateY(0); }
}

.vinyl-card__image {
  width: 100%;
  aspect-ratio: 1 / 1;
  overflow: hidden;
  background: oklch(18% 0.012 52);
  position: relative;
}
.vinyl-card__image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 420ms var(--ease-out);
}
.vinyl-card:hover .vinyl-card__image img {
  transform: scale(1.05);
}

/* Gradient overlay + "Zobrazit" pill */
.vinyl-card__overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top,
    oklch(12% 0.02 52 / 85%) 0%,
    oklch(12% 0.02 52 / 10%) 55%,
    transparent 100%);
  display: flex;
  align-items: flex-end;
  justify-content: center;
  padding-bottom: 0.875rem;
  opacity: 0;
  transition: opacity 240ms ease;
}
.vinyl-card:hover .vinyl-card__overlay { opacity: 1; }

.vinyl-card__overlay-pill {
  background: var(--accent);
  color: oklch(97% 0.01 148);
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.3em 0.9em;
  border-radius: 99px;
}

.vinyl-card__body { padding: 0.875rem 1rem; }

.vinyl-card__name {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--t1);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.vinyl-card__artist {
  font-size: 0.8rem;
  color: var(--t2);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-top: 0.15rem;
}
.vinyl-card__category {
  display: inline-block;
  margin-top: 0.5rem;
  font-size: 0.7rem;
  font-weight: 500;
  letter-spacing: 0.02em;
  color: var(--accent-txt);
  background: var(--accent-bg);
  padding: 0.18em 0.55em;
  border-radius: 4px;
}
.vinyl-card__actions {
  display: flex;
  gap: 0.375rem;
  margin-top: 0.75rem;
  padding-top: 0.625rem;
  border-top: 1px solid var(--border);
}

/* ============================================================
   FORMS
   ============================================================ */
.form-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 2rem;
  box-shadow: var(--shadow-sm);
}

.form-row { display: flex; flex-direction: column; gap: 1.125rem; }

.form-group { display: flex; flex-direction: column; gap: 0.375rem; }

.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }

.form-label, .label-sm {
  display: block;
  font-size: 0.8rem;
  font-weight: 500;
  color: var(--t2);
  letter-spacing: 0.01em;
}

.form-input, .form-select, .input-field {
  background: var(--bg-input);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 0.625rem 0.875rem;
  color: var(--t1);
  font-size: 0.9375rem;
  font-family: inherit;
  outline: none;
  width: 100%;
  transition: border-color 140ms ease, box-shadow 140ms ease, background 140ms ease;
  -webkit-appearance: none;
}
.form-input::placeholder, .input-field::placeholder { color: var(--t3); }
.form-input:focus, .form-select:focus, .input-field:focus {
  border-color: var(--border-focus);
  box-shadow: 0 0 0 3px var(--accent-bg);
  background: var(--bg-input-f);
}
.form-input[readonly] { opacity: 0.45; cursor: not-allowed; }
.form-select option { background: var(--bg-elevated); color: var(--t1); }

.form-section-label {
  display: block;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--t3);
  padding-bottom: 0.5rem;
  border-bottom: 1px solid var(--border);
  margin-top: 0.25rem;
}

/* Upload zone */
.upload-zone {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.3rem;
  padding: 1.75rem 1rem;
  border: 1.5px dashed var(--border-med);
  border-radius: 10px;
  background: var(--bg-input);
  cursor: pointer;
  text-align: center;
  transition: border-color 180ms ease, background 180ms ease;
}
.upload-zone:hover { border-color: var(--accent); background: var(--accent-bg); }
.upload-zone input[type="file"] { display: none; }
.upload-zone__title { font-size: 0.875rem; font-weight: 500; color: var(--t2); }
.upload-zone__hint  { font-size: 0.78rem; color: var(--t3); }

/* Required asterisk */
.required { color: var(--danger); }

/* Image preview grid (edit form) */
.img-preview-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; }
.img-preview-item {
  aspect-ratio: 1;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid var(--border);
  background: oklch(18% 0.012 52);
}
.img-preview-item img { width: 100%; height: 100%; object-fit: cover; }

/* ============================================================
   AUTH CARD
   ============================================================ */
.auth-card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 2rem;
  box-shadow: var(--shadow-md);
}
.auth-divider { height: 1px; background: var(--border); margin: 1.5rem 0; }

/* ============================================================
   DETAIL PAGE
   ============================================================ */
.detail-wrapper {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 14px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
}

.detail-image-box {
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid var(--border);
  background: oklch(18% 0.012 52);
}
.detail-image-box img { width: 100%; height: 500px; object-fit: cover; }

.detail-thumbs { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; margin-top: 0.5rem; }
.detail-thumb { aspect-ratio: 1; border-radius: 7px; overflow: hidden; border: 1px solid var(--border); background: oklch(18% 0.012 52); }
.detail-thumb img { width: 100%; height: 100%; object-fit: cover; }

.detail-meta {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 1.5rem;
}
.meta-table { display: flex; flex-direction: column; }
.meta-row {
  display: grid;
  grid-template-columns: 110px 1fr;
  gap: 1rem;
  align-items: baseline;
  padding: 0.6rem 0;
  border-bottom: 1px solid var(--border);
}
.meta-row:last-child { border-bottom: none; }
.meta-key { font-size: 0.78rem; font-weight: 500; color: var(--t3); }
.meta-val { font-size: 0.9375rem; color: var(--t1); }

.actions-box {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 1.25rem;
}

/* ============================================================
   EMPTY STATE
   ============================================================ */
.empty-state { text-align: center; padding: 5rem 2rem; }
.empty-state p { color: var(--t2); font-size: 1.05rem; margin-bottom: 0.4rem; }
.empty-state small { color: var(--t3); font-size: 0.875rem; }

/* ============================================================
   PAGE ANIMATIONS
   ============================================================ */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(14px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes spinVinyl {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}

.page-fade { animation: fadeUp 380ms var(--ease-out) both; }

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 640px) {
  .nav-inner { padding: 0 1rem; gap: 1rem; }
  .nav-links { display: none; }
  .site-main { padding: 1.5rem 1rem 3rem; }
  .form-grid-2, .form-grid-3 { grid-template-columns: 1fr; }
  .cards-grid { grid-template-columns: repeat(auto-fill, minmax(155px, 1fr)); gap: 0.875rem; }
  .detail-image-box img { height: 280px; }
}
</style>
</head>
<body>

<header class="site-header">
  <div class="nav-inner">
    <a href="?action=index" class="nav-logo">VinyLog</a>
    <ul class="nav-links">
      <li><a href="?action=index"  class="nav-link">Desky</a></li>
      <li><a href="?action=create" class="nav-link">Přidat desku</a></li>
    </ul>
    <div class="nav-right">
      <?php if (isset($_SESSION['user_id'])): ?>
        <div class="nav-user">
          <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>
          <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <span class="badge-admin">Admin</span>
          <?php endif; ?>
        </div>
        <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=logout"
           class="btn btn-ghost btn-sm">Odhlásit</a>
      <?php else: ?>
        <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=login"
           class="btn btn-ghost btn-sm">Přihlásit</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="site-main page-fade">

  <?php if (isset($_SESSION['messages']) && !empty($_SESSION['messages'])): ?>
    <div class="messages">
      <?php foreach ($_SESSION['messages'] as $type => $messages): ?>
        <?php
          $cls = ['success' => 'alert alert-success',
                  'error'   => 'alert alert-error',
                  'notice'  => 'alert alert-notice'][$type] ?? 'alert alert-notice';
        ?>
        <?php foreach ($messages as $message): ?>
          <div class="<?= $cls ?>"><?= htmlspecialchars($message) ?></div>
        <?php endforeach; ?>
      <?php endforeach; ?>
      <?php unset($_SESSION['messages']); ?>
    </div>
  <?php endif; ?>
