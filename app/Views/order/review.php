<div class="container container--wide">
    <header class="page-head"><p class="eyebrow">Kiểm tra một lần, ngon trọn bữa</p><h1>Đúng món. Đúng nhà.</h1><p>Chỉ khi bạn nhấn nút xác nhận bên dưới, Yumio mới tạo đơn hàng.</p>
        <ol class="checkout-steps" aria-label="Các bước đặt hàng"><li><b>01</b> Thông tin</li><li aria-current="step"><b>02</b> Xem lại đơn</li><li><b>03</b> Xác nhận COD</li></ol>
    </header>
    <div class="checkout-layout">
        <div class="review-content">
            <section class="form-card"><h2><?= icon('pin', 26) ?> Giao tới địa chỉ này</h2>
                <dl class="info-list"><div><dt>Người nhận</dt><dd><?= e($info['receiver_name']) ?></dd></div><div><dt>Điện thoại</dt><dd><?= e($info['phone']) ?></dd></div><div><dt>Địa chỉ</dt><dd><?= e($info['address']) ?></dd></div><div><dt>Ghi chú</dt><dd><?= e($info['note'] ?: 'Không có ghi chú') ?></dd></div><div><dt>Thanh toán</dt><dd>Tiền mặt khi nhận hàng (COD)</dd></div></dl>
                <?php $mapMode = 'view'; $mapOrder = $info; require BASE_PATH . '/app/Views/partials/delivery_map.php'; ?>
                <a class="btn" href="<?= url('/checkout') ?>"><?= icon('edit', 20) ?> Sửa thông tin nhận hàng</a>
            </section>
            <section class="form-card"><h2><?= icon('bag', 26) ?> <?= (int) $cart['count'] ?> phần ngon bạn đã chọn</h2>
                <?php foreach ($cart['items'] as $item): ?><div class="summary__item"><span class="tile-media"><img src="<?= image_url($item['image']) ?>" alt=""></span><span><strong><?= e($item['name']) ?></strong><small><?= format_price($item['price']) ?> × <?= (int) $item['quantity'] ?></small></span><strong><?= format_price($item['line_total']) ?></strong></div><?php endforeach; ?>
                <a class="link-underline" href="<?= url('/cart') ?>">Sửa món và số lượng</a>
            </section>
        </div>
        <aside class="summary"><p class="eyebrow">Bước xác nhận cuối</p><h2>Tổng đơn của bạn</h2>
            <div class="summary__row"><span>Tiền món</span><strong><?= format_price($cart['subtotal']) ?></strong></div><div class="summary__row"><span>Phí giao hàng</span><strong><?= $cart['shipping_fee'] ? format_price($cart['shipping_fee']) : 'Miễn phí' ?></strong></div><div class="summary__row summary__row--total"><span>Tổng COD</span><strong><?= format_price($cart['total']) ?></strong></div>
            <form action="<?= url('/checkout') ?>" method="post" data-checkout-confirm-form>
                <?= csrf_field() ?><input type="hidden" name="checkout_token" value="<?= e($checkoutToken) ?>">
                <?php foreach ($info as $key => $value): if ($key === 'distance_km') continue; ?><input type="hidden" name="<?= e($key) ?>" value="<?= e($value ?? '') ?>"><?php endforeach; ?>
                <button type="submit" name="checkout_action" value="confirm" class="btn btn--solid btn--lg btn--block" data-checkout-confirm>Xác nhận đặt hàng <?= icon('check', 22) ?></button>
            </form>
            <p class="checkout-hint">Đơn chỉ được ghi một lần. Bạn thanh toán <?= format_price($cart['total']) ?> cho tài xế khi nhận món.</p>
        </aside>
    </div>
</div>
<script src="<?= asset('js/checkout.js') ?>" defer></script>
