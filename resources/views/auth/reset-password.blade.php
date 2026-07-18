<x-guest-layout>
    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Set a new password</h2>
    <p class="mt-2 text-sm text-slate-500">
        Choose a strong password for your FixFlow account.
    </p>

    <form class="mt-8 space-y-5" action="{{ route('password.update') }}" method="POST">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

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
            <label for="email" class="ff-label">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required autocomplete="username" class="ff-input">
        </div>

        <x-password-input id="password" name="password" label="New password" autocomplete="new-password" />

        <x-password-input id="password_confirmation" name="password_confirmation" label="Confirm new password" autocomplete="new-password" />

        <button type="submit" class="ff-btn-primary w-full">Reset password</button>
    </form>
</x-guest-layout>
