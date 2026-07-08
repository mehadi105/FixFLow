const DEBOUNCE_MS = 300;

function buildFilterUrl(form) {
    const url = new URL(form.action, window.location.origin);

    new FormData(form).forEach((value, key) => {
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
    });

    return url;
}

async function loadFilteredResults(form, { resetPage = false } = {}) {
    const targetSelector = form.dataset.liveFilterTarget;
    const results = targetSelector ? document.querySelector(targetSelector) : null;

    if (!results) {
        form.requestSubmit();
        return;
    }

    const url = buildFilterUrl(form);

    if (resetPage) {
        url.searchParams.delete('page');
    }

    results.dataset.loading = 'true';

    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'text/html',
            },
        });

        if (!response.ok) {
            throw new Error('Filter request failed');
        }

        const html = await response.text();
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const next = doc.querySelector(targetSelector);

        if (next) {
            results.innerHTML = next.innerHTML;
        }

        window.history.replaceState({}, '', url);
    } catch (error) {
        console.error(error);
        window.location.assign(url.toString());
    } finally {
        delete results.dataset.loading;
    }
}

function initLiveFilterForm(form) {
    let debounceTimer = null;

    const scheduleSearch = () => {
        clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(() => {
            loadFilteredResults(form, { resetPage: true });
        }, DEBOUNCE_MS);
    };

    form.querySelectorAll('[data-live-filter-input]').forEach((input) => {
        input.addEventListener('input', scheduleSearch);
        input.addEventListener('change', () => loadFilteredResults(form, { resetPage: true }));
    });

    const targetSelector = form.dataset.liveFilterTarget;
    const results = targetSelector ? document.querySelector(targetSelector) : null;

    results?.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');

        if (!link || !link.closest('[data-live-filter-pagination]')) {
            return;
        }

        event.preventDefault();
        const url = new URL(link.href, window.location.origin);

        results.dataset.loading = 'true';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'text/html',
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Pagination request failed');
                }

                return response.text();
            })
            .then((html) => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const next = doc.querySelector(targetSelector);

                if (next) {
                    results.innerHTML = next.innerHTML;
                }

                window.history.replaceState({}, '', url);
            })
            .catch((error) => {
                console.error(error);
                window.location.assign(url.toString());
            })
            .finally(() => {
                delete results.dataset.loading;
            });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-live-filter-form]').forEach(initLiveFilterForm);
});
