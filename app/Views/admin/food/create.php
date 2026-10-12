<?php $headBack = '/admin/foods'; require BASE_PATH . '/app/Views/partials/admin_head.php'; ?>
<form action="<?= url('/admin/foods') ?>" method="post" enctype="multipart/form-data" data-validate>
    <?= csrf_field() ?>
    <?php require __DIR__ . '/_form.php'; ?>
    <div class="admin-actions"><a href="<?= url('/admin/foods') ?>" class="btn">Huỷ</a><button type="submit" class="btn btn--solid">Lưu món ăn</button></div>
</form>
