<?php
$pageTitle = 'Cadastro';
require_once __DIR__.'/partials/header.php';
require_once __DIR__.'/../src/classes/UserManager.php';
require_once __DIR__.'/../src/lib/Response.php';

$users = new UserManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRF::check('register_form', $_POST['csrf_token'] ?? '')) {
        Response::flash('error', 'CSRF inválido. Atualize a página.');
        Response::redirect(APP_URL.'/register.php');
    }
    $data = [
        'nome' => trim($_POST['nome'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'senha' => (string)($_POST['senha'] ?? ''),
        'telefone' => trim($_POST['telefone'] ?? ''),
        'data_nascimento' => $_POST['data_nascimento'] ?? null,
        'cpf' => trim($_POST['cpf'] ?? ''),
    ];
    $res = $users->create($data);
    if ($res['ok'] ?? false) {
        Response::flash('success', 'Conta criada! Faça login.');
        Response::redirect(APP_URL.'/index.php');
    } else {
        Response::flash('error', implode(' ', $res['errors'] ?? ['Erro ao cadastrar.']));
        Response::redirect(APP_URL.'/register.php');
    }
}
?>
<section class="card">
  <h1>Criar conta</h1>
  <form method="post" novalidate>
    <?= CSRF::input('register_form') ?>
    <div class="grid col-2">
      <div>
        <label for="nome">Nome</label>
        <input id="nome" name="nome" required>
      </div>
      <div>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required autocomplete="email">
      </div>
      <div>
        <label for="senha">Senha</label>
        <input id="senha" name="senha" type="password" minlength="6" required autocomplete="new-password">
      </div>
      <div>
        <label for="telefone">Telefone</label>
        <input id="telefone" name="telefone" data-mask="phone" placeholder="(xx) xxxxx-xxxx">
      </div>
      <div>
        <label for="data_nascimento">Data de nascimento</label>
        <input id="data_nascimento" name="data_nascimento" type="date">
      </div>
      <div>
        <label for="cpf">CPF</label>
        <input id="cpf" name="cpf" data-mask="cpf" placeholder="000.000.000-00">
      </div>
    </div>
    <button type="submit">Cadastrar</button>
  </form>
</section>
<?php require_once __DIR__.'/partials/footer.php'; ?>
