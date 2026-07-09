function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function initReveal() {
    const targets = document.querySelectorAll('[data-reveal], [data-reveal-child]');
    if (!targets.length) {
        return;
    }

    if (prefersReducedMotion()) {
        targets.forEach((el) => el.classList.add('ff-in-view'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('ff-in-view');
                observer.unobserve(entry.target);
            });
        },
        { rootMargin: '0px 0px -40px 0px', threshold: 0.12 },
    );

    targets.forEach((el) => observer.observe(el));
}

function initCounters() {
    document.querySelectorAll('[data-counter]').forEach((element) => {
        const target = Number(element.dataset.counter);
        const suffix = element.dataset.counterSuffix ?? '';

        if (prefersReducedMotion()) {
            element.textContent = `${target.toLocaleString()}${suffix}`;
            return;
        }

        const run = () => {
            const start = performance.now();
            const duration = 900;

            const tick = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - (1 - progress) ** 3;
                element.textContent = `${Math.round(target * eased).toLocaleString()}${suffix}`;

                if (progress < 1) {
                    requestAnimationFrame(tick);
                }
            };

            requestAnimationFrame(tick);
        };

        const observer = new IntersectionObserver(
            (entries) => {
                if (entries[0]?.isIntersecting) {
                    run();
                    observer.disconnect();
                }
            },
            { threshold: 0.5 },
        );

        observer.observe(element);
    });
}

function initHero() {
    const hero = document.querySelector('[data-landing-hero]');
    if (!hero || prefersReducedMotion()) {
        hero?.classList.add('ff-hero-ready');
        return;
    }

    requestAnimationFrame(() => hero.classList.add('ff-hero-ready'));
}

function initSmoothScroll() {
    const header = document.querySelector('header');
    const headerOffset = () => (header?.offsetHeight ?? 0) + 12;

    document.querySelectorAll('a[href*="#"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            const url = new URL(link.href, window.location.href);

            if (url.origin !== window.location.origin) {
                return;
            }

            if (url.pathname !== window.location.pathname && url.pathname !== '/') {
                return;
            }

            const target = document.querySelector(url.hash);
            if (!target) {
                return;
            }

            event.preventDefault();

            const top = target.getBoundingClientRect().top + window.scrollY - headerOffset();

            window.scrollTo({
                top,
                behavior: prefersReducedMotion() ? 'auto' : 'smooth',
            });

            history.pushState(null, '', url.hash);
        });
    });
}

function initNavSpy() {
    const links = document.querySelectorAll('[data-nav-section]');
    const sections = [...links]
        .map((link) => document.getElementById(link.dataset.navSection))
        .filter(Boolean);

    if (!sections.length) {
        return;
    }

    const setActive = (id) => {
        links.forEach((link) => {
            link.classList.toggle('is-active', link.dataset.navSection === id);
        });
    };

    const observer = new IntersectionObserver(
        (entries) => {
            const visible = entries
                .filter((entry) => entry.isIntersecting)
                .sort((a, b) => b.intersectionRatio - a.intersectionRatio);

            if (visible[0]?.target.id) {
                setActive(visible[0].target.id);
            }
        },
        { rootMargin: '-40% 0px -45% 0px', threshold: [0, 0.25, 0.5, 0.75, 1] },
    );

    sections.forEach((section) => observer.observe(section));

    if (window.location.hash) {
        const id = window.location.hash.replace('#', '');
        if (sections.some((section) => section.id === id)) {
            setActive(id);
        }
    }
}

function scrollToHashOnLoad() {
    if (!window.location.hash) {
        return;
    }

    const target = document.querySelector(window.location.hash);
    if (!target) {
        return;
    }

    const header = document.querySelector('header');
    const offset = (header?.offsetHeight ?? 0) + 12;

    requestAnimationFrame(() => {
        const top = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top, behavior: 'auto' });
    });
}

function initServiceModal() {
    const dataEl = document.getElementById('services-data');
    const modal = document.querySelector('[data-service-modal]');
    if (!dataEl || !modal) {
        return;
    }

    const services = JSON.parse(dataEl.textContent);
    const panel = modal.querySelector('.ff-service-modal-panel');
    const titleEl = modal.querySelector('[data-service-modal-title]');
    const descEl = modal.querySelector('[data-service-modal-description]');
    const turnaroundEl = modal.querySelector('[data-service-modal-turnaround]');
    const priceEl = modal.querySelector('[data-service-modal-price]');
    const includesEl = modal.querySelector('[data-service-modal-includes]');
    const tagsEl = modal.querySelector('[data-service-modal-tags]');
    const iconEl = modal.querySelector('[data-service-modal-icon]');
    const registerEl = modal.querySelector('[data-service-modal-register]');
    let lastFocus = null;

    const open = (index) => {
        const service = services[index];
        if (!service) {
            return;
        }

        lastFocus = document.activeElement;
        titleEl.textContent = service.title;
        descEl.textContent = service.details;
        turnaroundEl.textContent = service.turnaround;
        priceEl.textContent = service.from_price;
        registerEl.href = `${registerEl.dataset.baseHref || registerEl.getAttribute('href')}?device=${service.slug}`;

        iconEl.className = `ff-service-modal-icon flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br ${service.gradient} ${service.shadow} text-white shadow-lg`;
        iconEl.innerHTML = `<svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="${service.icon}" /></svg>`;

        includesEl.innerHTML = service.includes
            .map((item) => `<li class="ff-service-modal-includes-item"><svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg><span>${item}</span></li>`)
            .join('');

        tagsEl.innerHTML = service.tags
            .map((tag) => `<span class="ff-service-modal-tag">${tag}</span>`)
            .join('');

        modal.hidden = false;
        document.body.classList.add('overflow-hidden');
        panel.focus();
    };

    const close = () => {
        modal.hidden = true;
        document.body.classList.remove('overflow-hidden');
        lastFocus?.focus();
    };

    if (registerEl && !registerEl.dataset.baseHref) {
        registerEl.dataset.baseHref = registerEl.getAttribute('href');
    }

    document.querySelectorAll('[data-service-open]').forEach((button) => {
        button.addEventListener('click', () => open(Number(button.dataset.serviceOpen)));
    });

    modal.querySelectorAll('[data-service-close]').forEach((el) => {
        el.addEventListener('click', close);
    });

    document.addEventListener('keydown', (event) => {
        if (!modal.hidden && event.key === 'Escape') {
            close();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initHero();
    initReveal();
    initCounters();
    initSmoothScroll();
    initNavSpy();
    scrollToHashOnLoad();
    initServiceModal();
});
