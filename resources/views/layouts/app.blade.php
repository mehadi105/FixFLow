<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'FixFlow' }} - {{ config('app.name', 'FixFlow') }}</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="ff-mesh font-sans antialiased">
    <div class="min-h-screen lg:flex">
        <div id="sidebar-backdrop" class="fixed inset-0 z-40 hidden bg-slate-900/60 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>

        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-slate-950 text-slate-300 shadow-2xl shadow-slate-900/50 transition-transform duration-300 ease-out lg:static lg:translate-x-0">
            <div class="flex h-16 shrink-0 items-center gap-x-3 border-b border-white/5 px-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white shadow-lg shadow-indigo-500/25">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <a href="{{ url('/') }}" class="block truncate text-lg font-bold tracking-tight text-white">FixFlow</a>
                    <p class="truncate text-[10px] font-medium uppercase tracking-widest text-slate-500">Repair Management</p>
                </div>
                <button type="button" id="sidebar-close" class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-white/5 hover:text-white lg:hidden" aria-label="Close sidebar">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @include('layouts.partials.sidebar-nav')

            <div class="mt-auto border-t border-white/5 p-4">
                <div class="rounded-xl bg-white/5 px-3 py-2.5 ring-1 ring-white/5">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">Preview Role</p>
                    <p class="mt-0.5 text-sm font-medium capitalize text-slate-200">{{ $role ?? 'customer' }}</p>
                </div>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            @include('layouts.partials.top-navbar')

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @isset($header)
                    <div class="mb-6">{{ $header }}</div>
                @endisset

                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const openBtn = document.getElementById('sidebar-open');
            const closeBtn = document.getElementById('sidebar-close');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }

            openBtn?.addEventListener('click', openSidebar);
            closeBtn?.addEventListener('click', closeSidebar);
            backdrop?.addEventListener('click', closeSidebar);
        })();
    </script>

    @stack('scripts')
</body>
</html>
