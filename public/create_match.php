<?php
$pageTitle = 'Criar partida';
require_once __DIR__.'/../src/middleware/AuthGuard.php';
require_once __DIR__.'/../src/classes/BetManager.php';
require_once __DIR__.'/partials/header.php';
require_once __DIR__.'/../src/lib/Response.php';

$isAdmin = (strcasecmp($__user['email'],'admin@kittybetu.com')===0);
if (!$isAdmin) { Response::flash('error','Somente admin.'); Response::redirect(APP_URL.'/dashboard.php'); }

$bm = new BetManager();

if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!CSRF::check('create_match_form', $_POST['csrf_token'] ?? '')) {
    Response::flash('error','CSRF inválido.'); Response::redirect(APP_URL.'/create_match.php');
  }
  $mandante = trim($_POST['mandante'] ?? '');
  $visitante = trim($_POST['visitante'] ?? '');
  $fecha = $_POST['fecha_em'] ?: null;
  $mercado = $_POST['mercado'] ?? '1x2';

  $odds = [];
  if ($mercado === '1x2') {
    $odds = ['odd1'=>(float)($_POST['odd1'] ?? 0), 'oddx'=>(float)($_POST['oddx'] ?? 0), 'odd2'=>(float)($_POST['odd2'] ?? 0)];
  } elseif ($mercado === 'dupla_chance') {
    $odds = ['odd1x'=>(float)($_POST['odd1x'] ?? 0), 'oddx2'=>(float)($_POST['oddx2'] ?? 0), 'odd12'=>(float)($_POST['odd12'] ?? 0)];
  } else {
    $odds = ['odd1'=>(float)($_POST['odd1v'] ?? 0), 'odd2'=>(float)($_POST['odd2v'] ?? 0)];
  }

  $r = $bm->createMatchEvent((int)$__user['id'], $mandante, $visitante, $fecha, $mercado, $odds);
  if ($r['ok']) { Response::flash('success','Partida criada.'); Response::redirect(APP_URL.'/bets.php'); }
  else { Response::flash('error',$r['error'] ?? 'Falha ao criar.'); Response::redirect(APP_URL.'/create_match.php'); }
}
?>
<section class="card">
  <h1>Criar partida</h1>
  <form method="post" class="form-grid" id="matchForm">
    <?= CSRF::input('create_match_form') ?>
    <div>
      <label>Time mandante</label>
      <input name="mandante" required>
    </div>
    <div>
      <label>Time visitante</label>
      <input name="visitante" required>
    </div>
    <div>
      <label>Fecha em</label>
      <input type="datetime-local" name="fecha_em">
    </div>
    <div>
      <label>Mercado</label>
      <select name="mercado" id="mercadoSel">
        <option value="1x2">1X2 (mandante/empate/visitante)</option>
        <option value="dupla_chance">Dupla chance (1X, X2, 12)</option>
        <option value="vencedor">Vencedor (sem empate)</option>
      </select>
    </div>

    <!-- Odds 1X2 -->
    <div class="card odds-1x2">
      <h3>Odds — 1X2</h3>
      <label>Odd Mandante (1)</label>
      <input name="odd1" type="number" step="0.01" min="1.01">
      <label>Odd Empate (X)</label>
      <input name="oddx" type="number" step="0.01" min="1.01">
      <label>Odd Visitante (2)</label>
      <input name="odd2" type="number" step="0.01" min="1.01">
    </div>

    <!-- Odds Dupla Chance -->
    <div class="card odds-dc" style="display:none">
      <h3>Odds — Dupla chance</h3>
      <label>Odd 1X</label>
      <input name="odd1x" type="number" step="0.01" min="1.01">
      <label>Odd X2</label>
      <input name="oddx2" type="number" step="0.01" min="1.01">
      <label>Odd 12</label>
      <input name="odd12" type="number" step="0.01" min="1.01">
    </div>

    <!-- Odds Vencedor -->
    <div class="card odds-v" style="display:none">
      <h3>Odds — Vencedor</h3>
      <label>Odd Mandante</label>
      <input name="odd1v" type="number" step="0.01" min="1.01">
      <label>Odd Visitante</label>
      <input name="odd2v" type="number" step="0.01" min="1.01">
    </div>

    <div class="right">
      <button type="submit">Criar</button>
    </div>
  </form>
  <p class="helper mt-12">Preencha apenas o bloco de odds referente ao mercado escolhido.</p>
</section>
<?php require_once __DIR__.'/partials/footer.php'; ?>
