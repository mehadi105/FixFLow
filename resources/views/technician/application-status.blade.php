<x-app-layout :role="'technician'">
    <x-page-header
        title="Technician application"
        description="Your application is being reviewed by the FixFlow admin team"
    />

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="mx-auto max-w-2xl">
        <x-dashboard-card>
            @if ($application->isPending())
                <div class="text-center">
                    <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800 ring-1 ring-amber-500/20">Pending review</span>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">Thanks, {{ auth()->user()->name }}</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Your application is with our admin team. You will get access to assigned jobs and the technician dashboard once approved.
                    </p>
                </div>
            @elseif ($application->isRejected())
                <div class="text-center">
                    <span class="inline-flex rounded-full bg-rose-50 px-3 py-1 text-sm font-semibold text-rose-800 ring-1 ring-rose-500/20">Not approved</span>
                    <h3 class="mt-4 text-lg font-semibold text-slate-900">Application declined</h3>
                    <p class="mt-2 text-sm text-slate-600">Unfortunately your application was not approved at this time.</p>
                    @if ($application->admin_notes)
                        <div class="mt-4 rounded-xl bg-slate-50 px-4 py-3 text-left text-sm text-slate-700">
                            <p class="font-semibold text-slate-900">Admin note</p>
                            <p class="mt-1">{{ $application->admin_notes }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <dl class="ff-dl mt-8 border-t border-slate-100 pt-6">
                <div><dt>Email</dt><dd>{{ $application->applicant->email }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $application->phone }}</dd></div>
                <div><dt>Experience</dt><dd>{{ $application->years_experience }} year(s)</dd></div>
                <div><dt>Specialties</dt><dd>{{ $application->specialties }}</dd></div>
                <div><dt>Certification</dt><dd>{{ $application->certification ?? '—' }}</dd></div>
                <div class="ff-dl-wide"><dt>Motivation</dt><dd class="font-normal text-slate-700">{{ $application->motivation }}</dd></div>
                <div class="ff-dl-wide">
                    <dt>Supporting document</dt>
                    <dd class="font-normal text-slate-700">
                        @if ($application->hasDocument())
                            <a href="{{ asset('storage/'.$application->document_path) }}" target="_blank" rel="noopener" class="font-semibold text-indigo-600 hover:text-indigo-800">
                                {{ $application->documentLabel() }}
                            </a>
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div><dt>Submitted</dt><dd>{{ $application->created_at->format('M d, Y g:i A') }}</dd></div>
            </dl>
        </x-dashboard-card>
    </div>
</x-app-layout>
