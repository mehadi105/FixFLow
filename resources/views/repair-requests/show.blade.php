@php
    $isDeclined = $repairRequest->status === \App\Models\RepairRequest::STATUS_DECLINED;
    $timelineStatuses = \App\Models\RepairRequest::TIMELINE_STATUSES;
    if ($isDeclined) {
        $timelineStatuses = [
            \App\Models\RepairRequest::STATUS_PENDING,
            \App\Models\RepairRequest::STATUS_ASSIGNED,
            \App\Models\RepairRequest::STATUS_DIAGNOSING,
            \App\Models\RepairRequest::STATUS_QUOTED,
            \App\Models\RepairRequest::STATUS_DECLINED,
        ];
    }
    $currentIndex = array_search($repairRequest->status, $timelineStatuses, true);
    if ($currentIndex === false) {
        $currentIndex = 0;
    }
    $stepLabels = [
        'pending' => 'Submitted',
        'assigned' => 'Assigned',
        'diagnosing' => 'Diagnosing',
        'quoted' => 'Quote sent',
        'repairing' => 'Repairing',
        'completed' => 'Completed',
        'declined' => 'Declined',
    ];
    $statusTransitions = $repairRequest->availableStatusTransitions();

    $authUser = auth()->user();
    $canWork = $authUser->isAdmin()
        || ($authUser->isTechnician() && $repairRequest->technician_id === $authUser->id);
    $canChat = $canChat ?? $repairRequest->hasChatParticipant($authUser);

    $quoteService = old('quote_service_charge', $repairRequest->quote_service_charge ?? $suggestedServiceCharge);
    $quoteParts = old('quote_parts_cost', $repairRequest->quote_parts_cost ?? $suggestedPartsCost);
    $quoteDiscount = old('quote_discount', $repairRequest->quote_discount ?? 0);
    $diagnosisFee = old('diagnosis_fee', $repairRequest->diagnosis_fee ?? $suggestedDiagnosisFee);
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
                    @foreach ($timelineStatuses as $i => $statusKey)
                        @php
                            $isTerminal = in_array($repairRequest->status, [
                                \App\Models\RepairRequest::STATUS_COMPLETED,
                                \App\Models\RepairRequest::STATUS_DECLINED,
                            ], true);
                            $done = $i < $currentIndex || ($isTerminal && $i === $currentIndex);
                            $active = ! $isTerminal && $i === $currentIndex;
                        @endphp
                        <li class="mb-6 ml-6 last:mb-0">
                            <span class="absolute -left-3 flex h-6 w-6 items-center justify-center rounded-full {{ $done ? ($statusKey === 'declined' ? 'bg-rose-600' : 'bg-indigo-600') : ($active ? 'bg-indigo-100 ring-2 ring-indigo-600' : 'bg-slate-100') }}">
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
                <x-dashboard-card title="Messages">
                    <p class="text-sm text-slate-600">Chat with everyone involved in this repair in your inbox.</p>
                    <a href="{{ route('messages.show', $repairRequest) }}" class="ff-btn-primary mt-4 inline-flex w-full items-center justify-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                        </svg>
                        Open conversation
                    </a>
                </x-dashboard-card>
            @endif
        </div>

        <div class="ff-section">
            @if ($canWork && count($statusTransitions) > 1)
                <x-dashboard-card title="Update Status">
                    <form method="POST" action="{{ route('repair-requests.status', $repairRequest) }}" class="space-y-3">
                        @csrf
                        <div class="ff-field">
                            <label for="status" class="ff-label">Repair status</label>
                            <select id="status" name="status" class="ff-input">
                                @foreach ($statusTransitions as $statusOption)
                                    <option value="{{ $statusOption }}" @selected($repairRequest->status === $statusOption)>{{ ucfirst(str_replace('_', ' ', $statusOption)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-xs text-slate-500">After diagnosis, send a quote for customer approval before repairing.</p>
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
                @if ($canWork && ! in_array($repairRequest->status, [
                    \App\Models\RepairRequest::STATUS_COMPLETED,
                    \App\Models\RepairRequest::STATUS_DECLINED,
                    \App\Models\RepairRequest::STATUS_REPAIRING,
                ], true))
                    <form method="POST" action="{{ route('repair-requests.diagnosis', $repairRequest) }}" class="space-y-3">
                        @csrf
                        <div class="ff-field">
                            <label for="diagnosis_notes" class="sr-only">Diagnosis notes</label>
                            <textarea id="diagnosis_notes" name="diagnosis_notes" rows="4" placeholder="Add diagnosis notes..." class="ff-input">{{ old('diagnosis_notes', $repairRequest->diagnosis_notes) }}</textarea>
                        </div>
                        @error('diagnosis_notes')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="ff-btn-primary w-full">Save Notes</button>
                    </form>
                @else
                    <p class="text-sm {{ $repairRequest->diagnosis_notes ? 'text-slate-700' : 'text-slate-500' }}">
                        {{ $repairRequest->diagnosis_notes ?? 'No diagnosis notes yet.' }}
                    </p>
                @endif
            </x-dashboard-card>

            <x-dashboard-card title="Repair Quote">
                @if ($canWork && in_array($repairRequest->status, [
                    \App\Models\RepairRequest::STATUS_DIAGNOSING,
                    \App\Models\RepairRequest::STATUS_QUOTED,
                ], true))
                    <p class="mb-3 text-sm text-slate-600">
                        Send a quote after diagnosis. The customer must approve before repair continues. If they decline, only the diagnosis fee is charged.
                    </p>
                    @unless (filled($repairRequest->diagnosis_notes))
                        <p class="mb-3 rounded-lg bg-amber-50 px-3 py-2 text-sm text-amber-800 ring-1 ring-amber-200">
                            Save diagnosis notes first, then send the quote.
                        </p>
                    @endunless
                    <form method="POST" action="{{ route('repair-requests.quote', $repairRequest) }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div class="ff-field">
                                <label for="quote_service_charge" class="ff-label">Service charge</label>
                                <input id="quote_service_charge" type="number" step="0.01" min="0" name="quote_service_charge" value="{{ $quoteService }}" class="ff-input" required>
                            </div>
                            <div class="ff-field">
                                <label for="quote_parts_cost" class="ff-label">Parts cost</label>
                                <input id="quote_parts_cost" type="number" step="0.01" min="0" name="quote_parts_cost" value="{{ $quoteParts }}" class="ff-input" required>
                            </div>
                            <div class="ff-field">
                                <label for="quote_discount" class="ff-label">Discount</label>
                                <input id="quote_discount" type="number" step="0.01" min="0" name="quote_discount" value="{{ $quoteDiscount }}" class="ff-input" required>
                            </div>
                            <div class="ff-field">
                                <label for="diagnosis_fee" class="ff-label">Diagnosis fee (if declined)</label>
                                <input id="diagnosis_fee" type="number" step="0.01" min="0" name="diagnosis_fee" value="{{ $diagnosisFee }}" class="ff-input" required>
                            </div>
                        </div>
                        <div class="ff-field">
                            <label for="quote_notes" class="ff-label">Quote notes (optional)</label>
                            <textarea id="quote_notes" name="quote_notes" rows="2" class="ff-input" placeholder="What the repair includes...">{{ old('quote_notes', $repairRequest->quote_notes) }}</textarea>
                        </div>
                        @error('quote')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="ff-btn-primary w-full" @disabled(! filled($repairRequest->diagnosis_notes))>
                            {{ $repairRequest->status === \App\Models\RepairRequest::STATUS_QUOTED ? 'Update & resend quote' : 'Send quote to customer' }}
                        </button>
                    </form>
                @elseif ($repairRequest->hasQuote())
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">Service</dt><dd class="font-medium text-slate-900">${{ number_format((float) $repairRequest->quote_service_charge, 2) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Parts</dt><dd class="font-medium text-slate-900">${{ number_format((float) $repairRequest->quote_parts_cost, 2) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Discount</dt><dd class="font-medium text-slate-900">${{ number_format((float) $repairRequest->quote_discount, 2) }}</dd></div>
                        <div class="flex justify-between border-t border-slate-100 pt-2"><dt class="font-semibold text-slate-800">Quoted total</dt><dd class="font-semibold text-slate-900">${{ number_format($repairRequest->quoteTotal(), 2) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Diagnosis fee if declined</dt><dd class="font-medium text-slate-900">${{ number_format((float) ($repairRequest->diagnosis_fee ?? 0), 2) }}</dd></div>
                    </dl>
                    @if ($repairRequest->quote_notes)
                        <p class="mt-3 text-sm text-slate-600">{{ $repairRequest->quote_notes }}</p>
                    @endif

                    @if ($authUser->isCustomer() && $repairRequest->canRespondToQuote())
                        <div class="mt-4 space-y-3 rounded-xl bg-slate-50 p-4 ring-1 ring-slate-200">
                            <p class="text-sm text-slate-700">
                                Approve to continue the repair at the quoted price, or decline and pay only the diagnosis fee.
                            </p>
                            @error('quote')
                                <p class="text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                            <form method="POST" action="{{ route('repair-requests.quote.approve', $repairRequest) }}">
                                @csrf
                                <button type="submit" class="ff-btn-primary w-full">Approve quote &amp; continue repair</button>
                            </form>
                            <form method="POST" action="{{ route('repair-requests.quote.decline', $repairRequest) }}" onsubmit="return confirm('Decline this repair? You will only be charged the diagnosis fee.');">
                                @csrf
                                <button type="submit" class="w-full rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-rose-700 ring-1 ring-rose-200 hover:bg-rose-50">
                                    Decline repair (pay diagnosis fee only)
                                </button>
                            </form>
                        </div>
                    @elseif ($repairRequest->status === \App\Models\RepairRequest::STATUS_QUOTED)
                        <p class="mt-3 text-sm text-fuchsia-700">Waiting for the customer to approve or decline this quote.</p>
                    @elseif ($repairRequest->status === \App\Models\RepairRequest::STATUS_DECLINED)
                        <p class="mt-3 text-sm text-rose-700">Customer declined the repair. Diagnosis fee invoice applies.</p>
                    @elseif ($repairRequest->status === \App\Models\RepairRequest::STATUS_REPAIRING)
                        <p class="mt-3 text-sm text-emerald-700">Customer approved this quote. Repair is in progress.</p>
                    @elseif ($repairRequest->status === \App\Models\RepairRequest::STATUS_COMPLETED)
                        <p class="mt-3 text-sm text-emerald-700">Repair finished using the approved quote amounts.</p>
                    @endif
                @else
                    <p class="text-sm text-slate-500">
                        @if ($authUser->isCustomer())
                            After diagnosis, you will receive a quote to approve before any repair work continues.
                        @else
                            Move the job to diagnosing, save notes, then send a quote.
                        @endif
                    </p>
                @endif
            </x-dashboard-card>

            <x-dashboard-card title="Invoice">
                @if ($repairRequest->invoice)
                    @if ($authUser->isCustomer() && $repairRequest->invoice->isDraft())
                        <p class="text-sm text-slate-600">
                            Your repair is complete. Our team is preparing your invoice — you will be able to pay shortly.
                        </p>
                        <p class="mt-2 text-xs text-slate-500">Return status: <x-status-badge :status="str_replace('_', ' ', $repairRequest->fulfillment_status ?? 'awaiting invoice')" /></p>
                    @else
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between"><dt class="text-slate-500">Invoice No</dt><dd class="font-medium text-slate-900">{{ $repairRequest->invoice->invoice_number }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Amount</dt><dd class="font-medium text-slate-900">${{ number_format($repairRequest->invoice->total, 2) }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd><x-status-badge :status="$repairRequest->invoice->payment_status" /></dd></div>
                        </dl>
                        <a href="{{ route('invoices.show', $repairRequest->invoice) }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            @if ($repairRequest->invoice->isPaid())
                                View Invoice
                            @elseif ($repairRequest->invoice->isPayable())
                                Pay Invoice
                            @else
                                Review Invoice
                            @endif
                        </a>
                    @endif
                @elseif ($repairRequest->status === \App\Models\RepairRequest::STATUS_COMPLETED)
                    @if ($authUser->isAdmin())
                        <p class="text-sm text-slate-500">No invoice is attached to this completed repair.</p>
                        <a href="{{ route('invoices.create', ['repair_request_id' => $repairRequest->id]) }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">Create Replacement Invoice</a>
                    @else
                        <p class="text-sm text-slate-500">Our billing team is preparing your invoice.</p>
                    @endif
                @elseif ($authUser->isAdmin())
                    <p class="text-sm text-slate-500">No invoice generated yet.</p>
                    <a href="{{ route('invoices.create', ['repair_request_id' => $repairRequest->id]) }}" class="mt-3 inline-block text-sm font-semibold text-indigo-600 hover:text-indigo-800">Create Invoice</a>
                @else
                    <p class="text-sm text-slate-500">No invoice generated yet.</p>
                @endif
            </x-dashboard-card>

            @if (in_array($repairRequest->status, [
                \App\Models\RepairRequest::STATUS_COMPLETED,
                \App\Models\RepairRequest::STATUS_DECLINED,
            ], true))
                <x-dashboard-card title="Device Return">
                    @if ($repairRequest->fulfillment_status)
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Return status</dt>
                                <dd><x-status-badge :status="str_replace('_', ' ', $repairRequest->fulfillment_status)" /></dd>
                            </div>
                            @if ($repairRequest->fulfillment_method)
                                <div class="flex justify-between gap-3">
                                    <dt class="text-slate-500">Method</dt>
                                    <dd class="font-medium text-slate-900">{{ $repairRequest->fulfillment_method === 'delivery' ? 'Home delivery' : 'Service center pickup' }}</dd>
                                </div>
                            @endif
                            @if ($repairRequest->delivery_address)
                                <div>
                                    <dt class="text-slate-500">Delivery address</dt>
                                    <dd class="mt-1 font-medium text-slate-900">{{ $repairRequest->delivery_address }}</dd>
                                </div>
                            @endif
                        </dl>
                    @endif

                    @if ($repairRequest->fulfillment_status === \App\Models\RepairRequest::FULFILLMENT_AWAITING_INVOICE)
                        <p class="mt-3 text-sm text-slate-600">
                            @if ($authUser->isAdmin())
                                Repair is complete. Review the <strong>draft invoice</strong>, adjust amounts if needed, then <strong>send to customer</strong>.
                            @else
                                Repair is complete. Your invoice is being prepared by our billing team.
                            @endif
                        </p>
                    @endif

                    @if ($repairRequest->fulfillment_status === \App\Models\RepairRequest::FULFILLMENT_AWAITING_PAYMENT)
                        <p class="mt-3 text-sm text-slate-600">
                            @if ($isDeclined)
                                Quote was declined. Pay the <strong>diagnosis fee</strong>, then choose pickup or delivery for your unrepaired device.
                            @else
                                Repair is complete. Pay the invoice to choose <strong>home delivery</strong> or <strong>pickup at the service center</strong>.
                            @endif
                        </p>
                    @endif

                    @if ($authUser->isCustomer() && $repairRequest->canChooseFulfillment())
                        <form method="POST" action="{{ route('repair-requests.fulfillment', $repairRequest) }}" class="mt-4 space-y-4">
                            @csrf
                            <p class="text-sm font-medium text-slate-800">How would you like to receive your device?</p>
                            <div class="space-y-2">
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-3 hover:border-indigo-300">
                                    <input type="radio" name="fulfillment_method" value="pickup" class="mt-1" @checked(old('fulfillment_method') === 'pickup') required>
                                    <span>
                                        <span class="block text-sm font-semibold text-slate-900">Pickup at service center</span>
                                        <span class="block text-xs text-slate-500">123 Repair Lane, Tech City — Mon–Sat, 9 AM–6 PM</span>
                                    </span>
                                </label>
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-3 hover:border-indigo-300">
                                    <input type="radio" name="fulfillment_method" value="delivery" class="mt-1" @checked(old('fulfillment_method') === 'delivery')>
                                    <span>
                                        <span class="block text-sm font-semibold text-slate-900">Home delivery</span>
                                        <span class="block text-xs text-slate-500">We deliver your repaired device to your address</span>
                                    </span>
                                </label>
                            </div>
                            <div class="ff-field">
                                <label for="delivery_address" class="ff-label">Delivery address</label>
                                <textarea id="delivery_address" name="delivery_address" rows="3" class="ff-input" placeholder="Street, area, city, postal code">{{ old('delivery_address') }}</textarea>
                                <p class="mt-1 text-xs text-slate-500">Required only if you choose home delivery.</p>
                            </div>
                            @error('fulfillment')
                                <p class="text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                            @error('delivery_address')
                                <p class="text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="ff-btn-primary w-full">Confirm return option</button>
                        </form>
                    @elseif ($repairRequest->fulfillment_status === \App\Models\RepairRequest::FULFILLMENT_READY_FOR_PICKUP)
                        <p class="mt-3 text-sm text-emerald-700">
                            Your device is ready for pickup at <strong>FixFlow Service Center, 123 Repair Lane, Tech City</strong>.
                        </p>
                    @elseif ($repairRequest->fulfillment_status === \App\Models\RepairRequest::FULFILLMENT_OUT_FOR_DELIVERY)
                        <p class="mt-3 text-sm text-indigo-700">
                            Your device is out for delivery. Our team will contact you when it arrives.
                        </p>
                    @elseif ($repairRequest->fulfillment_status === \App\Models\RepairRequest::FULFILLMENT_FULFILLED)
                        <p class="mt-3 text-sm text-emerald-700">
                            {{ $repairRequest->fulfillment_method === 'delivery'
                                ? 'Your device has been delivered successfully.'
                                : 'Your device has been collected from the service center.' }}
                        </p>
                    @endif

                    @if ($authUser->isAdmin() && $repairRequest->canCompleteFulfillment())
                        <form method="POST" action="{{ route('repair-requests.fulfillment.complete', $repairRequest) }}" class="mt-4">
                            @csrf
                            <button type="submit" class="ff-btn-primary w-full">
                                Mark as {{ $repairRequest->fulfillment_method === 'delivery' ? 'Delivered' : 'Picked Up' }}
                            </button>
                        </form>
                    @endif
                </x-dashboard-card>
            @endif

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
