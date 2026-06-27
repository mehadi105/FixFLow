<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Controllers\WarrantyController;
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

            return redirect()->intended(route($request->user()->dashboardRoute()));
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
            'role' => ['required', 'in:customer,technician'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->dashboardRoute());
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
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'redirect']);

    Route::get('/dashboard/customer', [DashboardController::class, 'customer'])
        ->middleware('role:customer')
        ->name('dashboard.customer');

    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('role:admin')
        ->name('dashboard.admin');

    Route::get('/dashboard/technician', [DashboardController::class, 'technician'])
        ->middleware('role:technician')
        ->name('dashboard.technician');

    /*
    | Repair Requests (Module 2)
    */
    Route::get('/repair-requests', [RepairRequestController::class, 'index'])
        ->name('repair-requests.index');

    Route::get('/repair-requests/create', [RepairRequestController::class, 'create'])
        ->middleware('role:customer')
        ->name('repair-requests.create');

    Route::post('/repair-requests', [RepairRequestController::class, 'store'])
        ->middleware('role:customer')
        ->name('repair-requests.store');

    Route::get('/repair-requests/{repairRequest}', [RepairRequestController::class, 'show'])
        ->name('repair-requests.show');

    // Technician workflow (Module 3)
    Route::post('/repair-requests/{repairRequest}/assign', [RepairRequestController::class, 'assignTechnician'])
        ->middleware('role:admin')
        ->name('repair-requests.assign');

    Route::post('/repair-requests/{repairRequest}/status', [RepairRequestController::class, 'updateStatus'])
        ->middleware('role:admin,technician')
        ->name('repair-requests.status');

    Route::post('/repair-requests/{repairRequest}/diagnosis', [RepairRequestController::class, 'updateDiagnosis'])
        ->middleware('role:admin,technician')
        ->name('repair-requests.diagnosis');

    /*
    | Invoices (Module 4)
    */
    Route::get('/invoices', [InvoiceController::class, 'index'])
        ->name('invoices.index');

    Route::get('/invoices/create', [InvoiceController::class, 'create'])
        ->middleware('role:admin')
        ->name('invoices.create');

    Route::post('/invoices', [InvoiceController::class, 'store'])
        ->middleware('role:admin')
        ->name('invoices.store');

    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])
        ->name('invoices.show');

    Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])
        ->middleware('role:admin')
        ->name('invoices.mark-paid');

    /*
    | Warranties (Module 5)
    */
    Route::get('/warranties', [WarrantyController::class, 'index'])
        ->name('warranties.index');

    Route::get('/warranties/create', [WarrantyController::class, 'create'])
        ->middleware('role:admin')
        ->name('warranties.create');

    Route::post('/warranties', [WarrantyController::class, 'store'])
        ->middleware('role:admin')
        ->name('warranties.store');

    /*
    | Static previews (to be implemented in later modules)
    */
    Route::get('/reports', function () {
        return view('reports.index', ['role' => 'admin']);
    })->middleware('role:admin')->name('reports.index');
});
