/* Combobox địa chỉ: dữ liệu thật qua proxy Photon; không suy đoán vị trí. */
(() => {
    'use strict';
    const root = document.querySelector('[data-address-search]');
    if (!root) return;
    const input = root.querySelector('input');
    const list = root.querySelector('[role="listbox"]');
    const status = root.querySelector('[role="status"]');
    const cache = new Map();
    let timer, controller, version = 0, active = -1, results = [], lastRequest = 0, cooldown = 0, choosing = false, requests = [];
    const close = () => {
        list.hidden = true; input.setAttribute('aria-expanded', 'false');
        input.removeAttribute('aria-activedescendant'); active = -1;
    };
    function highlight(index) {
        active = index;
        [...list.children].forEach((option, i) => option.setAttribute('aria-selected', String(i === index)));
        const option = list.children[index];
        if (option) { input.setAttribute('aria-activedescendant', option.id); option.scrollIntoView({ block: 'nearest' }); }
    }
    function choose(index) {
        const item = results[index];
        if (!item) return;
        clearTimeout(timer); controller?.abort(); ++version;
        choosing = true;
        input.value = item.label; input.dispatchEvent(new Event('input', { bubbles: true }));
        choosing = false;
        close(); root.setAttribute('aria-busy', 'false');
        status.textContent = 'Đã chọn địa chỉ và cập nhật vị trí.';
        document.dispatchEvent(new CustomEvent('yumio:address-selected', { detail: item }));
        input.focus({ preventScroll: true });
    }
    function render(items) {
        results = items;
        list.replaceChildren();
        items.forEach((item, index) => {
            const option = document.createElement('li');
            option.id = `address-option-${index}`; option.setAttribute('role', 'option'); option.setAttribute('aria-selected', 'false');
            const title = document.createElement('strong'); title.textContent = item.name;
            const detail = document.createElement('span'); detail.textContent = item.label;
            option.append(title, detail);
            option.addEventListener('pointerdown', event => event.preventDefault());
            option.addEventListener('click', () => choose(index));
            list.append(option);
        });
        active = -1; input.removeAttribute('aria-activedescendant');
        list.hidden = items.length === 0; input.setAttribute('aria-expanded', String(items.length > 0));
        status.textContent = items.length ? `${items.length} gợi ý. Dùng ↑ ↓ và Enter để chọn, hoặc tiếp tục nhập địa chỉ.` : 'Chưa tìm thấy địa chỉ phù hợp. Thử thêm phường/xã, tỉnh/thành hoặc nhập tay; không tự ghim vị trí.';
    }
    async function search(query, current) {
        if (current !== version) return;
        if (cache.has(query)) { render(cache.get(query)); return; }
        const now = Date.now();
        requests = requests.filter(at => at > now - 60000);
        if (requests.length >= 12) { status.textContent = 'Tạm giới hạn tìm kiếm. Bạn có thể tiếp tục nhập địa chỉ thủ công.'; return; }
        requests.push(now); lastRequest = now;
        controller = new AbortController(); const thisController = controller;
        const timeout = setTimeout(() => thisController.abort(), 7000);
        status.textContent = 'Đang tìm địa chỉ ở Việt Nam…'; root.setAttribute('aria-busy', 'true');
        try {
            const url = new URL(root.dataset.endpoint, location.href); url.searchParams.set('q', query);
            const response = await fetch(url, { signal: thisController.signal, headers: { Accept: 'application/json' }, credentials: 'same-origin' });
            const data = await response.json().catch(() => null);
            if (current !== version || input.value.trim() !== query) return;
            if (!data || response.redirected) throw new Error('Chưa tải được gợi ý. Kiểm tra đăng nhập hoặc tiếp tục nhập địa chỉ thủ công.');
            if (!response.ok || !data.ok) {
                cooldown = Date.now() + Number(data.retry_after || 2) * 1000;
                throw new Error(data.message || 'Dịch vụ gợi ý chưa phản hồi. Bạn vẫn có thể nhập tay.');
            }
            const items = (Array.isArray(data.results) ? data.results : []).filter(item => typeof item.label === 'string' && typeof item.name === 'string'
                && Number.isFinite(item.lat) && Number.isFinite(item.lng) && Math.abs(item.lat) <= 90 && Math.abs(item.lng) <= 180).slice(0, 5);
            if (cache.size >= 30) cache.delete(cache.keys().next().value);
            cache.set(query, items); render(items);
        } catch (error) {
            if (current !== version) return;
            close(); status.textContent = error.name === 'AbortError' ? 'Dịch vụ phản hồi chậm. Bạn vẫn có thể nhập địa chỉ và đặt COD.'
                : error instanceof TypeError ? 'Không kết nối được dịch vụ gợi ý. Bạn vẫn có thể nhập địa chỉ thủ công.'
                : (error.message || 'Chưa tải được gợi ý. Bạn có thể nhập địa chỉ thủ công.');
        } finally {
            clearTimeout(timeout);
            if (current === version) root.setAttribute('aria-busy', 'false');
        }
    }
    input.addEventListener('input', () => {
        if (choosing) return;
        clearTimeout(timer); controller?.abort(); ++version;
        close(); results = []; root.setAttribute('aria-busy', 'false');
        document.dispatchEvent(new CustomEvent('yumio:address-cleared'));
        const query = input.value.trim();
        if (query.length < 4) { status.textContent = 'Nhập ít nhất 4 ký tự để tìm địa chỉ. Nhập đầy đủ số nhà nếu có.'; return; }
        const current = version;
        timer = setTimeout(() => search(query, current), Math.max(700, lastRequest + 1400 - Date.now(), cooldown - Date.now()));
    });
    input.addEventListener('keydown', event => {
        if (event.isComposing) return;
        if (event.key === 'ArrowDown' && results.length) {
            event.preventDefault(); list.hidden = false; input.setAttribute('aria-expanded', 'true'); highlight((active + 1) % results.length);
        } else if (event.key === 'ArrowUp' && results.length) {
            event.preventDefault(); highlight(active <= 0 ? results.length - 1 : active - 1);
        } else if (event.key === 'Enter') {
            event.preventDefault(); event.stopPropagation();
            if (!list.hidden && active >= 0) choose(active);
        } else if (event.key === 'Escape' || event.key === 'Tab') close();
    });
    document.addEventListener('pointerdown', event => { if (!root.contains(event.target)) close(); });
})();
