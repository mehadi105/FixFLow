<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard.customer'));
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    });

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard.customer');
    });
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (static Blade previews)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::redirect('/dashboard', '/dashboard/customer');

    Route::view('/dashboard/customer', 'dashboard.customer', ['role' => 'customer'])
        ->name('dashboard.customer');

    Route::view('/dashboard/admin', 'dashboard.admin', ['role' => 'admin'])
        ->name('dashboard.admin');

    Route::view('/dashboard/technician', 'dashboard.technician', ['role' => 'technician'])
        ->name('dashboard.technician');

    Route::view('/repair-requests', 'repair-requests.index', ['role' => 'customer'])
        ->name('repair-requests.index');

    Route::view('/repair-requests/create', 'repair-requests.create', ['role' => 'customer'])
        ->name('repair-requests.create');

    Route::get('/repair-requests/{id}', function (string $id) {
        return view('repair-requests.show', [
            'role' => 'customer',
            'id' => $id,
        ]);
    })->name('repair-requests.show');

    Route::view('/invoices', 'invoices.index', ['role' => 'customer'])
        ->name('invoices.index');

    Route::get('/invoices/{id}', function (string $id) {
        return view('invoices.show', [
            'role' => 'customer',
            'id' => $id,
        ]);
    })->name('invoices.show');

    Route::view('/warranties', 'warranties.index', ['role' => 'customer'])
        ->name('warranties.index');

    Route::view('/reports', 'reports.index', ['role' => 'admin'])
        ->name('reports.index');
});
