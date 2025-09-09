<?php
require_once __DIR__.'/../src/middleware/AuthGuard.php';
require_once __DIR__.'/../src/classes/BetManager.php';
require_once __DIR__.'/../src/lib/Response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  Response::flash('error','Método inválido.');
  Response::redirect(APP_URL.'/my_bets.php');
}

$bid = (int)($_POST['bet_id'] ?? 0);
$formKey = 'cancel_bet_'.$bid;
if (!CSRF::check($formKey, $_POST['csrf_token'] ?? '')) {
  Response::flash('error','CSRF inválido.');
  Response::redirect(APP_URL.'/my_bets.php');
}

$bm = new BetManager();
$res = $bm->cancelBet((int)$__user['id'], $bid);
if ($res['ok']) Response::flash('success','Aposta cancelada e estornada.');
else Response::flash('error', $res['error'] ?? 'Falha ao cancelar.');
Response::redirect(APP_URL.'/my_bets.php');
