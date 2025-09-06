<?php
require __DIR__ . '/../partials/header.php';
$errors = $errors ?? [];
?>
<h2>Cadastro</h2>
<?php require __DIR__ . '/../partials/alerts.php'; ?>
<form method="POST" action="/kittybetu/public/register" class="form" autocomplete="off">
    <label for="name">Nome</label>
    <input type="text" name="name" id="name" required maxlength="120">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" required maxlength="180">
    <label for="cpf">CPF</label>
    <input type="text" name="cpf" id="cpf" required maxlength="14" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}">
    <small class="input-hint">Digite o CPF no formato <b>000.000.000-00</b>. Apenas CPFs válidos são aceitos.</small>
    <label for="phone">Telefone</label>
    <input type="text" name="phone" id="phone" required maxlength="20" pattern="\(\d{2}\) \d{4,5}-\d{4}">
    <label for="birth_date">Data de Nascimento</label>
    <input type="date" name="birth_date" id="birth_date" required>
    <label for="password">Senha</label>
    <input type="password" name="password" id="password" required minlength="8">
    <small class="input-hint">A senha deve ter <b>mínimo 8 caracteres</b>, conter <b>ao menos 1 letra</b> e <b>1 dígito</b>.</small>
    <input type="hidden" name="csrf_token" value="<?= escape(csrf_token()) ?>">
    <button type="submit" class="btn">Cadastrar</button>
    <p><a href="/kittybetu/public/login">Já tenho conta</a></p>
</form>
<?php require __DIR__ . '/../partials/footer.php'; ?>
