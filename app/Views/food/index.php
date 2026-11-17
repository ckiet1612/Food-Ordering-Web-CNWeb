<div data-menu-page>
<section class="page-hero menu-heading">
    <p class="eyebrow">Một trang, đủ món ngon</p>
    <h1>Hôm nay ăn gì?</h1>
    <p>Gà giòn, burger đầy vị, combo no nê. Chọn danh mục hoặc cứ cuộn để khám phá.</p>
    <span class="menu-heading__stamp" aria-hidden="true">MENU<br>ĐẦY VỊ ✷</span>
</section>

<?php if ($sections): ?>
    <nav class="menu-tabs" data-menu-tabs aria-label="Chuyển đến danh mục món ăn">
        <div class="container container--wide chip-nav">
        <?php foreach ($sections as $index => $section): ?>
            <a href="#menu-category-<?= (int) $section['id'] ?>" class="chip <?= $index === 0 ? 'is-active' : '' ?>" data-menu-tab <?= $index === 0 ? 'aria-current="location"' : '' ?>>
                <?= e($section['name']) ?> <span class="menu-tabs__count"><?= count($section['foods']) ?></span>
            </a>
        <?php endforeach; ?>
        </div>
    </nav>

    <div class="container container--wide menu-sections">
        <?php foreach ($sections as $index => $section): ?>
        <section id="menu-category-<?= (int) $section['id'] ?>" class="menu-category" data-menu-section aria-labelledby="menu-title-<?= (int) $section['id'] ?>">
            <header class="menu-category__head">
                <div><p class="eyebrow">Yumio menu · <?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></p><h2 id="menu-title-<?= (int) $section['id'] ?>"><?= e($section['name']) ?></h2>
                <?php if ($section['description']): ?><p><?= e($section['description']) ?></p><?php endif; ?></div>
                <span class="menu-category__count"><?= count($section['foods']) ?> món ngon</span>
            </header>
            <div class="tile-grid tile-grid--4">
                <?php foreach ($section['foods'] as $food): ?>
                    <?php $tileShowAdd = true; $tileShowDesc = true; require BASE_PATH . '/app/Views/partials/food_tile.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="container empty"><h2 class="serif-title">Bếp đang chuẩn bị thực đơn</h2><p>Hiện chưa có món đang bán. Bạn quay lại sau nhé!</p></div>
<?php endif; ?>
</div>

<section class="section section--tint">
    <div class="container link-list">
        <a href="<?= url('/search') ?>" class="btn btn--lg">Tìm món theo tên</a>
        <a href="<?= url('/about') ?>#dinh-duong" class="link-underline">Thông tin dinh dưỡng &amp; dị ứng tại Yumio</a>
        <a href="<?= url('/about') ?>#giao-hang" class="link-underline">Miễn phí giao hàng cho đơn từ 200.000₫</a>
    </div>
</section>
