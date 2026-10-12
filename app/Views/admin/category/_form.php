<?php /** Form dùng chung cho create/edit; $item chỉ có khi edit */ $item ??= []; ?>
<div class="admin-form">
    <section class="panel form-grid">
        <?php $f = ['name' => 'name', 'label' => 'Tên danh mục', 'value' => $item['name'] ?? '', 'attrs' => 'required maxlength="100"', 'full' => true]; require BASE_PATH . '/app/Views/partials/field.php'; ?>
        <?php $f = ['name' => 'description', 'label' => 'Mô tả', 'type' => 'textarea', 'value' => $item['description'] ?? '', 'attrs' => 'maxlength="500"', 'full' => true]; require BASE_PATH . '/app/Views/partials/field.php'; ?>
        <?php $f = ['name' => 'sort_order', 'label' => 'Thứ tự hiển thị', 'type' => 'number', 'value' => $item['sort_order'] ?? 0, 'attrs' => 'min="0"', 'hint' => 'Số nhỏ hiển thị trước.']; require BASE_PATH . '/app/Views/partials/field.php'; ?>
    </section>

    <?php $previewImage = $item['image'] ?? null; $previewColor = old('color', $item['color'] ?? '#f9c94d'); $colorField = 'color'; require BASE_PATH . '/app/Views/partials/admin_media_field.php'; ?>
</div>
