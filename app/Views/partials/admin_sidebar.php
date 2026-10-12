<?php
$adminLinks = [
    ['/admin', 'home', 'Tổng quan'],
    ['/admin/orders', 'receipt', 'Đơn hàng'],
    ['/admin/foods', 'tag', 'Món ăn'],
    ['/admin/categories', 'grid', 'Danh mục'],
    ['/admin/users', 'users', 'Người dùng'],
];
?>
<aside class="admin-sidebar">
    <a href="<?= url('/admin') ?>" class="admin-logo">YUMIO <span>Admin</span></a>
    <nav class="admin-nav">
        <?php foreach ($adminLinks as [$path, $iconName, $label]): ?>
            <?php $active = $path === '/admin' ? current_path() === '/admin' : is_active($path); ?>
            <a href="<?= url($path) ?>" class="<?= $active ? 'is-active' : '' ?>" <?= $active ? 'aria-current="page"' : '' ?>><?= icon($iconName, 20) ?><span><?= $label ?></span></a>
        <?php endforeach; ?>
    </nav>
    <div class="admin-sidebar__foot">
        <a href="<?= url('/') ?>"><?= icon('left', 18) ?><span>Về trang web</span></a>
        <form action="<?= url('/logout') ?>" method="post">
            <?= csrf_field() ?>
            <button type="submit"><?= icon('logout', 18) ?><span>Đăng xuất</span></button>
        </form>
    </div>
</aside>
