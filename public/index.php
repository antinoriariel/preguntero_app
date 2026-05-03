<?php
declare(strict_types=1);

// ── Autoload ───────────────────────────────────────────────────────────────
require __DIR__ . '/../app/core/Database.php';
require __DIR__ . '/../app/core/Session.php';
require __DIR__ . '/../app/core/Csrf.php';
require __DIR__ . '/../app/core/Validator.php';
require __DIR__ . '/../app/models/Usuario.php';
require __DIR__ . '/../app/models/Pregunta.php';
require __DIR__ . '/../app/models/Respuesta.php';
require __DIR__ . '/../app/controllers/AuthController.php';
require __DIR__ . '/../app/controllers/PreguntaController.php';

// ── Bootstrap ──────────────────────────────────────────────────────────────
Session::start();

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// ── Routing ────────────────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri    = rtrim($uri, '/') ?: '/';

$auth      = new AuthController();
$preguntas = new PreguntaController();

// Raíz
if ($uri === '/' && $method === 'GET') {
    header('Location: ' . (Session::isLoggedIn() ? '/preguntas' : '/login'));
    exit;
}

// Auth
if ($uri === '/login'    && $method === 'GET')  { $auth->showLogin();    exit; }
if ($uri === '/login'    && $method === 'POST') { $auth->login();        exit; }
if ($uri === '/register' && $method === 'GET')  { $auth->showRegister(); exit; }
if ($uri === '/register' && $method === 'POST') { $auth->register();     exit; }
if ($uri === '/logout')                         { $auth->logout();       exit; }

// Simulador
if ($uri === '/simulador' && $method === 'GET') { $preguntas->simulador(); exit; }

// Preguntas (rutas estáticas primero)
if ($uri === '/preguntas'           && $method === 'GET')  { $preguntas->index();       exit; }
if ($uri === '/preguntas/create'    && $method === 'GET')  { $preguntas->create();      exit; }
if ($uri === '/preguntas/store'     && $method === 'POST') { $preguntas->store();       exit; }
if ($uri === '/preguntas/importar'  && $method === 'GET')  { $preguntas->importarForm(); exit; }
if ($uri === '/preguntas/importar'  && $method === 'POST') { $preguntas->importar();    exit; }

// Preguntas (rutas dinámicas con id)
if (preg_match('#^/preguntas/(\d+)$#', $uri, $m) && $method === 'GET') {
    $preguntas->show((int)$m[1]);
    exit;
}
if (preg_match('#^/preguntas/(\d+)/edit$#', $uri, $m) && $method === 'GET') {
    $preguntas->edit((int)$m[1]);
    exit;
}
if (preg_match('#^/preguntas/(\d+)/update$#', $uri, $m) && $method === 'POST') {
    $preguntas->update((int)$m[1]);
    exit;
}
if (preg_match('#^/preguntas/(\d+)/delete$#', $uri, $m) && $method === 'POST') {
    $preguntas->destroy((int)$m[1]);
    exit;
}

// 404
http_response_code(404);
$pageTitle = 'Página no encontrada';
require __DIR__ . '/../app/views/layouts/header.php';
echo '<div class="text-center py-5">';
echo '<h1 class="display-4">404</h1>';
echo '<p class="lead text-muted">La página que buscás no existe.</p>';
echo '<a href="/" class="btn btn-dark">Ir al inicio</a>';
echo '</div>';
require __DIR__ . '/../app/views/layouts/footer.php';
