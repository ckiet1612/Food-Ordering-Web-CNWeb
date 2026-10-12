<section class="auth">
    <div class="auth__media" style="--tile-bg: var(--c-orange)">
        <img src="<?= asset('images/foods/xo-ga-ran-6-mieng.webp') ?>" alt="">
        <p class="serif-lead">Giòn rụm, nóng hổi<br>và luôn đúng giờ.</p>
    </div>
    <div class="auth__panel">
        <form action="<?= url('/login') ?>" method="post" class="auth__form" data-validate>
            <?= csrf_field() ?>
            <h1>Chào mừng trở lại</h1>
            <p>Đăng nhập để đặt món và theo dõi đơn hàng của bạn.</p>
            <?php $f = ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'attrs' => 'required autocomplete="email" autofocus']; require BASE_PATH . '/app/Views/partials/field.php'; ?>
            <?php $f = ['name' => 'password', 'label' => 'Mật khẩu', 'type' => 'password', 'attrs' => 'required autocomplete="current-password"']; require BASE_PATH . '/app/Views/partials/field.php'; ?>
            <button type="submit" class="btn btn--solid btn--lg btn--block">Đăng nhập</button>
            <p class="auth__switch">Chưa có tài khoản? <a href="<?= url('/register') ?>" class="link-underline">Đăng ký ngay</a></p>
            <p class="demo-hint">Tài khoản demo — Admin: <strong>admin@yumio.vn</strong> / <strong>admin123</strong><br>Khách hàng: <strong>khach@yumio.vn</strong> / <strong>123456</strong></p>
        </form>
    </div>
</section>
