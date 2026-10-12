<?php $headBack = '/admin/categories'; require BASE_PATH . '/app/Views/partials/admin_head.php'; ?>
<form action="<?= url('/admin/categories/' . $item['id']) ?>" method="post" enctype="multipart/form-data" data-validate>
    <?= csrf_field() ?>
    <?php require __DIR__ . '/_form.php'; ?>
    <div class="admin-actions"><a href="<?= url('/admin/categories') ?>" class="btn">Huỷ</a><button type="submit" class="btn btn--solid">Cập nhật</button></div>
</form>
