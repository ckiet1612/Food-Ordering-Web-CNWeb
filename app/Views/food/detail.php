<div class="container container--wide">
    <section class="product">
        <div class="tile-media product__media" style="--tile-bg: <?= e($food['bg_color']) ?>">
            <img src="<?= image_url($food['image']) ?>" alt="<?= e($food['name']) ?>">
        </div>

        <div class="product__info">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="<?= url('/menu') ?>">Thực đơn</a><span>/</span>
                <a href="<?= url('/menu/' . $food['category_slug']) ?>"><?= e($food['category_name']) ?></a>
            </nav>
            <h1 class="product__title"><?= e($food['name']) ?></h1>
            <p class="product__price"><?= format_price($food['price']) ?></p>
            <?php if ($food['description']): ?>
                <p class="product__desc"><?= nl2br(e($food['description'])) ?></p>
            <?php endif; ?>

            <?php if ($food['is_available']): ?>
                <form action="<?= url('/cart/add') ?>" method="post" class="product__buy" data-cart-add>
                    <?= csrf_field() ?>
                    <input type="hidden" name="food_id" value="<?= $food['id'] ?>">
                    <div class="stepper" data-stepper>
                        <button type="button" data-step="-1" aria-label="Giảm số lượng"><?= icon('minus', 18) ?></button>
                        <input type="number" name="quantity" value="1" min="1" max="<?= App\Models\Cart::MAX_QUANTITY ?>" inputmode="numeric" aria-label="Số lượng">
                        <button type="button" data-step="1" aria-label="Tăng số lượng"><?= icon('plus', 18) ?></button>
                    </div>
                    <button type="submit" class="btn btn--solid btn--lg"><?= icon('bag', 18) ?> Thêm vào giỏ</button>
                </form>
            <?php else: ?>
                <p class="badge-soldout">Tạm hết món</p>
            <?php endif; ?>

            <div class="product__notes">
                <p><?= icon('truck', 18) ?> Miễn phí giao hàng cho đơn từ <?= format_price(App\Models\Order::FREE_SHIPPING_FROM) ?></p>
                <p><?= icon('clock', 18) ?> Giao nóng trong khoảng 30 phút</p>
            </div>
        </div>
    </section>
</div>

<?php if ($related): ?>
    <section class="section section--tint">
        <h2 class="section-title">Có thể bạn cũng thích</h2>
        <?php $carouselFoods = $related; $carouselLabel = 'Món gợi ý'; require BASE_PATH . '/app/Views/partials/carousel.php'; ?>
    </section>
<?php endif; ?>
