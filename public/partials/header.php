<?php
require_once __DIR__.'/../../src/config/config.php';
require_once __DIR__.'/../../src/lib/Response.php';
require_once __DIR__.'/../../src/lib/CSRF.php';
$flashes = Response::consumeFlash();

/* versão do arquivo p/ quebrar cache */
$__cssUrl  = '/kittybetu/public/assets/css/style.css';
$__cssFile = __DIR__.'/../assets/css/style.css';
$__v = is_file($__cssFile) ? (string)filemtime($__cssFile) : '1';
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title><?= isset($pageTitle) ? e($pageTitle).' | ' : '' ?>kittybetU</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?= $__cssUrl ?>?v=<?= $__v ?>">
</head>
<body>
<header class="topbar">
  <div class="container">
    <a class="brand" href="<?= e(APP_URL) ?>/dashboard.php">kittybetU</a>
    <nav class="nav">
      <a href="<?= e(APP_URL) ?>/dashboard.php">Dashboard</a>
      <a href="<?= e(APP_URL) ?>/users.php">Usuários</a>
      <a href="<?= e(APP_URL) ?>/profile.php">Perfil</a>
      <a href="<?= e(APP_URL) ?>/change_password.php">Senha</a>
      <a class="logout" href="<?= e(APP_URL) ?>/logout.php">Sair</a>
    </nav>
  </div>
</header>

<?php if (!empty($flashes)): ?>
  <div class="container flash">
    <?php foreach ($flashes as $f): ?>
      <div class="alert <?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<main class="container">
