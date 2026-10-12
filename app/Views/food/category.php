<section class="page-hero">
    <p class="eyebrow">Thực đơn</p>
    <h1><?= e($category['name']) ?></h1>
    <?php if ($category['description']): ?>
        <p><?= e($category['description']) ?></p>
    <?php endif; ?>
</section>

<div class="container container--wide" style="padding-bottom: var(--section-space)">
    <nav class="chip-nav" aria-label="Danh mục">
        <a href="<?= url('/menu') ?>" class="chip">Tất cả danh mục</a>
        <?php foreach ($categories as $item): ?>
            <a href="<?= url('/menu/' . $item['slug']) ?>" class="chip <?= $item['id'] === $category['id'] ? 'is-active' : '' ?>" <?= $item['id'] === $category['id'] ? 'aria-current="page"' : '' ?>><?= e($item['name']) ?></a>
        <?php endforeach; ?>
    </nav>

    <?php if ($foods): ?>
        <div class="tile-grid tile-grid--4">
            <?php foreach ($foods as $food): ?>
                <?php $tileShowAdd = true; $tileShowDesc = true; require BASE_PATH . '/app/Views/partials/food_tile.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty">
            <h2 class="serif-title">Sắp có món mới</h2>
            <p>Danh mục này đang được bếp Yumio chuẩn bị. Bạn xem các món khác nhé!</p>
            <a href="<?= url('/menu') ?>" class="btn">Xem thực đơn</a>
        </div>
    <?php endif; ?>
</div>
