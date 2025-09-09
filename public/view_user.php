<?php
$pageTitle = 'Ver usuário';
require_once __DIR__.'/../src/middleware/AuthGuard.php';
require_once __DIR__.'/../src/classes/UserManager.php';
require_once __DIR__.'/partials/header.php';

$users = new UserManager();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$u = $id ? $users->findById($id) : null;
if (!$u) {
    Response::flash('error', 'Usuário não encontrado.');
    Response::redirect(APP_URL.'/users.php');
}
?>
<section class="card">
  <h1>Usuário #<?= e($u['id']) ?></h1>
  <div class="grid col-2">
    <div>
      <p><strong>Nome:</strong> <?= e($u['nome']) ?></p>
      <p><strong>Email:</strong> <?= e($u['email']) ?></p>
      <p><strong>Status:</strong> <?= e($u['status']) ?></p>
    </div>
    <div>
      <p><strong>Telefone:</strong> <?= e($u['telefone'] ?? '-') ?></p>
      <p><strong>CPF:</strong> <?= e($u['cpf'] ?? '-') ?></p>
      <p><strong>Nascimento:</strong> <?= e($u['data_nascimento'] ?? '-') ?></p>
    </div>
  </div>
  <div class="actions">
    <a class="btn" href="<?= e(APP_URL) ?>/users.php">Voltar</a>
  </div>
</section>
<?php require_once __DIR__.'/partials/footer.php'; ?>
