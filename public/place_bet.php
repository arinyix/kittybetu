<?php
require_once __DIR__.'/../src/middleware/AuthGuard.php';
require_once __DIR__.'/../src/classes/BetManager.php';
require_once __DIR__.'/../src/lib/Response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  Response::flash('error','Método inválido.');
  Response::redirect(APP_URL.'/bets.php');
}

$sel = (int)($_POST['selecao_id'] ?? 0);
$formKey = 'bet_sel_'.$sel;
if (!CSRF::check($formKey, $_POST['csrf_token'] ?? '')) {
  Response::flash('error','CSRF inválido.');
  Response::redirect(APP_URL.'/bets.php');
}
$valor = (float)($_POST['valor'] ?? 0);

$bm = new BetManager();
$res = $bm->placeBet((int)$__user['id'], $sel, $valor);
if ($res['ok']) {
  Response::flash('success','Aposta criada com sucesso!');
} else {
  Response::flash('error',$res['error'] ?? 'Falha ao apostar.');
}
Response::redirect(APP_URL.'/bets.php');
