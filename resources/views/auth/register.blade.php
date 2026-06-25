<x-guest-layout>
    <h2 class="text-2xl font-bold tracking-tight text-slate-900">Create your account</h2>
    <p class="mt-2 text-sm text-slate-500">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500">Sign in</a>
    </p>

    <form class="mt-8 space-y-5" action="{{ route('register') }}" method="POST">
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
            <label for="role" class="ff-label">I am a</label>
            <select id="role" name="role" required class="ff-input">
                <option value="customer" @selected(old('role', 'customer') === 'customer')>Customer — I want to get a device repaired</option>
                <option value="technician" @selected(old('role') === 'technician')>Technician — I repair devices</option>
            </select>
        </div>

        <div class="ff-field">
            <label for="password" class="ff-label">Password</label>
            <input type="password" id="password" name="password" required autocomplete="new-password" class="ff-input">
        </div>

        <div class="ff-field">
            <label for="password_confirmation" class="ff-label">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" class="ff-input">
        </div>

        <button type="submit" class="ff-btn-primary w-full">Create account</button>
    </form>
</x-guest-layout>
