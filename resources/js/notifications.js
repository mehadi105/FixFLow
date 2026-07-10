const POLL_INTERVAL_MS = 30000;

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function notificationIcon(type) {
    if (type === 'application') {
        return `<span class="ff-notification-icon ff-notification-icon--application">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
            </svg>
        </span>`;
    }

    return `<span class="ff-notification-icon ff-notification-icon--chat">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
        </svg>
    </span>`;
}

class NotificationPanel {
    constructor(root) {
        this.root = root;
        this.url = root.dataset.notificationsUrl;
        this.toggle = root.querySelector('#notification-toggle');
        this.panel = root.querySelector('#notification-panel');
        this.closeBtn = root.querySelector('#notification-close');
        this.list = root.querySelector('#notification-list');
        this.badge = root.querySelector('#notification-badge');
        this.summary = root.querySelector('#notification-summary');
        this.isOpen = false;
        this.pollTimer = null;
    }

    init() {
        if (!this.url) {
            return;
        }

        this.toggle?.addEventListener('click', (event) => {
            event.stopPropagation();
            this.isOpen ? this.close() : this.open();
        });

        this.closeBtn?.addEventListener('click', () => this.close());

        document.addEventListener('click', (event) => {
            if (this.isOpen && !this.root.contains(event.target)) {
                this.close();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });

        this.fetchNotifications();
        this.pollTimer = window.setInterval(() => this.fetchNotifications(), POLL_INTERVAL_MS);
    }

    open() {
        this.isOpen = true;
        this.panel?.classList.remove('hidden');
        this.toggle?.setAttribute('aria-expanded', 'true');
        this.fetchNotifications();
    }

    close() {
        this.isOpen = false;
        this.panel?.classList.add('hidden');
        this.toggle?.setAttribute('aria-expanded', 'false');
    }

    async fetchNotifications() {
        try {
            const response = await fetch(this.url, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            this.render(data);
        } catch {
            if (this.summary) {
                this.summary.textContent = 'Could not load notifications';
            }
        }
    }

    render(data) {
        const total = Number(data.total ?? 0);
        const chatUnread = Number(data.chat_unread ?? 0);
        const pendingApps = Number(data.pending_applications ?? 0);
        const items = Array.isArray(data.notifications) ? data.notifications : [];

        if (this.badge) {
            if (total > 0) {
                this.badge.textContent = total > 99 ? '99+' : String(total);
                this.badge.classList.remove('hidden');
            } else {
                this.badge.classList.add('hidden');
            }
        }

        if (this.summary) {
            const parts = [];
            if (chatUnread > 0) {
                parts.push(`${chatUnread} unread message${chatUnread === 1 ? '' : 's'}`);
            }
            if (pendingApps > 0) {
                parts.push(`${pendingApps} pending application${pendingApps === 1 ? '' : 's'}`);
            }
            this.summary.textContent = parts.length ? parts.join(' · ') : 'You are all caught up';
        }

        if (!this.list) {
            return;
        }

        if (items.length === 0) {
            this.list.innerHTML = `
                <div class="ff-notification-empty">
                    <svg class="mx-auto h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <p class="mt-3 text-sm font-medium text-slate-700">No new notifications</p>
                    <p class="mt-1 text-xs text-slate-500">Chat messages and admin alerts will appear here.</p>
                </div>
            `;
            return;
        }

        this.list.innerHTML = items.map((item) => `
            <a href="${escapeHtml(item.url)}" class="ff-notification-item">
                ${notificationIcon(item.type)}
                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <p class="truncate text-sm font-semibold text-slate-900">${escapeHtml(item.title)}</p>
                        <span class="shrink-0 text-[10px] text-slate-400">${escapeHtml(item.time)}</span>
                    </div>
                    ${item.subtitle ? `<p class="mt-0.5 truncate text-xs text-slate-500">${escapeHtml(item.subtitle)}</p>` : ''}
                    <p class="mt-1 line-clamp-2 text-xs text-slate-600">${escapeHtml(item.preview)}</p>
                    <div class="mt-2 flex items-center justify-between gap-2">
                        <span class="text-[10px] font-medium text-slate-400">${escapeHtml(item.sender)}</span>
                        ${item.unread_count > 1 ? `<span class="ff-unread-badge">${item.unread_count > 99 ? '99+' : item.unread_count}</span>` : ''}
                    </div>
                </div>
            </a>
        `).join('');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('notification-root');
    if (root) {
        new NotificationPanel(root).init();
    }
});
