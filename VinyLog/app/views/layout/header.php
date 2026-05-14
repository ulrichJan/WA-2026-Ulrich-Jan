<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="cs" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Monoton&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>VinyLog</title>
    <style>
      /* =============================================
         VINYLOG DESIGN SYSTEM
         ============================================= */

      :root {
        --ease-out:    cubic-bezier(0.23, 1, 0.32, 1);
        --ease-in-out: cubic-bezier(0.77, 0, 0.175, 1);
        --accent:      #c8a86b;
        --accent-h:    #d4b87b;
        --green:       #4a7c59;
        --green-h:     #3a6347;
        --red:         #e55c5c;
        --s1:          #111111;
        --s2:          #181818;
        --s3:          #222222;
        --border:      #252525;
        --border-h:    #3a3a3a;
        --t1:          #f0ece4;
        --t2:          #888888;
        --t3:          #505050;
      }

      /* Scrollbar */
      ::-webkit-scrollbar           { width: 5px; }
      ::-webkit-scrollbar-track     { background: #080808; }
      ::-webkit-scrollbar-thumb     { background: #2a2a2a; border-radius: 10px; }
      ::-webkit-scrollbar-thumb:hover { background: #3a3a3a; }

      /* ---- KEYFRAMES ---- */
      @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
      }
      @keyframes slideDown {
        from { opacity: 0; transform: translateY(-12px); }
        to   { opacity: 1; transform: translateY(0); }
      }
      @keyframes spinVinyl {
        to { transform: rotate(360deg); }
      }
      @keyframes pulseBorder {
        0%, 100% { border-color: var(--border); }
        50%      { border-color: var(--accent); }
      }

      /* ---- GLOBAL ---- */
      body { background: #080808; color: var(--t1); }
      a    { text-decoration: none; }
      .page-content { animation: fadeUp 0.5s var(--ease-out) both; }

      /* ---- SITE HEADER ---- */
      .site-header {
        background: rgba(8, 8, 8, 0.88);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        border-bottom: 1px solid var(--border);
      }

      /* ---- LOGO ---- */
      .logo-link {
        transition: color 300ms ease, text-shadow 300ms ease;
        color: var(--t1);
        display: inline-block;
      }
      .logo-link:hover {
        color: var(--accent);
        text-shadow: 0 0 24px rgba(200,168,107,0.5), 0 0 48px rgba(200,168,107,0.2);
      }

      /* ---- NAV LINKS ---- */
      .nav-link {
        position: relative;
        padding-bottom: 3px;
        color: var(--t2);
        font-size: 15px;
        font-weight: 500;
        transition: color 180ms ease;
      }
      .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0;
        width: 0; height: 1px;
        background: var(--accent);
        transition: width 220ms var(--ease-out);
      }
      .nav-link:hover            { color: var(--t1); }
      .nav-link:hover::after     { width: 100%; }

      /* ---- AUTH BUTTONS ---- */
      .auth-btn {
        font-size: 13px;
        font-weight: 500;
        padding: 6px 14px;
        border-radius: 8px;
        border: 1px solid transparent;
        transition: color 150ms ease, background 150ms ease, border-color 150ms ease;
        color: var(--t2);
      }
      .auth-btn:hover {
        color: var(--t1);
        background: var(--s2);
        border-color: var(--border);
      }
      .admin-badge {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        background: var(--accent);
        color: #080808;
        padding: 2px 7px;
        border-radius: 5px;
        margin-left: 6px;
        vertical-align: middle;
      }

      /* ---- NOTIFICATIONS ---- */
      .notif-wrap { animation: slideDown 0.32s var(--ease-out) both; }
      .notif-item {
        background: var(--s1);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 14px;
        color: var(--t1);
        border-left-width: 3px;
      }

      /* ---- BACK LINK ---- */
      .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--t3);
        font-size: 14px;
        font-weight: 500;
        transition: color 180ms ease, gap 200ms var(--ease-out);
      }
      .back-link:hover { color: var(--t1); gap: 12px; }

      /* ---- CARDS (vinyl index) ---- */
      .vinyl-card {
        background: var(--s1);
        border: 1px solid var(--border);
        border-radius: 18px;
        overflow: hidden;
        cursor: pointer;
        transition: transform 280ms var(--ease-out),
                    box-shadow 280ms var(--ease-out),
                    border-color 280ms ease;
        will-change: transform;
      }
      .vinyl-card:hover {
        transform: translateY(-9px);
        border-color: var(--border-h);
        box-shadow: 0 28px 56px rgba(0,0,0,0.72),
                    0 0 0 1px rgba(200,168,107,0.1);
      }
      .vinyl-card:active {
        transform: translateY(-3px) scale(0.985);
        transition-duration: 100ms;
      }
      /* Image zoom */
      .vinyl-img-wrap { overflow: hidden; }
      .vinyl-img-inner {
        display: block;
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 420ms var(--ease-out);
      }
      .vinyl-card:hover .vinyl-img-inner { transform: scale(1.08); }
      /* Bottom gradient overlay */
      .card-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, transparent 55%);
        opacity: 0;
        transition: opacity 220ms ease;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding-bottom: 18px;
      }
      .vinyl-card:hover .card-overlay { opacity: 1; }
      .card-cta {
        background: var(--accent);
        color: #080808;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 7px 18px;
        border-radius: 20px;
        transform: translateY(10px);
        opacity: 0;
        transition: transform 240ms var(--ease-out), opacity 240ms ease;
      }
      .vinyl-card:hover .card-cta {
        transform: translateY(0);
        opacity: 1;
      }
      /* Vinyl disc peek on hover */
      .vinyl-disc {
        position: absolute;
        top: 50%; right: -56px;
        width: 72px; height: 72px;
        border-radius: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        opacity: 0;
        transition: right 320ms var(--ease-out), opacity 320ms ease;
        background: radial-gradient(
          circle,
          #080808 0%, #080808 22%,
          #2a2a2a 23%, #2a2a2a 38%,
          #141414 39%, #141414 47%,
          var(--accent) 48%, var(--accent) 52%,
          #141414 53%, #141414 100%
        );
        box-shadow: 0 6px 24px rgba(0,0,0,0.7);
      }
      .vinyl-disc-inner {
        position: absolute; inset: 0;
        border-radius: 50%;
        animation: spinVinyl 4s linear infinite;
        background: repeating-conic-gradient(
          #111 0deg, #1a1a1a 3deg, #111 6deg
        );
        border-radius: 50%;
        opacity: 0.6;
      }
      .vinyl-card:hover .vinyl-disc { right: 12px; opacity: 0.9; }

      /* Card stagger — animation set via PHP inline style */
      .card-stagger { animation: fadeUp 0.48s var(--ease-out) both; }

      /* Card action buttons */
      .card-action {
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 7px;
        border: 1px solid;
        transition: transform 120ms ease, opacity 120ms ease, background 130ms ease;
      }
      .card-action:active { transform: scale(0.93); }
      .card-action-edit  { color: var(--t1); border-color: var(--border-h); background: var(--s2); }
      .card-action-edit:hover  { background: var(--s3); border-color: #555; }
      .card-action-delete { color: var(--red); border-color: rgba(229,92,92,0.25); background: transparent; }
      .card-action-delete:hover { background: rgba(229,92,92,0.08); border-color: var(--red); }

      /* ---- BUTTONS (global) ---- */
      .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 600;
        font-size: 14px;
        border-radius: 10px;
        padding: 11px 22px;
        cursor: pointer;
        border: 1px solid transparent;
        transition: transform 140ms ease,
                    box-shadow 140ms ease,
                    background 150ms ease,
                    border-color 150ms ease,
                    color 150ms ease;
        will-change: transform;
      }
      .btn:active             { transform: scale(0.96); }
      .btn-gold               { background: var(--accent); color: #080808; }
      .btn-gold:hover         { background: var(--accent-h); box-shadow: 0 6px 22px rgba(200,168,107,0.35); }
      .btn-green              { background: var(--green); color: #fff; }
      .btn-green:hover        { background: var(--green-h); box-shadow: 0 6px 22px rgba(74,124,89,0.3); }
      .btn-outline            { background: transparent; color: var(--t1); border-color: var(--border); }
      .btn-outline:hover      { background: var(--s2); border-color: var(--border-h); }
      .btn-danger             { background: transparent; color: var(--red); border-color: rgba(229,92,92,0.28); }
      .btn-danger:hover       { background: rgba(229,92,92,0.09); border-color: var(--red); }

      /* ---- FORM ELEMENTS ---- */
      .form-card {
        background: var(--s1);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 40px;
      }
      .form-label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--t2);
        margin-bottom: 8px;
      }
      .input-field {
        width: 100%;
        background: var(--s2);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 15px;
        color: var(--t1);
        transition: border-color 150ms ease, box-shadow 150ms ease, background 150ms ease;
      }
      .input-field::placeholder { color: var(--t3); }
      .input-field:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(200,168,107,0.12);
        background: #1d1d1d;
      }
      .input-readonly {
        background: var(--s3);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 15px;
        color: var(--t3);
        cursor: not-allowed;
        width: 100%;
      }
      select.input-field {
        cursor: pointer;
        appearance: none; -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='7' viewBox='0 0 11 7'%3E%3Cpath d='M1 1l4.5 4.5L10 1' stroke='%23666' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 13px center;
        padding-right: 36px;
      }
      select.input-field option { background: #181818; color: var(--t1); }

      .form-section-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--accent);
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 24px;
        display: block;
      }

      /* File upload */
      .upload-zone {
        background: var(--s2);
        border: 2px dashed var(--border);
        border-radius: 12px;
        padding: 36px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: border-color 200ms ease, background 200ms ease;
      }
      .upload-zone:hover   { border-color: var(--accent); background: rgba(200,168,107,0.04); }
      .upload-zone.active  { border-color: var(--green); border-style: solid; background: rgba(74,124,89,0.06); }

      /* Existing images grid */
      .img-thumb {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid var(--border);
        aspect-ratio: 1;
        transition: transform 200ms var(--ease-out), border-color 200ms ease;
      }
      .img-thumb:hover { transform: scale(1.04); border-color: var(--border-h); }
      .img-thumb img   { width: 100%; height: 100%; object-fit: cover; display: block; }

      /* ---- SHOW PAGE ---- */
      .info-card {
        background: var(--s1);
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 28px;
      }
      .info-row {
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
        animation: fadeUp 0.4s var(--ease-out) both;
      }
      .info-row:last-child { border-bottom: none; }
      .info-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--t3);
        margin-bottom: 4px;
      }
      .info-value { font-size: 15px; font-weight: 500; color: var(--t1); }
      .show-hero-img {
        transition: transform 400ms var(--ease-out);
      }
      .show-hero-img:hover { transform: scale(1.02); }
      .thumb-strip-item {
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid transparent;
        aspect-ratio: 1;
        cursor: pointer;
        transition: border-color 180ms ease, transform 180ms var(--ease-out);
      }
      .thumb-strip-item:hover { border-color: var(--accent); transform: scale(1.06); }

      /* ---- AUTH PAGES ---- */
      .auth-card {
        background: var(--s1);
        border: 1px solid var(--border);
        border-radius: 22px;
        padding: 44px 48px;
      }
      .auth-divider {
        height: 1px;
        background: var(--border);
        margin: 24px 0;
      }

      /* ---- HOVER GATE for touch devices ---- */
      @media (hover: none) {
        .vinyl-card:hover  { transform: none; box-shadow: none; }
        .vinyl-disc        { display: none; }
      }

      /* ---- REDUCED MOTION ---- */
      @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
          animation-duration: 0.01ms !important;
          transition-duration: 0.01ms !important;
        }
      }
    </style>
