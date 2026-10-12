<div class="container container--wide">
    <header class="page-head">
        <h1>Thanh toán</h1>
        <p>Chỉ một bước nữa thôi — món ngon sẽ tới tay bạn trong khoảng 30 phút.</p>
    </header>

    <form action="<?= url('/checkout') ?>" method="post" class="checkout-layout" data-validate>
        <?= csrf_field() ?>
        <div>
            <section class="form-card">
                <h2>Thông tin nhận hàng</h2>
                <div class="form-grid">
                    <?php $f = ['name' => 'receiver_name', 'label' => 'Tên người nhận', 'value' => auth()['name'], 'attrs' => 'required maxlength="100" autocomplete="name"']; require BASE_PATH . '/app/Views/partials/field.php'; ?>
                    <?php $f = ['name' => 'phone', 'label' => 'Số điện thoại', 'type' => 'tel', 'value' => auth()['phone'] ?? '', 'attrs' => 'required pattern="0[0-9]{9}" inputmode="tel" autocomplete="tel" data-pattern-message="Số điện thoại phải gồm 10 chữ số, bắt đầu bằng 0."']; require BASE_PATH . '/app/Views/partials/field.php'; ?>
                    <?php $f = ['name' => 'address', 'label' => 'Địa chỉ giao hàng', 'value' => auth()['address'] ?? '', 'attrs' => 'required maxlength="255" autocomplete="street-address" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành"', 'full' => true]; require BASE_PATH . '/app/Views/partials/field.php'; ?>
                    <?php $f = ['name' => 'note', 'label' => 'Ghi chú cho bếp & tài xế', 'type' => 'textarea', 'attrs' => 'maxlength="500" placeholder="VD: ít cay, gọi trước khi giao..."', 'full' => true]; require BASE_PATH . '/app/Views/partials/field.php'; ?>
                </div>
            </section>

            <section class="form-card">
                <h2>Phương thức thanh toán</h2>
                <div class="choice-list">
                    <?php foreach (App\Models\Order::PAYMENT_METHODS as $value => $label): ?>
                        <label class="choice">
                            <input type="radio" name="payment_method" value="<?= $value ?>" <?= old('payment_method', 'cod') === $value ? 'checked' : '' ?>>
                            <span>
                                <strong><?= e($label) ?></strong>
                                <small><?= $value === 'cod' ? 'Trả tiền mặt cho tài xế khi nhận món' : 'Chuyển khoản theo thông tin trong email xác nhận' ?></small>
                            </span>
                            <?= icon($value === 'cod' ? 'truck' : 'receipt', 22) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <aside class="summary" aria-label="Tóm tắt đơn hàng">
            <h2>Đơn của bạn (<?= $cart['count'] ?> món)</h2>
            <div class="summary__items">
                <?php foreach ($cart['items'] as $item): ?>
                    <div class="summary__item">
                        <span class="tile-media" style="--tile-bg: <?= e($item['bg_color']) ?>"><img src="<?= image_url($item['image']) ?>" alt="" loading="lazy"></span>
                        <span><?= e($item['name']) ?><small>× <?= $item['quantity'] ?></small></span>
                        <span><?= format_price($item['line_total']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="summary__row"><span>Tạm tính</span><span><?= format_price($cart['subtotal']) ?></span></div>
            <div class="summary__row"><span>Phí giao hàng</span><span><?= $cart['shipping_fee'] ? format_price($cart['shipping_fee']) : 'Miễn phí' ?></span></div>
            <div class="summary__row summary__row--total"><span>Tổng cộng</span><span><?= format_price($cart['total']) ?></span></div>
            <button type="submit" class="btn btn--solid btn--lg btn--block">Xác nhận đặt hàng</button>
            <a href="<?= url('/cart') ?>" class="btn btn--block">Quay lại giỏ hàng</a>
        </aside>
    </form>
</div>
