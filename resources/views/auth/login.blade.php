<x-guest-layout>
    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Welcome back</h2>
    <p class="mt-2 text-sm text-slate-500">
        New to FixFlow?
        <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Create an account</a>
    </p>

    <form class="mt-8 space-y-5" action="{{ route('login') }}" method="POST">
        @csrf

        @if ($errors->any())
            <div class="rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="ff-field">
            <label for="email" class="ff-label">Email address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="ff-input">
        </div>

        <div class="ff-field">
            <label for="password" class="ff-label">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password" class="ff-input">
        </div>

        <div class="flex items-center">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/40">
                <span class="text-sm text-slate-600">Remember me</span>
            </label>
        </div>

        <button type="submit" class="ff-btn-primary w-full">Sign in</button>
    </form>
</x-guest-layout>
