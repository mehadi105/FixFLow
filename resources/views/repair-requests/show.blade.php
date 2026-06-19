@php
    $timeline = [
        ['status' => 'Submitted', 'date' => 'Jun 15, 2026 09:30 AM', 'active' => true, 'done' => true],
        ['status' => 'Assigned', 'date' => 'Jun 15, 2026 11:00 AM', 'active' => true, 'done' => true],
        ['status' => 'Diagnosing', 'date' => 'Jun 16, 2026 10:15 AM', 'active' => true, 'done' => true],
        ['status' => 'Repairing', 'date' => 'In progress', 'active' => true, 'done' => false],
        ['status' => 'Completed', 'date' => 'Pending', 'active' => false, 'done' => false],
    ];
@endphp

<x-app-layout :role="$role ?? 'customer'">
    <x-page-header title="Repair Request #RR-1042" description="Submitted on Jun 15, 2026">
        <x-slot name="actions">
            <x-status-badge status="Repairing" />
            <x-back-link :href="url('/repair-requests')" />
        </x-slot>
    </x-page-header>

    <div class="ff-grid-sidebar">
        <div class="ff-section lg:col-span-2">
            <x-dashboard-card title="Device Information">
                <dl class="ff-dl">
                    <div><dt>Device Type</dt><dd>Smartphone</dd></div>
                    <div><dt>Brand</dt><dd>Apple</dd></div>
                    <div><dt>Model</dt><dd>iPhone 14 Pro</dd></div>
                    <div><dt>Serial Number</dt><dd>SN-APL-8847291</dd></div>
                    <div class="ff-dl-wide"><dt>Issue Description</dt><dd class="font-normal text-slate-700">Cracked screen after drop. Touch partially unresponsive on the right side of the display.</dd></div>
                    <div><dt>Priority</dt><dd><x-status-badge status="High" /></dd></div>
                </dl>
            </x-dashboard-card>

            <x-dashboard-card title="Customer Information">
                <dl class="ff-dl">
                    <div><dt>Name</dt><dd>John Customer</dd></div>
                    <div><dt>Email</dt><dd>john@example.com</dd></div>
                    <div><dt>Phone</dt><dd>+1 (555) 123-4567</dd></div>
                    <div><dt>Address</dt><dd>123 Main St, Cityville</dd></div>
                </dl>
            </x-dashboard-card>

            <x-dashboard-card title="Repair Status Timeline">
                <ol class="relative ml-3 border-l border-slate-200">
                    @foreach ($timeline as $step)
                        <li class="mb-6 ml-6 last:mb-0">
                            <span class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full {{ $step['done'] ? 'bg-indigo-600' : ($step['active'] ? 'bg-indigo-100 ring-2 ring-indigo-600' : 'bg-slate-100') }}">
                                @if ($step['done'])
                                    <svg class="h-3.5 w-3.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                @endif
                            </span>
                            <h4 class="text-sm font-semibold {{ $step['active'] ? 'text-slate-900' : 'text-slate-400' }}">{{ $step['status'] }}</h4>
                            <p class="text-xs text-slate-500">{{ $step['date'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </x-dashboard-card>

            <x-dashboard-card title="Uploaded Image">
                <div class="flex aspect-video items-center justify-center rounded-xl bg-slate-50">
                    <div class="text-center text-slate-400">
                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        <p class="mt-2 text-sm">Device image preview placeholder</p>
                    </div>
                </div>
            </x-dashboard-card>
        </div>

        <div class="ff-section">
            <x-dashboard-card title="Technician Assignment">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">MT</span>
                    <div>
                        <p class="text-sm font-medium text-slate-900">Mike Torres</p>
                        <p class="text-xs text-slate-500">Senior Technician</p>
                    </div>
                </div>
                <p class="ff-placeholder-note">Placeholder — connect to technician assignment logic later.</p>
            </x-dashboard-card>

            <x-dashboard-card title="Diagnosis Notes">
                <p class="text-sm text-slate-600">Screen digitizer and LCD assembly need replacement. No internal damage detected. Estimated repair time: 2 hours.</p>
                <p class="ff-placeholder-note">Placeholder — connect to diagnosis notes module later.</p>
            </x-dashboard-card>

            <x-dashboard-card title="Invoice">
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Invoice No</dt><dd class="font-medium text-slate-900">INV-2026-0089</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Amount</dt><dd class="font-medium text-slate-900">$189.00</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd><x-status-badge status="Unpaid" /></dd></div>
                </dl>
                <a href="{{ url('/invoices/1') }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">View Invoice</a>
            </x-dashboard-card>

            <x-dashboard-card title="Warranty">
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-slate-500">Warranty Code</dt><dd class="font-medium text-slate-900">WRN-8847-APL</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Coverage</dt><dd><x-status-badge status="Active" /></dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Valid Until</dt><dd class="font-medium text-slate-900">Dec 15, 2026</dd></div>
                </dl>
                <p class="ff-placeholder-note">Placeholder — connect to warranty module later.</p>
            </x-dashboard-card>
        </div>
    </div>
</x-app-layout>
