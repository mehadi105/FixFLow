<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'FixFlow' }} — Electronic Device Repair Management</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="ff-mesh font-sans text-slate-900 antialiased">
    <header class="sticky top-0 z-50 ff-glass">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3.5 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="group flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white shadow-lg shadow-indigo-500/30 transition-transform group-hover:scale-105">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                    </svg>
                </span>
                <div>
                    <span class="text-lg font-bold tracking-tight text-slate-900">FixFlow</span>
                    <span class="block text-[10px] font-medium uppercase tracking-[0.2em] text-slate-400">Repair Studio</span>
                </div>
            </a>

            <div class="hidden items-center gap-1 md:flex">
                <a href="{{ url('/#about') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-white/60 hover:text-indigo-600">About</a>
                <a href="{{ url('/#services') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-white/60 hover:text-indigo-600">Services</a>
                <a href="{{ url('/#how-it-works') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-white/60 hover:text-indigo-600">How It Works</a>
                <a href="{{ url('/#contact') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-white/60 hover:text-indigo-600">Contact</a>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="ff-btn-ghost hidden sm:inline-flex">Log in</a>
                <a href="{{ route('register') }}" class="ff-btn-primary !px-4 !py-2">Sign up</a>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-slate-200/80 bg-slate-950 text-slate-300">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 md:grid-cols-3">
                <div>
                    <p class="text-xl font-bold text-white">FixFlow</p>
                    <p class="mt-3 max-w-sm text-sm leading-relaxed text-slate-400">Premium electronic device repair management — built for customers, technicians, and service centers who expect clarity and care.</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Portal</p>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li><a href="{{ route('login') }}" class="transition-colors hover:text-white">Customer Login</a></li>
                        <li><a href="{{ route('register') }}" class="transition-colors hover:text-white">Create Account</a></li>
                        <li><a href="{{ url('/dashboard/customer') }}" class="transition-colors hover:text-white">Dashboard Preview</a></li>
                    </ul>
                </div>
                <div id="contact">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Contact</p>
                    <ul class="mt-4 space-y-3 text-sm text-slate-400">
                        <li>123 Repair Lane, Tech City</li>
                        <li>support@fixflow.com</li>
                        <li>+1 (555) 100-2000</li>
                        <li>Mon–Sat, 9:00 AM – 6:00 PM</li>
                    </ul>
                </div>
            </div>
            <p class="mt-12 border-t border-slate-800 pt-8 text-center text-sm text-slate-500">&copy; {{ date('Y') }} FixFlow. Crafted for excellence.</p>
        </div>
    </footer>
</body>
</html>
