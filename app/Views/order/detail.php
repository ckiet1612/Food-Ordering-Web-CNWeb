<?php
$steps = ['pending', 'confirmed', 'delivering', 'completed'];
$currentStep = array_search($order['status'], $steps, true);
?>
<div class="container container--wide">
    <header class="page-head">
        <nav class="breadcrumb" style="margin-bottom: 16px"><a href="<?= url('/orders') ?>">Đơn hàng của tôi</a><span>/</span><span>#<?= $order['id'] ?></span></nav>
        <h1>Đơn #<?= $order['id'] ?></h1>
        <p>Đặt lúc <?= date('H:i, d/m/Y', strtotime($order['created_at'])) ?></p>
    </header>

    <div class="checkout-layout">
        <div>
            <section class="form-card">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px">
                    <h2>Trạng thái</h2>
                    <span class="status-pill status--<?= $order['status'] ?>"><?= e(App\Models\Order::STATUSES[$order['status']]) ?></span>
                </div>
                <?php if ($order['status'] !== 'cancelled'): ?>
                    <div class="timeline">
                        <?php foreach ($steps as $index => $step): ?>
                            <div class="timeline__step <?= $currentStep !== false && $index <= $currentStep ? 'is-done' : '' ?>">
                                <span class="timeline__bar"></span>
                                <?= e(App\Models\Order::STATUSES[$step]) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="muted">Đơn hàng này đã được huỷ.</p>
                <?php endif; ?>
            </section>

            <section class="form-card">
                <h2>Giao tới</h2>
                <dl class="info-list">
                    <div><dt>Người nhận</dt><dd><?= e($order['receiver_name']) ?></dd></div>
                    <div><dt>Điện thoại</dt><dd><?= e($order['phone']) ?></dd></div>
                    <div><dt>Địa chỉ</dt><dd><?= e($order['address']) ?></dd></div>
                    <div><dt>Thanh toán</dt><dd><?= e(App\Models\Order::PAYMENT_METHODS[$order['payment_method']]) ?></dd></div>
                    <?php if ($order['note']): ?><div><dt>Ghi chú</dt><dd><?= e($order['note']) ?></dd></div><?php endif; ?>
                </dl>
            </section>

            <?php if ($order['status'] === 'pending'): ?>
                <form action="<?= url('/orders/' . $order['id'] . '/cancel') ?>" method="post" style="margin-top: 20px" onsubmit="return confirm('Bạn chắc chắn muốn huỷ đơn hàng này?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn">Huỷ đơn hàng</button>
                </form>
            <?php endif; ?>
        </div>

        <aside class="summary" aria-label="Các món đã đặt">
            <h2>Món đã đặt</h2>
            <div class="summary__items">
                <?php foreach ($items as $item): ?>
                    <div class="summary__item">
                        <span class="tile-media" style="--tile-bg: <?= e($item['bg_color']) ?>"><img src="<?= image_url($item['image']) ?>" alt="" loading="lazy"></span>
                        <span><?= e($item['name']) ?><small><?= format_price($item['price']) ?> × <?= $item['quantity'] ?></small></span>
                        <span><?= format_price($item['price'] * $item['quantity']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="summary__row"><span>Tạm tính</span><span><?= format_price($order['subtotal']) ?></span></div>
            <div class="summary__row"><span>Phí giao hàng</span><span><?= $order['shipping_fee'] > 0 ? format_price($order['shipping_fee']) : 'Miễn phí' ?></span></div>
            <div class="summary__row summary__row--total"><span>Tổng cộng</span><span><?= format_price($order['total_amount']) ?></span></div>
            <a href="<?= url('/menu') ?>" class="btn btn--block">Đặt thêm món</a>
        </aside>
    </div>
</div>
