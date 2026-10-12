/* ==========================================================================
   Yumio — tương tác phía khách hàng (không dùng thư viện ngoài)
   ========================================================================== */
(() => {
    'use strict';

    const $ = (selector, root = document) => root.querySelector(selector);
    const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const formatPrice = (value) => new Intl.NumberFormat('vi-VN').format(value) + '₫';

    /* ----------------------------------------------------------------------
       Spring — tham số kiểu Apple: damping ratio (độ nảy) + response (giây).
       Luôn chạy từ giá trị hiện tại và giữ vận tốc khi đổi đích, nên có thể
       bị ngắt / đảo chiều bất cứ lúc nào mà không giật.
       ---------------------------------------------------------------------- */
    class Spring {
        constructor({ value = 0, damping = 1, response = 0.4, onUpdate = () => {}, onRest = () => {} }) {
            this.value = value;
            this.target = value;
            this.velocity = 0;
            this.onUpdate = onUpdate;
            this.onRest = onRest;
            this.frame = 0;
            this.configure(damping, response);
        }

        configure(damping, response) {
            this.damping = damping;
            this.response = response;
            this.k = (2 * Math.PI / response) ** 2;    // stiffness (mass = 1)
            this.c = (4 * Math.PI * damping) / response; // damping coefficient
        }

        to(target, { velocity, damping = this.damping, response = this.response } = {}) {
            this.target = target;
            if (velocity !== undefined) this.velocity = velocity;
            this.configure(damping, response);

            if (reduceMotion.matches) {
                this.set(target);
                this.onRest();
                return;
            }
            if (!this.frame) {
                this.last = performance.now();
                this.frame = requestAnimationFrame(this.step);
            }
        }

        set(value) {
            this.stop();
            this.value = this.target = value;
            this.velocity = 0;
            this.onUpdate(value);
        }

        stop() {
            cancelAnimationFrame(this.frame);
            this.frame = 0;
        }

        step = (now) => {
            let dt = Math.min((now - this.last) / 1000, 1 / 30);
            this.last = now;

            // Tích phân nhiều bước nhỏ cố định để ổn định ở mọi tần số màn hình
            while (dt > 0) {
                const h = Math.min(dt, 1 / 240);
                const force = -this.k * (this.value - this.target) - this.c * this.velocity;
                this.velocity += force * h;
                this.value += this.velocity * h;
                dt -= h;
            }

            if (Math.abs(this.velocity) < 4 && Math.abs(this.value - this.target) < 0.3) {
                this.value = this.target;
                this.velocity = 0;
                this.frame = 0;
                this.onUpdate(this.value);
                this.onRest();
                return;
            }

            this.onUpdate(this.value);
            this.frame = requestAnimationFrame(this.step);
        };
    }

    // Vị trí dừng dự đoán theo đà (công thức trong sample code "Designing Fluid Interfaces")
    const project = (velocity, decelerationRate = 0.998) =>
        (velocity / 1000) * decelerationRate / (1 - decelerationRate);

    // Kéo quá biên: càng xa càng nặng tay, không dừng cứng
    const rubberband = (overshoot, dimension, constant = 0.55) =>
        (overshoot * dimension * constant) / (dimension + constant * Math.abs(overshoot));

    /* ----------------------------------------------------------------------
       Carousel kéo được
       ---------------------------------------------------------------------- */
    function initCarousel(root) {
        const viewport = $('[data-carousel-viewport]', root);
        const track = $('[data-carousel-track]', root);
        const slides = $$('.carousel__slide', track);
        const prevButton = $('[data-carousel-prev]', root);
        const nextButton = $('[data-carousel-next]', root);
        if (!viewport || !track || slides.length === 0) return;

        let x = 0;
        let min = 0;
        let snaps = [0];

        const spring = new Spring({
            damping: 1,
            response: 0.45,
            onUpdate: (value) => { x = value; render(); },
        });

        function measure() {
            min = Math.min(0, viewport.clientWidth - track.scrollWidth);
            const padding = parseFloat(getComputedStyle(track).paddingLeft) || 0;
            snaps = [...new Set(slides.map((slide) => Math.max(min, -(slide.offsetLeft - padding))).concat(min))]
                .sort((a, b) => b - a);
            spring.set(Math.min(0, Math.max(min, x)));
        }

        function render() {
            track.style.transform = `translate3d(${x}px, 0, 0)`;
            if (prevButton) prevButton.disabled = x >= -1;
            if (nextButton) nextButton.disabled = x <= min + 1;
        }

        const nearestSnap = (position) =>
            snaps.reduce((best, snap) => (Math.abs(snap - position) < Math.abs(best - position) ? snap : best), snaps[0]);

        const withRubberband = (raw) => {
            if (raw > 0) return rubberband(raw, viewport.clientWidth);
            if (raw < min) return min - rubberband(min - raw, viewport.clientWidth);
            return raw;
        };

        // --- Kéo bằng chuột / cảm ứng ---
        let pointerId = null;
        let startX = 0;
        let startPosition = 0;
        let dragging = false;
        let suppressClick = false;
        let history = [];

        viewport.addEventListener('pointerdown', (event) => {
            if (event.button !== 0) return;
            spring.stop(); // nắm lại giữa chừng: bắt đầu từ vị trí đang hiển thị
            pointerId = event.pointerId;
            startX = event.clientX;
            startPosition = x;
            dragging = false;
            suppressClick = false;
            history = [{ x: event.clientX, t: event.timeStamp }];
        });

        viewport.addEventListener('pointermove', (event) => {
            if (event.pointerId !== pointerId) return;

            // Ngưỡng ~8px trước khi xác nhận là kéo (để vẫn click được vào thẻ)
            if (!dragging) {
                if (Math.abs(event.clientX - startX) < 8) return;
                dragging = true;
                suppressClick = true;
                startX = event.clientX;
                startPosition = x;
                viewport.setPointerCapture(pointerId);
                viewport.classList.add('is-dragging');
            }

            x = withRubberband(startPosition + (event.clientX - startX));
            render();

            history.push({ x: event.clientX, t: event.timeStamp });
            while (history.length > 2 && event.timeStamp - history[0].t > 100) history.shift();
        });

        const release = (event) => {
            if (event.pointerId !== pointerId) return;
            pointerId = null;
            if (!dragging) return;
            dragging = false;
            viewport.classList.remove('is-dragging');

            // Vận tốc lúc thả tay (px/s) từ ~100ms chuyển động cuối
            const first = history[0];
            const last = history[history.length - 1];
            const elapsed = last.t - first.t;
            const stale = event.timeStamp - last.t > 60;
            const velocity = elapsed > 0 && !stale ? ((last.x - first.x) / elapsed) * 1000 : 0;

            const target = nearestSnap(Math.min(0, Math.max(min, x + project(velocity))));

            // Chỉ nảy nhẹ khi người dùng "ném" (có đà); còn lại dừng êm không nảy
            spring.value = x;
            spring.to(target, { velocity, damping: Math.abs(velocity) > 400 ? 0.85 : 1, response: 0.45 });
        };

        viewport.addEventListener('pointerup', release);
        viewport.addEventListener('pointercancel', release);

        // Không mở link nếu vừa kéo
        viewport.addEventListener('click', (event) => {
            if (suppressClick) {
                event.preventDefault();
                event.stopPropagation();
                suppressClick = false;
            }
        }, true);

        // --- Nút trước / sau: luôn tính từ đích hiện tại để bấm liên tiếp vẫn mượt ---
        const go = (direction) => {
            const current = snaps.indexOf(nearestSnap(spring.target));
            const index = Math.max(0, Math.min(snaps.length - 1, current + direction));
            spring.value = x;
            spring.to(snaps[index], { damping: 1, response: 0.5 });
        };
        prevButton?.addEventListener('click', () => go(-1));
        nextButton?.addEventListener('click', () => go(1));

        viewport.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowRight') { event.preventDefault(); go(1); }
            if (event.key === 'ArrowLeft') { event.preventDefault(); go(-1); }
        });

        // --- Vuốt ngang bằng trackpad ---
        let wheelTimer = 0;
        viewport.addEventListener('wheel', (event) => {
            if (Math.abs(event.deltaX) <= Math.abs(event.deltaY)) return;
            event.preventDefault();
            spring.stop();
            x = Math.min(0, Math.max(min, x - event.deltaX));
            render();
            clearTimeout(wheelTimer);
            wheelTimer = setTimeout(() => {
                spring.value = x;
                spring.to(nearestSnap(x), { damping: 1, response: 0.4 });
            }, 140);
        }, { passive: false });

        // Tab tới thẻ nằm ngoài khung nhìn -> trượt tới thẻ đó
        track.addEventListener('focusin', (event) => {
            const slide = event.target.closest('.carousel__slide');
            if (!slide) return;
            const rect = slide.getBoundingClientRect();
            const box = viewport.getBoundingClientRect();
            if (rect.left < box.left || rect.right > box.right) {
                const padding = parseFloat(getComputedStyle(track).paddingLeft) || 0;
                viewport.scrollLeft = 0;
                spring.value = x;
                spring.to(Math.max(min, Math.min(0, -(slide.offsetLeft - padding))));
            }
        });

        new ResizeObserver(measure).observe(viewport);
        window.addEventListener('load', measure, { once: true });
        measure();
    }

    /* ----------------------------------------------------------------------
       Header: hiện scroll-edge khi nội dung cuộn dưới lớp kính
       ---------------------------------------------------------------------- */
    function initHeader() {
        const header = $('[data-header]');
        if (!header) return;

        let ticking = false;
        const update = () => {
            header.classList.toggle('is-scrolled', window.scrollY > 4);
            ticking = false;
        };
        window.addEventListener('scroll', () => {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(update);
            }
        }, { passive: true });
        update();

        // Đóng menu tài khoản khi click ra ngoài / bấm Esc
        $$('[data-popover]').forEach((details) => {
            document.addEventListener('click', (event) => {
                if (details.open && !details.contains(event.target)) details.open = false;
            });
            details.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    details.open = false;
                    $('summary', details).focus();
                }
            });
        });
    }

    /* ----------------------------------------------------------------------
       Menu mobile (dialog): trượt vào từ trái, đóng cũng trượt về trái
       ---------------------------------------------------------------------- */
    function initNavSheet() {
        const sheet = $('[data-nav-sheet]');
        const openButton = $('[data-nav-open]');
        if (!sheet || !openButton) return;

        let closing = 0;

        const open = () => {
            clearTimeout(closing);
            if (!sheet.open) sheet.showModal();
            requestAnimationFrame(() => sheet.classList.add('is-open'));
            document.documentElement.style.overflow = 'hidden';
        };

        const close = () => {
            sheet.classList.remove('is-open');
            document.documentElement.style.overflow = '';
            closing = setTimeout(() => sheet.close(), reduceMotion.matches ? 200 : 420);
        };

        openButton.addEventListener('click', open);
        $('[data-nav-close]', sheet)?.addEventListener('click', close);
        sheet.addEventListener('cancel', (event) => { event.preventDefault(); close(); });
        sheet.addEventListener('click', (event) => { if (event.target === sheet) close(); });
        $$('a', sheet).forEach((link) => link.addEventListener('click', close));
    }

    /* ----------------------------------------------------------------------
       Toast (flash message)
       ---------------------------------------------------------------------- */
    function dismissToast(toast) {
        if (!toast || toast.classList.contains('is-leaving')) return;
        toast.classList.add('is-leaving');
        toast.addEventListener('animationend', () => toast.remove(), { once: true });
    }

    function showToast(message, type = 'success') {
        let stack = $('[data-toast-stack]');
        if (!stack) {
            stack = document.createElement('div');
            stack.className = 'toast-stack';
            stack.dataset.toastStack = '';
            document.body.append(stack);
        }
        const toast = document.createElement('div');
        toast.className = `toast toast--${type}`;
        toast.setAttribute('role', type === 'error' ? 'alert' : 'status');
        toast.innerHTML = `<span class="toast__icon">${type === 'error' ? '!' : '✓'}</span><p></p>`;
        $('p', toast).textContent = message;
        stack.append(toast);
        setTimeout(() => dismissToast(toast), 4500);
    }

    function initToasts() {
        $$('[data-toast]').forEach((toast) => {
            $('[data-toast-close]', toast)?.addEventListener('click', () => dismissToast(toast));
            setTimeout(() => dismissToast(toast), 5000);
        });
    }

    /* ----------------------------------------------------------------------
       Giỏ hàng: thêm món bằng fetch, cập nhật số lượng, popover xác nhận
       ---------------------------------------------------------------------- */
    const cartPopover = (() => {
        const el = $('[data-cart-popover]');
        let hideTimer = 0;
        let hiddenTimer = 0;

        const hide = () => {
            if (!el) return;
            clearTimeout(hideTimer);
            el.classList.remove('is-open');
            hiddenTimer = setTimeout(() => { el.hidden = true; }, 400);
        };

        const show = (item, message) => {
            if (!el) return;
            clearTimeout(hiddenTimer); // mở lại khi đang đóng: tiếp tục từ trạng thái hiện tại
            $('[data-cart-popover-message]', el).textContent = message;
            $('[data-cart-popover-name]', el).textContent = item.name;
            $('[data-cart-popover-price]', el).textContent = item.price;
            $('[data-cart-popover-image]', el).src = item.image;
            $('[data-cart-popover-image]', el).alt = item.name;
            $('[data-cart-popover-thumb]', el).style.background = item.bg_color;
            el.hidden = false;
            requestAnimationFrame(() => el.classList.add('is-open'));
            clearTimeout(hideTimer);
            hideTimer = setTimeout(hide, 4500);
        };

        if (el) {
            $('[data-cart-popover-close]', el).addEventListener('click', hide);
            el.addEventListener('pointerenter', () => clearTimeout(hideTimer));
            el.addEventListener('pointerleave', () => { hideTimer = setTimeout(hide, 2000); });
            document.addEventListener('keydown', (event) => { if (event.key === 'Escape') hide(); });
        }

        return { show, hide };
    })();

    function updateCartCount(count) {
        $$('[data-cart-count]').forEach((badge) => {
            badge.textContent = count;
            badge.hidden = count === 0;
            badge.classList.remove('is-bumping');
            void badge.offsetWidth; // khởi động lại animation
            badge.classList.add('is-bumping');
        });
        $$('[data-cart-count-inline]').forEach((el) => { el.textContent = count; });
    }

    async function postForm(form) {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });
        const data = await response.json().catch(() => ({ ok: false, message: 'Có lỗi xảy ra, vui lòng thử lại.' }));
        if (!response.ok) throw new Error(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
        return data;
    }

    function initAddToCart() {
        document.addEventListener('submit', async (event) => {
            const form = event.target.closest('[data-cart-add]');
            if (!form) return;
            event.preventDefault();

            const button = $('button[type="submit"]', form);
            button?.classList.add('is-loading');

            try {
                const data = await postForm(form);
                updateCartCount(data.count);
                cartPopover.show(data.item, data.message);

                if (button?.dataset.doneIcon !== undefined) {
                    const original = button.innerHTML;
                    button.classList.add('is-done');
                    button.innerHTML = button.dataset.doneIcon;
                    setTimeout(() => { button.classList.remove('is-done'); button.innerHTML = original; }, 1400);
                }
            } catch (error) {
                showToast(error.message, 'error');
            } finally {
                button?.classList.remove('is-loading');
            }
        });
    }

    /* Bộ tăng giảm số lượng */
    function initSteppers() {
        $$('[data-stepper]').forEach((stepper) => {
            const input = $('input', stepper);
            const minus = $('[data-step="-1"]', stepper);
            const plus = $('[data-step="1"]', stepper);
            const min = Number(input.min || 1);
            const max = Number(input.max || 50);

            const sync = () => {
                minus.disabled = Number(input.value) <= min;
                plus.disabled = Number(input.value) >= max;
            };

            const set = (value) => {
                const next = Math.max(min, Math.min(max, value || min));
                if (String(next) === input.value) return;
                input.value = next;
                sync();
                input.dispatchEvent(new Event('change', { bubbles: true }));
            };

            minus.addEventListener('click', () => set(Number(input.value) - 1));
            plus.addEventListener('click', () => set(Number(input.value) + 1));
            input.addEventListener('change', sync);
            input.addEventListener('blur', () => set(Number(input.value)));
            sync();
        });
    }

    /* Trang giỏ hàng: cập nhật giao diện ngay, gửi request ở nền */
    function initCartPage() {
        const page = $('[data-cart-page]');
        if (!page) return;

        const summary = {
            subtotal: $('[data-summary-subtotal]', page),
            shipping: $('[data-summary-shipping]', page),
            total: $('[data-summary-total]', page),
            meterText: $('[data-shipping-text]', page),
            meterFill: $('[data-shipping-fill]', page),
        };

        const applySummary = (data) => {
            summary.subtotal.textContent = data.subtotal;
            summary.shipping.textContent = data.shipping_fee;
            summary.total.textContent = data.total;
            if (summary.meterText) summary.meterText.innerHTML = data.shipping_message;
            if (summary.meterFill) summary.meterFill.style.transform = `scaleX(${data.shipping_progress})`;
            updateCartCount(data.count);
        };

        $$('[data-cart-row]', page).forEach((row) => {
            const updateForm = $('[data-cart-update]', row);
            const removeForm = $('[data-cart-remove]', row);
            const lineTotal = $('[data-line-total]', row);
            const price = Number(row.dataset.price);
            let timer = 0;

            updateForm.addEventListener('change', () => {
                const quantity = Number($('input[name="quantity"]', updateForm).value);
                lineTotal.textContent = formatPrice(price * quantity); // phản hồi tức thì
                clearTimeout(timer);
                timer = setTimeout(() => {
                    postForm(updateForm).then(applySummary).catch((error) => showToast(error.message, 'error'));
                }, 250);
            });
            updateForm.addEventListener('submit', (event) => event.preventDefault());

            removeForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                row.classList.add('is-removing');
                try {
                    const data = await postForm(removeForm);
                    applySummary(data);
                    setTimeout(() => {
                        row.remove();
                        if (data.count === 0) window.location.reload();
                    }, reduceMotion.matches ? 0 : 300);
                } catch (error) {
                    row.classList.remove('is-removing');
                    showToast(error.message, 'error');
                }
            });
        });
    }

    /* ----------------------------------------------------------------------
       Kiểm tra form ngay khi rời ô nhập (inline), không đợi submit
       ---------------------------------------------------------------------- */
    function initValidation() {
        $$('form[data-validate]').forEach((form) => {
            form.noValidate = true;
            const fields = $$('input, select, textarea', form).filter((el) => el.name && el.type !== 'hidden');

            const messageFor = (input) => {
                const label = (form.querySelector(`label[for="${input.id}"]`)?.textContent || 'thông tin này').trim().toLowerCase();
                const v = input.validity;
                if (v.valueMissing) return `Vui lòng nhập ${label}.`;
                if (v.typeMismatch && input.type === 'email') return 'Email không đúng định dạng.';
                if (v.patternMismatch) return input.dataset.patternMessage || 'Giá trị không hợp lệ.';
                if (v.tooShort) return `Cần ít nhất ${input.minLength} ký tự.`;
                if (v.rangeUnderflow) return `Giá trị tối thiểu là ${input.min}.`;
                if (v.customError) return input.validationMessage;
                return '';
            };

            const check = (input) => {
                if (input.dataset.match) {
                    const other = form.elements[input.dataset.match];
                    input.setCustomValidity(other && input.value !== other.value ? 'Mật khẩu xác nhận không khớp.' : '');
                }

                const field = input.closest('.field');
                if (!field) return input.checkValidity();

                const message = input.checkValidity() ? '' : messageFor(input);
                let error = $('.field-error', field);

                field.classList.toggle('has-error', message !== '');
                input.setAttribute('aria-invalid', message ? 'true' : 'false');

                if (message) {
                    if (!error) {
                        error = document.createElement('p');
                        error.className = 'field-error';
                        error.id = `${input.id || input.name}-error`;
                        field.append(error);
                    }
                    error.textContent = message;
                    input.setAttribute('aria-describedby', error.id);
                } else {
                    error?.remove();
                }
                return message === '';
            };

            fields.forEach((input) => {
                input.addEventListener('blur', () => { if (input.value !== '' || input.dataset.touched) check(input); input.dataset.touched = '1'; });
                input.addEventListener('input', () => { if (input.closest('.field')?.classList.contains('has-error')) check(input); });
            });

            form.addEventListener('submit', (event) => {
                const invalid = fields.filter((input) => !check(input));
                if (invalid.length) {
                    event.preventDefault();
                    invalid[0].focus();
                    return;
                }
                $('button[type="submit"]', form)?.classList.add('is-loading');
            });
        });
    }

    /* ----------------------------------------------------------------------
       Footer: mở sẵn các cột trên desktop, thu gọn trên mobile
       ---------------------------------------------------------------------- */
    function initFooter() {
        const columns = $$('[data-footer-col]');
        const desktop = window.matchMedia('(min-width: 821px)');
        const sync = () => columns.forEach((col) => { col.open = desktop.matches; });

        columns.forEach((col) => {
            $('summary', col).addEventListener('click', (event) => { if (desktop.matches) event.preventDefault(); });
        });
        desktop.addEventListener('change', sync);
        sync();
    }

    document.addEventListener('DOMContentLoaded', () => {
        $$('[data-carousel]').forEach(initCarousel);
        initHeader();
        initNavSheet();
        initToasts();
        initAddToCart();
        initSteppers();
        initCartPage();
        initValidation();
        initFooter();
    });
})();
