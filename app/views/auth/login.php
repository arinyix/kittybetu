<?php
require __DIR__ . '/../partials/header.php';
$errors = $errors ?? [];
?>
<h2>Login</h2>
<?php require __DIR__ . '/../partials/alerts.php'; ?>
<form method="POST" action="/kittybetu/public/login" class="form">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" required autofocus>
    <label for="password">Senha</label>
    <input type="password" name="password" id="password" required minlength="8">
    <input type="hidden" name="csrf_token" value="<?= escape(csrf_token()) ?>">
    <button type="submit" class="btn">Entrar</button>
    <p><a href="/kittybetu/public/register">Criar conta</a></p>
</form>
<?php require __DIR__ . '/../partials/footer.php'; ?>
