@php
    $statuses = \App\Models\RepairRequest::STATUSES;
    $currentIndex = array_search($repairRequest->status, $statuses, true);
    $stepLabels = [
        'pending' => 'Submitted',
        'assigned' => 'Assigned',
        'diagnosing' => 'Diagnosing',
        'repairing' => 'Repairing',
        'completed' => 'Completed',
    ];

    $authUser = auth()->user();
    $canWork = $authUser->isAdmin()
        || ($authUser->isTechnician() && $repairRequest->technician_id === $authUser->id);
    $canChat = $canChat ?? $repairRequest->hasChatParticipant($authUser);
@endphp

<x-app-layout :role="$role ?? 'customer'">
    @if (session('status'))
        <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <x-page-header
        title="Repair Request {{ $repairRequest->reference }}"
        description="Submitted on {{ $repairRequest->created_at->format('M d, Y') }}"
    >
        <x-slot name="actions">
            <x-status-badge :status="$repairRequest->status" />
            <x-back-link :href="route('repair-requests.index')" />
        </x-slot>
    </x-page-header>

    <div class="ff-grid-sidebar">
        <div class="ff-section lg:col-span-2">
            <x-dashboard-card title="Device Information">
                <dl class="ff-dl">
                    <div><dt>Device Type</dt><dd>{{ $repairRequest->device_type }}</dd></div>
                    <div><dt>Brand</dt><dd>{{ $repairRequest->brand ?? '—' }}</dd></div>
                    <div><dt>Model</dt><dd>{{ $repairRequest->model ?? '—' }}</dd></div>
                    <div><dt>Serial Number</dt><dd>{{ $repairRequest->serial_number ?? '—' }}</dd></div>
                    <div class="ff-dl-wide"><dt>Issue Description</dt><dd class="font-normal text-slate-700">{{ $repairRequest->issue_description }}</dd></div>
                    <div><dt>Priority</dt><dd><x-status-badge :status="$repairRequest->priority" /></dd></div>
                </dl>
            </x-dashboard-card>

            <x-dashboard-card title="Customer Information">
                <dl class="ff-dl">
                    <div><dt>Name</dt><dd>{{ $repairRequest->customer->name }}</dd></div>
                    <div><dt>Email</dt><dd>{{ $repairRequest->customer->email }}</dd></div>
                </dl>
            </x-dashboard-card>

            <x-dashboard-card title="Repair Status Timeline">
                <ol class="relative ml-3 border-l border-slate-200">
                    @foreach ($statuses as $i => $statusKey)
                        @php
                            $done = $i < $currentIndex;
                            $active = $i === $currentIndex;
                        @endphp
                        <li class="mb-6 ml-6 last:mb-0">
                            <span class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full {{ $done ? 'bg-indigo-600' : ($active ? 'bg-indigo-100 ring-2 ring-indigo-600' : 'bg-slate-100') }}">
                                @if ($done)
                                    <svg class="h-3.5 w-3.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                @endif
                            </span>
                            <h4 class="text-sm font-semibold {{ ($done || $active) ? 'text-slate-900' : 'text-slate-400' }}">{{ $stepLabels[$statusKey] }}</h4>
                            <p class="text-xs text-slate-500">{{ $active ? 'Current stage' : ($done ? 'Completed' : 'Pending') }}</p>
                        </li>
                    @endforeach
                </ol>
            </x-dashboard-card>

            <x-dashboard-card title="Uploaded Image">
                @if ($repairRequest->image_path)
                    <img src="{{ asset('storage/'.$repairRequest->image_path) }}" alt="Device image" class="w-full rounded-xl">
                @else
                    <div class="flex aspect-video items-center justify-center rounded-xl bg-slate-50">
                        <div class="text-center text-slate-400">
                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <p class="mt-2 text-sm">No image uploaded</p>
                        </div>
                    </div>
                @endif
            </x-dashboard-card>

            @if ($canChat)
                <x-dashboard-card title="Conversation" description="Chat with everyone involved in this repair">
                    <x-chat-panel :repair-request="$repairRequest" :messages="$messages" />
                </x-dashboard-card>
            @endif
        </div>

        <div class="ff-section">
            @if ($canWork)
                <x-dashboard-card title="Update Status">
                    <form method="POST" action="{{ route('repair-requests.status', $repairRequest) }}" class="space-y-3">
                        @csrf
                        <div class="ff-field">
                            <label for="status" class="ff-label">Repair status</label>
                            <select id="status" name="status" class="ff-input">
                                @foreach ($statuses as $statusOption)
                                    <option value="{{ $statusOption }}" @selected($repairRequest->status === $statusOption)>{{ ucfirst($statusOption) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="ff-btn-primary w-full">Save Status</button>
                    </form>
                </x-dashboard-card>
            @endif

            <x-dashboard-card title="Technician Assignment">
                @if ($repairRequest->technician)
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                            {{ strtoupper(substr($repairRequest->technician->name, 0, 2)) }}
                        </span>
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $repairRequest->technician->name }}</p>
                            <p class="text-xs text-slate-500">Technician</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-500">No technician assigned yet.</p>
                @endif

                @if ($authUser->isAdmin())
                    <form method="POST" action="{{ route('repair-requests.assign', $repairRequest) }}" class="mt-4 space-y-3">
                        @csrf
                        <div class="ff-field">
                            <label for="technician_id" class="ff-label">{{ $repairRequest->technician ? 'Reassign technician' : 'Assign technician' }}</label>
                            <select id="technician_id" name="technician_id" class="ff-input" required>
                                <option value="">Select a technician</option>
                                @foreach ($technicians as $technician)
                                    <option value="{{ $technician->id }}" @selected($repairRequest->technician_id === $technician->id)>{{ $technician->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="ff-btn-primary w-full">{{ $repairRequest->technician ? 'Reassign' : 'Assign' }}</button>
                    </form>
                @endif
            </x-dashboard-card>

            <x-dashboard-card title="Diagnosis Notes">
                @if ($canWork)
                    <form method="POST" action="{{ route('repair-requests.diagnosis', $repairRequest) }}" class="space-y-3">
                        @csrf
                        <div class="ff-field">
                            <label for="diagnosis_notes" class="sr-only">Diagnosis notes</label>
                            <textarea id="diagnosis_notes" name="diagnosis_notes" rows="4" placeholder="Add diagnosis notes..." class="ff-input">{{ old('diagnosis_notes', $repairRequest->diagnosis_notes) }}</textarea>
                        </div>
                        <button type="submit" class="ff-btn-primary w-full">Save Notes</button>
                    </form>
                @else
                    <p class="text-sm {{ $repairRequest->diagnosis_notes ? 'text-slate-700' : 'text-slate-500' }}">
                        {{ $repairRequest->diagnosis_notes ?? 'No diagnosis notes yet.' }}
                    </p>
                @endif
            </x-dashboard-card>

            <x-dashboard-card title="Invoice">
                @if ($repairRequest->invoice)
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">Invoice No</dt><dd class="font-medium text-slate-900">{{ $repairRequest->invoice->invoice_number }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Amount</dt><dd class="font-medium text-slate-900">${{ number_format($repairRequest->invoice->total, 2) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd><x-status-badge :status="$repairRequest->invoice->payment_status" /></dd></div>
                    </dl>
                    <a href="{{ route('invoices.show', $repairRequest->invoice) }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">View Invoice</a>
                @elseif ($authUser->isAdmin())
                    <p class="text-sm text-slate-500">No invoice generated yet.</p>
                    <a href="{{ route('invoices.create', ['repair_request_id' => $repairRequest->id]) }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">Create Invoice</a>
                @else
                    <p class="text-sm text-slate-500">No invoice generated yet.</p>
                @endif
            </x-dashboard-card>

            <x-dashboard-card title="Warranty">
                @if ($repairRequest->warranty)
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">Warranty Code</dt><dd class="font-medium text-slate-900">{{ $repairRequest->warranty->warranty_code }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Coverage</dt><dd><x-status-badge :status="$repairRequest->warranty->status" /></dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Valid Until</dt><dd class="font-medium text-slate-900">{{ $repairRequest->warranty->end_date->format('M d, Y') }}</dd></div>
                    </dl>
                @elseif ($authUser->isAdmin())
                    <p class="text-sm text-slate-500">No warranty issued yet.</p>
                    <a href="{{ route('warranties.create', ['repair_request_id' => $repairRequest->id]) }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">Issue Warranty</a>
                @else
                    <p class="text-sm text-slate-500">No warranty issued yet.</p>
                @endif
            </x-dashboard-card>
        </div>
    </div>
</x-app-layout>
