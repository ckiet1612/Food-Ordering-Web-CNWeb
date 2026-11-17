<div class="container container--wide">
    <header class="page-head">
        <p class="eyebrow">Một bữa ngon đang chờ</p>
        <h1>Chốt bữa ngon.</h1>
        <p>Kiểm tra món, thêm địa chỉ và thanh toán khi nhận hàng.</p>
        <ol class="checkout-steps" aria-label="Các bước đặt hàng"><li aria-current="step"><b>01</b> Thông tin</li><li><b>02</b> Xem lại đơn</li><li><b>03</b> Xác nhận COD</li></ol>
    </header>

    <form action="<?= url('/checkout/review') ?>" method="post" class="checkout-layout" data-validate data-checkout-form>
        <?= csrf_field() ?>
        <input type="hidden" name="checkout_token" value="<?= e($checkoutToken) ?>">
        <div>
            <section class="form-card">
                <h2>Thông tin nhận hàng</h2>
                <div class="form-grid">
                    <?php $f = ['name' => 'receiver_name', 'label' => 'Tên người nhận', 'value' => auth()['name'], 'attrs' => 'required maxlength="100" autocomplete="name"']; require BASE_PATH . '/app/Views/partials/field.php'; ?>
                    <?php $f = ['name' => 'phone', 'label' => 'Số điện thoại', 'type' => 'tel', 'value' => auth()['phone'] ?? '', 'attrs' => 'required pattern="0[0-9]{9}" inputmode="tel" autocomplete="tel" data-pattern-message="Số điện thoại phải gồm 10 chữ số, bắt đầu bằng 0."']; require BASE_PATH . '/app/Views/partials/field.php'; ?>
                    <div class="field field--full address-field <?= error('address') ? 'has-error' : '' ?>" data-address-search data-endpoint="<?= url('/addresses/search') ?>">
                        <label for="f-address">Địa chỉ giao hàng <span class="address-country">Việt Nam</span></label>
                        <input id="f-address" name="address" type="text" value="<?= e(old('address', auth()['address'] ?? '')) ?>" required maxlength="255" autocomplete="off" placeholder="Số nhà, đường, phường/xã, tỉnh/thành phố" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="address-options" aria-describedby="address-hint address-status">
                        <ul id="address-options" class="address-options" role="listbox" hidden></ul>
                        <p id="address-status" class="address-status" role="status" aria-live="polite"></p>
                        <p id="address-hint" class="hint">Gợi ý từ Photon / <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap</a>. Địa chỉ tìm kiếm được gửi tới dịch vụ; vẫn có thể nhập tay.</p>
                        <?php if (error('address')): ?><p class="field-error"><?= e(error('address')) ?></p><?php endif; ?>
                    </div>
                </div>
                <?php $mapMode = 'pick'; $mapOrder = null; require BASE_PATH . '/app/Views/partials/delivery_map.php'; ?>
                <?php $f = ['name' => 'note', 'label' => 'Ghi chú cho bếp & tài xế', 'type' => 'textarea', 'attrs' => 'maxlength="500" placeholder="VD: ít cay, gọi trước khi giao..."', 'full' => true]; require BASE_PATH . '/app/Views/partials/field.php'; ?>
            </section>

            <section class="form-card">
                <h2>Phương thức thanh toán</h2>
                <div class="choice-list">
                    <?php foreach (['cod' => App\Models\Order::PAYMENT_METHODS['cod']] as $value => $label): ?>
                        <label class="choice">
                            <input type="radio" name="payment_method" value="<?= $value ?>" <?= old('payment_method', 'cod') === $value ? 'checked' : '' ?>>
                            <span>
                                <strong><?= e($label) ?></strong>
                                <small>Trả tiền mặt cho tài xế khi nhận món</small>
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
            <p class="muted shipping-policy">Phí cố định <?= format_price(App\Models\Order::SHIPPING_FEE) ?>; miễn phí từ <?= format_price(App\Models\Order::FREE_SHIPPING_FROM) ?>. Vị trí bản đồ không làm đổi phí.</p>
            <div class="summary__row summary__row--total"><span>Tổng cộng</span><span><?= format_price($cart['total']) ?></span></div>
            <button type="submit" class="btn btn--solid btn--lg btn--block" data-checkout-review>Xem lại đơn hàng <?= icon('right', 22) ?></button>
            <p class="checkout-hint">Chưa tạo đơn ở bước này. Bạn sẽ kiểm tra lại trước khi xác nhận.</p>
            <a href="<?= url('/cart') ?>" class="btn btn--block">Quay lại giỏ hàng</a>
        </aside>
    </form>
</div>
<script src="<?= asset('js/address-search.js') ?>" defer></script>
<script src="<?= asset('js/checkout.js') ?>" defer></script>
