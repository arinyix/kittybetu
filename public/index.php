<?php
$pageTitle = 'Login';
require_once __DIR__.'/partials/header.php';
require_once __DIR__.'/../src/classes/Auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRF::check('login_form', $_POST['csrf_token'] ?? '')) {
        Response::flash('error', 'CSRF inválido. Atualize a página.');
        Response::redirect(APP_URL.'/index.php');
    }
    $auth = new Auth();
    $res = $auth->login(trim($_POST['email'] ?? ''), (string)($_POST['senha'] ?? ''));
    if ($res['ok']) {
        Response::flash('success', 'Bem-vindo(a)!');
        Response::redirect(APP_URL.'/dashboard.php');
    } else {
        Response::flash('error', $res['error'] ?? 'Falha no login.');
        Response::redirect(APP_URL.'/index.php');
    }
}
?>
<div class="grid col-2">
  <section class="card">
    <h1>Entrar</h1>
    <form method="post" novalidate>
      <?= CSRF::input('login_form') ?>
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required autocomplete="email" placeholder="seu@email.com">
      <label for="senha">Senha</label>
      <input id="senha" name="senha" type="password" minlength="6" required autocomplete="current-password">
      <button type="submit">Entrar</button>
    </form>
    <p class="helper">Ainda não tem conta? <a class="btn" href="<?= e(APP_URL) ?>/register.php">Cadastrar</a></p>
  </section>
  <section class="card">
    <h2>Sobre o projeto</h2>
    <p class="muted">Demonstração acadêmica com foco em segurança (PDO, CSRF, XSS, JWT) e UI neon.</p>
  </section>
</div>
<?php require_once __DIR__.'/partials/footer.php'; ?>
