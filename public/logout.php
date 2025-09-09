<?php
require_once __DIR__.'/../src/config/config.php';
require_once __DIR__.'/../src/lib/Response.php';
require_once __DIR__.'/../src/classes/Auth.php';

$auth = new Auth();
$auth->logout();
Response::flash('success', 'Você saiu com segurança.');
Response::redirect(APP_URL.'/index.php');
