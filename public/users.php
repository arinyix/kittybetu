<?php
$pageTitle = 'Usuários';
require_once __DIR__.'/../src/middleware/AuthGuard.php';
require_once __DIR__.'/../src/classes/UserManager.php';
require_once __DIR__.'/partials/header.php';

$users = new UserManager();
$list = $users->listAll();
?>
<section class="card">
  <h1>Usuários</h1>
  <table class="table">
    <thead>
      <tr><th>ID</th><th>Nome</th><th>Email</th><th>Status</th><th>Criado</th><th>Ações</th></tr>
    </thead>
    <tbody>
      <?php foreach ($list as $u): ?>
        <tr>
          <td><?= e($u['id']) ?></td>
          <td><?= e($u['nome']) ?></td>
          <td><?= e($u['email']) ?></td>
          <td><?= e($u['status']) ?></td>
          <td><?= e($u['created_at']) ?></td>
          <td class="actions">
            <a class="btn btn-secondary" href="<?= e(APP_URL) ?>/view_user.php?id=<?= e($u['id']) ?>">Ver</a>
            <?php if (strcasecmp($__user['email'],'admin@kittybetu.com')===0 || (int)$__user['id']===(int)$u['id']): ?>
            <form action="<?= e(APP_URL) ?>/delete_user.php" method="post" onsubmit="return confirm('Confirmar exclusão?');" style="display:inline">
              <?= CSRF::input('del_user_'.$u['id']) ?>
              <input type="hidden" name="id" value="<?= e($u['id']) ?>">
              <button type="submit">Excluir</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php require_once __DIR__.'/partials/footer.php'; ?>
