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
</head>
<body class="d-flex flex-column min-vh-100 bg-body-tertiary">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
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
                <span class="navbar-text text-white-50">
                    <i class="fa-solid fa-user me-1"></i><?= htmlspecialchars($_user['nombre'] . ' ' . $_user['apellido']) ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="/logout">
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

        <div class="offcanvas offcanvas-end text-bg-dark d-lg-none" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="mobileNavLabel">
                    <i class="fa-solid fa-circle-question me-2"></i><?= $_name ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
            </div>

            <div class="offcanvas-body">
                <?php if ($_user): ?>
                <ul class="navbar-nav gap-1 mb-3">
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

                <div class="border-top pt-3 d-grid gap-2">
                    <div class="text-white-50 small">
                        <i class="fa-solid fa-user me-1"></i><?= htmlspecialchars($_user['nombre'] . ' ' . $_user['apellido']) ?>
                    </div>
                    <a class="btn btn-outline-light" href="/logout">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>Salir
                    </a>
                </div>
                <?php else: ?>
                <ul class="navbar-nav gap-1">
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
    </div>
</nav>

<main class="container py-4 flex-grow-1">