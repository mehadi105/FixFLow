const POLL_INTERVAL_MS = 12000;

function formatUnreadCount(count) {
    const total = Number(count ?? 0);
    return total > 99 ? '99+' : String(total);
}

export function updateChatUnreadBadges(count) {
    const total = Number(count ?? 0);
    const label = formatUnreadCount(total);

    document.querySelectorAll('[data-chat-unread-badge]').forEach((badge) => {
        badge.textContent = label;

        if (total > 0) {
            badge.classList.remove('hidden');
            badge.setAttribute('aria-hidden', 'false');
        } else {
            badge.classList.add('hidden');
            badge.setAttribute('aria-hidden', 'true');
        }
    });

    document.querySelectorAll('[data-chat-unread-root]').forEach((root) => {
        root.classList.toggle('ff-has-unread', total > 0);
        root.setAttribute(
            'aria-label',
            total > 0 ? `Open messages (${label} unread)` : 'Open messages',
        );
    });
}

export async function refreshChatUnreadBadges() {
    const url = document.body.dataset.chatUnreadUrl;
    if (!url) {
        return;
    }

    try {
        const response = await fetch(url, {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            return;
        }

        const data = await response.json();
        updateChatUnreadBadges(data.chat_unread ?? 0);
    } catch {
        // Ignore transient network errors; badges keep last known count.
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (!document.body.dataset.chatUnreadUrl) {
        return;
    }

    refreshChatUnreadBadges();
    window.setInterval(refreshChatUnreadBadges, POLL_INTERVAL_MS);
});

window.updateChatUnreadBadges = updateChatUnreadBadges;
window.refreshChatUnreadBadges = refreshChatUnreadBadges;
