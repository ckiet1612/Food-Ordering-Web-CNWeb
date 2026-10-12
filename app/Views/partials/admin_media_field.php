<?php /** Ảnh + màu nền có xem trước. Biến vào: $previewImage, $previewColor, $colorField */ ?>
<section class="panel media-panel">
    <div class="tile-media media-preview" style="--tile-bg: <?= e($previewColor) ?>" data-media-preview>
        <img src="<?= image_url($previewImage) ?>" alt="Xem trước" data-media-preview-img>
    </div>

    <div class="field <?= error('image') ? 'has-error' : '' ?>">
        <label for="f-image">Ảnh (PNG nền trong suốt đẹp nhất)</label>
        <input type="file" id="f-image" name="image" accept="image/png,image/jpeg,image/webp" data-image-input>
        <p class="hint">JPG, PNG hoặc WebP, tối đa 3MB.<?= $previewImage ? ' Để trống nếu giữ ảnh hiện tại.' : '' ?></p>
        <?php if (error('image')): ?><p class="field-error"><?= e(error('image')) ?></p><?php endif; ?>
    </div>

    <div class="field">
        <label for="f-<?= $colorField ?>">Màu nền thẻ</label>
        <div class="swatches" data-swatches>
            <?php foreach (['#f9c94d', '#f6893b', '#e9573d', '#9cc94a', '#2fb3d3', '#f8a46b'] as $swatch): ?>
                <button type="button" class="swatch" style="background: <?= $swatch ?>" data-swatch="<?= $swatch ?>" aria-label="Chọn màu <?= $swatch ?>"></button>
            <?php endforeach; ?>
            <input type="color" id="f-<?= $colorField ?>" name="<?= $colorField ?>" value="<?= e($previewColor) ?>" data-color-input>
        </div>
    </div>
</section>
