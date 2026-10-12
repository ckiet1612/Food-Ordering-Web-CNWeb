<article class="store-card">
    <h3><?= e($store['name']) ?></h3>
    <p><?= icon('pin', 18) ?> <?= e($store['address']) ?></p>
    <p><?= icon('clock', 18) ?> <?= e($store['hours']) ?></p>
    <p><?= icon('phone', 18) ?> <?= e($store['phone']) ?></p>
    <a href="https://www.google.com/maps/search/?api=1&amp;query=<?= urlencode($store['address']) ?>" target="_blank" rel="noopener" class="link-underline">Chỉ đường</a>
</article>
