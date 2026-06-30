@props(['repairRequest', 'messages'])

@php
    $authUser = auth()->user();
@endphp

<div
    class="ff-chat"
    data-chat-root
    data-repair-request-id="{{ $repairRequest->id }}"
    data-messages-url="{{ route('repair-requests.messages.index', $repairRequest) }}"
    data-store-url="{{ route('repair-requests.messages.store', $repairRequest) }}"
    data-read-url="{{ route('repair-requests.messages.read', $repairRequest) }}"
    data-current-user-id="{{ $authUser->id }}"
    data-current-user-name="{{ $authUser->name }}"
    data-csrf-token="{{ csrf_token() }}"
>
    <div class="mb-3 flex items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Messages update in real time when Reverb is running.</p>
        <span id="chat-status" class="ff-chat-status" data-mode="connecting">Connecting…</span>
    </div>

    <div id="chat-messages" class="ff-chat-messages">
        @forelse ($messages as $message)
            @php $isMine = $message->user_id === $authUser->id; @endphp
            <div
                class="ff-chat-bubble-wrap {{ $isMine ? 'ff-chat-bubble-wrap--mine' : 'ff-chat-bubble-wrap--theirs' }}"
                data-message-id="{{ $message->id }}"
            >
                <div class="ff-chat-bubble {{ $isMine ? 'ff-chat-bubble--mine' : 'ff-chat-bubble--theirs' }}">
                    @unless ($isMine)
                        <div class="ff-chat-meta">
                            <span class="ff-chat-avatar">{{ strtoupper(substr($message->sender->name, 0, 2)) }}</span>
                            <span class="ff-chat-sender">{{ $message->sender->name }}</span>
                            <x-status-badge :status="$message->sender->role" />
                        </div>
                    @endunless
                    <p class="ff-chat-body">{{ $message->body }}</p>
                    <p class="ff-chat-time">{{ $message->created_at->format('M d, Y g:i A') }}</p>
                </div>
            </div>
        @empty
            <div class="ff-chat-empty">
                <p>No messages yet.</p>
                <p class="text-xs">Start the conversation about this repair request.</p>
            </div>
        @endforelse
        <div id="chat-typing" class="ff-chat-typing hidden" aria-live="polite"></div>
    </div>

    <form class="ff-chat-form">
        <div class="ff-field">
            <label for="chat-body" class="sr-only">Message</label>
            <textarea
                id="chat-body"
                name="body"
                rows="2"
                maxlength="2000"
                placeholder="Type your message..."
                class="ff-input ff-chat-input"
            ></textarea>
        </div>
        <div class="mt-3 flex justify-end">
            <button type="submit" class="ff-btn-primary" id="chat-send-btn" disabled>Send Message</button>
        </div>
    </form>
</div>
