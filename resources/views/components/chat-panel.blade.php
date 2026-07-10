@props(['repairRequest', 'messages', 'variant' => 'default'])

@php
    $authUser = auth()->user();
    $isInbox = $variant === 'inbox';
    $messagesByDate = $messages->groupBy(fn ($message) => $message->created_at->format('Y-m-d'));
@endphp

<div
    class="ff-chat {{ $isInbox ? 'ff-chat--inbox' : '' }}"
    data-chat-root
    data-repair-request-id="{{ $repairRequest->id }}"
    data-messages-url="{{ route('repair-requests.messages.index', $repairRequest) }}"
    data-store-url="{{ route('repair-requests.messages.store', $repairRequest) }}"
    data-read-url="{{ route('repair-requests.messages.read', $repairRequest) }}"
    data-current-user-id="{{ $authUser->id }}"
    data-current-user-name="{{ $authUser->name }}"
    data-csrf-token="{{ csrf_token() }}"
>
    @unless ($isInbox)
        <div class="mb-3 flex items-center justify-between gap-3">
            <p class="text-sm text-slate-500">Messages sync in real time when the live server is running.</p>
            <span id="chat-status" class="ff-chat-status" data-mode="connecting">Connecting…</span>
        </div>
    @endunless

    @if ($isInbox)
        <div class="ff-chat-toolbar">
            <span id="chat-status" class="ff-chat-status ff-chat-status--inline" data-mode="connecting">
                <span class="ff-chat-status-dot"></span>
                <span class="ff-chat-status-label">Connecting</span>
            </span>
            <span class="text-xs text-slate-400">End-to-end repair thread</span>
        </div>
    @endif

    <div id="chat-messages" class="ff-chat-messages {{ $isInbox ? 'ff-chat-messages--inbox' : '' }}">
        @forelse ($messagesByDate as $date => $dayMessages)
            <div class="ff-chat-date-divider">
                <span>{{ \Illuminate\Support\Carbon::parse($date)->isToday() ? 'Today' : (\Illuminate\Support\Carbon::parse($date)->isYesterday() ? 'Yesterday' : \Illuminate\Support\Carbon::parse($date)->format('M j, Y')) }}</span>
            </div>
            @foreach ($dayMessages as $message)
                @php $isMine = $message->user_id === $authUser->id; @endphp
                <div
                    class="ff-chat-bubble-wrap {{ $isMine ? 'ff-chat-bubble-wrap--mine' : 'ff-chat-bubble-wrap--theirs' }}"
                    data-message-id="{{ $message->id }}"
                >
                    @if ($isInbox && ! $isMine)
                        <span class="ff-chat-avatar ff-chat-avatar--inbox">{{ strtoupper(substr($message->sender->name, 0, 2)) }}</span>
                    @endif
                    <div class="ff-chat-bubble-col">
                        @if ($isInbox && ! $isMine)
                            <p class="ff-chat-sender-label">{{ $message->sender->name }}</p>
                        @endif
                        <div class="ff-chat-bubble {{ $isMine ? 'ff-chat-bubble--mine' : 'ff-chat-bubble--theirs' }} {{ $isInbox ? 'ff-chat-bubble--inbox' : '' }}">
                            @unless ($isInbox || $isMine)
                                <div class="ff-chat-meta">
                                    <span class="ff-chat-avatar">{{ strtoupper(substr($message->sender->name, 0, 2)) }}</span>
                                    <span class="ff-chat-sender">{{ $message->sender->name }}</span>
                                    <x-status-badge :status="$message->sender->role" />
                                </div>
                            @endunless
                            <p class="ff-chat-body">{{ $message->body }}</p>
                        </div>
                        <p class="ff-chat-time {{ $isMine ? 'ff-chat-time--mine' : 'ff-chat-time--theirs' }}">{{ $message->created_at->format('g:i A') }}</p>
                    </div>
                </div>
            @endforeach
        @empty
            <div class="ff-chat-empty">
                <div class="ff-chat-empty-icon">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.256 3.418.271m0 0a48.11 48.11 0 013.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 015.912-.998m12.062 0a48.11 48.11 0 00-5.912.998m0 0a48.11 48.11 0 01-3.418-.271m0 0c1.129-.166 2.27-.256 3.418-.271M3 12c0-1.6 1.123-2.994 2.707-3.227A48.11 48.11 0 0112 3c2.392 0 4.744.175 7.043.513 1.584.233 2.707 1.627 2.707 3.227v3.01" />
                    </svg>
                </div>
                <p class="mt-3 font-medium text-slate-700">No messages yet</p>
                <p class="mt-1 text-xs text-slate-500">Send a message below to begin this repair conversation.</p>
            </div>
        @endforelse
        <div id="chat-typing" class="ff-chat-typing hidden" aria-live="polite">
            <span class="ff-chat-typing-dots" aria-hidden="true"><span></span><span></span><span></span></span>
            <span class="ff-chat-typing-text"></span>
        </div>
    </div>

    <form class="ff-chat-form {{ $isInbox ? 'ff-chat-form--inbox' : '' }}">
        <div class="ff-chat-compose">
            <label for="chat-body" class="sr-only">Message</label>
            <div class="ff-chat-input-wrap">
                <textarea
                    id="chat-body"
                    name="body"
                    rows="1"
                    maxlength="2000"
                    placeholder="Write a professional message..."
                    class="ff-chat-input {{ $isInbox ? 'ff-chat-input--inbox' : 'ff-input' }}"
                ></textarea>
            </div>
            <button type="submit" class="ff-chat-send" id="chat-send-btn" disabled aria-label="Send message">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
            </button>
        </div>
        <p class="ff-chat-compose-hint">Press Enter to send · Shift+Enter for new line</p>
    </form>
</div>
