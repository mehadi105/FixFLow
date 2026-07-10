document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('inbox-search');
    const threads = document.querySelectorAll('[data-thread-search]');
    const filters = document.querySelectorAll('[data-inbox-filter]');
    let activeFilter = 'all';

    const applyFilters = () => {
        const query = search?.value.trim().toLowerCase() ?? '';

        threads.forEach((thread) => {
            const haystack = thread.dataset.threadSearch ?? '';
            const isUnread = thread.dataset.threadUnread === '1';
            const matchesSearch = query === '' || haystack.includes(query);
            const matchesFilter = activeFilter === 'all' || (activeFilter === 'unread' && isUnread);

            thread.classList.toggle('hidden', ! matchesSearch || ! matchesFilter);
        });

        document.querySelectorAll('[data-inbox-group]').forEach((group) => {
            const visible = group.querySelector('[data-thread-search]:not(.hidden)');
            group.classList.toggle('hidden', ! visible);
        });
    };

    search?.addEventListener('input', applyFilters);

    filters.forEach((button) => {
        button.addEventListener('click', () => {
            activeFilter = button.dataset.inboxFilter ?? 'all';
            filters.forEach((item) => {
                item.classList.toggle('ff-inbox-filter--active', item === button);
            });
            applyFilters();
        });
    });
});
