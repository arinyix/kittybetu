<?php
$pageTitle = 'Apostas';
require_once __DIR__.'/../src/middleware/AuthGuard.php';
require_once __DIR__.'/../src/classes/BetManager.php';
require_once __DIR__.'/partials/header.php';

$bm = new BetManager();
$eventos = $bm->listOpen();
?>
<section class="card">
  <h1>Apostas abertas</h1>
  <?php if (!$eventos): ?>
    <p class="muted">Sem eventos abertos.</p>
  <?php else: ?>
    <?php foreach ($eventos as $ev): ?>
      <div class="card" style="margin-top:12px">
        <h2><?= e($ev['titulo']) ?></h2>
        <?php if (!empty($ev['descricao'])): ?><p class="muted"><?= e($ev['descricao']) ?></p><?php endif; ?>
        <?php if (!empty($ev['fecha_em'])): ?><p class="muted">Fecha em: <?= e($ev['fecha_em']) ?></p><?php endif; ?>
        <div class="grid col-2">
        <?php foreach ($ev['selecoes'] as $sel): ?>
          <div class="card">
            <strong><?= e($sel['rotulo']) ?></strong>
            <p>Odd: <span style="color:#ff66e7;font-weight:700"><?= e(number_format((float)$sel['odd'],2)) ?>x</span></p>
            <form action="<?= e(APP_URL) ?>/place_bet.php" method="post">
              <?= CSRF::input('bet_sel_'.$sel['id']) ?>
              <input type="hidden" name="selecao_id" value="<?= e($sel['id']) ?>">
              <label>Valor (R$)</label>
              <input type="number" name="valor" min="1" step="0.01" required>
              <button type="submit">Apostar</button>
            </form>
          </div>
        <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</section>
<?php require_once __DIR__.'/partials/footer.php'; ?>
