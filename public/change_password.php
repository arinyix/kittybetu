<?php
$pageTitle = 'Alterar senha';
require_once __DIR__.'/../src/middleware/AuthGuard.php';
require_once __DIR__.'/../src/classes/UserManager.php';
require_once __DIR__.'/partials/header.php';
require_once __DIR__.'/../src/lib/Response.php';

$users = new UserManager();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRF::check('pw_form', $_POST['csrf_token'] ?? '')) {
        Response::flash('error', 'CSRF inválido.');
        Response::redirect(APP_URL.'/change_password.php');
    }
    $res = $users->changePassword((int)$__user['id'], (string)($_POST['senha_atual'] ?? ''), (string)($_POST['nova_senha'] ?? ''));
    if ($res['ok']) {
        Response::flash('success', 'Senha alterada.');
    } else {
        Response::flash('error', $res['error'] ?? 'Erro ao alterar.');
    }
    Response::redirect(APP_URL.'/change_password.php');
}
?>
<section class="card">
  <h1>Alterar senha</h1>
  <form method="post">
    <?= CSRF::input('pw_form') ?>
    <label for="senha_atual">Senha atual</label>
    <input id="senha_atual" name="senha_atual" type="password" required>
    <label for="nova_senha">Nova senha</label>
    <input id="nova_senha" name="nova_senha" type="password" minlength="6" required>
    <button type="submit">Salvar</button>
  </form>
</section>
<?php require_once __DIR__.'/partials/footer.php'; ?>
