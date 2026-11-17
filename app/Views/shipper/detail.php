<div class="container container--narrow">
    <header class="page-head"><a class="link-underline" href="<?= url('/shipper') ?>">← Đơn giao của tôi</a><h1>Giao đơn #<?= $order['id'] ?></h1><p><span class="status-pill status--<?= e($order['status']) ?>"><?= e(App\Models\Order::STATUSES[$order['status']]) ?></span></p></header>
    <section class="form-card">
        <h2>Thông tin nhận hàng</h2>
        <dl class="info-list">
            <div><dt>Người nhận</dt><dd><?= e($order['receiver_name']) ?></dd></div>
            <div><dt>Điện thoại</dt><dd><a href="tel:<?= e($order['phone']) ?>"><?= e($order['phone']) ?></a></dd></div>
            <div><dt>Địa chỉ</dt><dd><?= e($order['address']) ?></dd></div>
            <div><dt>Thanh toán</dt><dd><?= e(App\Models\Order::PAYMENT_METHODS[$order['payment_method']]) ?> · <?= format_price($order['total_amount']) ?></dd></div>
            <?php if ($order['note']): ?><div><dt>Ghi chú</dt><dd><?= e($order['note']) ?></dd></div><?php endif; ?>
        </dl>
        <?php $mapMode = 'view'; $mapOrder = $order; require BASE_PATH . '/app/Views/partials/delivery_map.php'; ?>
        <?php if (in_array($order['status'], ['preparing', 'delivering'], true)): ?>
            <form action="<?= url('/shipper/orders/' . $order['id'] . '/status') ?>" method="post">
                <?= csrf_field() ?><input type="hidden" name="status" value="<?= $order['status'] === 'preparing' ? 'delivering' : 'completed' ?>">
                <button class="btn btn--solid btn--block" type="submit"><?= $order['status'] === 'preparing' ? 'Đã nhận món · Bắt đầu giao' : 'Đã giao thành công' ?></button>
            </form>
        <?php elseif ($order['status'] === 'confirmed'): ?><p class="muted">Chờ bếp chuẩn bị món trước khi nhận giao.</p><?php endif; ?>
    </section>
    <section class="form-card"><h2>Món cần giao</h2><?php foreach ($items as $item): ?><p><?= (int) $item['quantity'] ?> × <?= e($item['name']) ?></p><?php endforeach; ?></section>
    <?php require BASE_PATH . '/app/Views/partials/order_events.php'; ?>
    <div class="section-cta"></div>
</div>
