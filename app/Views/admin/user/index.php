<?php require BASE_PATH . '/app/Views/partials/admin_head.php'; ?>

<section class="panel">
    <table class="table">
        <thead><tr><th>Người dùng</th><th>Điện thoại</th><th>Vai trò</th><th>Số đơn</th><th>Đã chi</th><th>Ngày tham gia</th></tr></thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= e($user['name']) ?><small><?= e($user['email']) ?></small></td>
                <td><?= e($user['phone'] ?? '—') ?></td>
                <td><span class="status-pill <?= $user['role'] === 'admin' ? 'status--delivering' : 'status--confirmed' ?>"><?= $user['role'] === 'admin' ? 'Quản trị' : 'Khách hàng' ?></span></td>
                <td><?= (int) $user['order_count'] ?></td>
                <td><?= format_price($user['total_spent']) ?></td>
                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