</head>
<body class="min-h-screen flex flex-col" style="background:#080808;">

    <header class="site-header sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-8 py-4 flex items-center gap-10">

            <a href="?action=index" class="logo-link flex-shrink-0">
                <span class="text-5xl leading-none font-normal" style="font-family:'Monoton',cursive;">VinyLog</span>
            </a>

            <nav class="hidden md:flex items-center gap-8">
                <a href="?action=index"  class="nav-link">Desky</a>
                <a href="?action=create" class="nav-link">Přidat desku</a>
            </nav>

            <div class="ml-auto flex items-center gap-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span style="font-size:14px;color:var(--t2);font-weight:500;">
                        <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                            <span class="admin-badge">Admin</span>
                        <?php endif; ?>
                    </span>
                    <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=logout" class="auth-btn">Odhlásit</a>
                <?php else: ?>
                    <a href="/WA-2026-Ulrich-Jan/VinyLog/app/controllers/AuthController.php?action=login" class="auth-btn">Přihlásit</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-8 py-10 flex-grow w-full">

        <?php if (isset($_SESSION['messages']) && !empty($_SESSION['messages'])): ?>
            <div class="space-y-2 mb-8 notif-wrap">
                <?php foreach ($_SESSION['messages'] as $type => $messages):
                    $lc = ['success'=>'#4a7c59','error'=>'#e55c5c','notice'=>'#666'];
                    $color = $lc[$type] ?? $lc['notice'];
                ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="notif-item" style="border-left-color:<?= $color ?>;">
                            <?= htmlspecialchars($msg) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <?php unset($_SESSION['messages']); ?>
            </div>
        <?php endif; ?>
