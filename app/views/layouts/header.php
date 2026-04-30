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
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Merriweather', Georgia, serif; background: #f8f9fa; }
        .navbar-brand { font-weight: 700; letter-spacing: .4px; }
        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,.09); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
            </ul>
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item">
                    <span class="navbar-text">
                        <i class="fa-solid fa-user me-1"></i><?= htmlspecialchars($_user['nombre'] . ' ' . $_user['apellido']) ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-light btn-sm" href="/logout">
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
