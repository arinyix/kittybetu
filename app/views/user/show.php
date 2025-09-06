<?php
require __DIR__ . '/../partials/header.php';
?>
<h2>Usuário</h2>
<table class="table">
    <tr><th>ID</th><td><?= escape($user['id']) ?></td></tr>
    <tr><th>Nome</th><td><?= escape($user['name']) ?></td></tr>
    <tr><th>Email</th><td><?= escape($user['email']) ?></td></tr>
    <tr><th>CPF</th><td><?= escape($user['cpf']) ?></td></tr>
    <tr><th>Telefone</th><td><?= escape($user['phone']) ?></td></tr>
    <tr><th>Nascimento</th><td><?= escape($user['birth_date']) ?></td></tr>
    <tr><th>Função</th><td><?= escape($user['role']) ?></td></tr>
</table>
<p>
    <a href="/kittybetu/public/users/<?= escape($user['id']) ?>/edit" class="btn">Editar</a>
    <a href="/kittybetu/public/users/<?= escape($user['id']) ?>/password" class="btn">Trocar Senha</a>
</p>
<?php require __DIR__ . '/../partials/footer.php'; ?>
