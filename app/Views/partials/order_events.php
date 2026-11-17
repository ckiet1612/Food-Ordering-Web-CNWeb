<section class="form-card order-events">
    <h2>Tiến trình đơn hàng</h2>
    <p class="muted">Cập nhật gần nhất khi tải trang. <a class="link-underline" href="<?= e(url(current_path())) ?>">Làm mới tiến trình</a></p>
    <?php if (!$events): ?><p class="muted">Đơn cũ chưa có lịch sử cập nhật.</p><?php endif; ?>
    <ol class="event-list">
        <?php foreach ($events as $event): ?>
            <li><span class="event-dot" aria-hidden="true"></span><div><strong><?= e($event['note']) ?></strong><time datetime="<?= e(date('c', strtotime($event['created_at']))) ?>"><?= date('H:i:s · d/m/Y', strtotime($event['created_at'])) ?></time></div></li>
        <?php endforeach; ?>
    </ol>
</section>
