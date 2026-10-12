<?php /** Form dùng chung cho create/edit; $item chỉ có khi edit */ $item ??= []; ?>
<div class="admin-form">
    <section class="panel form-grid">
        <?php $f = ['name' => 'name', 'label' => 'Tên món', 'value' => $item['name'] ?? '', 'attrs' => 'required maxlength="150"', 'full' => true]; require BASE_PATH . '/app/Views/partials/field.php'; ?>

        <div class="field <?= error('category_id') ? 'has-error' : '' ?>">
            <label for="f-category_id">Danh mục</label>
            <select id="f-category_id" name="category_id" required>
                <option value="">— Chọn danh mục —</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= (string) old('category_id', $item['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (error('category_id')): ?><p class="field-error"><?= e(error('category_id')) ?></p><?php endif; ?>
        </div>

        <?php $f = ['name' => 'price', 'label' => 'Giá (VNĐ)', 'type' => 'number', 'value' => isset($item['price']) ? (int) $item['price'] : '', 'attrs' => 'required min="0" step="1000"']; require BASE_PATH . '/app/Views/partials/field.php'; ?>
        <?php $f = ['name' => 'description', 'label' => 'Mô tả', 'type' => 'textarea', 'value' => $item['description'] ?? '', 'attrs' => 'maxlength="1000"', 'full' => true]; require BASE_PATH . '/app/Views/partials/field.php'; ?>

        <div class="field field--full">
            <span class="field-label">Hiển thị</span>
            <label class="checkbox"><input type="checkbox" name="is_available" value="1" <?= (form_state()['old'] ? isset(form_state()['old']['is_available']) : ($item['is_available'] ?? 1)) ? 'checked' : '' ?>> Đang bán</label>
            <label class="checkbox"><input type="checkbox" name="is_featured" value="1" <?= (form_state()['old'] ? isset(form_state()['old']['is_featured']) : ($item['is_featured'] ?? 0)) ? 'checked' : '' ?>> Nổi bật trên trang chủ</label>
        </div>
    </section>

    <?php $previewImage = $item['image'] ?? null; $previewColor = old('bg_color', $item['bg_color'] ?? '#f9c94d'); $colorField = 'bg_color'; require BASE_PATH . '/app/Views/partials/admin_media_field.php'; ?>
</div>
