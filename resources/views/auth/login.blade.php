<x-guest-layout>
    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Welcome back</h2>
    <p class="mt-2 text-sm text-slate-500">
        New to FixFlow?
        <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Create an account</a>
    </p>

    <form class="mt-8 space-y-5" action="{{ url('/dashboard/customer') }}" method="GET">
        <div>
            <label for="email" class="ff-label">Email address</label>
            <input type="email" id="email" name="email" value="john@example.com" required autocomplete="username" class="ff-input mt-1.5">
        </div>

        <div>
            <label for="password" class="ff-label">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password" class="ff-input mt-1.5">
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/40">
                <span class="text-sm text-slate-600">Remember me</span>
            </label>
            <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Forgot password?</a>
        </div>

        <button type="submit" class="ff-btn-primary w-full">Sign in</button>
    </form>

    <p class="mt-6 text-center text-xs text-slate-400">Preview mode — install Breeze for live authentication.</p>
</x-guest-layout>
