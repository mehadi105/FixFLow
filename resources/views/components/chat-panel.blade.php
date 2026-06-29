@props(['repairRequest', 'messages'])

@php
    $authUser = auth()->user();
@endphp

<div class="ff-chat" data-repair-request-id="{{ $repairRequest->id }}">
    <div id="chat-messages" class="ff-chat-messages">
        @forelse ($messages as $message)
            @php $isMine = $message->user_id === $authUser->id; @endphp
            <div class="ff-chat-bubble-wrap {{ $isMine ? 'ff-chat-bubble-wrap--mine' : 'ff-chat-bubble-wrap--theirs' }}">
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
    </div>

    <form method="POST" action="{{ route('repair-requests.messages.store', $repairRequest) }}" class="ff-chat-form">
        @csrf
        <div class="ff-field">
            <label for="chat-body" class="sr-only">Message</label>
            <textarea
                id="chat-body"
                name="body"
                rows="2"
                required
                maxlength="2000"
                placeholder="Type your message..."
                class="ff-input ff-chat-input"
            >{{ old('body') }}</textarea>
        </div>
        @error('body')
            <p class="mt-1 text-sm text-rose-600">{{ $errors->first('body') }}</p>
        @enderror
        <div class="mt-3 flex justify-end">
            <button type="submit" class="ff-btn-primary" id="chat-send-btn">Send Message</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    (function () {
        const container = document.getElementById('chat-messages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }

        const input = document.getElementById('chat-body');
        const sendBtn = document.getElementById('chat-send-btn');
        if (input && sendBtn) {
            const toggleSend = () => {
                sendBtn.disabled = input.value.trim().length === 0;
            };
            input.addEventListener('input', toggleSend);
            toggleSend();
        }
    })();
</script>
@endpush
