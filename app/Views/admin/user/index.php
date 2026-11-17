<?php require BASE_PATH . '/app/Views/partials/admin_head.php'; ?>

<section class="panel">
    <table class="table">
        <thead><tr><th>Người dùng</th><th>Điện thoại</th><th>Vai trò</th><th>Số đơn</th><th>Đã chi</th><th>Ngày tham gia</th></tr></thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= e($user['name']) ?><small><?= e($user['email']) ?></small></td>
                <td><?= e($user['phone'] ?? '—') ?></td>
                <td>
                    <span class="status-pill"><?= e(App\Models\User::ROLES[$user['role']] ?? $user['role']) ?></span>
                    <?php if ($user['role'] === 'customer'): ?>
                    <form method="post" action="<?= url('/admin/users/' . $user['id'] . '/role') ?>">
                        <?= csrf_field() ?><input type="hidden" name="role" value="shipper">
                        <button class="btn btn--sm" type="submit">Cấp vai trò giao hàng</button>
                    </form>
                    <?php endif; ?>
                </td>
                <td><?= (int) $user['order_count'] ?></td>
                <td><?= format_price($user['total_spent']) ?></td>
                <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
