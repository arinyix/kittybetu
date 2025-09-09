<?php
require_once __DIR__.'/../src/middleware/AuthGuard.php';
require_once __DIR__.'/../src/classes/UserManager.php';
require_once __DIR__.'/../src/lib/Response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::flash('error', 'Método inválido.');
    Response::redirect(APP_URL.'/users.php');
}

$id = (int)($_POST['id'] ?? 0);
$formKey = 'del_user_'.$id;
if (!CSRF::check($formKey, $_POST['csrf_token'] ?? '')) {
    Response::flash('error', 'CSRF inválido.');
    Response::redirect(APP_URL.'/users.php');
}

$users = new UserManager();
$res = $users->deleteUser((int)$__user['id'], $id, (string)$__user['email']);
if ($res['ok']) {
    // se deletar a si mesmo, desloga
    if ($id === (int)$__user['id']) {
        require_once __DIR__.'/../src/classes/Auth.php';
        (new Auth())->logout();
        Response::flash('success', 'Sua conta foi excluída.');
        Response::redirect(APP_URL.'/index.php');
    }
    Response::flash('success', 'Usuário excluído.');
} else {
    Response::flash('error', $res['error'] ?? 'Falha ao excluir.');
}
Response::redirect(APP_URL.'/users.php');
