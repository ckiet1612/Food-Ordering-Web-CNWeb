<?php /** Tiêu đề trang admin. Biến vào: $title, tuỳ chọn $headAction = ['url', 'label'], $headBack */ ?>
<header class="admin-head">
    <div>
        <?php if (!empty($headBack)): ?>
            <a href="<?= url($headBack) ?>" class="admin-back"><?= icon('left', 16) ?> Quay lại</a>
        <?php endif; ?>
        <h1><?= e($title) ?></h1>
    </div>
    <?php if (!empty($headAction)): ?>
        <a href="<?= url($headAction[0]) ?>" class="btn btn--solid"><?= icon('plus', 18) ?> <?= e($headAction[1]) ?></a>
    <?php endif; ?>
</header>
