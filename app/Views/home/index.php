<section class="hero hero--product hero--plain">
    <?php if ($heroFood): ?>
        <img class="hero__product" src="<?= image_url($heroFood['image']) ?>" alt="" fetchpriority="high">
    <?php endif; ?>
    <div class="hero__content">
        <p class="hero__brand">YUMIO</p>
        <h1 class="hero__title"><?= mb_strtoupper('Giòn rụm') ?><br><?= mb_strtoupper('nóng hổi') ?></h1>
        <p class="hero__lead">Gà tẩm 11 loại gia vị chiên mới mỗi giờ, burger nướng tại chỗ — đồ ăn nhanh nhưng ngon thật.</p>
        <div class="hero__actions">
            <a href="<?= url('/menu') ?>" class="btn btn--light btn--lg">Đặt món ngay</a>
            <?php if ($heroFood): ?>
                <a href="<?= url('/foods/' . $heroFood['slug']) ?>" class="hero__tag"><?= e($heroFood['name']) ?> · <?= format_price($heroFood['price']) ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($featured): ?>
    <section class="section">
        <h2 class="section-title">Thực đơn</h2>
        <?php $carouselFoods = $featured; $carouselLabel = 'Món nổi bật'; require BASE_PATH . '/app/Views/partials/carousel.php'; ?>
        <div class="section-cta">
            <a href="<?= url('/menu') ?>" class="btn">Xem thực đơn</a>
        </div>
    </section>
<?php endif; ?>

<section class="section section--tint feature">
    <h2 class="section-title">Combo trong vài phút</h2>
    <?php if ($featureFood): ?>
        <a href="<?= url('/foods/' . $featureFood['slug']) ?>" class="feature__media" style="--tile-bg: <?= e($featureFood['bg_color']) ?>">
            <img src="<?= image_url($featureFood['image']) ?>" alt="<?= e($featureFood['name']) ?>" loading="lazy">
            <span class="feature__chip"><?= e($featureFood['name']) ?> · <?= format_price($featureFood['price']) ?></span>
        </a>
    <?php endif; ?>
    <div class="container">
        <p class="serif-lead">Mỗi combo Yumio được kết hợp sẵn giữa món chính, món ăn kèm và đồ uống. Đủ no cho bữa trưa vội, đủ vui cho buổi tối cùng bạn bè — và luôn có giá tốt hơn khi gọi lẻ.</p>
        <span class="divider-v"></span>
        <a href="<?= url('/menu/combo') ?>" class="btn">Khám phá combo</a>
    </div>
</section>

<?php if ($categories): ?>
    <section class="section">
        <div class="section-head">
            <h2 class="section-title">Hôm nay bạn thèm gì?</h2>
        </div>
        <div class="container category-strip">
            <?php foreach ($categories as $category): ?>
                <a href="<?= url('/menu/' . $category['slug']) ?>">
                    <span class="tile-media" style="--tile-bg: <?= e($category['color']) ?>">
                        <img src="<?= image_url($category['image']) ?>" alt="" loading="lazy">
                    </span>
                    <span class="link-underline"><?= e($category['name']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="section section--tint">
    <div class="section-head">
        <h2 class="serif-title">Tìm Yumio</h2>
        <p>Hơn 30 cửa hàng tại Hà Nội, TP. Hồ Chí Minh và Đà Nẵng. Ghé ăn tại chỗ, mang đi hoặc đặt giao tận nơi.</p>
    </div>
    <div class="container store-list">
        <?php foreach ($stores as $store): ?>
            <?php require BASE_PATH . '/app/Views/partials/store_card.php'; ?>
        <?php endforeach; ?>
    </div>
    <div class="section-cta">
        <a href="<?= url('/stores') ?>" class="btn">Xem tất cả cửa hàng</a>
    </div>
</section>

<?php if (!auth()): ?>
    <section class="section">
        <div class="container club">
            <?php if ($clubFood): ?>
                <div class="tile-media club__media" style="--tile-bg: <?= e($clubFood['bg_color']) ?>">
                    <img src="<?= image_url($clubFood['image']) ?>" alt="" loading="lazy">
                </div>
            <?php endif; ?>
            <div class="club__body">
                <p class="eyebrow">Yumio Club</p>
                <h2 class="serif-title">Thành viên luôn được ăn ngon hơn</h2>
                <ul>
                    <li><?= icon('check', 20) ?> Lưu địa chỉ, đặt lại món yêu thích chỉ với một chạm</li>
                    <li><?= icon('check', 20) ?> Theo dõi đơn hàng theo thời gian thực</li>
                    <li><?= icon('check', 20) ?> Miễn phí giao hàng cho đơn từ <?= format_price(App\Models\Order::FREE_SHIPPING_FROM) ?></li>
                </ul>
                <a href="<?= url('/register') ?>" class="btn btn--solid btn--lg">Tham gia miễn phí</a>
            </div>
        </div>
    </section>
<?php endif; ?>
