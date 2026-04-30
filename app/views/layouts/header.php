<?php
declare(strict_types=1);
$_app     = require __DIR__ . '/../../../config/app.php';
$_user    = Session::currentUser();
$_name    = htmlspecialchars($_app['app_name']);
$_title   = isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' . $_name : $_name;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $_title ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,400;0,700;1,400&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
    /* ── Variables ───────────────────────────────────────────────────────── */
    :root {
        --tech-bg:      #080c18;
        --tech-surface: rgba(13, 20, 40, 0.82);
        --tech-border:  rgba(0, 229, 255, 0.14);
        --tech-accent:  #00e5ff;
        --tech-accent2: #7c3aed;
        --tech-text:    #dde6f0;
        --tech-muted:   #7a92ac;
    }

    /* ── Reset base ──────────────────────────────────────────────────────── */
    html { background: var(--tech-bg); }

    body {
        font-family: 'Merriweather', Georgia, serif;
        background: var(--tech-bg);
        color: var(--tech-text);
        min-height: 100vh;
    }

    /* ── Tech grid overlay (fixed) ───────────────────────────────────────── */
    body::before {
        content: '';
        position: fixed;
        inset: 0;
        background-image:
            linear-gradient(rgba(0,229,255,.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0,229,255,.035) 1px, transparent 1px);
        background-size: 48px 48px;
        pointer-events: none;
        z-index: 0;
    }

    /* ── Particles canvas sits below everything ──────────────────────────── */
    #particles-js {
        position: fixed;
        inset: 0;
        z-index: 0;
        pointer-events: none;
    }

    /* ── Stacking context: nav + main float above bg ─────────────────────── */
    .navbar,
    main.container,
    footer {
        position: relative;
        z-index: 1;
    }

    /* ── Navbar ──────────────────────────────────────────────────────────── */
    .navbar {
        background: rgba(8, 12, 24, 0.88) !important;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border-bottom: 1px solid var(--tech-border);
        position: sticky !important;
        top: 0;
        z-index: 1050 !important;
    }

    .navbar-brand {
        font-weight: 700;
        letter-spacing: .4px;
        color: var(--tech-accent) !important;
        text-shadow: 0 0 12px rgba(0,229,255,.45);
        transition: text-shadow .3s;
    }
    .navbar-brand:hover { text-shadow: 0 0 20px rgba(0,229,255,.8); }

    .navbar-toggler {
        border-color: var(--tech-border) !important;
    }
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%280%2C229%2C255%2C0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
    }

    .nav-link {
        color: rgba(221,230,240,.65) !important;
        transition: color .25s, text-shadow .25s;
    }
    .nav-link:hover {
        color: var(--tech-accent) !important;
        text-shadow: 0 0 10px rgba(0,229,255,.5);
    }
    .navbar-text { color: var(--tech-muted) !important; }

    /* ── Cards ───────────────────────────────────────────────────────────── */
    .card {
        background: var(--tech-surface) !important;
        border: 1px solid var(--tech-border) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 12px !important;
        box-shadow: 0 4px 28px rgba(0,0,0,.45);
        color: var(--tech-text) !important;
        transition: box-shadow .35s, transform .35s;
    }
    .card:hover {
        box-shadow: 0 8px 36px rgba(0,229,255,.1), 0 0 0 1px rgba(0,229,255,.25);
        transform: translateY(-2px);
    }
    .card-header {
        background: rgba(0,229,255,.04) !important;
        border-color: var(--tech-border) !important;
        color: var(--tech-text) !important;
    }

    /* ── Typography ──────────────────────────────────────────────────────── */
    h1, h2, h3, h4, h5, h6, .card-title { color: var(--tech-text); }
    .text-muted { color: var(--tech-muted) !important; }
    .form-label { color: var(--tech-text); }
    .form-text  { color: var(--tech-muted) !important; }
    a { color: var(--tech-accent); transition: color .2s; }
    a:hover { color: #67e8f9; }
    hr { border-color: var(--tech-border) !important; opacity: 1; }
    small { color: var(--tech-muted); }

    /* ── Form controls ───────────────────────────────────────────────────── */
    .form-control,
    .form-select {
        background: rgba(8,12,24,.6) !important;
        border-color: var(--tech-border) !important;
        color: var(--tech-text) !important;
        transition: border-color .25s, box-shadow .25s;
    }
    .form-control:focus,
    .form-select:focus {
        background: rgba(8,12,24,.85) !important;
        border-color: var(--tech-accent) !important;
        box-shadow: 0 0 0 3px rgba(0,229,255,.18) !important;
        color: var(--tech-text) !important;
    }
    .form-control::placeholder { color: var(--tech-muted) !important; }
    .form-control.is-invalid { border-color: #f87171 !important; }
    .invalid-feedback { color: #f87171 !important; }
    .input-group-text {
        background: rgba(8,12,24,.7) !important;
        border-color: var(--tech-border) !important;
        color: var(--tech-muted) !important;
    }

    /* ── Alerts ──────────────────────────────────────────────────────────── */
    .alert-danger {
        background: rgba(220,53,69,.14) !important;
        border-color: rgba(220,53,69,.35) !important;
        color: #f87171 !important;
    }
    .alert-success {
        background: rgba(25,135,84,.14) !important;
        border-color: rgba(25,135,84,.35) !important;
        color: #4ade80 !important;
    }
    .alert .btn-close { filter: invert(1) grayscale(1); }

    /* ── Buttons — global reset & animations ─────────────────────────────── */
    .btn {
        position: relative;
        overflow: hidden;
        transition: transform .28s cubic-bezier(.4,0,.2,1),
                    box-shadow .28s cubic-bezier(.4,0,.2,1),
                    background .28s, border-color .28s, color .28s !important;
        letter-spacing: .3px;
        font-size: .9rem;
    }
    /* ripple sweep */
    .btn::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,.09), transparent);
        transform: translateX(-100%);
        transition: transform .35s ease;
        border-radius: inherit;
        pointer-events: none;
    }
    .btn:hover::after  { transform: translateX(0); }
    .btn:hover         { transform: translateY(-2px); }
    .btn:active        { transform: translateY(0) scale(.96); }

    /* btn-dark → cyan-glow primary */
    .btn-dark, .btn-dark:focus {
        background: linear-gradient(135deg, #1a2540, #0c1528) !important;
        border: 1px solid var(--tech-accent) !important;
        color: var(--tech-accent) !important;
    }
    .btn-dark:hover {
        background: linear-gradient(135deg, #0c1528, #060e1c) !important;
        box-shadow: 0 0 18px rgba(0,229,255,.45), 0 4px 18px rgba(0,0,0,.5) !important;
        color: #fff !important;
    }

    /* btn-outline-dark → accent outline */
    .btn-outline-dark, .btn-outline-dark:focus {
        border-color: rgba(0,229,255,.4) !important;
        color: var(--tech-accent) !important;
        background: transparent !important;
    }
    .btn-outline-dark:hover {
        background: rgba(0,229,255,.1) !important;
        border-color: var(--tech-accent) !important;
        box-shadow: 0 0 14px rgba(0,229,255,.3) !important;
        color: #fff !important;
    }

    /* btn-outline-secondary */
    .btn-outline-secondary, .btn-outline-secondary:focus {
        border-color: rgba(122,146,172,.4) !important;
        color: var(--tech-muted) !important;
        background: transparent !important;
    }
    .btn-outline-secondary:hover {
        background: rgba(122,146,172,.1) !important;
        border-color: var(--tech-muted) !important;
        color: var(--tech-text) !important;
        box-shadow: 0 4px 14px rgba(0,0,0,.3) !important;
    }

    /* btn-outline-danger */
    .btn-outline-danger, .btn-outline-danger:focus {
        border-color: rgba(220,53,69,.4) !important;
        color: #f87171 !important;
        background: transparent !important;
    }
    .btn-outline-danger:hover {
        background: rgba(220,53,69,.14) !important;
        border-color: rgba(220,53,69,.6) !important;
        box-shadow: 0 0 14px rgba(220,53,69,.3) !important;
        color: #f87171 !important;
    }

    /* btn-lg size tweak */
    .btn-lg { font-size: 1rem; padding: .65rem 1.4rem; }

    /* active / selected pill (simulador) */
    .time-pill.btn-dark.active {
        box-shadow: 0 0 14px rgba(0,229,255,.5) !important;
    }

    /* ── List group ──────────────────────────────────────────────────────── */
    .list-group-item {
        background: rgba(13,20,40,.72) !important;
        border-color: var(--tech-border) !important;
        color: var(--tech-text) !important;
        transition: background .22s, transform .22s, box-shadow .22s;
    }
    .list-group-item-action:hover {
        background: rgba(0,229,255,.07) !important;
        transform: translateX(4px);
        box-shadow: inset 3px 0 0 var(--tech-accent);
    }

    /* ── Badges ──────────────────────────────────────────────────────────── */
    .badge.bg-secondary {
        background: rgba(100,116,139,.28) !important;
        color: var(--tech-muted) !important;
        border: 1px solid rgba(100,116,139,.4);
    }
    .badge.bg-success {
        background: rgba(25,135,84,.22) !important;
        color: #4ade80 !important;
        border: 1px solid rgba(25,135,84,.4);
    }
    .badge.bg-warning {
        background: rgba(255,193,7,.18) !important;
        color: #fcd34d !important;
        border: 1px solid rgba(255,193,7,.4);
    }

    /* ── Breadcrumb ──────────────────────────────────────────────────────── */
    .breadcrumb-item a { color: var(--tech-accent); text-decoration: none; }
    .breadcrumb-item a:hover { text-decoration: underline; color: #67e8f9; }
    .breadcrumb-item.active { color: var(--tech-muted); }
    .breadcrumb-item + .breadcrumb-item::before { color: var(--tech-muted); }

    /* ── Pagination ──────────────────────────────────────────────────────── */
    .page-link {
        background: rgba(13,20,40,.8) !important;
        border-color: var(--tech-border) !important;
        color: var(--tech-text) !important;
        transition: background .22s, color .22s, box-shadow .22s;
    }
    .page-link:hover {
        background: rgba(0,229,255,.1) !important;
        color: var(--tech-accent) !important;
        box-shadow: 0 0 10px rgba(0,229,255,.25);
    }
    .page-item.active .page-link {
        background: rgba(0,229,255,.18) !important;
        border-color: var(--tech-accent) !important;
        color: var(--tech-accent) !important;
    }

    /* ── Tables ──────────────────────────────────────────────────────────── */
    .table { color: var(--tech-text) !important; }
    .table > :not(caption) > * > * {
        background-color: transparent !important;
        border-color: var(--tech-border) !important;
        color: var(--tech-text) !important;
    }
    .table-light th, thead.table-light th {
        background: rgba(0,229,255,.05) !important;
        color: var(--tech-muted) !important;
        border-color: var(--tech-border) !important;
    }
    .table-hover tbody tr:hover > * {
        background: rgba(0,229,255,.05) !important;
    }

    /* ── Progress bar ────────────────────────────────────────────────────── */
    .progress { background: rgba(255,255,255,.07) !important; }

    /* ── Footer ──────────────────────────────────────────────────────────── */
    footer {
        border-color: var(--tech-border) !important;
        color: var(--tech-muted) !important;
    }

    /* ── Scrollbar ───────────────────────────────────────────────────────── */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: var(--tech-bg); }
    ::-webkit-scrollbar-thumb { background: rgba(0,229,255,.28); border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(0,229,255,.5); }

    /* ── Simulador – answer buttons ──────────────────────────────────────── */
    .answer-btn {
        min-height: 60px;
        font-size: .92rem;
        white-space: normal;
        line-height: 1.4;
        text-align: left;
    }
    .answer-btn:disabled { cursor: not-allowed; opacity: 1; }

    /* simulador SVG ring defaults */
    .score-ring-bg  { stroke: rgba(255,255,255,.1) !important; }
    #score-text     { fill: var(--tech-text) !important; }

    /* ── Mobile-first tweaks (xs < 576px) ───────────────────────────────── */
    @media (max-width: 575.98px) {
        main.container { padding-left: 10px !important; padding-right: 10px !important; }
        .card-body { padding: 1.1rem !important; }
        h2 { font-size: 1.3rem !important; }
        h4 { font-size: 1.1rem !important; }
        .btn-lg { font-size: .95rem; padding: .55rem 1.1rem; }
        .answer-btn { min-height: 52px; font-size: .875rem; }
        .display-6 { font-size: 1.6rem !important; }
        .navbar-brand { font-size: 1rem; }
        /* stack time pills vertically on very small screens */
        .time-pill { min-width: 64px; font-size: .82rem; }
    }

    /* ── Entrance animation for main content ─────────────────────────────── */
    main.container > * {
        animation: fadeUp .32s ease both;
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: none; }
    }
    </style>
</head>
<body>

<div id="particles-js" aria-hidden="true"></div>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= $_user ? '/preguntas' : '/login' ?>">
            <i class="fa-solid fa-circle-question me-2"></i><?= $_name ?>
        </a>
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <?php if ($_user): ?>
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/preguntas">
                        <i class="fa-solid fa-list-ul me-1"></i>Preguntas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/preguntas/create">
                        <i class="fa-solid fa-plus me-1"></i>Nueva
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/simulador">
                        <i class="fa-solid fa-bolt me-1"></i>Simulador
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item">
                    <span class="navbar-text">
                        <i class="fa-solid fa-user me-1"></i><?= htmlspecialchars($_user['nombre'] . ' ' . $_user['apellido']) ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-dark btn-sm" href="/logout">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>Salir
                    </a>
                </li>
            </ul>
            <?php else: ?>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/login">Ingresar</a></li>
                <li class="nav-item"><a class="nav-link" href="/register">Registrarse</a></li>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main class="container py-4">
