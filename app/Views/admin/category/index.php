<?php $headAction = ['/admin/categories/create', 'Thêm danh mục']; require BASE_PATH . '/app/Views/partials/admin_head.php'; ?>

<section class="panel">
    <table class="table">
        <thead><tr><th>Danh mục</th><th>Số món</th><th>Thứ tự</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($items as $category): ?>
            <tr>
                <td>
                    <div class="cell-media">
                        <span class="tile-media" style="--tile-bg: <?= e($category['color']) ?>"><img src="<?= image_url($category['image']) ?>" alt="" loading="lazy"></span>
                        <span><a href="<?= url('/menu/' . $category['slug']) ?>" target="_blank"><?= e($category['name']) ?></a><small>/menu/<?= e($category['slug']) ?></small></span>
                    </div>
                </td>
                <td><?= (int) $category['food_count'] ?></td>
                <td><?= (int) $category['sort_order'] ?></td>
                <td class="table__actions">
                    <a href="<?= url('/admin/categories/' . $category['id'] . '/edit') ?>" class="icon-btn" aria-label="Sửa"><?= icon('edit', 18) ?></a>
                    <form action="<?= url('/admin/categories/' . $category['id'] . '/delete') ?>" method="post" data-confirm="Xoá danh mục “<?= e($category['name']) ?>”?">
                        <?= csrf_field() ?>
                        <button type="submit" class="icon-btn icon-btn--danger" aria-label="Xoá"><?= icon('trash', 18) ?></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
