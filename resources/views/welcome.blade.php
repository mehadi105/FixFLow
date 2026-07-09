@extends('layouts.marketing')

@section('content')
    {{-- Hero --}}
    <section class="relative min-h-[92vh] overflow-hidden" data-landing-hero>
        <div class="ff-hero-glow"></div>
        <div class="ff-hero-glow ff-hero-glow--secondary"></div>
        <div class="absolute inset-0 ff-dot-grid opacity-30"></div>

        <div class="ff-orb ff-orb--indigo absolute left-[8%] top-[18%] h-64 w-64"></div>
        <div class="ff-orb ff-orb--blue absolute right-[6%] top-[28%] h-48 w-48"></div>
        <div class="ff-orb ff-orb--violet absolute bottom-[12%] left-[42%] h-36 w-36"></div>

        <div class="relative mx-auto max-w-7xl px-4 pb-20 pt-16 sm:px-6 sm:pb-28 sm:pt-20 lg:px-8">
            <div class="grid items-center gap-16 lg:grid-cols-2 lg:gap-12">
                <div class="text-center lg:text-left">
                    <div data-hero-item class="ff-hero-delay-1 inline-flex items-center gap-2 rounded-full border border-indigo-200/80 bg-white/70 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-indigo-600 shadow-sm backdrop-blur">
                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                        Premium Repair Experience
                    </div>

                    <h1 data-hero-item class="ff-hero-delay-2 mt-8 text-5xl font-bold tracking-tight text-slate-900 sm:text-6xl xl:text-7xl">
                        Device repairs,
                        <span class="ff-gradient-text ff-gradient-text-animated">reimagined.</span>
                    </h1>

                    <p data-hero-item class="ff-hero-delay-3 mx-auto mt-6 max-w-xl text-lg leading-relaxed text-slate-600 sm:text-xl lg:mx-0">
                        From intake to invoice — track every repair in real time, chat with technicians, pay online, and manage warranties from one elegant platform.
                    </p>

                    <div data-hero-item class="ff-hero-delay-4 mt-10 flex flex-col items-center gap-4 sm:flex-row lg:justify-start">
                        <a href="{{ route('register') }}" class="ff-btn-primary group w-full sm:w-auto">
                            Get Started Free
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </a>
                        <a href="{{ route('login') }}" class="ff-btn-secondary w-full sm:w-auto">Sign In to Portal</a>
                    </div>

                    <dl data-hero-item class="ff-hero-delay-5 mt-12 grid grid-cols-3 gap-4 border-t border-slate-200/80 pt-8">
                        <div>
                            <dt class="text-2xl font-bold ff-gradient-text" data-counter="5000" data-counter-suffix="+">0</dt>
                            <dd class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500">Repairs</dd>
                        </div>
                        <div>
                            <dt class="text-2xl font-bold ff-gradient-text" data-counter="48" data-counter-suffix="hr">0</dt>
                            <dd class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500">Turnaround</dd>
                        </div>
                        <div>
                            <dt class="text-2xl font-bold ff-gradient-text" data-counter="98" data-counter-suffix="%">0</dt>
                            <dd class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-500">Satisfaction</dd>
                        </div>
                    </dl>
                </div>

                {{-- Dashboard preview --}}
                <div data-hero-preview class="relative mx-auto w-full max-w-xl lg:max-w-none">
                    <div class="ff-preview-glow"></div>
                    <div class="ff-dashboard-mockup">
                        <div class="ff-dashboard-frame">
                            {{-- Sidebar --}}
                            <aside class="ff-dashboard-sidebar hidden sm:flex">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white shadow-lg shadow-indigo-500/40">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" /></svg>
                                </div>
                                <nav class="mt-8 space-y-1.5">
                                    <span class="ff-dashboard-nav ff-dashboard-nav--active">Dashboard</span>
                                    <span class="ff-dashboard-nav">Repair Requests</span>
                                    <span class="ff-dashboard-nav">Invoices</span>
                                    <span class="ff-dashboard-nav">Reports</span>
                                </nav>
                            </aside>

                            {{-- Main --}}
                            <div class="ff-dashboard-main">
                                <div class="flex items-center justify-between gap-3 border-b border-white/10 pb-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex gap-1 sm:hidden">
                                            <span class="h-2.5 w-2.5 rounded-full bg-rose-400/80"></span>
                                            <span class="h-2.5 w-2.5 rounded-full bg-amber-400/80"></span>
                                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400/80"></span>
                                        </div>
                                        <p class="text-sm font-semibold text-white">FixFlow Dashboard</p>
                                    </div>
                                    <span class="ff-dashboard-live">
                                        <span class="ff-dashboard-live-dot"></span>
                                        Live
                                    </span>
                                </div>

                                <div class="mt-5 grid grid-cols-2 gap-2.5 lg:grid-cols-4">
                                    @foreach ([
                                        ['24', 'Active jobs', '↑ 12%', 'text-emerald-300'],
                                        ['6', 'Pending', '3 urgent', 'text-amber-300'],
                                        ['$12.4k', 'Revenue', 'This month', 'text-blue-300'],
                                        ['3', 'Unread chat', 'New', 'text-violet-300'],
                                    ] as $stat)
                                        <div class="ff-dashboard-stat">
                                            <p class="text-lg font-bold text-white sm:text-xl">{{ $stat[0] }}</p>
                                            <p class="mt-0.5 text-[10px] text-slate-400">{{ $stat[1] }}</p>
                                            <p class="mt-1 text-[9px] font-medium {{ $stat[3] }}">{{ $stat[2] }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4 grid gap-3 lg:grid-cols-5">
                                    <div class="ff-dashboard-panel lg:col-span-3">
                                        <div class="flex items-center justify-between">
                                            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Recent repairs</p>
                                            <span class="text-[10px] text-indigo-300">View all</span>
                                        </div>
                                        <div class="mt-3 space-y-2">
                                            @foreach ([
                                                ['RR-1042', 'iPhone 14 Pro', 'Repairing', 72, 'amber'],
                                                ['RR-1038', 'MacBook Air M2', 'Diagnosing', 35, 'blue'],
                                                ['RR-1035', 'Galaxy S23', 'Completed', 100, 'emerald'],
                                            ] as $row)
                                                <div class="ff-dashboard-row">
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-center gap-2">
                                                            <p class="text-[11px] font-semibold text-white">{{ $row[0] }}</p>
                                                            <span class="ff-dashboard-badge ff-dashboard-badge--{{ $row[4] }}">{{ $row[2] }}</span>
                                                        </div>
                                                        <p class="truncate text-[10px] text-slate-400">{{ $row[1] }}</p>
                                                        <div class="ff-dashboard-progress mt-2">
                                                            <div class="ff-dashboard-progress-bar ff-dashboard-progress-bar--{{ $row[4] }}" style="width: {{ $row[3] }}%"></div>
                                                        </div>
                                                    </div>
                                                    @if ($row[0] === 'RR-1042')
                                                        <span class="ff-dashboard-unread">1</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="ff-dashboard-panel lg:col-span-2">
                                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Revenue trend</p>
                                        <div class="mt-4 flex h-20 items-end justify-between gap-1.5">
                                            @foreach ([40, 55, 48, 72, 65, 88, 95] as $h)
                                                <div class="ff-dashboard-bar" style="height: {{ $h }}%"></div>
                                            @endforeach
                                        </div>
                                        <div class="mt-4 rounded-xl bg-white/5 p-3 ring-1 ring-white/10">
                                            <div class="flex gap-2">
                                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-500/30 text-[9px] font-bold text-indigo-200">MT</div>
                                                <div class="min-w-0">
                                                    <p class="text-[10px] font-medium text-white">Mike · Technician</p>
                                                    <p class="truncate text-[9px] text-slate-400">"Screen replacement is on track for tomorrow."</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ff-float-badge absolute -left-2 top-[18%] hidden rounded-2xl border border-white/80 bg-white/95 px-4 py-3 shadow-xl backdrop-blur sm:block">
                        <div class="flex items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
                            </span>
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Live chat</p>
                                <p class="text-sm font-bold text-slate-900">Technician replied</p>
                            </div>
                        </div>
                    </div>
                    <div class="ff-float-badge ff-float-badge--delayed absolute -right-1 bottom-6 hidden rounded-2xl border border-white/80 bg-white/95 px-4 py-3 shadow-xl backdrop-blur sm:block">
                        <div class="flex items-center gap-2">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                            </span>
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Invoice paid</p>
                                <p class="text-sm font-bold text-emerald-600">$189.00 · Stripe</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Trust marquee --}}
    <section class="border-y border-slate-200/80 bg-white/50 py-8">
        <p class="mb-6 text-center text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Trusted by leading brands</p>
        <div class="ff-marquee">
            @php
                $brands = [
                    ['slug' => 'apple', 'name' => 'Apple'],
                    ['slug' => 'samsung', 'name' => 'Samsung'],
                    ['slug' => 'dell', 'name' => 'Dell'],
                    ['slug' => 'hp', 'name' => 'HP'],
                    ['slug' => 'lenovo', 'name' => 'Lenovo'],
                    ['slug' => 'sony', 'name' => 'Sony'],
                    ['slug' => 'asus', 'name' => 'ASUS'],
                    ['slug' => 'microsoft', 'name' => 'Microsoft'],
                ];
            @endphp
            <div class="ff-marquee-track">
                @foreach (range(1, 2) as $copy)
                    <div class="ff-marquee-group" @if ($copy === 2) aria-hidden="true" @endif>
                        @foreach ($brands as $brand)
                            <div class="ff-brand-slot">
                                <img
                                    src="{{ asset('images/brands/'.$brand['slug'].'.svg') }}"
                                    alt="{{ $copy === 1 ? $brand['name'] : '' }}"
                                    class="ff-brand-logo"
                                    width="136"
                                    height="36"
                                    loading="eager"
                                    decoding="sync"
                                >
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Platform features --}}
    <section class="py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div data-reveal class="mx-auto max-w-3xl text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Platform</p>
                <h2 class="mt-3 text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">Everything your repair studio needs</h2>
                <p class="mt-4 text-lg text-slate-600">A complete workflow — not just a booking form. Built for customers, technicians, and admins.</p>
            </div>

            <div data-reveal-stagger class="mt-16 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['Repair tracking', 'Live status timeline from pending to completed with full visibility.', 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['Live chat', 'Real-time messaging between customers and technicians with unread badges.', 'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z'],
                    ['Stripe payments', 'Customers pay invoices online in test mode; admins can mark cash payments.', 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z'],
                    ['Warranty management', 'Issue and track coverage with automatic active/expired status.', 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z'],
                    ['Admin reports', 'Revenue, repair breakdowns, monthly trends, and technician performance.', 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
                    ['Role-based access', 'Separate dashboards for customers, technicians, and administrators.', 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z'],
                ] as $feature)
                    <div data-reveal-child class="ff-feature-card group p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white shadow-lg shadow-indigo-500/30 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $feature[2] }}" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-semibold text-slate-900">{{ $feature[0] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $feature[1] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- About --}}
    <section id="about" class="relative overflow-hidden border-y border-slate-200/80 bg-white/60 py-24 sm:py-32">
        <div class="absolute inset-0 ff-dot-grid opacity-20"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2">
                <div data-reveal>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">About Us</p>
                    <h2 class="mt-3 text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">Crafted for clarity.<br>Built for trust.</h2>
                    <p class="mt-6 text-base leading-relaxed text-slate-600">
                        FixFlow is a premium repair management platform for electronics service centers and their customers. We streamline the entire lifecycle — from request to diagnosis, repair, invoicing, and warranty.
                    </p>
                    <p class="mt-4 text-base leading-relaxed text-slate-600">
                        Certified technicians, transparent pricing, and real-time updates — so you always know exactly where your device stands.
                    </p>
                    <div data-reveal-stagger class="mt-10 grid grid-cols-2 gap-4">
                        @foreach ([['4.9', '★ Rating'], ['6 mo', 'Warranty'], ['24/7', 'Tracking'], ['100%', 'Transparent']] as $stat)
                            <div data-reveal-child class="ff-card-flat p-5">
                                <dt class="text-2xl font-bold ff-gradient-text">{{ $stat[0] }}</dt>
                                <dd class="mt-1 text-sm text-slate-500">{{ $stat[1] }}</dd>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div data-reveal class="relative">
                    <div class="ff-about-panel overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-blue-800 p-8 shadow-2xl shadow-indigo-500/25 sm:p-10">
                        <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
                        <div class="absolute -bottom-12 -left-8 h-48 w-48 rounded-full bg-blue-400/20 blur-3xl"></div>
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
        </div>
    </section>

    {{-- Services --}}
    <section id="services" class="ff-services-section py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div data-reveal class="text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Services</p>
                <h2 class="mt-3 text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">Expert care for every device</h2>
                <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-600">Professional repair services across all major device categories — with genuine parts and transparent pricing.</p>
            </div>

            @php
                $services = [
                    [
                        'slug' => 'smartphones',
                        'title' => 'Smartphones',
                        'description' => 'Cracked screens, battery issues, charging ports, and water damage — restored with precision.',
                        'details' => 'Our smartphone lab handles everything from shattered displays to liquid damage recovery. Every repair includes a pre-check, genuine-grade parts, and post-repair quality testing before handoff.',
                        'includes' => ['Free diagnostic assessment', 'OEM-quality screen & battery parts', 'Water damage cleaning & board inspect', '6-month workmanship warranty'],
                        'turnaround' => '24–48 hours',
                        'from_price' => '$49',
                        'gradient' => 'from-indigo-500 to-violet-600',
                        'shadow' => 'shadow-indigo-500/30',
                        'tags' => ['Screen', 'Battery', 'Water damage'],
                        'stat' => '2.4k+',
                        'stat_label' => 'Repairs',
                        'icon' => 'M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25h-7.5zM12 18.75h.008v.008H12v-.008z',
                    ],
                    [
                        'slug' => 'laptops',
                        'title' => 'Laptops',
                        'description' => 'Keyboard, display, SSD upgrades, and motherboard repairs for work and study machines.',
                        'details' => 'Whether it is a MacBook, ThinkPad, or gaming laptop, we diagnose power, display, and board-level faults with clear estimates before any work begins.',
                        'includes' => ['Keyboard & trackpad replacement', 'Display panel swap & calibration', 'SSD / RAM upgrades', 'Thermal paste & fan service'],
                        'turnaround' => '2–4 business days',
                        'from_price' => '$79',
                        'gradient' => 'from-blue-500 to-indigo-600',
                        'shadow' => 'shadow-blue-500/30',
                        'tags' => ['Display', 'Keyboard', 'SSD upgrade'],
                        'stat' => '48hr',
                        'stat_label' => 'Avg. turnaround',
                        'icon' => 'M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25',
                    ],
                    [
                        'slug' => 'tablets',
                        'title' => 'Tablets',
                        'description' => 'Touchscreen calibration, speaker fixes, and connectivity issues for iPad and Android tablets.',
                        'details' => 'Tablets need careful handling — we specialize in digitizer replacements, speaker repairs, and charging port fixes while preserving your data where possible.',
                        'includes' => ['Touchscreen / digitizer repair', 'Speaker & mic restoration', 'Wi‑Fi & Bluetooth diagnostics', 'Battery replacement'],
                        'turnaround' => '1–3 business days',
                        'from_price' => '$59',
                        'gradient' => 'from-cyan-500 to-blue-600',
                        'shadow' => 'shadow-cyan-500/30',
                        'tags' => ['Touchscreen', 'Speakers', 'Wi‑Fi'],
                        'stat' => '6 mo',
                        'stat_label' => 'Warranty',
                        'icon' => 'M10.5 19.5h3m-6.75 2.25h10.5a2.25 2.25 0 002.25-2.25v-15a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 4.5v15a2.25 2.25 0 002.25 2.25z',
                    ],
                    [
                        'slug' => 'desktops',
                        'title' => 'Desktops',
                        'description' => 'Full diagnostics, PSU replacement, RAM upgrades, and storage fixes for home and office PCs.',
                        'details' => 'From custom gaming rigs to office workstations, we troubleshoot boot failures, power issues, and performance bottlenecks with transparent part-and-labor quotes.',
                        'includes' => ['Full hardware diagnostics', 'PSU & motherboard repair', 'RAM & storage upgrades', 'OS tune-up & data backup advice'],
                        'turnaround' => '1–2 business days',
                        'from_price' => '$39',
                        'gradient' => 'from-slate-600 to-indigo-700',
                        'shadow' => 'shadow-slate-500/30',
                        'tags' => ['Diagnostics', 'PSU', 'Storage'],
                        'stat' => '98%',
                        'stat_label' => 'Success rate',
                        'icon' => 'M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25M4.5 6.75h15M4.5 10.5h15m-15 3.75h10.5',
                    ],
                ];
            @endphp

            <script type="application/json" id="services-data">@json($services)</script>

            <div data-reveal-stagger class="mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($services as $index => $service)
                    <article data-reveal-child class="ff-service-card-premium group">
                        <div class="flex items-start justify-between gap-4">
                            <div class="ff-service-icon-wrap bg-gradient-to-br {{ $service['gradient'] }} {{ $service['shadow'] }}">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $service['icon'] }}" />
                                </svg>
                            </div>
                            <div class="ff-service-stat text-right">
                                <strong>{{ $service['stat'] }}</strong>
                                {{ $service['stat_label'] }}
                            </div>
                        </div>

                        <h3 class="mt-6 text-xl font-semibold text-slate-900">{{ $service['title'] }}</h3>
                        <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-500">{{ $service['description'] }}</p>

                        <div class="mt-5 flex flex-wrap gap-2">
                            @foreach ($service['tags'] as $tag)
                                <span class="ff-service-tag">{{ $tag }}</span>
                            @endforeach
                        </div>

                        <button type="button" class="ff-service-details-btn mt-6 flex w-full items-center justify-between border-t border-slate-100 pt-5 text-left" data-service-open="{{ $index }}" aria-label="View {{ $service['title'] }} service details">
                            <span class="text-xs font-semibold uppercase tracking-wide text-indigo-600">View details</span>
                            <span class="ff-service-arrow" aria-hidden="true">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </span>
                        </button>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Service details modal --}}
    <div class="ff-service-modal" data-service-modal hidden>
        <div class="ff-service-modal-backdrop" data-service-close></div>
        <div class="ff-service-modal-panel" role="dialog" aria-modal="true" aria-labelledby="service-modal-title" tabindex="-1">
            <button type="button" class="ff-service-modal-close" data-service-close aria-label="Close service details">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            <div class="ff-service-modal-icon" data-service-modal-icon></div>
            <h3 id="service-modal-title" class="mt-5 text-2xl font-bold text-slate-900" data-service-modal-title></h3>
            <p class="mt-3 text-sm leading-relaxed text-slate-600" data-service-modal-description></p>

            <div class="mt-6 grid grid-cols-2 gap-3">
                <div class="ff-service-modal-meta">
                    <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Turnaround</span>
                    <span class="mt-1 block text-sm font-semibold text-slate-900" data-service-modal-turnaround></span>
                </div>
                <div class="ff-service-modal-meta">
                    <span class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Starting from</span>
                    <span class="mt-1 block text-sm font-semibold text-indigo-600" data-service-modal-price></span>
                </div>
            </div>

            <div class="mt-6">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">What&apos;s included</p>
                <ul class="mt-3 space-y-2" data-service-modal-includes></ul>
            </div>

            <div class="mt-6 flex flex-wrap gap-2" data-service-modal-tags></div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('register') }}" class="ff-btn-primary flex-1 justify-center" data-service-modal-register>Book this repair</a>
                <button type="button" class="ff-btn-secondary flex-1 justify-center" data-service-close>Close</button>
            </div>
        </div>
    </div>

    {{-- How it works --}}
    <section id="how-it-works" class="border-y border-slate-200/80 bg-white/60 py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div data-reveal class="text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Process</p>
                <h2 class="mt-3 text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">How it works</h2>
                <p class="mt-4 text-lg text-slate-600">Four refined steps from broken to fully warranted.</p>
            </div>

            <ol data-reveal-stagger class="relative mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div class="ff-timeline-line absolute left-0 right-0 top-12 hidden h-px bg-gradient-to-r from-transparent via-indigo-200 to-transparent lg:block"></div>
                @foreach ([
                    ['01', 'Sign Up & Submit', 'Create your account and describe the issue online.', 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
                    ['02', 'Diagnosis', 'Our technician inspects and provides a clear estimate.', 'M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5'],
                    ['03', 'Repair', 'Quality parts, skilled hands, live status updates.', 'M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z'],
                    ['04', 'Pickup & Warranty', 'Pay, collect, and enjoy 6-month peace of mind.', 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z'],
                ] as $item)
                    <li data-reveal-child class="relative ff-step-card text-center">
                        <div class="relative z-10 mx-auto flex h-24 w-24 items-center justify-center rounded-2xl bg-white shadow-lg shadow-indigo-500/10 ring-1 ring-indigo-100">
                            <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item[3] }}" /></svg>
                            <span class="absolute -right-1 -top-1 flex h-7 w-7 items-center justify-center rounded-full bg-indigo-600 text-[10px] font-bold text-white">{{ $item[0] }}</span>
                        </div>
                        <h3 class="mt-6 text-lg font-semibold text-slate-900">{{ $item[1] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ $item[2] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div data-reveal class="text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">Testimonials</p>
                <h2 class="mt-3 text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">Loved by customers</h2>
                <div class="mt-6 inline-flex items-center gap-3 rounded-full border border-slate-200/80 bg-white px-5 py-2.5 shadow-sm">
                    <div class="flex gap-0.5 text-amber-400">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                        @endfor
                    </div>
                    <span class="text-sm font-semibold text-slate-900">4.9 average</span>
                    <span class="text-slate-300">|</span>
                    <span class="text-sm text-slate-500"><span data-counter="500" data-counter-suffix="+">500+</span> reviews</span>
                </div>
            </div>

            @php
                $testimonials = [
                    ['quote' => 'Fixed my MacBook in 2 days. The live chat kept me updated the whole time — felt like a premium service.', 'name' => 'Sarah M.', 'role' => 'MacBook Pro repair', 'photo' => 'sarah.jpg'],
                    ['quote' => 'Paid my invoice online in seconds with Stripe. Super smooth experience from drop-off to pickup.', 'name' => 'James K.', 'role' => 'iPhone screen repair', 'photo' => 'james.jpg'],
                    ['quote' => 'As a technician, FixFlow makes managing assigned jobs effortless. Status updates and chat are a game changer.', 'name' => 'Mike Torres', 'role' => 'Certified technician', 'photo' => 'mike.jpg'],
                    ['quote' => 'Submitted my repair request at midnight and had a diagnosis note by morning. Transparency I have never seen before.', 'name' => 'Aisha R.', 'role' => 'Samsung Galaxy repair', 'photo' => 'aisha.jpg'],
                    ['quote' => 'The warranty tracking alone is worth it. I knew exactly when my iPad coverage expired — no surprises.', 'name' => 'David L.', 'role' => 'iPad Air repair', 'photo' => 'david.jpg'],
                ];
            @endphp

            <div data-reveal class="ff-reviews-marquee mt-14 -mx-4 sm:-mx-6 lg:-mx-8">
                <div class="ff-reviews-track">
                    @foreach (range(1, 2) as $copy)
                        <div class="ff-reviews-group" @if ($copy === 2) aria-hidden="true" @endif>
                            @foreach ($testimonials as $testimonial)
                                <blockquote class="ff-testimonial-card w-[20rem] shrink-0 p-6 sm:w-[22rem]">
                                    <div class="flex gap-0.5 text-amber-400">
                                        @for ($i = 0; $i < 5; $i++)
                                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                        @endfor
                                    </div>
                                    <p class="mt-4 text-sm leading-relaxed text-slate-600">{{ $testimonial['quote'] }}</p>
                                    <footer class="mt-5 flex items-center gap-3 border-t border-slate-100 pt-4">
                                        <img
                                            src="{{ asset('images/testimonials/'.$testimonial['photo']) }}"
                                            alt="{{ $testimonial['name'] }}"
                                            class="ff-testimonial-photo"
                                            width="40"
                                            height="40"
                                            loading="eager"
                                            decoding="sync"
                                        >
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-slate-900">{{ $testimonial['name'] }}</p>
                                            <p class="truncate text-xs text-slate-500">{{ $testimonial['role'] }}</p>
                                        </div>
                                    </footer>
                                </blockquote>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="relative overflow-hidden py-24 sm:py-32">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-indigo-700 to-blue-800"></div>
        <div class="absolute inset-0 ff-dot-grid opacity-10"></div>
        <div class="ff-orb ff-orb--white absolute -left-20 top-0 h-72 w-72 opacity-30"></div>
        <div class="ff-orb ff-orb--white absolute -right-16 bottom-0 h-56 w-56 opacity-20"></div>

        <div data-reveal class="relative mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold tracking-tight text-white sm:text-5xl">Ready to experience premium repair?</h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-indigo-100">Join FixFlow and manage every repair from one elegant dashboard — free to get started.</p>
            <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="{{ route('register') }}" class="ff-btn-secondary w-full border-0 !bg-white !text-indigo-700 hover:!bg-indigo-50 sm:w-auto">Create Free Account</a>
                <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-xl px-6 py-2.5 text-sm font-semibold text-white ring-1 ring-white/30 transition-colors hover:bg-white/10 sm:w-auto">Log in to Portal</a>
            </div>
        </div>
    </section>
@endsection
