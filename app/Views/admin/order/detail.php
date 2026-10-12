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
            <form action="<?= url('/admin/orders/' . $order['id'] . '/status') ?>" method="post" class="status-form">
                <?= csrf_field() ?>
                <div class="choice-list">
                    <?php foreach (App\Models\Order::STATUSES as $value => $label): ?>
                        <label class="choice choice--compact">
                            <input type="radio" name="status" value="<?= $value ?>" <?= $order['status'] === $value ? 'checked' : '' ?>>
                            <span class="status-pill status--<?= $value ?>"><?= e($label) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn btn--solid btn--block">Lưu trạng thái</button>
            </form>
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
        </section>
    </div>
</div>
