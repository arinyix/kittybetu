<?php
$pageTitle = 'Perfil';
require_once __DIR__.'/../src/middleware/AuthGuard.php';
require_once __DIR__.'/../src/classes/UserManager.php';
require_once __DIR__.'/partials/header.php';
require_once __DIR__.'/../src/lib/Response.php';

$users = new UserManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRF::check('profile_form', $_POST['csrf_token'] ?? '')) {
        Response::flash('error', 'CSRF inválido.');
        Response::redirect(APP_URL.'/profile.php');
    }
    $ok = $users->updateProfile((int)$__user['id'], [
        'nome' => trim($_POST['nome'] ?? ''),
        'telefone' => trim($_POST['telefone'] ?? ''),
        'data_nascimento' => $_POST['data_nascimento'] ?? null,
    ]);
    if ($ok) {
        Response::flash('success', 'Perfil atualizado.');
        Response::redirect(APP_URL.'/profile.php');
    } else {
        Response::flash('error', 'Falha ao atualizar.');
        Response::redirect(APP_URL.'/profile.php');
    }
}
$u = $users->findById((int)$__user['id']);
?>
<section class="card">
  <h1>Editar perfil</h1>
  <form method="post">
    <?= CSRF::input('profile_form') ?>
    <div class="grid col-2">
      <div>
        <label for="nome">Nome</label>
        <input id="nome" name="nome" value="<?= e($u['nome'] ?? '') ?>" required>
      </div>
      <div>
        <label for="email">Email (somente leitura)</label>
        <input id="email" value="<?= e($u['email'] ?? '') ?>" disabled>
      </div>
      <div>
        <label for="telefone">Telefone</label>
        <input id="telefone" name="telefone" data-mask="phone" value="<?= e($u['telefone'] ?? '') ?>">
      </div>
      <div>
        <label for="data_nascimento">Data de nascimento</label>
        <input id="data_nascimento" name="data_nascimento" type="date" value="<?= e($u['data_nascimento'] ?? '') ?>">
      </div>
    </div>
    <button type="submit">Salvar</button>
  </form>
</section>
<?php require_once __DIR__.'/partials/footer.php'; ?>
