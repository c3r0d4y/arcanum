<?php

/*
 * Archivo: public/index.php
 * Autor:   C3r0d4y
 *
 * Front Controller de ARCANUM. Aplica cabeceras de seguridad HTTP,
 * endurece la sesión y despacha cada petición al controlador correcto.
 */

declare(strict_types=1);

ini_set('pcre.jit', '0');

/* ── Cabeceras de seguridad HTTP ── */
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'; object-src 'self' blob:; frame-src 'self' blob:; img-src 'self' data: blob:;");

/* ── Endurecimiento de sesión ── */
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

/* ── Carga base ── */
require_once __DIR__ . '/../config/config.php';
require_once APP_ROOT . '/app/core/helpers.php';
require_once APP_ROOT . '/app/core/Database.php';
require_once APP_ROOT . '/app/core/Crypto.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/core/Csrf.php';
require_once APP_ROOT . '/app/core/Logger.php';
require_once APP_ROOT . '/app/core/Controller.php';

/* ── Autocarga de controladores y modelos ── */
spl_autoload_register(static function (string $class): void {
    foreach (['controllers', 'models'] as $folder) {
        $path = APP_ROOT . "/app/{$folder}/{$class}.php";
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});

/* ── Sesión segura con nombre único ── */
session_name('arcanum_session');
session_start();

/* ── Lectura y normalización de la ruta ── */
$route = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', '/');
$base  = trim(BASE_URL, '/');

if ($base !== '' && str_starts_with($route, $base)) {
    $route = trim(substr($route, strlen($base)), '/');
}

$route  = $route === 'public' ? '' : preg_replace('#^public/?#', '', $route);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

/* ── Guardián de VERSIÓN DEMO ──
 * Bloquea en el servidor toda petición que modifique la base de datos.
 * Solo se permiten el inicio de sesión, el PIN de acceso y la
 * verificación de PIN, para que se pueda navegar por toda la app.
 * Es la segunda barrera: aunque alguien evite el aviso del navegador,
 * el servidor nunca ejecutará la operación. */
if (DEMO_MODE && $method === 'POST') {
    $rutasPermitidas = ['login', 'login/pin', 'pin/verify'];

    if (!in_array($route, $rutasPermitidas, true)) {
        // Peticiones AJAX reciben JSON; las demás, aviso y regreso
        $esAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

        if ($esAjax) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'demo' => true, 'error' => DEMO_MESSAGE]);
        } else {
            flash('error', DEMO_MESSAGE);
            // Regresa a la página anterior solo si pertenece a este sitio
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            $host    = $_SERVER['HTTP_HOST'] ?? '';
            if ($referer !== '' && parse_url($referer, PHP_URL_HOST) === $host) {
                header('Location: ' . $referer);
            } else {
                header('Location: ' . url(''));
            }
        }
        exit;
    }
}

/* ── Tabla de rutas estáticas ── */
$routes = [
    'GET' => [
        ''                 => [DocumentsController::class, 'index'],
        'login'            => [AuthController::class,      'showLogin'],
        'login/pin'        => [AuthController::class,      'showLoginPin'],
        'logout'           => [AuthController::class,      'logout'],
        'ping'             => [AuthController::class,      'ping'],
        'documents'        => [DocumentsController::class, 'index'],
        'documents/create' => [DocumentsController::class, 'create'],
        'users'            => [UsersController::class,     'index'],
        'users/create'     => [UsersController::class,     'create'],
        'logs'             => [LogsController::class,      'index'],
        'catalogs'         => [CatalogsController::class,  'index'],
        'profile'          => [ProfileController::class,   'index'],
    ],
    'POST' => [
        'login'                  => [AuthController::class,   'login'],
        'login/pin'              => [AuthController::class,   'verifyLoginPin'],
        'documents/store'        => [DocumentsController::class, 'store'],
        'users/store'            => [UsersController::class,  'store'],
        'catalogs/types/store'   => [CatalogsController::class, 'storeType'],
        'catalogs/senders/store' => [CatalogsController::class, 'storeSender'],
        'profile/password'       => [ProfileController::class, 'updatePassword'],
        'profile/pin'            => [ProfileController::class, 'updatePin'],
        'profile/avatar'         => [ProfileController::class, 'updateAvatar'],
        'pin/verify'             => [ProfileController::class, 'verifyPin'],
    ],
];

/* ── Rutas dinámicas con ID ── */
if (preg_match('#^documents/(\d+)$#', $route, $m) && $method === 'GET') {
    [new DocumentsController(), 'show']((int) $m[1]);
    exit;
}
if (preg_match('#^documents/(\d+)/edit$#', $route, $m) && $method === 'GET') {
    [new DocumentsController(), 'edit']((int) $m[1]);
    exit;
}
if (preg_match('#^documents/(\d+)/update$#', $route, $m) && $method === 'POST') {
    [new DocumentsController(), 'update']((int) $m[1]);
    exit;
}
if (preg_match('#^documents/(\d+)/delete$#', $route, $m) && $method === 'POST') {
    [new DocumentsController(), 'delete']((int) $m[1]);
    exit;
}
if (preg_match('#^documents/(\d+)/file$#', $route, $m) && $method === 'GET') {
    [new DocumentsController(), 'file']((int) $m[1]);
    exit;
}
if (preg_match('#^users/(\d+)/edit$#', $route, $m) && $method === 'GET') {
    [new UsersController(), 'edit']((int) $m[1]);
    exit;
}
if (preg_match('#^users/(\d+)/avatar$#', $route, $m) && $method === 'GET') {
    [new UsersController(), 'avatar']((int) $m[1]);
    exit;
}
if (preg_match('#^users/(\d+)/update$#', $route, $m) && $method === 'POST') {
    [new UsersController(), 'update']((int) $m[1]);
    exit;
}
if (preg_match('#^users/(\d+)/delete$#', $route, $m) && $method === 'POST') {
    [new UsersController(), 'delete']((int) $m[1]);
    exit;
}
if (preg_match('#^users/(\d+)/unlock$#', $route, $m) && $method === 'POST') {
    [new UsersController(), 'unlock']((int) $m[1]);
    exit;
}

/* ── Rutas de catálogos con ID ── */
if (preg_match('#^catalogs/types/(\d+)/edit$#', $route, $m) && $method === 'GET') {
    [new CatalogsController(), 'editType']((int) $m[1]);
    exit;
}
if (preg_match('#^catalogs/types/(\d+)/update$#', $route, $m) && $method === 'POST') {
    [new CatalogsController(), 'updateType']((int) $m[1]);
    exit;
}
if (preg_match('#^catalogs/types/(\d+)/delete$#', $route, $m) && $method === 'POST') {
    [new CatalogsController(), 'deleteType']((int) $m[1]);
    exit;
}
if (preg_match('#^catalogs/senders/(\d+)/edit$#', $route, $m) && $method === 'GET') {
    [new CatalogsController(), 'editSender']((int) $m[1]);
    exit;
}
if (preg_match('#^catalogs/senders/(\d+)/update$#', $route, $m) && $method === 'POST') {
    [new CatalogsController(), 'updateSender']((int) $m[1]);
    exit;
}
if (preg_match('#^catalogs/senders/(\d+)/delete$#', $route, $m) && $method === 'POST') {
    [new CatalogsController(), 'deleteSender']((int) $m[1]);
    exit;
}

/* ── Despacho de rutas estáticas ── */
$handler = $routes[$method][$route] ?? null;
if ($handler === null) {
    http_response_code(404);
    (new Controller())->view('errors/404');
    exit;
}

[$controller, $action] = $handler;
[new $controller(), $action]();
