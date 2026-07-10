@props(['notificationTotal' => 0])

<div
    id="notification-root"
    class="relative"
    data-notifications-url="{{ route('notifications.index') }}"
    data-repair-requests-url="{{ route('repair-requests.index') }}"
    @if (auth()->user()?->isAdmin())
        data-applications-url="{{ route('technician-applications.index') }}"
    @endif
>
    <button
        type="button"
        id="notification-toggle"
        class="ff-notification-bell relative rounded-xl border border-slate-200/80 bg-white/80 p-2.5 text-slate-500 shadow-sm transition-colors hover:bg-white hover:text-indigo-600"
        aria-label="Notifications"
        aria-expanded="false"
        aria-controls="notification-panel"
    >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        <span
            id="notification-badge"
            class="ff-notification-badge {{ $notificationTotal > 0 ? '' : 'hidden' }}"
            aria-hidden="true"
        >{{ $notificationTotal > 99 ? '99+' : $notificationTotal }}</span>
    </button>

    <div
        id="notification-panel"
        class="ff-notification-panel absolute right-0 z-50 mt-2 hidden w-[22rem] sm:w-96"
        role="dialog"
        aria-label="Notifications"
    >
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Notifications</h3>
                <p id="notification-summary" class="text-xs text-slate-500">Loading…</p>
            </div>
            <button
                type="button"
                id="notification-close"
                class="rounded-lg p-1 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600"
                aria-label="Close notifications"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="notification-list" class="ff-notification-list max-h-[min(24rem,60vh)] overflow-y-auto">
            <div class="px-4 py-8 text-center text-sm text-slate-500">Loading notifications…</div>
        </div>

        <div class="flex items-center justify-between gap-2 border-t border-slate-100 px-4 py-2.5">
            <a href="{{ route('messages.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">
                Open messages
            </a>
            @if (auth()->user()?->isAdmin())
                <a href="{{ route('technician-applications.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-500">
                    Applications
                </a>
            @endif
        </div>
    </div>
</div>
