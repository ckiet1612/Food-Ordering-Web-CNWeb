<section class="hero hero--product hero--plain yumio-hero">
    <div class="hero-spark hero-spark--one" aria-hidden="true">✳</div><div class="hero-spark hero-spark--two" aria-hidden="true">✷</div>
    <div class="hero-plate" data-parallax="18">
    <?php if ($heroFood): ?>
        <img class="hero__product" src="<?= image_url($heroFood['image']) ?>" alt="" fetchpriority="high">
    <?php endif; ?>
        <span class="yumio-sticker hero-sticker">THÊM CHÚT<br><b>NGON!</b></span>
        <span class="hero-handnote" aria-hidden="true">Bữa vui ở đây ↗</span>
    </div>
    <div class="hero__content">
        <p class="hero__brand"><span aria-hidden="true">✦</span> ĐÓI BỤNG? ĐÚNG CHỖ RỒI.</p>
        <h1 class="hero__title">GIÒN RỤM.<br><span>VUI HẾT CỠ.</span></h1>
        <p class="hero__lead">Gà giòn, burger đầy vị, combo vừa bụng. Một bữa thật ngon cho những chiếc bụng không muốn chờ.</p>
        <div class="hero__actions">
            <a href="<?= url('/menu') ?>" class="btn btn--light btn--lg">Cho mình xem món <?= icon('right', 24) ?></a>
            <?php if ($heroFood): ?>
                <a href="<?= url('/foods/' . $heroFood['slug']) ?>" class="hero__tag"><?= e($heroFood['name']) ?> · <?= format_price($heroFood['price']) ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="service-strip" aria-label="Đặt món tại Yumio">
    <span><?= icon('bag', 20) ?> Chọn món yêu thích</span>
    <span><?= icon('truck', 20) ?> Phí giao <?= format_price(App\Models\Order::SHIPPING_FEE) ?></span>
    <span><?= icon('check', 20) ?> Miễn phí từ <?= format_price(App\Models\Order::FREE_SHIPPING_FROM) ?></span>
    <a href="<?= url(auth() ? '/orders' : '/login') ?>">Theo dõi đơn của bạn <?= icon('right', 18) ?></a>
</div>

<?php if ($featured): ?>
    <section class="section home-featured" data-reveal>
        <p class="eyebrow home-eyebrow">Món quen, vị ngon</p>
        <h2 class="section-title">Hôm nay, ăn thật đã.</h2>
        <?php $carouselFoods = $featured; $carouselLabel = 'Món nổi bật'; require BASE_PATH . '/app/Views/partials/carousel.php'; ?>
        <div class="section-cta">
            <a href="<?= url('/menu') ?>" class="btn">Xem thực đơn</a>
        </div>
    </section>
<?php endif; ?>

<section class="section section--tint feature combo-story">
    <div class="container combo-story__layout">
    <div class="combo-story__copy" data-reveal><p class="eyebrow">Một combo, đủ cuộc vui</p><h2 class="section-title">Hợp cạ.<br><em>Hợp cả bụng.</em></h2>
        <p>Món chính đậm vị, khoai giòn rôm rốp, thêm một ngụm mát lạnh. Khỏi chọn từng món — Yumio đã ghép một bữa ngon cho bạn.</p>
        <a href="<?= url('/menu/combo') ?>" class="btn btn--solid btn--lg">Khám phá combo <?= icon('right', 22) ?></a>
    </div>
    <?php if ($featureFood): ?>
        <a href="<?= url('/foods/' . $featureFood['slug']) ?>" class="feature__media" data-reveal>
            <img src="<?= image_url($featureFood['image']) ?>" alt="<?= e($featureFood['name']) ?>" loading="lazy">
            <span class="feature__chip"><?= e($featureFood['name']) ?> · <?= format_price($featureFood['price']) ?></span>
        </a>
    <?php endif; ?>
    </div>
</section>

<?php if ($categories): ?>
    <section class="section home-categories" data-reveal>
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
        <p>Các địa điểm minh hoạ tại Hà Nội, TP. Hồ Chí Minh và Đà Nẵng cho bản demo Yumio.</p>
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
                    <li><?= icon('check', 20) ?> Xem lại món và thông tin trong lịch sử đặt hàng</li>
                    <li><?= icon('check', 20) ?> Theo dõi từng bước chuẩn bị và giao hàng</li>
                    <li><?= icon('check', 20) ?> Miễn phí giao hàng cho đơn từ <?= format_price(App\Models\Order::FREE_SHIPPING_FROM) ?></li>
                </ul>
                <a href="<?= url('/register') ?>" class="btn btn--solid btn--lg">Tham gia miễn phí</a>
            </div>
        </div>
    </section>
<?php endif; ?>
