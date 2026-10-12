/* Yumio Admin: xem trước ảnh / màu, xác nhận xoá, hàng bảng bấm được */
(() => {
    'use strict';

    const $ = (selector, root = document) => root.querySelector(selector);
    const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];

    document.addEventListener('DOMContentLoaded', () => {
        const preview = $('[data-media-preview]');
        const previewImg = $('[data-media-preview-img]');
        const colorInput = $('[data-color-input]');
        const swatches = $$('[data-swatch]');

        // Xem trước ảnh ngay khi chọn file (chưa cần upload)
        $('[data-image-input]')?.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file && previewImg) previewImg.src = URL.createObjectURL(file);
        });

        const setColor = (color) => {
            if (!colorInput) return;
            colorInput.value = color;
            preview?.style.setProperty('--tile-bg', color);
            swatches.forEach((swatch) => swatch.classList.toggle('is-active', swatch.dataset.swatch.toLowerCase() === color.toLowerCase()));
        };
        swatches.forEach((swatch) => swatch.addEventListener('click', () => setColor(swatch.dataset.swatch)));
        colorInput?.addEventListener('input', () => setColor(colorInput.value));
        if (colorInput) setColor(colorInput.value);

        // Chỉ xác nhận với thao tác xoá (không thể hoàn tác)
        $$('form[data-confirm]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (!window.confirm(form.dataset.confirm)) event.preventDefault();
            });
        });

        $$('tr[data-href]').forEach((row) => {
            row.addEventListener('click', (event) => {
                if (!event.target.closest('a, button, form')) window.location.href = row.dataset.href;
            });
        });
    });
})();
