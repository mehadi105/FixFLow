<x-guest-layout>
    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Apply as a technician</h2>
    <p class="mt-2 text-sm text-slate-500">
        Submit your experience for admin review. You can sign in after applying, but job access starts once approved.
        <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Already applied? Sign in</a>
    </p>

    <form class="mt-8 space-y-5" action="{{ route('technician.apply.store') }}" method="POST">
        @csrf

        @if ($errors->any())
            <div class="rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
                <ul class="list-disc space-y-1 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="ff-field">
            <label for="name" class="ff-label">Full name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" class="ff-input">
        </div>

        <div class="ff-field">
            <label for="email" class="ff-label">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="ff-input">
        </div>

        <div class="ff-field">
            <label for="phone" class="ff-label">Phone number</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required class="ff-input" placeholder="+880 1XXX XXXXXX">
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="ff-field">
                <label for="years_experience" class="ff-label">Years of experience</label>
                <input type="number" id="years_experience" name="years_experience" min="0" max="40" value="{{ old('years_experience', '1') }}" required class="ff-input">
            </div>
            <div class="ff-field">
                <label for="certification" class="ff-label">Certification (optional)</label>
                <input type="text" id="certification" name="certification" value="{{ old('certification') }}" class="ff-input" placeholder="e.g. CompTIA A+">
            </div>
        </div>

        <div class="ff-field">
            <label for="specialties" class="ff-label">Specialties</label>
            <input type="text" id="specialties" name="specialties" value="{{ old('specialties') }}" required class="ff-input" placeholder="Smartphones, laptops, soldering, board repair">
        </div>

        <div class="ff-field">
            <label for="motivation" class="ff-label">Why do you want to join FixFlow?</label>
            <textarea id="motivation" name="motivation" rows="4" required class="ff-input" placeholder="Tell us about your repair background and why you are a good fit...">{{ old('motivation') }}</textarea>
        </div>

        <x-password-input id="password" name="password" label="Password" autocomplete="new-password" />

        <x-password-input id="password_confirmation" name="password_confirmation" label="Confirm password" autocomplete="new-password" />

        <button type="submit" class="ff-btn-primary w-full">Submit application</button>

        <p class="text-center text-sm text-slate-500">
            Need a repair instead?
            <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Create a customer account</a>
        </p>
    </form>
</x-guest-layout>
