<?php
$pageTitle = 'Criar evento';
require_once __DIR__.'/../src/middleware/AuthGuard.php';
require_once __DIR__.'/../src/classes/BetManager.php';
require_once __DIR__.'/partials/header.php';
require_once __DIR__.'/../src/lib/Response.php';

$isAdmin = (strcasecmp($__user['email'],'admin@kittybetu.com')===0);
if (!$isAdmin) { Response::flash('error','Somente admin.'); Response::redirect(APP_URL.'/dashboard.php'); }

$bm = new BetManager();

if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!CSRF::check('create_event_form', $_POST['csrf_token'] ?? '')) {
    Response::flash('error','CSRF inválido.'); Response::redirect(APP_URL.'/create_event.php');
  }
  $titulo = trim($_POST['titulo'] ?? '');
  $descricao = trim($_POST['descricao'] ?? '');
  $fecha = $_POST['fecha_em'] ?: null;

  $r = $bm->createEvent((int)$__user['id'], $titulo, $descricao, $fecha);
  if (!$r['ok']) { Response::flash('error',$r['error']); Response::redirect(APP_URL.'/create_event.php'); }
  $eid = (int)$r['id'];

  // 3 linhas de seleções por padrão
  for ($i=1;$i<=3;$i++){
    $rot = trim($_POST["rotulo_$i"] ?? '');
    $odd = (float)($_POST["odd_$i"] ?? 0);
    if ($rot !== '' && $odd > 0) $bm->addSelection($eid, $rot, $odd);
  }
  Response::flash('success','Evento criado.');
  Response::redirect(APP_URL.'/bets.php');
}
?>
<section class="card">
  <h1>Novo evento</h1>
  <form method="post">
    <?= CSRF::input('create_event_form') ?>
    <label>Título</label>
    <input name="titulo" required>
    <label>Descrição (opcional)</label>
    <textarea name="descricao" rows="3"></textarea>
    <label>Fecha em (opcional)</label>
    <input type="datetime-local" name="fecha_em">
    <h2>Seleções</h2>
    <div class="grid col-2">
      <?php for ($i=1;$i<=3;$i++): ?>
        <div class="card">
          <label>Rótulo <?= $i ?> (ex.: "Time A")</label>
          <input name="rotulo_<?= $i ?>">
          <label>Odd (ex.: 1.50)</label>
          <input name="odd_<?= $i ?>" type="number" step="0.01" min="1.01">
        </div>
      <?php endfor; ?>
    </div>
    <button type="submit">Criar</button>
  </form>
</section>
<?php require_once __DIR__.'/partials/footer.php'; ?>
