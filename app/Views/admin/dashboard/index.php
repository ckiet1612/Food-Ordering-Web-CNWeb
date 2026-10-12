<?php require BASE_PATH . '/app/Views/partials/admin_head.php'; ?>

<div class="stat-grid">
    <div class="stat"><span class="stat__label">Doanh thu (đơn hoàn thành)</span><span class="stat__value"><?= format_price($stats['revenue']) ?></span></div>
    <div class="stat"><span class="stat__label">Tổng đơn hàng</span><span class="stat__value"><?= (int) $stats['total_orders'] ?></span><span class="stat__meta"><?= (int) $stats['today_orders'] ?> đơn hôm nay</span></div>
    <a href="<?= url('/admin/orders?status=pending') ?>" class="stat stat--accent"><span class="stat__label">Chờ xác nhận</span><span class="stat__value"><?= (int) $stats['pending_orders'] ?></span><span class="stat__meta">Xử lý ngay →</span></a>
    <div class="stat"><span class="stat__label">Khách hàng · Món ăn</span><span class="stat__value"><?= $customers ?> · <?= $foodCount ?></span></div>
</div>

<div class="admin-grid">
    <section class="panel">
        <div class="panel__head"><h2>Đơn hàng mới</h2><a href="<?= url('/admin/orders') ?>" class="link-underline">Xem tất cả</a></div>
        <?php if ($recent): ?>
            <table class="table">
                <thead><tr><th>Mã</th><th>Khách</th><th>Tổng</th><th>Trạng thái</th></tr></thead>
                <tbody>
                <?php foreach ($recent as $order): ?>
                    <tr data-href="<?= url('/admin/orders/' . $order['id']) ?>">
                        <td><a href="<?= url('/admin/orders/' . $order['id']) ?>">#<?= $order['id'] ?></a></td>
                        <td><?= e($order['receiver_name']) ?><small><?= date('H:i d/m', strtotime($order['created_at'])) ?></small></td>
                        <td><?= format_price($order['total_amount']) ?></td>
                        <td><span class="status-pill status--<?= $order['status'] ?>"><?= e(App\Models\Order::STATUSES[$order['status']]) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="muted">Chưa có đơn hàng nào.</p>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel__head"><h2>Bán chạy nhất</h2></div>
        <?php if ($bestSellers): ?>
            <ol class="rank-list">
                <?php foreach ($bestSellers as $food): ?>
                    <li>
                        <span class="tile-media" style="--tile-bg: <?= e($food['bg_color']) ?>"><img src="<?= image_url($food['image']) ?>" alt=""></span>
                        <span><?= e($food['name']) ?></span>
                        <strong><?= (int) $food['sold'] ?></strong>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php else: ?>
            <p class="muted">Chưa có dữ liệu bán hàng.</p>
        <?php endif; ?>
    </section>
</div>
