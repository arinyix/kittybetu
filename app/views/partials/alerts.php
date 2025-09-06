<?php if (!empty($errors)): ?>
<div class="alert alert-err">
    <?php foreach ($errors as $err): ?>
        <div><?= escape($err) ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php if (!empty($success)): ?>
<div class="alert alert-ok">
    <?= escape($success) ?>
</div>
<?php endif; ?>
