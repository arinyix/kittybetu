<?php
require __DIR__ . '/../partials/header.php';
$errors = $errors ?? [];
?>
<h2>Editar Usuário</h2>
<?php require __DIR__ . '/../partials/alerts.php'; ?>
<form method="POST" action="/kittybetu/public/users/<?= escape($user['id']) ?>/update" class="form">
    <label for="name">Nome</label>
    <input type="text" name="name" id="name" required maxlength="120" value="<?= escape($user['name']) ?>">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" required maxlength="180" value="<?= escape($user['email']) ?>">
    <label for="cpf">CPF</label>
    <input type="text" name="cpf" id="cpf" required maxlength="14" value="<?= escape($user['cpf']) ?>">
    <label for="phone">Telefone</label>
    <input type="text" name="phone" id="phone" required maxlength="20" value="<?= escape($user['phone']) ?>">
    <label for="birth_date">Data de Nascimento</label>
    <input type="date" name="birth_date" id="birth_date" required value="<?= escape($user['birth_date']) ?>">
    <input type="hidden" name="csrf_token" value="<?= escape(csrf_token()) ?>">
    <button type="submit" class="btn">Salvar</button>
</form>
<?php require __DIR__ . '/../partials/footer.php'; ?>
