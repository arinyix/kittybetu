<?php
declare(strict_types=1);

require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../lib/Response.php';
require_once __DIR__.'/../lib/CSRF.php';
require_once __DIR__.'/../classes/Auth.php';

$__auth = new Auth();
if (!$__auth->check()) {
    Response::flash('error', 'Faça login para continuar.');
    Response::redirect(APP_URL.'/index.php');
}

// Disponibiliza o usuário logado
$__user = $__auth->user();
