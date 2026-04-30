<?php
/**
 * Router para el servidor de desarrollo integrado de PHP:
 *   php -S localhost:8000 -t public public/router.php
 *
 * Sirve archivos estáticos directamente; todo lo demás pasa a index.php.
 */
declare(strict_types=1);

$path = $_SERVER['REQUEST_URI'];
$file = __DIR__ . parse_url($path, PHP_URL_PATH);

if (is_file($file)) {
    return false; // servidor sirve el archivo estático
}

require __DIR__ . '/index.php';
