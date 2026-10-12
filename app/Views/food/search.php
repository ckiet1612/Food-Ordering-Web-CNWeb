<section class="page-hero">
    <p class="eyebrow">Tìm kiếm</p>
    <h1>Bạn đang thèm gì?</h1>
    <form action="<?= url('/search') ?>" method="get" class="search-form" role="search">
        <label for="q" class="visually-hidden">Tên món</label>
        <input type="search" id="q" name="q" class="input" value="<?= e($keyword) ?>" placeholder="Gà rán, burger, trà đào..." autofocus>
        <button type="submit" class="btn btn--solid btn--lg"><?= icon('search', 18) ?> Tìm</button>
    </form>
</section>

<div class="container container--wide" style="padding-bottom: var(--section-space)">
    <?php if ($keyword !== '' && $foods): ?>
        <p class="muted" style="text-align:center; margin-bottom: 40px">Tìm thấy <?= count($foods) ?> món cho “<?= e($keyword) ?>”</p>
        <div class="tile-grid tile-grid--4">
            <?php foreach ($foods as $food): ?>
                <?php $tileShowAdd = true; require BASE_PATH . '/app/Views/partials/food_tile.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php elseif ($keyword !== ''): ?>
        <div class="empty">
            <h2 class="serif-title">Chưa tìm thấy món nào</h2>
            <p>Không có món nào khớp với “<?= e($keyword) ?>”. Thử từ khoá khác hoặc xem toàn bộ thực đơn.</p>
            <a href="<?= url('/menu') ?>" class="btn">Xem thực đơn</a>
        </div>
    <?php endif; ?>
</div>
