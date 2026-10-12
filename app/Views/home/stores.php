<section class="page-hero">
    <p class="eyebrow">Cửa hàng</p>
    <h1>Tìm Yumio gần bạn</h1>
    <p>Ăn tại chỗ, mang đi hoặc đặt giao tận nơi — Yumio luôn ở gần hơn bạn nghĩ.</p>
</section>

<div class="container" style="padding-bottom: var(--section-space)">
    <div class="store-list">
        <?php foreach ($stores as $store): ?>
            <?php require BASE_PATH . '/app/Views/partials/store_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>
