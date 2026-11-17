/* Dùng chung bản đồ: checkout tự mở, trang chi tiết mở theo yêu cầu. */
(() => {
    'use strict';
    let library;
    function load(root) {
        return library ??= new Promise((resolve, reject) => {
            if (window.L) { resolve(window.L); return; }
            const css = document.createElement('link');
            css.rel = 'stylesheet'; css.href = root.dataset.styles;
            document.head.append(css);
            const script = document.createElement('script');
            script.src = root.dataset.library;
            const timeout = setTimeout(() => reject(new Error('timeout')), 7000);
            script.onload = () => { clearTimeout(timeout); window.L ? resolve(window.L) : reject(new Error('library')); };
            script.onerror = () => { clearTimeout(timeout); reject(new Error('network')); };
            document.head.append(script);
        });
    }

    document.querySelectorAll('[data-delivery-map]').forEach((root) => {
        const open = root.querySelector('[data-map-open]');
        const auto = root.hasAttribute('data-map-auto');
        const canvas = root.querySelector('[data-map-canvas]');
        if (!canvas || (!open && !auto)) return;
        const retry = root.querySelector('[data-map-retry]');
        const message = root.querySelector('[data-map-message]');
        const locate = root.querySelector('[data-map-locate]');
        const latInput = root.querySelector('[data-map-lat]');
        const lngInput = root.querySelector('[data-map-lng]');
        const pick = root.dataset.mode === 'pick';
        const store = [Number(root.dataset.storeLat), Number(root.dataset.storeLng)];
        let map, marker, selected = null, loading = false;
        if (root.dataset.lat !== '' && root.dataset.lng !== '') {
            const lat = Number(root.dataset.lat), lng = Number(root.dataset.lng);
            if (Number.isFinite(lat) && Number.isFinite(lng) && Math.abs(lat) <= 90 && Math.abs(lng) <= 180) selected = [lat, lng];
        }
        function mark(point) {
            selected = point;
            if (marker) marker.remove();
            if (map) marker = window.L.circleMarker(point, { radius: 10, color: '#211d19', weight: 3, fillColor: '#ffce49', fillOpacity: 1 }).addTo(map).bindPopup('Điểm nhận hàng');
            if (pick) {
                latInput.value = point[0].toFixed(7); lngInput.value = point[1].toFixed(7);
            }
            message.textContent = auto ? 'Đã chọn vị trí nhận hàng.' : map ? `Đã ghim vị trí. Khoảng cách đường chim bay: ${(map.distance(store, point) / 1000).toFixed(2)} km; phí giao hàng không đổi.` : 'Đã lưu toạ độ từ địa chỉ đã chọn; đang mở bản đồ…';
        }
        async function openMap(force = false) {
            if (loading) return loading;
            if (map) {
                canvas.hidden = auto || force ? false : !canvas.hidden;
                open?.setAttribute('aria-expanded', String(!canvas.hidden));
                if (open) open.textContent = canvas.hidden ? 'Mở bản đồ' : 'Thu gọn bản đồ';
                if (!canvas.hidden) map.invalidateSize();
                return;
            }
            root.dataset.mapState = 'loading';
            if (retry) retry.hidden = true;
            if (open) open.textContent = 'Đang mở…';
            loading = (async () => { try {
                const L = await load(root);
                canvas.hidden = false;
                map = L.map(canvas).setView(selected || store, 15);
                L.tileLayer(root.dataset.tiles, { maxZoom: 19, attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' })
                    .on('tileerror', () => { message.textContent = 'Chưa tải được nền bản đồ. Vẫn có thể nhập địa chỉ và đặt COD.'; }).addTo(map);
                L.circleMarker(store, { radius: 7, color: '#e9573d', fillOpacity: 1 }).addTo(map).bindPopup('Điểm xuất phát Yumio');
                if (selected) { mark(selected); map.fitBounds([store, selected], { padding: [30, 30], maxZoom: 16 }); }
                else message.textContent = auto ? 'Chọn gợi ý địa chỉ hoặc chạm bản đồ để đặt ghim.' : 'Chạm hoặc nhấp bản đồ để ghim điểm nhận hàng.';
                if (pick) map.on('click', (event) => mark([event.latlng.lat, event.latlng.lng]));
                if (locate) locate.hidden = false;
                root.dataset.mapState = 'ready';
                if (open) { open.textContent = 'Thu gọn bản đồ'; open.setAttribute('aria-expanded', 'true'); }
            } catch {
                map?.remove(); map = null; marker = null;
                canvas.hidden = !auto;
                root.dataset.mapState = 'error';
                message.textContent = 'Không tải được bản đồ. Bạn vẫn có thể nhập địa chỉ và đặt COD.';
                if (open) open.textContent = 'Thử mở lại bản đồ';
                if (retry) retry.hidden = false;
                if (locate) locate.hidden = true;
                library = null;
            } finally { loading = false; } })();
            return loading;
        }
        open?.addEventListener('click', () => openMap());
        retry?.addEventListener('click', () => openMap(true));
        if (pick) {
            document.addEventListener('yumio:address-selected', async event => {
                const { lat, lng } = event.detail;
                if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
                mark([lat, lng]);
                await openMap(true);
                if (map && selected) { mark(selected); map.setView(selected, 17); }
            });
            document.addEventListener('yumio:address-cleared', () => {
                marker?.remove(); marker = null; selected = null;
                latInput.value = ''; lngInput.value = '';
                message.textContent = 'Chọn gợi ý để cập nhật vị trí, hoặc tiếp tục nhập tay.';
            });
        }
        locate?.addEventListener('click', () => {
            if (!navigator.geolocation) { message.textContent = 'Thiết bị chưa hỗ trợ định vị. Hãy chọn điểm trên bản đồ.'; return; }
            message.textContent = 'Đang xin vị trí thiết bị…';
            navigator.geolocation.getCurrentPosition((position) => {
                mark([position.coords.latitude, position.coords.longitude]); map.setView(selected, 17);
            }, () => { message.textContent = 'Không lấy được vị trí. Hãy chọn trên bản đồ hoặc nhập địa chỉ.'; }, { timeout: 8000, maximumAge: 60000 });
        });
        root.querySelector('[data-map-clear]')?.addEventListener('click', () => {
            marker?.remove(); marker = null; selected = null;
            latInput.value = ''; lngInput.value = '';
            message.textContent = 'Đã bỏ ghim. Đơn hàng sẽ dùng địa chỉ bạn nhập.';
        });
        if (auto) openMap(true);
    });
})();
