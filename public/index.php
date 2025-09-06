<?php
// Exibir erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bootstrap: Autoload Composer, configs, helpers
require_once __DIR__ . '/../vendor/autoload.php';
$appConfig = require __DIR__ . '/../config/app.php';
$dbConfig = require __DIR__ . '/../config/database.php';
$jwtConfig = require __DIR__ . '/../config/jwt.php';

// Helpers
require_once __DIR__ . '/../app/helpers/security.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/helpers/validation.php';
require_once __DIR__ . '/../app/helpers/cpf.php';
require_once __DIR__ . '/../app/helpers/phone.php';

// Middlewares
require_once __DIR__ . '/../app/middlewares/AuthMiddleware.php';
require_once __DIR__ . '/../app/middlewares/CsrfMiddleware.php';

// Controllers
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';

// Simple router by query string
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$method = $_SERVER['REQUEST_METHOD'];

// Auth check
$auth = new AuthMiddleware($jwtConfig);
$isAuth = $auth->check();

// CSRF check for POST
if ($method === 'POST') {
    $csrf = new CsrfMiddleware();
    if (!$csrf->validate($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        echo 'CSRF token inválido.';
        exit;
    }
}

// Routing
switch (true) {
    case $uri === '/' || $uri === '/kittybetu/public/':
        if ($isAuth) {
            header('Location: /kittybetu/public/dashboard');
        } else {
            header('Location: /kittybetu/public/login');
        }
        exit;
    case $uri === '/kittybetu/public/login' && $method === 'GET':
        (new AuthController($dbConfig, $jwtConfig))->showLogin();
        break;
    case $uri === '/kittybetu/public/login' && $method === 'POST':
        (new AuthController($dbConfig, $jwtConfig))->login($_POST);
        break;
    case $uri === '/kittybetu/public/register' && $method === 'GET':
        (new AuthController($dbConfig, $jwtConfig))->showRegister();
        break;
    case $uri === '/kittybetu/public/register' && $method === 'POST':
        (new AuthController($dbConfig, $jwtConfig))->register($_POST);
        break;
    case $uri === '/kittybetu/public/logout' && $method === 'POST':
        (new AuthController($dbConfig, $jwtConfig))->logout();
        break;
    case $uri === '/kittybetu/public/dashboard' && $method === 'GET':
        if (!$isAuth) { header('Location: /kittybetu/public/login'); exit; }
        require __DIR__ . '/../app/views/dashboard/index.php';
        break;
    case $uri === '/kittybetu/public/users' && $method === 'GET':
        if (!$isAuth) { header('Location: /kittybetu/public/login'); exit; }
        (new UserController($dbConfig))->list($_GET);
        break;
    case preg_match('#^/kittybetu/public/users/(\d+)$#', $uri, $m) && $method === 'GET':
        if (!$isAuth) { header('Location: /kittybetu/public/login'); exit; }
        (new UserController($dbConfig))->show($m[1]);
        break;
    case preg_match('#^/kittybetu/public/users/(\d+)/edit$#', $uri, $m) && $method === 'GET':
        if (!$isAuth) { header('Location: /kittybetu/public/login'); exit; }
        (new UserController($dbConfig))->edit($m[1]);
        break;
    case preg_match('#^/kittybetu/public/users/(\d+)/update$#', $uri, $m) && $method === 'POST':
        if (!$isAuth) { header('Location: /kittybetu/public/login'); exit; }
        (new UserController($dbConfig))->update($m[1], $_POST);
        break;
    case preg_match('#^/kittybetu/public/users/(\d+)/password$#', $uri, $m) && $method === 'GET':
        if (!$isAuth) { header('Location: /kittybetu/public/login'); exit; }
        (new UserController($dbConfig))->changePasswordForm($m[1]);
        break;
    case preg_match('#^/kittybetu/public/users/(\d+)/password$#', $uri, $m) && $method === 'POST':
        if (!$isAuth) { header('Location: /kittybetu/public/login'); exit; }
        (new UserController($dbConfig))->changePassword($m[1], $_POST);
        break;
    case preg_match('#^/kittybetu/public/users/(\d+)/delete$#', $uri, $m) && $method === 'POST':
        if (!$isAuth) { header('Location: /kittybetu/public/login'); exit; }
        (new UserController($dbConfig))->delete($m[1]);
        break;
    default:
        http_response_code(404);
        echo 'Página não encontrada.';
        break;
}
