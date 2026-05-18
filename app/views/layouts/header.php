<?php
declare(strict_types=1);
$_app   = require __DIR__ . '/../../../config/app.php';
$_user  = Session::currentUser();
$_name  = htmlspecialchars($_app['app_name']);
$_title = isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' . $_name : $_name;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="/css/main.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<div class="bg-orbs" aria-hidden="true">
  <div class="bg-orb bg-orb-1"></div>
  <div class="bg-orb bg-orb-2"></div>
  <div class="bg-orb bg-orb-3"></div>
</div>

<nav class="navbar navbar-expand-lg navbar-light glass-nav sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= $_user ? '/preguntas' : '/login' ?>">
            <i class="fa-solid fa-circle-question me-2"></i><?= $_name ?>
        </a>

        <button class="navbar-toggler d-lg-none" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#mobileNav"
                aria-controls="mobileNav" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="d-none d-lg-flex align-items-center ms-auto gap-3">
            <?php if ($_user): ?>
            <ul class="navbar-nav flex-row align-items-center gap-2 mb-0">
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
                    <a class="nav-link" href="/preguntas/importar">
                        <i class="fa-solid fa-file-import me-1"></i>Importar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/simulador">
                        <i class="fa-solid fa-bolt me-1"></i>Simulador
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <span class="navbar-text text-muted small">
                    <i class="fa-solid fa-user me-1"></i><?= htmlspecialchars($_user['nombre'] . ' ' . $_user['apellido']) ?>
                </span>
                <a class="btn btn-outline-dark btn-sm" href="/logout">
                    <i class="fa-solid fa-right-from-bracket me-1"></i>Salir
                </a>
            </div>
            <?php else: ?>
            <ul class="navbar-nav flex-row align-items-center gap-2 mb-0 ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/login">
                        <i class="fa-solid fa-right-to-bracket me-1"></i>Ingresar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/register">
                        <i class="fa-solid fa-user-plus me-1"></i>Registrarse
                    </a>
                </li>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php /* Offcanvas FUERA del <nav>: backdrop-filter en el navbar crea un
         containing-block para position:fixed, lo que confina el offcanvas
         al alto del navbar en lugar del viewport completo. */ ?>
<div class="offcanvas offcanvas-end app-offcanvas" tabindex="-1"
     id="mobileNav" aria-labelledby="mobileNavLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold" id="mobileNavLabel">
            <i class="fa-solid fa-circle-question me-2 text-accent"></i><?= $_name ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column">
        <?php if ($_user): ?>
        <ul class="navbar-nav gap-1 flex-grow-1">
            <li class="nav-item">
                <a class="nav-link" href="/preguntas">
                    <i class="fa-solid fa-list-ul me-2"></i>Preguntas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/preguntas/create">
                    <i class="fa-solid fa-plus me-2"></i>Nueva pregunta
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/preguntas/importar">
                    <i class="fa-solid fa-file-import me-2"></i>Importar
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/simulador">
                    <i class="fa-solid fa-bolt me-2"></i>Simulador
                </a>
            </li>
        </ul>

        <div class="border-top pt-3 mt-3">
            <div class="text-muted small mb-2">
                <i class="fa-solid fa-user me-1"></i><?= htmlspecialchars($_user['nombre'] . ' ' . $_user['apellido']) ?>
            </div>
            <a class="btn btn-outline-dark w-100" href="/logout">
                <i class="fa-solid fa-right-from-bracket me-1"></i>Salir
            </a>
        </div>
        <?php else: ?>
        <ul class="navbar-nav gap-1">
            <li class="nav-item">
                <a class="nav-link" href="/login">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>Ingresar
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/register">
                    <i class="fa-solid fa-user-plus me-2"></i>Registrarse
                </a>
            </li>
        </ul>
        <?php endif; ?>
    </div>
</div>

<main class="container py-4 flex-grow-1">
