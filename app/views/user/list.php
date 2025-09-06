<?php
require __DIR__ . '/../partials/header.php';
?>
<h2>Usuários</h2>
<form method="GET" action="/kittybetu/public/users" class="form-inline">
    <input type="text" name="q" placeholder="Buscar por nome ou email" value="<?= escape($_GET['q'] ?? '') ?>">
    <button type="submit" class="btn">Buscar</button>
</form>
<table class="table">
    <thead>
        <tr>
            <th>ID</th><th>Nome</th><th>Email</th><th>CPF</th><th>Telefone</th><th>Nascimento</th><th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= escape($u['id']) ?></td>
            <td><?= escape($u['name']) ?></td>
            <td><?= escape($u['email']) ?></td>
            <td><?= escape($u['cpf']) ?></td>
            <td><?= escape($u['phone']) ?></td>
            <td><?= escape($u['birth_date']) ?></td>
            <td>
                <a href="/kittybetu/public/users/<?= escape($u['id']) ?>">Ver</a> |
                <a href="/kittybetu/public/users/<?= escape($u['id']) ?>/edit">Editar</a> |
                <form method="POST" action="/kittybetu/public/users/<?= escape($u['id']) ?>/delete" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= escape(csrf_token()) ?>">
                    <button type="submit" class="btn btn-warn" onclick="return confirm('Excluir usuário?')">Excluir</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../partials/footer.php'; ?>
