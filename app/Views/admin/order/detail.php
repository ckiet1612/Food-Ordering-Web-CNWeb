<?php $headBack = '/admin/orders'; require BASE_PATH . '/app/Views/partials/admin_head.php'; ?>

<div class="admin-grid admin-grid--wide-left">
    <section class="panel">
        <div class="panel__head"><h2>Các món</h2><span class="muted"><?= date('H:i, d/m/Y', strtotime($order['created_at'])) ?></span></div>
        <table class="table">
            <thead><tr><th>Món</th><th>Đơn giá</th><th>SL</th><th>Thành tiền</th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><div class="cell-media"><span class="tile-media" style="--tile-bg: <?= e($item['bg_color']) ?>"><img src="<?= image_url($item['image']) ?>" alt=""></span><span><?= e($item['name']) ?></span></div></td>
                    <td><?= format_price($item['price']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= format_price($item['price'] * $item['quantity']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="totals">
            <div class="summary__row"><span>Tạm tính</span><span><?= format_price($order['subtotal']) ?></span></div>
            <div class="summary__row"><span>Phí giao hàng</span><span><?= $order['shipping_fee'] > 0 ? format_price($order['shipping_fee']) : 'Miễn phí' ?></span></div>
            <div class="summary__row summary__row--total"><span>Tổng cộng</span><span><?= format_price($order['total_amount']) ?></span></div>
        </div>
    </section>

    <div class="stack">
        <section class="panel">
            <h2>Cập nhật trạng thái</h2>
            <p><span class="status-pill status--<?= e($order['status']) ?>"><?= e(App\Models\Order::STATUSES[$order['status']]) ?></span></p>
            <?php $nextStatuses = App\Models\Order::TRANSITIONS[$order['status']] ?? []; ?>
            <?php if ($nextStatuses): ?>
            <form action="<?= url('/admin/orders/' . $order['id'] . '/status') ?>" method="post" class="status-form">
                <?= csrf_field() ?>
                <div class="choice-list">
                    <?php foreach ($nextStatuses as $value): $label = App\Models\Order::STATUSES[$value]; ?>
                        <label class="choice choice--compact">
                            <input type="radio" name="status" value="<?= $value ?>" required <?= $value === 'delivering' && !$order['shipper_id'] ? 'disabled' : '' ?>>
                            <span class="status-pill status--<?= $value ?>"><?= e($label) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn btn--solid btn--block">Lưu trạng thái</button>
            </form>
            <?php else: ?><p class="muted">Đơn hàng đã kết thúc.</p><?php endif; ?>
            <?php if ($order['status'] === 'preparing' && !$order['shipper_id']): ?><p class="muted">Phân công tài xế trước khi chuyển sang đang giao.</p><?php endif; ?>
        </section>

        <section class="panel">
            <h2>Phân công giao hàng</h2>
            <?php $assignedShipper = null; foreach ($shippers as $candidate) { if ((int) $candidate['id'] === (int) $order['shipper_id']) { $assignedShipper = $candidate; } } ?>
            <p class="muted"><?= $assignedShipper ? e($assignedShipper['name']) . ' · ' . e($assignedShipper['phone'] ?? '') : 'Chưa phân công nhân viên giao hàng.' ?></p>
            <?php if (in_array($order['status'], ['confirmed', 'preparing'], true)): ?>
                <?php if ($shippers): ?>
                <form action="<?= url('/admin/orders/' . $order['id'] . '/assign') ?>" method="post" class="status-form">
                    <?= csrf_field() ?>
                    <div class="field"><label for="assign-shipper">Nhân viên giao hàng</label><select id="assign-shipper" name="shipper_id" required>
                        <option value="">Chọn nhân viên</option>
                        <?php foreach ($shippers as $candidate): ?><option value="<?= $candidate['id'] ?>" <?= (int) $candidate['id'] === (int) $order['shipper_id'] ? 'selected' : '' ?>><?= e($candidate['name']) ?> · <?= e($candidate['phone'] ?? '') ?></option><?php endforeach; ?>
                    </select></div>
                    <button class="btn btn--solid btn--block" type="submit">Lưu phân công</button>
                </form>
                <?php else: ?><a class="link-underline" href="<?= url('/admin/users') ?>">Cấp vai trò giao hàng cho nhân viên</a><?php endif; ?>
            <?php elseif ($order['status'] === 'pending'): ?><p class="muted">Xác nhận đơn trước khi phân công.</p><?php endif; ?>
        </section>

        <section class="panel">
            <h2>Thông tin giao hàng</h2>
            <dl class="info-list">
                <div><dt>Người nhận</dt><dd><?= e($order['receiver_name']) ?></dd></div>
                <div><dt>Điện thoại</dt><dd><a href="tel:<?= e($order['phone']) ?>"><?= e($order['phone']) ?></a></dd></div>
                <div><dt>Địa chỉ</dt><dd><?= e($order['address']) ?></dd></div>
                <div><dt>Thanh toán</dt><dd><?= e(App\Models\Order::PAYMENT_METHODS[$order['payment_method']]) ?></dd></div>
                <?php if ($order['note']): ?><div><dt>Ghi chú</dt><dd><?= e($order['note']) ?></dd></div><?php endif; ?>
            </dl>
            <?php $mapMode = 'view'; $mapOrder = $order; require BASE_PATH . '/app/Views/partials/delivery_map.php'; ?>
        </section>
        <?php require BASE_PATH . '/app/Views/partials/order_events.php'; ?>
    </div>
</div>
