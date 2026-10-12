<?php if (is_file(BASE_PATH . '/public/assets/images/hero-1.webp')): ?>
    <section class="hero hero--photo">
        <img class="hero__media" src="<?= asset('images/hero-1.webp') ?>" alt="Ba chiếc burger bò phô mai" fetchpriority="high">
        <div class="hero__content">
            <h1 class="hero__title"><?= mb_strtoupper('Thực đơn') ?></h1>
            <p class="hero__lead">Gà rán giòn rụm, burger nóng hổi, combo no nê và đồ uống mát lạnh.</p>
        </div>
    </section>
<?php endif; ?>

<section class="page-hero">
    <h2 class="section-title">Hôm nay ăn gì?</h2>
    <p>Chọn một danh mục để bắt đầu.</p>
</section>

<section class="container" style="padding-bottom: var(--section-space)">
    <div class="tile-grid">
        <?php foreach ($categories as $category): ?>
            <a href="<?= url('/menu/' . $category['slug']) ?>" class="category-tile">
                <span class="tile-media" style="--tile-bg: <?= e($category['color']) ?>">
                    <img src="<?= image_url($category['image']) ?>" alt="" loading="lazy">
                </span>
                <span class="tile-label link-underline"><?= e($category['name']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section section--tint">
    <div class="container link-list">
        <a href="<?= url('/search') ?>" class="btn btn--lg">Tìm món theo tên</a>
        <a href="<?= url('/about') ?>#dinh-duong" class="link-underline">Thông tin dinh dưỡng &amp; dị ứng tại Yumio</a>
        <a href="<?= url('/about') ?>#giao-hang" class="link-underline">Miễn phí giao hàng cho đơn từ 200.000₫</a>
    </div>
</section>
