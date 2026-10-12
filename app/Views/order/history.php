<div class="container container--narrow">
    <header class="page-head">
        <h1>Đơn hàng của tôi</h1>
    </header>

    <?php if (!$orders): ?>
        <div class="empty" style="padding-bottom: var(--section-space)">
            <h2 class="serif-title">Bạn chưa có đơn nào</h2>
            <p>Đơn hàng đầu tiên luôn là đơn đáng nhớ nhất. Bắt đầu thôi!</p>
            <a href="<?= url('/menu') ?>" class="btn btn--solid btn--lg">Xem thực đơn</a>
        </div>
    <?php else: ?>
        <div class="order-list">
            <?php foreach ($orders as $order): ?>
                <a href="<?= url('/orders/' . $order['id']) ?>" class="order-card">
                    <div>
                        <div class="order-card__head">
                            <strong>Đơn #<?= $order['id'] ?></strong>
                            <span class="status-pill status--<?= $order['status'] ?>"><?= e(App\Models\Order::STATUSES[$order['status']]) ?></span>
                        </div>
                        <p class="order-card__meta"><?= date('H:i · d/m/Y', strtotime($order['created_at'])) ?> · <?= (int) $order['item_count'] ?> món</p>
                    </div>
                    <span class="order-card__total"><?= format_price($order['total_amount']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
