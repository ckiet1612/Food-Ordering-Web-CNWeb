<?php
/** Thẻ món ăn. Biến vào: $food; tuỳ chọn $tileShowAdd (mặc định true), $tileShowDesc */
$tileShowAdd ??= true;
$tileShowDesc ??= false;
?>
<article class="food-tile">
    <a href="<?= url('/foods/' . $food['slug']) ?>" class="tile-media" style="--tile-bg: <?= e($food['bg_color']) ?>" tabindex="-1" aria-hidden="true">
        <img src="<?= image_url($food['image']) ?>" alt="" loading="lazy" draggable="false">
    </a>

    <?php if ($tileShowAdd): ?>
        <form action="<?= url('/cart/add') ?>" method="post" class="quick-add" data-cart-add>
            <?= csrf_field() ?>
            <input type="hidden" name="food_id" value="<?= $food['id'] ?>">
            <button type="submit" aria-label="Thêm <?= e($food['name']) ?> vào giỏ" data-done-icon='<?= icon('check', 20) ?>'><?= icon('plus', 20) ?></button>
        </form>
    <?php endif; ?>

    <div class="food-tile__body">
        <a href="<?= url('/foods/' . $food['slug']) ?>" class="food-tile__name link-underline"><?= e($food['name']) ?></a>
        <span class="food-tile__price"><?= format_price($food['price']) ?></span>
    </div>
    <?php if ($tileShowDesc && $food['description']): ?>
        <p class="food-tile__desc"><?= e($food['description']) ?></p>
    <?php endif; ?>
</article>
