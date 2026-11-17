/* Motion theo storytelling Yumio: không có vòng lặp animation khi người dùng đứng yên. */
(() => {
    'use strict';
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const reveals = [...document.querySelectorAll('[data-reveal]')];
    const layers = [...document.querySelectorAll('[data-parallax]')];
    let frame = 0;
    const visible = new Set();
    if ('IntersectionObserver' in window && !reduced.matches) {
        const observer = new IntersectionObserver(entries => entries.forEach(entry => {
            if (entry.isIntersecting) { entry.target.classList.add('is-revealed'); observer.unobserve(entry.target); }
        }), { threshold: .06 });
        reveals.forEach(el => { el.classList.add('reveal-ready'); observer.observe(el); });
        const visibility = new IntersectionObserver(entries => entries.forEach(entry => {
            entry.isIntersecting ? visible.add(entry.target) : visible.delete(entry.target);
        }));
        layers.forEach(el => visibility.observe(el));
    }
    const update = () => {
        frame = 0;
        if (reduced.matches) return;
        const height = innerHeight;
        const changes = [...visible].map(el => [el, Math.max(-1, Math.min(1, (height / 2 - el.getBoundingClientRect().top) / height)) * Number(el.dataset.parallax)]);
        changes.forEach(([el, y]) => el.style.setProperty('--parallax-y', `${y.toFixed(2)}px`));
    };
    const schedule = () => { if (!frame && !reduced.matches) frame = requestAnimationFrame(update); };
    addEventListener('scroll', schedule, { passive: true }); addEventListener('resize', schedule, { passive: true });
    reduced.addEventListener('change', () => {
        if (reduced.matches) {
            cancelAnimationFrame(frame); frame = 0;
            reveals.forEach(el => el.classList.add('is-revealed'));
            layers.forEach(el => el.style.removeProperty('--parallax-y'));
        } else schedule();
    });
    // Mở đúng phần thông tin khi đi từ các link neo ở footer/thực đơn.
    const openPolicy = () => {
        const target = document.getElementById(decodeURIComponent(location.hash.slice(1)));
        if (target?.matches('details')) target.open = true;
    };
    openPolicy(); addEventListener('hashchange', openPolicy); schedule();
})();
