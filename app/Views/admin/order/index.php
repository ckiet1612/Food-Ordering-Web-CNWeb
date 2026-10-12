<?php require BASE_PATH . '/app/Views/partials/admin_head.php'; ?>

<nav class="chip-nav chip-nav--start" aria-label="Lọc theo trạng thái">
    <a href="<?= url('/admin/orders') ?>" class="chip <?= !$status ? 'is-active' : '' ?>">Tất cả</a>
    <?php foreach (App\Models\Order::STATUSES as $value => $label): ?>
        <a href="<?= url('/admin/orders?status=' . $value) ?>" class="chip <?= $status === $value ? 'is-active' : '' ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
</nav>

<section class="panel">
    <?php if ($orders): ?>
        <table class="table">
            <thead><tr><th>Mã</th><th>Khách hàng</th><th>Thời gian</th><th>Thanh toán</th><th>Tổng</th><th>Trạng thái</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr data-href="<?= url('/admin/orders/' . $order['id']) ?>">
                    <td><a href="<?= url('/admin/orders/' . $order['id']) ?>">#<?= $order['id'] ?></a></td>
                    <td><?= e($order['receiver_name']) ?><small><?= e($order['phone']) ?></small></td>
                    <td><?= date('H:i d/m/Y', strtotime($order['created_at'])) ?></td>
                    <td><?= $order['payment_method'] === 'cod' ? 'COD' : 'Chuyển khoản' ?></td>
                    <td><?= format_price($order['total_amount']) ?></td>
                    <td><span class="status-pill status--<?= $order['status'] ?>"><?= e(App\Models\Order::STATUSES[$order['status']]) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">Không có đơn hàng nào.</p>
    <?php endif; ?>
</section>
