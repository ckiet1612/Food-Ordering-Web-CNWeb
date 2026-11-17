(() => {
    'use strict';
    document.querySelectorAll('[data-checkout-form], [data-checkout-confirm-form]').forEach((form) => {
        const button = form.querySelector('[data-checkout-review], [data-checkout-confirm]');
        // Enter trong ô nhập liệu không được trở thành implicit submit; textarea vẫn xuống dòng.
        form.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && event.target.matches('input, select')) event.preventDefault();
        });
        form.addEventListener('submit', (event) => {
            if (event.submitter !== button) {
                event.preventDefault();
                delete form.dataset.saving;
                button.classList.remove('is-loading');
                return;
            }
            if (form.matches('[data-checkout-confirm-form]')) {
                if (form.dataset.saving === '1') { event.preventDefault(); return; }
                form.dataset.saving = '1';
                button.classList.add('is-loading');
                button.setAttribute('aria-disabled', 'true');
            }
        });
    });
    window.addEventListener('pageshow', () => {
        document.querySelectorAll('[data-checkout-confirm-form]').forEach((form) => {
            delete form.dataset.saving;
            const button = form.querySelector('[data-checkout-confirm]');
            button.classList.remove('is-loading'); button.removeAttribute('aria-disabled');
        });
    });
})();
