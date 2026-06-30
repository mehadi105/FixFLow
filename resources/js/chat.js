import './echo.js';

const POLL_INTERVAL_MS = 4000;
const ECHO_CONNECT_TIMEOUT_MS = 4000;
const TYPING_HIDE_MS = 3000;

function roleBadgeClass(role) {
    const styles = {
        pending: 'bg-amber-50 text-amber-800 ring-amber-500/20',
        assigned: 'bg-sky-50 text-sky-800 ring-sky-500/20',
        diagnosing: 'bg-violet-50 text-violet-800 ring-violet-500/20',
        repairing: 'bg-indigo-50 text-indigo-800 ring-indigo-500/20',
        completed: 'bg-emerald-50 text-emerald-800 ring-emerald-500/20',
        customer: 'bg-slate-100 text-slate-600 ring-slate-400/20',
        technician: 'bg-indigo-50 text-indigo-800 ring-indigo-500/20',
        admin: 'bg-emerald-50 text-emerald-800 ring-emerald-500/20',
    };

    return styles[role?.toLowerCase()] ?? styles.customer;
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

class RepairChat {
    constructor(root) {
        this.root = root;
        this.repairRequestId = root.dataset.repairRequestId;
        this.messagesUrl = root.dataset.messagesUrl;
        this.storeUrl = root.dataset.storeUrl;
        this.readUrl = root.dataset.readUrl;
        this.currentUserId = Number(root.dataset.currentUserId);
        this.currentUserName = root.dataset.currentUserName ?? 'Someone';
        this.csrfToken = root.dataset.csrfToken;

        this.container = root.querySelector('#chat-messages');
        this.form = root.querySelector('.ff-chat-form');
        this.input = root.querySelector('#chat-body');
        this.sendBtn = root.querySelector('#chat-send-btn');
        this.statusEl = root.querySelector('#chat-status');
        this.typingEl = root.querySelector('#chat-typing');

        this.knownMessageIds = new Set(
            [...this.container.querySelectorAll('[data-message-id]')].map((el) => Number(el.dataset.messageId))
        );
        this.lastMessageId = this.knownMessageIds.size
            ? Math.max(...this.knownMessageIds)
            : 0;

        this.mode = 'connecting';
        this.pollTimer = null;
        this.echoChannel = null;
        this.typingTimeout = null;
    }

    init() {
        this.setupForm();
        this.setupInputToggle();
        this.setupTyping();
        this.scrollToBottom();
        this.markRead();
        this.connectRealtime();
    }

    setupForm() {
        if (!this.form) {
            return;
        }

        this.form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const body = this.input.value.trim();
            if (!body) {
                return;
            }

            this.sendBtn.disabled = true;

            try {
                const response = await fetch(this.storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ body }),
                });

                if (!response.ok) {
                    throw new Error('Failed to send message');
                }

                const data = await response.json();
                this.appendMessage(data.message);
                this.input.value = '';
                this.setupInputToggle();
            } catch (error) {
                console.error(error);
                this.setStatus('Unable to send message. Try again.', 'error');
            } finally {
                this.setupInputToggle();
            }
        });
    }

    setupInputToggle() {
        if (!this.input || !this.sendBtn) {
            return;
        }

        const toggle = () => {
            this.sendBtn.disabled = this.input.value.trim().length === 0;
        };

        if (!this.input.dataset.bound) {
            this.input.dataset.bound = 'true';
            this.input.addEventListener('input', toggle);
        }

        toggle();
    }

    setupTyping() {
        if (!this.input) {
            return;
        }

        const sendTyping = () => {
            if (this.mode !== 'live' || !this.echoChannel) {
                return;
            }

            this.echoChannel.whisper('typing', {
                id: this.currentUserId,
                name: this.currentUserName,
            });
        };

        if (!this.input.dataset.typingBound) {
            this.input.dataset.typingBound = 'true';
            this.input.addEventListener('input', () => {
                if (this.input.value.trim()) {
                    sendTyping();
                }
            });
        }
    }

    bindTypingListener() {
        if (!this.echoChannel) {
            return;
        }

        this.echoChannel.listenForWhisper('typing', (event) => {
            if (Number(event?.id) === this.currentUserId) {
                return;
            }

            this.showTyping(event?.name ?? 'Someone');
        });
    }

    showTyping(name) {
        if (!this.typingEl) {
            return;
        }

        this.typingEl.textContent = `${name} is typing…`;
        this.typingEl.classList.remove('hidden');
        this.scrollToBottom();

        clearTimeout(this.typingTimeout);
        this.typingTimeout = window.setTimeout(() => {
            this.typingEl.classList.add('hidden');
            this.typingEl.textContent = '';
        }, TYPING_HIDE_MS);
    }

    hideTyping() {
        if (!this.typingEl) {
            return;
        }

        clearTimeout(this.typingTimeout);
        this.typingEl.classList.add('hidden');
        this.typingEl.textContent = '';
    }

    async connectRealtime() {
        if (!window.Echo || !import.meta.env.VITE_REVERB_APP_KEY) {
            this.startPolling();
            return;
        }

        this.setStatus('Connecting…', 'connecting');

        const connected = await new Promise((resolve) => {
            let settled = false;

            const timer = setTimeout(() => {
                if (!settled) {
                    settled = true;
                    resolve(false);
                }
            }, ECHO_CONNECT_TIMEOUT_MS);

            try {
                this.echoChannel = window.Echo.private(`repair-request.${this.repairRequestId}`);

                this.echoChannel.listen('.MessageSent', (event) => {
                    if (event?.message) {
                        this.hideTyping();
                        this.appendMessage(event.message);
                    }
                });

                this.bindTypingListener();

                this.echoChannel.subscription.bind('pusher:subscription_succeeded', () => {
                    if (!settled) {
                        settled = true;
                        clearTimeout(timer);
                        resolve(true);
                    }
                });

                this.echoChannel.subscription.bind('pusher:subscription_error', () => {
                    if (!settled) {
                        settled = true;
                        clearTimeout(timer);
                        resolve(false);
                    }
                });
            } catch (error) {
                clearTimeout(timer);
                console.error(error);
                resolve(false);
            }
        });

        if (connected) {
            this.setStatus('Live', 'live');
        } else {
            this.startPolling();
        }
    }

    startPolling() {
        if (this.pollTimer) {
            return;
        }

        this.setStatus('Polling (fallback)', 'polling');
        this.pollTimer = window.setInterval(() => this.pollMessages(), POLL_INTERVAL_MS);
        this.pollMessages();
    }

    async pollMessages() {
        try {
            const response = await fetch(this.messagesUrl, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Poll failed');
            }

            const data = await response.json();
            (data.messages ?? []).forEach((message) => {
                if (message.id > this.lastMessageId) {
                    this.appendMessage(message);
                }
            });
        } catch (error) {
            console.error(error);
            this.setStatus('Reconnecting…', 'polling');
        }
    }

    appendMessage(message) {
        if (!message?.id || this.knownMessageIds.has(message.id)) {
            return;
        }

        this.knownMessageIds.add(message.id);
        this.lastMessageId = Math.max(this.lastMessageId, message.id);

        const emptyState = this.container.querySelector('.ff-chat-empty');
        if (emptyState) {
            emptyState.remove();
        }

        const isMine = message.user_id === this.currentUserId;
        const wrap = document.createElement('div');
        wrap.className = `ff-chat-bubble-wrap ${isMine ? 'ff-chat-bubble-wrap--mine' : 'ff-chat-bubble-wrap--theirs'}`;
        wrap.dataset.messageId = String(message.id);

        const badgeClass = roleBadgeClass(message.sender?.role);

        wrap.innerHTML = `
            <div class="ff-chat-bubble ${isMine ? 'ff-chat-bubble--mine' : 'ff-chat-bubble--theirs'}">
                ${isMine ? '' : `
                    <div class="ff-chat-meta">
                        <span class="ff-chat-avatar">${escapeHtml(message.sender?.initials ?? '')}</span>
                        <span class="ff-chat-sender">${escapeHtml(message.sender?.name ?? 'User')}</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide ring-1 ring-inset ${badgeClass}">
                            ${escapeHtml(message.sender?.role ?? '')}
                        </span>
                    </div>
                `}
                <p class="ff-chat-body">${escapeHtml(message.body)}</p>
                <p class="ff-chat-time">${escapeHtml(message.created_at_human ?? '')}</p>
            </div>
        `;

        this.container.appendChild(wrap);
        this.scrollToBottom();

        if (!isMine) {
            this.markRead();
        }
    }

    async markRead() {
        try {
            await fetch(this.readUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
        } catch (error) {
            console.error(error);
        }
    }

    scrollToBottom() {
        if (this.container) {
            this.container.scrollTop = this.container.scrollHeight;
        }
    }

    setStatus(text, mode) {
        this.mode = mode;
        if (this.statusEl) {
            this.statusEl.textContent = text;
            this.statusEl.dataset.mode = mode;
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-chat-root]').forEach((root) => {
        const chat = new RepairChat(root);
        chat.init();
    });
});
