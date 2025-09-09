<?php
$pageTitle = 'Minhas apostas';
require_once __DIR__.'/../src/middleware/AuthGuard.php';
require_once __DIR__.'/../src/classes/BetManager.php';
require_once __DIR__.'/partials/header.php';

$bm = new BetManager();
$bets = $bm->listUserBets((int)$__user['id']);
?>
<section class="card">
  <h1>Minhas apostas</h1>
  <table class="table">
    <thead>
      <tr><th>#</th><th>Evento</th><th>Seleção</th><th>Valor</th><th>Odd</th><th>Potencial</th><th>Status</th><th>Ações</th></tr>
    </thead>
    <tbody>
      <?php foreach ($bets as $b): 
        $canCancel = ($b['status']==='aberta' && $b['evento_status']==='aberto' && (empty($b['fecha_em']) || strtotime((string)$b['fecha_em'])>time()));
      ?>
      <tr>
        <td><?= e($b['id']) ?></td>
        <td><?= e($b['titulo']) ?></td>
        <td><?= e($b['rotulo']) ?></td>
        <td>R$ <?= e(number_format((float)$b['valor'],2,',','.')) ?></td>
        <td><?= e(number_format((float)$b['odd'],2)) ?>x</td>
        <td>R$ <?= e(number_format((float)$b['retorno_potencial'],2,',','.')) ?></td>
        <td>
          <?php if ($b['status']==='aberta'): ?>
            <span class="badge s-aberta"><span class="dot" style="background:#ffb020"></span>Aberta</span>
          <?php elseif ($b['status']==='ganha'): ?>
            <span class="badge s-ganha"><span class="dot" style="background:#18d26e"></span>Ganha</span>
          <?php elseif ($b['status']==='perdida'): ?>
            <span class="badge s-perdida"><span class="dot" style="background:#ff3860"></span>Perdida</span>
          <?php else: ?>
            <span class="badge s-cancelada"><span class="dot" style="background:#ffcc66"></span>Cancelada</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($canCancel): ?>
          <form action="<?= e(APP_URL) ?>/cancel_bet.php" method="post" onsubmit="return confirm('Cancelar esta aposta e estornar o valor?');" style="display:inline">
            <?= CSRF::input('cancel_bet_'.$b['id']) ?>
            <input type="hidden" name="bet_id" value="<?= e($b['id']) ?>">
            <button type="submit" class="btn-secondary">Cancelar</button>
          </form>
          <?php else: ?>
            <span class="muted">—</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php require_once __DIR__.'/partials/footer.php'; ?>
