<?php /** Carousel kéo được. Biến vào: $carouselFoods, $carouselLabel */ ?>
<div class="carousel" data-carousel>
    <div class="carousel__controls">
        <button type="button" class="icon-btn" data-carousel-prev aria-label="Món trước"><?= icon('left', 18) ?></button>
        <span class="carousel__hint" aria-hidden="true">KÉO</span>
        <button type="button" class="icon-btn" data-carousel-next aria-label="Món tiếp theo"><?= icon('right', 18) ?></button>
    </div>
    <div class="carousel__viewport" data-carousel-viewport tabindex="0" role="region" aria-roledescription="carousel" aria-label="<?= e($carouselLabel ?? 'Danh sách món') ?>">
        <div class="carousel__track" data-carousel-track>
            <?php foreach ($carouselFoods as $food): ?>
                <div class="carousel__slide">
                    <?php $tileShowAdd = false; require __DIR__ . '/food_tile.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
