<x-app-layout :role="$role ?? 'admin'">
    <x-page-header :title="$application->applicant->name" description="Technician application review">
        <x-slot name="actions">
            <x-status-badge :status="$application->status" />
            <x-back-link :href="route('technician-applications.index')" label="Back to applications" />
        </x-slot>
    </x-page-header>

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="ff-grid-sidebar">
        <x-dashboard-card title="Application details" class="lg:col-span-2">
            <dl class="ff-dl">
                <div><dt>Full name</dt><dd>{{ $application->applicant->name }}</dd></div>
                <div><dt>Email</dt><dd>{{ $application->applicant->email }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $application->phone }}</dd></div>
                <div><dt>Years of experience</dt><dd>{{ $application->years_experience }}</dd></div>
                <div><dt>Specialties</dt><dd>{{ $application->specialties }}</dd></div>
                <div><dt>Certification</dt><dd>{{ $application->certification ?? '—' }}</dd></div>
                <div class="ff-dl-wide"><dt>Why FixFlow?</dt><dd class="font-normal text-slate-700">{{ $application->motivation }}</dd></div>
                <div><dt>Submitted</dt><dd>{{ $application->created_at->format('M d, Y g:i A') }}</dd></div>
                @if ($application->reviewed_at)
                    <div><dt>Reviewed</dt><dd>{{ $application->reviewed_at->format('M d, Y g:i A') }} by {{ $application->reviewer?->name ?? 'Admin' }}</dd></div>
                @endif
                @if ($application->admin_notes)
                    <div class="ff-dl-wide"><dt>Admin notes</dt><dd class="font-normal text-slate-700">{{ $application->admin_notes }}</dd></div>
                @endif
            </dl>
        </x-dashboard-card>

        @if ($application->isPending())
            <div class="ff-section space-y-6">
                <x-dashboard-card title="Approve application">
                    <form method="POST" action="{{ route('technician-applications.approve', $application) }}" class="space-y-4">
                        @csrf
                        <div class="ff-field">
                            <label for="approve_notes" class="ff-label">Internal note (optional)</label>
                            <textarea id="approve_notes" name="admin_notes" rows="3" class="ff-input" placeholder="Optional note for your records">{{ old('admin_notes') }}</textarea>
                        </div>
                        <button type="submit" class="ff-btn-primary w-full">Approve technician</button>
                    </form>
                </x-dashboard-card>

                <x-dashboard-card title="Reject application">
                    <form method="POST" action="{{ route('technician-applications.reject', $application) }}" class="space-y-4">
                        @csrf
                        <div class="ff-field">
                            <label for="reject_notes" class="ff-label">Reason for rejection</label>
                            <textarea id="reject_notes" name="admin_notes" rows="4" required class="ff-input" placeholder="Explain why the application was declined">{{ old('admin_notes') }}</textarea>
                        </div>
                        <button type="submit" class="ff-btn-secondary w-full !text-rose-700 ring-rose-200 hover:!bg-rose-50">Reject application</button>
                    </form>
                </x-dashboard-card>
            </div>
        @endif
    </div>
</x-app-layout>
