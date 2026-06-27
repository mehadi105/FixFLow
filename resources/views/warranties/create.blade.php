<x-app-layout :role="$role ?? 'admin'">
    <x-page-header title="Issue Warranty" description="Create warranty coverage for a repaired device">
        <x-slot name="actions">
            <x-back-link :href="route('warranties.index')" label="Back to warranties" />
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
                Every repair request already has a warranty. Create a repair request first.
            </p>
        @else
            <form action="{{ route('warranties.store') }}" method="POST" class="space-y-6">
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

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="ff-field">
                        <label for="start_date" class="ff-label">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" class="ff-input" required>
                    </div>
                    <div class="ff-field">
                        <label for="duration_months" class="ff-label">Coverage Duration</label>
                        <select id="duration_months" name="duration_months" class="ff-input" required>
                            @foreach ([3, 6, 12, 24] as $months)
                                <option value="{{ $months }}" @selected(old('duration_months', 6) == $months)>{{ $months }} months</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="ff-card-actions">
                    <a href="{{ route('warranties.index') }}" class="ff-btn-secondary">Cancel</a>
                    <button type="submit" class="ff-btn-primary">Issue Warranty</button>
                </div>
            </form>
        @endif
    </x-dashboard-card>
</x-app-layout>
