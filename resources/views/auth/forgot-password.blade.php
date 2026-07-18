<x-guest-layout>
    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Forgot your password?</h2>
    <p class="mt-2 text-sm text-slate-500">
        Enter your email and we will send you a reset link.
        <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Back to sign in</a>
    </p>

    <form class="mt-8 space-y-5" action="{{ route('password.email') }}" method="POST">
        @csrf

        @if (session('status'))
            <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="ff-field">
            <label for="email" class="ff-label">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="ff-input">
        </div>

        <button type="submit" class="ff-btn-primary w-full">Email reset link</button>
    </form>
</x-guest-layout>
