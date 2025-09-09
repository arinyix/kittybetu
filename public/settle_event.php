<?php
$pageTitle = 'Liquidar evento';
require_once __DIR__.'/../src/middleware/AuthGuard.php';
require_once __DIR__.'/../src/classes/BetManager.php';
require_once __DIR__.'/partials/header.php';
require_once __DIR__.'/../src/lib/Response.php';

$isAdmin = (strcasecmp($__user['email'],'admin@kittybetu.com')===0);
if (!$isAdmin) { Response::flash('error','Somente admin.'); Response::redirect(APP_URL.'/dashboard.php'); }

$bm = new BetManager();

/* Etapa 2: POST para liquidar */
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '') === 'settle') {
  if (!CSRF::check('settle_event_form', $_POST['csrf_token'] ?? '')) {
    Response::flash('error','CSRF inválido.'); Response::redirect(APP_URL.'/settle_event.php');
  }
  $evento_id = (int)($_POST['evento_id'] ?? 0);
  $sel_id    = (int)($_POST['winning_id'] ?? 0);
  $res = $bm->settleEvent((int)$__user['id'], (string)$__user['email'], $evento_id, $sel_id);
  if ($res['ok']) Response::flash('success','Evento liquidado com sucesso.');
  else            Response::flash('error', $res['error'] ?? 'Falha ao liquidar.');
  Response::redirect(APP_URL.'/settle_event.php');
  exit;
}

/* Etapa 1: escolher evento (GET) */
$abertos = $bm->listOpen();
$eid = isset($_GET['evento']) ? (int)$_GET['evento'] : 0;

/* Auto-seleção: se só existe 1 evento aberto e nenhum ?evento=... */
if ($eid === 0 && count($abertos) === 1) {
  $eid = (int)$abertos[0]['id'];
}

/* Carrega o evento selecionado (se houver) */
$ev  = $eid ? $bm->getEvent($eid) : null;
?>
<section class="card">
  <h1>Liquidar evento (admin)</h1>

  <?php if (empty($abertos)): ?>
    <p class="muted">Nenhum evento aberto no momento.</p>
  <?php else: ?>
    <form method="get" action="<?= e(APP_URL) ?>/settle_event.php" class="row">
      <div class="w-100">
        <label>Escolha o evento</label>
        <select name="evento" required onchange="this.form.submit()">
          <option value="">Selecione...</option>
          <?php foreach ($abertos as $e): ?>
            <option value="<?= e($e['id']) ?>" <?= $eid===(int)$e['id']?'selected':'' ?>>
              <?= e($e['titulo']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn-secondary">Carregar</button>
    </form>
  <?php endif; ?>
</section>

<?php if ($ev): ?>
  <section class="card mt-16">
    <h2><?= e($ev['titulo']) ?></h2>
    <?php if (empty($ev['selecoes'])): ?>
      <p class="muted">Este evento não possui seleções cadastradas.</p>
    <?php else: ?>
      <form method="post">
        <?= CSRF::input('settle_event_form') ?>
        <input type="hidden" name="action" value="settle">
        <input type="hidden" name="evento_id" value="<?= e($ev['id']) ?>">

        <?php foreach ($ev['selecoes'] as $s): ?>
          <label style="display:block;margin:.35rem 0">
            <input type="radio" name="winning_id" value="<?= e($s['id']) ?>" required>
            <?= e($s['rotulo']) ?> — odd <?= e(number_format((float)$s['odd'],2)) ?>x
          </label>
        <?php endforeach; ?>

        <button type="submit">Liquidar</button>
      </form>
    <?php endif; ?>
  </section>
<?php endif; ?>

<?php require_once __DIR__.'/partials/footer.php'; ?>
