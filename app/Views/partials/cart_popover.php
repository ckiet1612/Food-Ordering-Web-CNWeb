<!-- Popover "Đã thêm vào giỏ": xuất hiện từ icon giỏ hàng (transform-origin góc phải trên) -->
<div class="cart-popover" data-cart-popover role="status" aria-live="polite" hidden>
    <div class="cart-popover__head">
        <span class="cart-popover__check"><?= icon('check', 16) ?></span>
        <span data-cart-popover-message>Đã thêm vào giỏ hàng</span>
        <button type="button" class="icon-btn icon-btn--sm" data-cart-popover-close aria-label="Đóng"><?= icon('close', 16) ?></button>
    </div>
    <div class="cart-popover__item">
        <div class="cart-popover__thumb" data-cart-popover-thumb><img src="" alt="" data-cart-popover-image></div>
        <div>
            <p class="cart-popover__name" data-cart-popover-name></p>
            <p class="cart-popover__price" data-cart-popover-price></p>
        </div>
    </div>
    <div class="cart-popover__actions">
        <a href="<?= url('/cart') ?>" class="btn btn--sm"><span>Xem giỏ hàng (<span data-cart-count-inline>0</span>)</span></a>
        <a href="<?= url('/checkout') ?>" class="btn btn--sm btn--solid">Thanh toán</a>
    </div>
</div>
