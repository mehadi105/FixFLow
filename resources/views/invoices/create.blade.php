<x-app-layout :role="$role ?? 'admin'">
    <x-page-header title="Create Invoice" description="Generate an invoice for a repair request">
        <x-slot name="actions">
            <x-back-link :href="route('invoices.index')" label="Back to invoices" />
        </x-slot>
    </x-page-header>

    <x-dashboard-card>
        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
                <ul class="list-disc space-y-1 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($repairRequests->isEmpty())
            <p class="text-sm text-slate-500">
                Every repair request already has an invoice. Create a repair request first.
            </p>
        @else
            <form action="{{ route('invoices.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="ff-field">
                    <label for="repair_request_id" class="ff-label">Repair Request</label>
                    <select id="repair_request_id" name="repair_request_id" class="ff-input" required>
                        <option value="">Select a repair request</option>
                        @foreach ($repairRequests as $repairRequest)
                            <option value="{{ $repairRequest->id }}" @selected(old('repair_request_id', $selectedRequestId) == $repairRequest->id)>
                                {{ $repairRequest->reference }} — {{ $repairRequest->customer->name }} ({{ $repairRequest->device_label }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="ff-field">
                        <label for="service_charge" class="ff-label">Service Charge ($)</label>
                        <input type="number" step="0.01" min="0" id="service_charge" name="service_charge" value="{{ old('service_charge', '0.00') }}" class="ff-input js-amount" required>
                    </div>
                    <div class="ff-field">
                        <label for="parts_cost" class="ff-label">Parts Cost ($)</label>
                        <input type="number" step="0.01" min="0" id="parts_cost" name="parts_cost" value="{{ old('parts_cost', '0.00') }}" class="ff-input js-amount" required>
                    </div>
                    <div class="ff-field">
                        <label for="discount" class="ff-label">Discount ($)</label>
                        <input type="number" step="0.01" min="0" id="discount" name="discount" value="{{ old('discount', '0.00') }}" class="ff-input js-amount" required>
                    </div>
                </div>

                <div class="ff-field sm:w-60">
                    <label for="payment_status" class="ff-label">Payment Status</label>
                    <select id="payment_status" name="payment_status" class="ff-input" required>
                        <option value="unpaid" @selected(old('payment_status', 'unpaid') === 'unpaid')>Unpaid</option>
                        <option value="paid" @selected(old('payment_status') === 'paid')>Paid</option>
                    </select>
                </div>

                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                    <span class="text-sm font-medium text-slate-600">Total Amount</span>
                    <span class="text-lg font-semibold text-indigo-600" id="total-preview">$0.00</span>
                </div>

                <div class="ff-card-actions">
                    <a href="{{ route('invoices.index') }}" class="ff-btn-secondary">Cancel</a>
                    <button type="submit" class="ff-btn-primary">Create Invoice</button>
                </div>
            </form>
        @endif
    </x-dashboard-card>

    @push('scripts')
    <script>
        (function () {
            const amounts = document.querySelectorAll('.js-amount');
            const preview = document.getElementById('total-preview');
            if (!preview) return;

            function num(id) {
                return parseFloat(document.getElementById(id).value) || 0;
            }

            function update() {
                const total = Math.max(0, num('service_charge') + num('parts_cost') - num('discount'));
                preview.textContent = '$' + total.toFixed(2);
            }

            amounts.forEach((el) => el.addEventListener('input', update));
            update();
        })();
    </script>
    @endpush
</x-app-layout>
