<?php
require __DIR__ . '/../partials/header.php';
$errors = $errors ?? [];
?>
<h2>Trocar Senha</h2>
<?php require __DIR__ . '/../partials/alerts.php'; ?>
<form method="POST" action="/kittybetu/public/users/<?= escape($user['id']) ?>/password" class="form">
    <label for="password">Nova Senha</label>
    <input type="password" name="password" id="password" required minlength="8">
    <input type="hidden" name="csrf_token" value="<?= escape(csrf_token()) ?>">
    <button type="submit" class="btn">Alterar</button>
</form>
<?php require __DIR__ . '/../partials/footer.php'; ?>
