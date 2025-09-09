<?php
declare(strict_types=1);

/**
 * Configurações de app e sessão
 */
define('APP_URL', 'http://localhost/kittybetu/public');
define('APP_NAME', 'kittybetU');
define('APP_TIMEZONE', 'America/Santarem');

date_default_timezone_set(APP_TIMEZONE);

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);


// Banco
define('DB_HOST', 'localhost');
define('DB_NAME', 'kittybetu_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Segurança
define('JWT_SECRET', 'YOUR_SECRET_KEY_HERE'); // troque por um segredo longo/aleatório
define('JWT_ISS', 'kittybetu.local');
define('JWT_EXP_SECONDS', 7200); // 2h

// Sessão endurecida
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,        // true se for HTTPS
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('kittybetu_sess');
    session_start();
}

// Helpers globais
function e(?string $v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

