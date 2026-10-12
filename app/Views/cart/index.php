<div class="container container--wide">
    <header class="page-head">
        <h1>Giỏ hàng</h1>
        <?php if ($cart['items']): ?>
            <p><?= $cart['count'] ?> món đang chờ bạn</p>
        <?php endif; ?>
    </header>

    <?php if (!$cart['items']): ?>
        <div class="empty" style="padding-bottom: var(--section-space)">
            <h2 class="serif-title">Giỏ hàng đang trống</h2>
            <p>Thêm vài món ngon từ thực đơn — gà rán, burger hay một ly trà đào mát lạnh.</p>
            <a href="<?= url('/menu') ?>" class="btn btn--solid btn--lg">Chọn món ngay</a>
        </div>
    <?php else: ?>
        <div class="checkout-layout" data-cart-page>
            <div class="cart-list">
                <?php foreach ($cart['items'] as $item): ?>
                    <div class="cart-row" data-cart-row data-price="<?= (int) $item['price'] ?>">
                        <a href="<?= url('/foods/' . $item['slug']) ?>" class="tile-media cart-row__thumb" style="--tile-bg: <?= e($item['bg_color']) ?>">
                            <img src="<?= image_url($item['image']) ?>" alt="<?= e($item['name']) ?>" loading="lazy">
                        </a>
                        <div class="cart-row__info">
                            <a href="<?= url('/foods/' . $item['slug']) ?>" class="cart-row__name"><?= e($item['name']) ?></a>
                            <span class="cart-row__unit"><?= format_price($item['price']) ?> / phần</span>
                            <div class="cart-row__controls">
                                <form action="<?= url('/cart/update') ?>" method="post" data-cart-update>
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="food_id" value="<?= $item['id'] ?>">
                                    <div class="stepper stepper--sm" data-stepper>
                                        <button type="button" data-step="-1" aria-label="Giảm số lượng"><?= icon('minus', 16) ?></button>
                                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= App\Models\Cart::MAX_QUANTITY ?>" inputmode="numeric" aria-label="Số lượng <?= e($item['name']) ?>">
                                        <button type="button" data-step="1" aria-label="Tăng số lượng"><?= icon('plus', 16) ?></button>
                                    </div>
                                    <noscript><button type="submit" class="btn btn--sm">Cập nhật</button></noscript>
                                </form>
                                <form action="<?= url('/cart/remove') ?>" method="post" data-cart-remove>
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="food_id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="cart-row__remove"><?= icon('trash', 16) ?> Xoá</button>
                                </form>
                            </div>
                        </div>
                        <span class="cart-row__total" data-line-total><?= format_price($item['line_total']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <aside class="summary" aria-label="Tóm tắt đơn hàng">
                <h2>Tóm tắt</h2>
                <div class="shipping-meter">
                    <span data-shipping-text><?= $cart['shipping_message'] ?></span>
                    <div class="shipping-meter__bar"><div class="shipping-meter__fill" data-shipping-fill style="transform: scaleX(<?= $cart['shipping_progress'] ?>)"></div></div>
                </div>
                <div class="summary__row"><span>Tạm tính</span><span data-summary-subtotal><?= format_price($cart['subtotal']) ?></span></div>
                <div class="summary__row"><span>Phí giao hàng</span><span data-summary-shipping><?= $cart['shipping_fee'] ? format_price($cart['shipping_fee']) : 'Miễn phí' ?></span></div>
                <div class="summary__row summary__row--total"><span>Tổng cộng</span><span data-summary-total><?= format_price($cart['total']) ?></span></div>
                <a href="<?= url('/checkout') ?>" class="btn btn--solid btn--lg btn--block">Đặt hàng</a>
                <a href="<?= url('/menu') ?>" class="btn btn--block">Chọn thêm món</a>
            </aside>
        </div>
    <?php endif; ?>
</div>
