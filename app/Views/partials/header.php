<?php $navCategories = array_slice(App\Models\Category::ordered(), 0, 4); ?>
<header class="site-header" data-header>
    <div class="header-top">
        <div class="header-side">
            <button type="button" class="icon-btn nav-toggle" data-nav-open aria-label="Mở menu" aria-controls="mobile-nav">
                <?= icon('menu', 22) ?>
            </button>
            <a href="<?= url('/search') ?>" class="header-link header-link--search">
                <?= icon('search', 18) ?><span>Tìm món</span>
            </a>
        </div>

        <a href="<?= url('/') ?>" class="logo" aria-label="Yumio – Trang chủ">YUMIO</a>

        <div class="header-side header-side--end">
            <a href="<?= url('/stores') ?>" class="header-link header-link--store">
                <?= icon('pin', 18) ?><span>Tìm Yumio</span>
            </a>

            <?php if (auth()): ?>
                <details class="account-menu" data-popover>
                    <summary class="icon-btn" aria-label="Tài khoản"><?= icon('user', 20) ?></summary>
                    <div class="popover" role="menu">
                        <p class="popover__title">Xin chào, <?= e(auth()['name']) ?></p>
                        <a href="<?= url('/orders') ?>" role="menuitem"><?= icon('receipt', 18) ?> Đơn hàng của tôi</a>
                        <?php if (is_admin()): ?>
                            <a href="<?= url('/admin') ?>" role="menuitem"><?= icon('grid', 18) ?> Trang quản trị</a>
                        <?php endif; ?>
                        <form action="<?= url('/logout') ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" role="menuitem"><?= icon('logout', 18) ?> Đăng xuất</button>
                        </form>
                    </div>
                </details>
            <?php else: ?>
                <a href="<?= url('/login') ?>" class="icon-btn" aria-label="Đăng nhập"><?= icon('user', 20) ?></a>
            <?php endif; ?>

            <a href="<?= url('/cart') ?>" class="icon-btn cart-link" data-cart-link aria-label="Giỏ hàng">
                <?= icon('bag', 21) ?>
                <span class="cart-count" data-cart-count <?= App\Models\Cart::count() === 0 ? 'hidden' : '' ?>><?= App\Models\Cart::count() ?></span>
            </a>
        </div>
    </div>

    <nav class="header-nav" aria-label="Điều hướng chính">
        <a href="<?= url('/about') ?>" class="<?= is_active('/about') ? 'is-active' : '' ?>">Về Yumio</a>
        <a href="<?= url('/menu') ?>" class="<?= current_path() === '/menu' ? 'is-active' : '' ?>">Thực đơn</a>
        <?php foreach ($navCategories as $navCategory): ?>
            <a href="<?= url('/menu/' . $navCategory['slug']) ?>" class="<?= is_active('/menu/' . $navCategory['slug']) ? 'is-active' : '' ?>"><?= e($navCategory['name']) ?></a>
        <?php endforeach; ?>
        <a href="<?= url('/stores') ?>" class="<?= is_active('/stores') ? 'is-active' : '' ?>">Cửa hàng</a>
        <a href="#lien-he">Liên hệ</a>
    </nav>

    <?php require BASE_PATH . '/app/Views/partials/cart_popover.php'; ?>
</header>

<dialog class="nav-sheet" id="mobile-nav" data-nav-sheet aria-label="Menu">
    <div class="nav-sheet__head">
        <a href="<?= url('/') ?>" class="logo">YUMIO</a>
        <button type="button" class="icon-btn" data-nav-close aria-label="Đóng menu"><?= icon('close', 22) ?></button>
    </div>
    <nav class="nav-sheet__links">
        <a href="<?= url('/menu') ?>">Thực đơn</a>
        <?php foreach (App\Models\Category::ordered() as $navCategory): ?>
            <a href="<?= url('/menu/' . $navCategory['slug']) ?>"><?= e($navCategory['name']) ?></a>
        <?php endforeach; ?>
        <a href="<?= url('/about') ?>">Về Yumio</a>
        <a href="<?= url('/stores') ?>">Cửa hàng</a>
        <a href="<?= url('/search') ?>">Tìm món</a>
    </nav>
    <div class="nav-sheet__foot">
        <?php if (auth()): ?>
            <a href="<?= url('/orders') ?>" class="btn btn--block">Đơn hàng của tôi</a>
        <?php else: ?>
            <a href="<?= url('/login') ?>" class="btn btn--solid btn--block">Đăng nhập</a>
            <a href="<?= url('/register') ?>" class="btn btn--block">Tạo tài khoản</a>
        <?php endif; ?>
    </div>
</dialog>
