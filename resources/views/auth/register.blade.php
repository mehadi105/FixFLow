<x-guest-layout>
    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Create your account</h2>
    <p class="mt-2 text-sm text-slate-500">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Sign in</a>
    </p>

    <form class="mt-8 space-y-5" action="{{ url('/dashboard/customer') }}" method="GET">
        <div>
            <label for="name" class="ff-label">Full name</label>
            <input type="text" id="name" name="name" required autocomplete="name" class="ff-input mt-1.5">
        </div>

        <div>
            <label for="email" class="ff-label">Email address</label>
            <input type="email" id="email" name="email" required autocomplete="username" class="ff-input mt-1.5">
        </div>

        <div>
            <label for="password" class="ff-label">Password</label>
            <input type="password" id="password" name="password" required autocomplete="new-password" class="ff-input mt-1.5">
        </div>

        <div>
            <label for="password_confirmation" class="ff-label">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" class="ff-input mt-1.5">
        </div>

        <button type="submit" class="ff-btn-primary w-full">Create account</button>
    </form>

    <p class="mt-6 text-center text-xs text-slate-400">Preview mode — install Breeze for live registration.</p>
</x-guest-layout>
