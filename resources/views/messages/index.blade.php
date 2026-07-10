@php
    $authUser = auth()->user();
@endphp

<x-app-layout :role="$role ?? 'customer'">
    <x-page-header
        title="Messages"
        description="One conversation per repair — multiple issues stay in separate threads"
    />

    <div class="ff-inbox-info">
        <svg class="h-4 w-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <p class="text-xs leading-relaxed text-slate-600">
            Each repair request has its <strong class="font-semibold text-slate-800">own inbox thread</strong>.
            If you or your technician are handling multiple devices, you will see a separate conversation for every repair.
        </p>
    </div>

    <div class="ff-inbox" data-inbox-root>
        <aside class="ff-inbox-sidebar {{ $activeRepair ? 'ff-inbox-sidebar--hidden-mobile' : '' }}">
            <div class="ff-inbox-sidebar-header">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Inbox</h2>
                        <p class="text-xs text-slate-500">{{ $threads->count() }} conversation{{ $threads->count() === 1 ? '' : 's' }}</p>
                    </div>
                    @if (($unreadChatCount ?? 0) > 0)
                        <span class="ff-inbox-unread-pill">{{ $unreadChatCount }} unread</span>
                    @endif
                </div>
            </div>

            <div class="ff-inbox-search-wrap">
                <svg class="ff-inbox-search-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input
                    type="search"
                    id="inbox-search"
                    placeholder="Search repairs, devices, or contacts..."
                    class="ff-inbox-search"
                    autocomplete="off"
                >
            </div>

            <div class="ff-inbox-filters" role="tablist" aria-label="Filter conversations">
                <button type="button" class="ff-inbox-filter ff-inbox-filter--active" data-inbox-filter="all">All</button>
                <button type="button" class="ff-inbox-filter" data-inbox-filter="unread">Unread</button>
            </div>

            <div class="ff-inbox-threads" id="inbox-threads">
                @php $groupedThreads = $threads->groupBy('contact_name'); @endphp
                @forelse ($groupedThreads as $contactName => $contactThreads)
                    <div class="ff-inbox-thread-group" data-inbox-group>
                        @if ($contactThreads->count() > 1)
                            <div class="ff-inbox-group-label">
                                <span>{{ $contactName }}</span>
                                <span class="ff-inbox-group-count">{{ $contactThreads->count() }} repair threads</span>
                            </div>
                        @endif
                        @foreach ($contactThreads as $thread)
                        @php
                            $repair = $thread['repair'];
                            $isActive = $activeRepair?->id === $repair->id;
                            $preview = $thread['last_message']
                                ? ($thread['last_message']->user_id === $authUser->id
                                    ? 'You: '.$thread['last_message']->body
                                    : $thread['last_message']->body)
                                : 'Start the conversation';
                        @endphp
                        <a
                            href="{{ route('messages.show', $repair) }}"
                            class="ff-inbox-thread {{ $isActive ? 'ff-inbox-thread--active' : '' }} {{ $thread['unread_count'] > 0 ? 'ff-inbox-thread--unread' : '' }}"
                            data-thread-search="{{ strtolower($thread['contact_name'].' '.$repair->reference.' '.$repair->device_label.' '.$repair->status) }}"
                            data-thread-unread="{{ $thread['unread_count'] > 0 ? '1' : '0' }}"
                        >
                            <span class="ff-inbox-thread-avatar" aria-hidden="true" title="{{ $thread['contact_name'] }}">{{ $thread['contact_initials'] }}</span>
                            <span class="min-w-0 flex-1">
                                <span class="flex items-center justify-between gap-2">
                                    <span class="truncate text-sm font-semibold text-slate-900">{{ $repair->reference }}</span>
                                    <span class="shrink-0 text-[11px] font-medium text-slate-400">
                                        {{ $thread['sort_at']->diffForHumans(short: true) }}
                                    </span>
                                </span>
                                <span class="mt-0.5 block truncate text-xs font-medium text-slate-700">{{ $repair->device_label }}</span>
                                <span class="mt-1 flex flex-wrap items-center gap-1.5">
                                    <span class="text-[11px] text-slate-500">{{ $thread['contact_name'] }}</span>
                                    <span class="ff-inbox-status-chip ff-inbox-status-chip--{{ $repair->status }}">{{ ucfirst($repair->status) }}</span>
                                </span>
                                <span class="mt-1.5 flex items-center justify-between gap-2">
                                    <span class="truncate text-xs text-slate-500">{{ $preview }}</span>
                                    @if ($thread['unread_count'] > 0)
                                        <span class="ff-inbox-unread-count">{{ $thread['unread_count'] > 9 ? '9+' : $thread['unread_count'] }}</span>
                                    @endif
                                </span>
                            </span>
                        </a>
                        @endforeach
                    </div>
                @empty
                    <div class="ff-inbox-empty-list">
                        <div class="ff-inbox-empty-icon">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-slate-800">No conversations yet</p>
                        <p class="mt-1 text-xs leading-relaxed text-slate-500">Chats open once a technician is assigned to your repair.</p>
                    </div>
                @endforelse
            </div>
        </aside>

        <section class="ff-inbox-main {{ $activeRepair ? 'ff-inbox-main--visible-mobile' : '' }}">
            @if ($activeRepair && $activeThread)
                <header class="ff-inbox-chat-header">
                    <div class="flex min-w-0 flex-1 items-center gap-3">
                        <a href="{{ route('messages.index') }}" class="ff-inbox-back lg:hidden" aria-label="Back to conversations">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                        </a>
                        <span class="ff-inbox-thread-avatar ff-inbox-thread-avatar--header">{{ $activeThread['contact_initials'] }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $activeRepair->reference }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $activeRepair->device_label }} · {{ $activeThread['contact_name'] }}</p>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <x-status-badge :status="$activeRepair->status" />
                        <a href="{{ route('repair-requests.show', $activeRepair) }}" class="ff-inbox-order-link">
                            View repair
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </a>
                    </div>
                </header>

                <x-chat-panel
                    :repair-request="$activeRepair"
                    :messages="$messages"
                    variant="inbox"
                />
            @else
                <div class="ff-inbox-placeholder">
                    <div class="ff-inbox-placeholder-icon">
                        <svg class="h-8 w-8 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                    </div>
                    <h2 class="mt-5 text-lg font-semibold text-slate-900">Select a conversation</h2>
                    <p class="mt-2 max-w-xs text-sm leading-relaxed text-slate-500">Choose a repair thread from the inbox to message your technician or customer.</p>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
