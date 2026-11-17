<div class="container container--narrow">
    <header class="page-head"><p class="eyebrow">Yumio · Giao hàng</p><h1>Đơn giao của tôi</h1><p>Chỉ các đơn được phân công cho bạn xuất hiện tại đây.</p></header>
    <div class="order-list">
        <?php if (!$orders): ?><section class="form-card"><h2>Chưa có đơn được phân công</h2><p class="muted">Admin sẽ phân công khi đơn được xác nhận.</p></section><?php endif; ?>
        <?php foreach ($orders as $order): ?>
            <a class="order-card" href="<?= url('/shipper/orders/' . $order['id']) ?>">
                <div><div class="order-card__head"><strong>#<?= $order['id'] ?> · <?= e($order['receiver_name']) ?></strong><span class="status-pill status--<?= e($order['status']) ?>"><?= e(App\Models\Order::STATUSES[$order['status']]) ?></span></div><p class="order-card__meta"><?= e($order['address']) ?></p></div>
                <span class="order-card__total"><?= format_price($order['total_amount']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>
