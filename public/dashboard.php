<?php
$pageTitle = 'Dashboard';
require_once __DIR__.'/../src/middleware/AuthGuard.php';
require_once __DIR__.'/partials/header.php';
?>
<section class="card">
  <h1>Olá, <?= e($__user['nome'] ?? 'Usuário') ?> 👋</h1>
  <p class="muted">Bem-vindo(a) ao kittybetU. Este painel é didático, sem transações reais.</p>
  <div class="actions">
    <a class="btn" href="<?= e(APP_URL) ?>/users.php">Gerenciar usuários</a>
    <a class="btn" href="<?= e(APP_URL) ?>/profile.php">Editar perfil</a>
    <a class="btn" href="<?= e(APP_URL) ?>/change_password.php">Alterar senha</a>
  </div>
</section>

<section class="grid col-2">
  <div class="card">
    <h2>Conta</h2>
    <p class="muted">Sua conta foi criada automaticamente com saldo 0. Use esta base para futuros módulos.</p>
  </div>
  <div class="card">
    <h2>Segurança</h2>
    <ul class="muted">
      <li>JWT (cookie HttpOnly + verificação no banco)</li>
      <li>CSRF em todos os POST</li>
      <li>Prepared statements (PDO)</li>
      <li>XSS escape com <code>htmlspecialchars</code></li>
    </ul>
  </div>
</section>
<?php require_once __DIR__.'/partials/footer.php'; ?>
