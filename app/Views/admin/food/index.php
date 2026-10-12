<?php $headAction = ['/admin/foods/create', 'Thêm món']; require BASE_PATH . '/app/Views/partials/admin_head.php'; ?>

<section class="panel">
    <table class="table">
        <thead><tr><th>Món ăn</th><th>Danh mục</th><th>Giá</th><th>Trạng thái</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($items as $food): ?>
            <tr>
                <td>
                    <div class="cell-media">
                        <span class="tile-media" style="--tile-bg: <?= e($food['bg_color']) ?>"><img src="<?= image_url($food['image']) ?>" alt="" loading="lazy"></span>
                        <span><a href="<?= url('/foods/' . $food['slug']) ?>" target="_blank"><?= e($food['name']) ?></a><?php if ($food['is_featured']): ?><small>★ Nổi bật trên trang chủ</small><?php endif; ?></span>
                    </div>
                </td>
                <td><?= e($food['category_name']) ?></td>
                <td><?= format_price($food['price']) ?></td>
                <td><span class="status-pill <?= $food['is_available'] ? 'status--completed' : 'status--cancelled' ?>"><?= $food['is_available'] ? 'Đang bán' : 'Ngừng bán' ?></span></td>
                <td class="table__actions">
                    <a href="<?= url('/admin/foods/' . $food['id'] . '/edit') ?>" class="icon-btn" aria-label="Sửa"><?= icon('edit', 18) ?></a>
                    <form action="<?= url('/admin/foods/' . $food['id'] . '/delete') ?>" method="post" data-confirm="Xoá món “<?= e($food['name']) ?>”?">
                        <?= csrf_field() ?>
                        <button type="submit" class="icon-btn icon-btn--danger" aria-label="Xoá"><?= icon('trash', 18) ?></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
