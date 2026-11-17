<?php
$mapConfig = config('delivery');
$mapPick = $mapMode === 'pick';
$mapLat = $mapPick ? old('delivery_lat', '') : ($mapOrder['delivery_lat'] ?? '');
$mapLng = $mapPick ? old('delivery_lng', '') : ($mapOrder['delivery_lng'] ?? '');
// Dữ liệu cũ từ request có thể sai định dạng; bản đồ không được làm hỏng checkout.
$mapLat = is_scalar($mapLat) ? (string) $mapLat : '';
$mapLng = is_scalar($mapLng) ? (string) $mapLng : '';
?>
<div class="delivery-map <?= $mapPick ? 'delivery-map--inline' : '' ?>" data-delivery-map data-mode="<?= $mapPick ? 'pick' : 'view' ?>" <?= $mapPick ? 'data-map-auto' : '' ?>
     data-store-lat="<?= e($mapConfig['lat']) ?>" data-store-lng="<?= e($mapConfig['lng']) ?>"
     data-lat="<?= e($mapLat) ?>" data-lng="<?= e($mapLng) ?>" data-tiles="<?= e($mapConfig['tile_url']) ?>"
     data-library="<?= url('/vendor/leaflet/leaflet.js') ?>" data-styles="<?= url('/vendor/leaflet/leaflet.css') ?>">
    <?php if (!$mapPick): ?><div class="delivery-map__head"><strong><?= icon('pin', 18) ?> Vị trí nhận hàng</strong><span class="eyebrow">Bản đồ</span></div><?php endif; ?>
    <?php if ($mapPick): ?>
        <input type="hidden" name="delivery_lat" value="<?= e($mapLat) ?>" data-map-lat>
        <input type="hidden" name="delivery_lng" value="<?= e($mapLng) ?>" data-map-lng>
    <?php endif; ?>
    <?php if ($mapPick || ($mapLat !== '' && $mapLng !== '')): ?>
        <?php if (!$mapPick): ?><div class="delivery-map__actions"><button type="button" class="btn btn--sm" data-map-open aria-expanded="false">Mở bản đồ</button></div><?php endif; ?>
        <div class="delivery-map__canvas" data-map-canvas <?= $mapPick ? '' : 'hidden' ?> aria-label="Bản đồ vị trí giao hàng"></div>
        <?php if ($mapPick): ?><div class="delivery-map__actions"><button type="button" class="btn btn--sm" data-map-locate hidden><?= icon('pin', 18) ?> Vị trí của tôi</button><button type="button" class="btn btn--sm" data-map-clear>Bỏ ghim</button><button type="button" class="btn btn--sm" data-map-retry hidden>Thử lại bản đồ</button></div><?php endif; ?>
        <p class="delivery-map__message muted" data-map-message role="status"><?= $mapPick ? 'Đang tải bản đồ…' : 'Đã lưu ghim vị trí nhận hàng.' ?></p>
        <?php if ($mapPick): ?><noscript><p class="muted">Bật JavaScript để dùng bản đồ, hoặc tiếp tục nhập địa chỉ và đặt COD.</p></noscript><?php endif; ?>
        <?php if (!$mapPick): ?><a class="link-underline" href="<?= e('https://www.openstreetmap.org/?mlat=' . rawurlencode($mapLat) . '&mlon=' . rawurlencode($mapLng) . '#map=17/' . rawurlencode($mapLat) . '/' . rawurlencode($mapLng)) ?>" target="_blank" rel="noopener noreferrer">Mở trong OpenStreetMap ↗</a><?php endif; ?>
    <?php else: ?><p class="muted">Đơn này dùng địa chỉ chữ, chưa có ghim vị trí.</p><?php endif; ?>
    <?php if (!$mapPick && isset($mapOrder['distance_km'])): ?><p class="muted">Khoảng cách đường chim bay từ <?= e($mapConfig['name']) ?>: <?= number_format((float) $mapOrder['distance_km'], 2, ',', '.') ?> km. Không phải quãng đường lái xe.</p><?php endif; ?>
</div>
<?php if (!isset($deliveryMapScriptLoaded)): $deliveryMapScriptLoaded = true; ?>
<script src="<?= asset('js/delivery-map.js') ?>" defer></script>
<?php endif; ?>
