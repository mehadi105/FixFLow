<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'FixFlow') }}</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased">
    <div class="flex min-h-screen">
        {{-- Brand panel --}}
        <div class="relative hidden w-1/2 overflow-hidden ff-mesh-dark lg:flex lg:flex-col lg:justify-between lg:p-12">
            <div class="ff-hero-glow"></div>
            <a href="{{ url('/') }}" class="relative z-10 flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-white ring-1 ring-white/20 backdrop-blur">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                    </svg>
                </span>
                <span class="text-2xl font-bold text-white">FixFlow</span>
            </a>

            <div class="relative z-10 max-w-md">
                <p class="text-sm font-semibold uppercase tracking-widest text-indigo-300">Welcome back</p>
                <h1 class="mt-4 text-4xl font-bold leading-tight tracking-tight text-white">Repair management,<br>reimagined.</h1>
                <p class="mt-4 text-base leading-relaxed text-slate-300">Track every repair, invoice, and warranty from a single elegant dashboard.</p>
            </div>

            <p class="relative z-10 text-sm text-slate-500">&copy; {{ date('Y') }} FixFlow</p>
        </div>

        {{-- Form panel --}}
        <div class="flex w-full flex-col items-center justify-center ff-mesh px-4 py-12 sm:px-6 lg:w-1/2 lg:px-12">
            <a href="{{ url('/') }}" class="mb-8 flex items-center gap-2 lg:hidden">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white shadow-lg shadow-indigo-500/30">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                    </svg>
                </span>
                <span class="text-xl font-bold text-slate-900">FixFlow</span>
            </a>

            <div class="w-full max-w-md">
                <div class="ff-card p-8 sm:p-10">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
