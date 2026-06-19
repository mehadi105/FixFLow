@extends('layouts.marketing')

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="ff-hero-glow"></div>
        <div class="absolute inset-0 ff-dot-grid opacity-40"></div>
        <div class="relative mx-auto max-w-7xl px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200/80 bg-white/80 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-indigo-600 shadow-sm backdrop-blur">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                    Premium Repair Experience
                </div>
                <h1 class="mt-8 text-5xl font-bold tracking-tight text-slate-900 sm:text-6xl lg:text-7xl">
                    Device repairs,<br>
                    <span class="ff-gradient-text">elevated.</span>
                </h1>
                <p class="mx-auto mt-8 max-w-2xl text-lg leading-relaxed text-slate-600 sm:text-xl">
                    Submit requests, track every stage, manage invoices and warranties — all from a beautifully simple platform built for modern repair studios.
                </p>
                <div class="mt-12 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <a href="{{ route('register') }}" class="ff-btn-primary w-full sm:w-auto">Get Started Free</a>
                    <a href="{{ route('login') }}" class="ff-btn-secondary w-full sm:w-auto">Sign In to Portal</a>
                </div>
            </div>

            {{-- Floating preview card --}}
            <div class="mx-auto mt-20 max-w-4xl">
                <div class="ff-card overflow-hidden p-1">
                    <div class="rounded-[14px] bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 p-6 sm:p-8">
                        <div class="flex items-center justify-between border-b border-white/10 pb-4">
                            <div class="flex gap-1.5">
                                <span class="h-3 w-3 rounded-full bg-rose-400/80"></span>
                                <span class="h-3 w-3 rounded-full bg-amber-400/80"></span>
                                <span class="h-3 w-3 rounded-full bg-emerald-400/80"></span>
                            </div>
                            <span class="text-xs font-medium text-slate-400">FixFlow Dashboard</span>
                        </div>
                        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                            @foreach ([['12', 'Requests'], ['3', 'Pending'], ['8', 'Done'], ['2', 'Warranty']] as $stat)
                                <div class="rounded-xl bg-white/5 p-4 ring-1 ring-white/10">
                                    <p class="text-2xl font-bold text-white">{{ $stat[0] }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ $stat[1] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- About --}}
    <section id="about" class="py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">About Us</p>
                    <h2 class="mt-3 text-4xl font-bold tracking-tight text-slate-900">Crafted for clarity.<br>Built for trust.</h2>
                    <p class="mt-6 text-base leading-relaxed text-slate-600">
                        FixFlow is a premium repair management platform for electronics service centers and their customers. We streamline the entire lifecycle — from request to diagnosis, repair, invoicing, and warranty.
                    </p>
                    <p class="mt-4 text-base leading-relaxed text-slate-600">
                        Certified technicians, transparent pricing, and real-time updates — so you always know exactly where your device stands.
                    </p>
                    <dl class="mt-10 grid grid-cols-2 gap-4">
                        @foreach ([['5,000+', 'Devices repaired'], ['4.8★', 'Customer rating'], ['48hr', 'Avg. turnaround'], ['6 mo', 'Warranty']] as $stat)
                            <div class="ff-card-flat p-5">
                                <dt class="text-2xl font-bold ff-gradient-text">{{ $stat[0] }}</dt>
                                <dd class="mt-1 text-sm text-slate-500">{{ $stat[1] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-blue-800 p-8 shadow-2xl shadow-indigo-500/25 sm:p-10">
                    <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
                    <h3 class="relative text-2xl font-bold text-white">Why customers choose FixFlow</h3>
                    <ul class="relative mt-8 space-y-5">
                        @foreach (['Certified technicians for all major brands', 'Real-time tracking from drop-off to pickup', 'Genuine parts with upfront, fair pricing', '6-month warranty on every completed repair'] as $point)
                            <li class="flex gap-4">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15 ring-1 ring-white/20">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                </span>
                                <span class="text-sm leading-relaxed text-indigo-100">{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Services --}}
    <section id="services" class="border-y border-slate-200/80 bg-white/60 py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Services</p>
                <h2 class="mt-3 text-4xl font-bold tracking-tight text-slate-900">Expert care for every device</h2>
                <p class="mx-auto mt-4 max-w-2xl text-slate-600">Professional repair services across all major device categories.</p>
            </div>
            <div class="mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['Smartphones', 'Screen, battery, charging port, and water damage.'],
                    ['Laptops', 'Keyboard, display, SSD upgrade, and motherboard.'],
                    ['Tablets', 'Touchscreen, speaker, and connectivity repairs.'],
                    ['Desktops', 'Diagnostics, PSU, RAM, and storage fixes.'],
                ] as $service)
                    <div class="ff-card group p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-50 to-blue-50 text-indigo-600 ring-1 ring-indigo-100 transition-transform group-hover:scale-110">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25h-7.5zM12 18.75h.008v.008H12v-.008z" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-semibold text-slate-900">{{ $service[0] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $service[1] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Process</p>
                <h2 class="mt-3 text-4xl font-bold tracking-tight text-slate-900">How it works</h2>
                <p class="mt-4 text-slate-600">Four refined steps from broken to fully warranted.</p>
            </div>
            <ol class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['01', 'Sign Up & Submit', 'Create your account and describe the issue online.'],
                    ['02', 'Diagnosis', 'Our technician inspects and provides a clear estimate.'],
                    ['03', 'Repair', 'Quality parts, skilled hands, live status updates.'],
                    ['04', 'Pickup & Warranty', 'Pay, collect, and enjoy 6-month peace of mind.'],
                ] as $item)
                    <li class="relative ff-card-flat p-6 text-center">
                        <span class="text-4xl font-bold text-indigo-100">{{ $item[0] }}</span>
                        <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ $item[1] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $item[2] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- CTA --}}
    <section class="relative overflow-hidden py-24">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-indigo-700 to-blue-800"></div>
        <div class="absolute inset-0 ff-dot-grid opacity-10"></div>
        <div class="relative mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold tracking-tight text-white">Ready to experience premium repair?</h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-indigo-100">Join FixFlow and manage every repair from one elegant dashboard.</p>
            <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="{{ route('register') }}" class="ff-btn-secondary w-full border-0 !bg-white !text-indigo-700 hover:!bg-indigo-50 sm:w-auto">Create Free Account</a>
                <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-xl px-6 py-2.5 text-sm font-semibold text-white ring-1 ring-white/30 transition-colors hover:bg-white/10 sm:w-auto">Log in to Portal</a>
            </div>
        </div>
    </section>
@endsection
