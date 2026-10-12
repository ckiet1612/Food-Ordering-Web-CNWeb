<section class="auth">
    <div class="auth__media" style="--tile-bg: var(--c-yellow)">
        <img src="<?= asset('images/foods/burger-bo-hai-tang.webp') ?>" alt="">
        <p class="serif-lead">Tạo tài khoản,<br>đặt món chỉ trong vài chạm.</p>
    </div>
    <div class="auth__panel">
        <form action="<?= url('/register') ?>" method="post" class="auth__form" data-validate>
            <?= csrf_field() ?>
            <h1>Tham gia Yumio</h1>
            <p>Lưu địa chỉ, theo dõi đơn và nhận ưu đãi dành riêng cho thành viên.</p>
            <?php $f = ['name' => 'name', 'label' => 'Họ tên', 'attrs' => 'required maxlength="100" autocomplete="name" autofocus']; require BASE_PATH . '/app/Views/partials/field.php'; ?>
            <?php $f = ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'attrs' => 'required maxlength="150" autocomplete="email"']; require BASE_PATH . '/app/Views/partials/field.php'; ?>
            <?php $f = ['name' => 'phone', 'label' => 'Số điện thoại', 'type' => 'tel', 'attrs' => 'required pattern="0[0-9]{9}" inputmode="tel" autocomplete="tel" data-pattern-message="Số điện thoại phải gồm 10 chữ số, bắt đầu bằng 0."']; require BASE_PATH . '/app/Views/partials/field.php'; ?>
            <?php $f = ['name' => 'password', 'label' => 'Mật khẩu', 'type' => 'password', 'attrs' => 'required minlength="6" autocomplete="new-password"', 'hint' => 'Tối thiểu 6 ký tự.']; require BASE_PATH . '/app/Views/partials/field.php'; ?>
            <?php $f = ['name' => 'password_confirmation', 'label' => 'Xác nhận mật khẩu', 'type' => 'password', 'attrs' => 'required autocomplete="new-password" data-match="password"']; require BASE_PATH . '/app/Views/partials/field.php'; ?>
            <button type="submit" class="btn btn--solid btn--lg btn--block">Tạo tài khoản</button>
            <p class="auth__switch">Đã có tài khoản? <a href="<?= url('/login') ?>" class="link-underline">Đăng nhập</a></p>
        </form>
    </div>
</section>
