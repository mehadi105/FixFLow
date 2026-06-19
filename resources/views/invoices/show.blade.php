<x-app-layout :role="$role ?? 'customer'">
    <x-page-header title="Invoice #INV-2026-0095" description="Issued on Jun 18, 2026">
        <x-slot name="actions">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.22H7.231c-.662 0-1.18-.568-1.12-1.22L6.34 18m11.318 0h1.091a2.25 2.25 0 002.25-2.25V9.75a2.25 2.25 0 00-2.25-2.25h-1.091M6.34 18H5.25A2.25 2.25 0 003 15.75V9.75A2.25 2.25 0 015.25 7.5h1.091m11.318 0H18a2.25 2.25 0 012.25 2.25v6a2.25 2.25 0 01-2.25 2.25h-1.091" /></svg>
                Print
            </button>
            <a href="{{ url('/invoices') }}" class="text-sm font-medium text-gray-600 hover:text-gray-800">← Back</a>
        </x-slot>
    </x-page-header>

    <div class="mx-auto max-w-3xl">
        <div class="rounded-xl bg-white p-8 shadow-sm ring-1 ring-gray-900/5 print:shadow-none print:ring-0">
            {{-- Header --}}
            <div class="flex items-start justify-between border-b border-gray-200 pb-6">
                <div>
                    <h2 class="text-2xl font-bold text-indigo-600">FixFlow</h2>
                    <p class="mt-1 text-sm text-gray-500">Electronic Device Repair Services</p>
                    <p class="mt-1 text-sm text-gray-500">123 Repair Lane, Tech City, TC 10001</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-semibold text-gray-900">INVOICE</p>
                    <p class="mt-1 text-sm text-gray-600">#INV-2026-0095</p>
                    <div class="mt-2"><x-status-badge status="Paid" /></div>
                </div>
            </div>

            {{-- Bill to & repair info --}}
            <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Bill To</h3>
                    <p class="mt-2 text-sm font-medium text-gray-900">Sarah Ahmed</p>
                    <p class="text-sm text-gray-600">sarah@example.com</p>
                    <p class="text-sm text-gray-600">+1 (555) 987-6543</p>
                    <p class="text-sm text-gray-600">456 Oak Ave, Cityville</p>
                </div>
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500">Repair Details</h3>
                    <p class="mt-2 text-sm text-gray-600">Request ID: <span class="font-medium text-gray-900">RR-1042</span></p>
                    <p class="text-sm text-gray-600">Device: <span class="font-medium text-gray-900">Apple iPhone 14 Pro</span></p>
                    <p class="text-sm text-gray-600">Service: <span class="font-medium text-gray-900">Screen replacement</span></p>
                    <p class="text-sm text-gray-600">Technician: <span class="font-medium text-gray-900">Mike Torres</span></p>
                </div>
            </div>

            {{-- Line items --}}
            <div class="mt-8">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Description</th>
                            <th class="py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-3 text-sm text-gray-900">Service Charge — Screen replacement labor</td>
                            <td class="py-3 text-right text-sm text-gray-900">$89.00</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-sm text-gray-900">Parts Cost — LCD + digitizer assembly</td>
                            <td class="py-3 text-right text-sm text-gray-900">$120.00</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-sm text-gray-900">Discount — Loyalty customer</td>
                            <td class="py-3 text-right text-sm text-red-600">-$20.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Total --}}
            <div class="mt-6 flex justify-end border-t border-gray-200 pt-4">
                <dl class="w-64 space-y-2">
                    <div class="flex justify-between text-sm"><dt class="text-gray-500">Subtotal</dt><dd class="text-gray-900">$209.00</dd></div>
                    <div class="flex justify-between text-sm"><dt class="text-gray-500">Discount</dt><dd class="text-red-600">-$20.00</dd></div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 text-base font-semibold"><dt class="text-gray-900">Total Amount</dt><dd class="text-indigo-600">$189.00</dd></div>
                </dl>
            </div>

            <p class="mt-8 text-center text-xs text-gray-400">Thank you for choosing FixFlow. For questions, contact billing@fixflow.com</p>
        </div>
    </div>
</x-app-layout>
