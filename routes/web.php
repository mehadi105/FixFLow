<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicePaymentController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Controllers\TechnicianApplicationController;
use App\Http\Controllers\UserController;
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
    })->middleware('throttle:login');

    Route::get('/forgot-password', [PasswordResetController::class, 'create'])
        ->name('password.request');

    Route::post('/forgot-password', [PasswordResetController::class, 'store'])
        ->middleware('throttle:password-reset')
        ->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])
        ->name('password.reset');

    Route::post('/reset-password', [PasswordResetController::class, 'update'])
        ->name('password.update');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::get('/technician/apply', [TechnicianApplicationController::class, 'create'])
        ->name('technician.apply');

    Route::post('/technician/apply', [TechnicianApplicationController::class, 'store'])
        ->middleware('throttle:registration')
        ->name('technician.apply.store');

    Route::post('/register', function (Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => User::ROLE_CUSTOMER,
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->dashboardRoute());
    })->middleware('throttle:registration');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::post('/stripe/webhook', [InvoicePaymentController::class, 'webhook'])
    ->name('stripe.webhook');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'redirect']);

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->middleware('throttle:ajax')
        ->name('notifications.index');

    Route::post('/preferences/table-density', [PreferenceController::class, 'updateTableDensity'])
        ->middleware('throttle:ajax')
        ->name('preferences.table-density');

    Route::get('/technician/application', [TechnicianApplicationController::class, 'status'])
        ->middleware('role:technician')
        ->name('technician.application.status');

    Route::middleware(['role:technician', 'approved.technician'])->group(function () {
        Route::get('/dashboard/technician', [DashboardController::class, 'technician'])
            ->name('dashboard.technician');
    });

    Route::get('/dashboard/customer', [DashboardController::class, 'customer'])
        ->middleware('role:customer')
        ->name('dashboard.customer');

    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('role:admin')
        ->name('dashboard.admin');

    /*
    | Technician applications (admin review)
    */
    Route::middleware('role:admin')->prefix('technician-applications')->group(function () {
        Route::get('/', [TechnicianApplicationController::class, 'index'])
            ->name('technician-applications.index');
        Route::get('/{technicianApplication}', [TechnicianApplicationController::class, 'show'])
            ->name('technician-applications.show');
        Route::post('/{technicianApplication}/approve', [TechnicianApplicationController::class, 'approve'])
            ->name('technician-applications.approve');
        Route::post('/{technicianApplication}/reject', [TechnicianApplicationController::class, 'reject'])
            ->name('technician-applications.reject');
    });

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
        ->middleware('role:admin,technician', 'approved.technician')
        ->name('repair-requests.status');

    Route::post('/repair-requests/{repairRequest}/diagnosis', [RepairRequestController::class, 'updateDiagnosis'])
        ->middleware('role:admin,technician', 'approved.technician')
        ->name('repair-requests.diagnosis');

    Route::post('/repair-requests/{repairRequest}/fulfillment', [RepairRequestController::class, 'chooseFulfillment'])
        ->middleware('role:customer')
        ->name('repair-requests.fulfillment');

    Route::post('/repair-requests/{repairRequest}/fulfillment/complete', [RepairRequestController::class, 'completeFulfillment'])
        ->middleware('role:admin')
        ->name('repair-requests.fulfillment.complete');

    /*
    | Messages inbox (Fiverr-style)
    */
    Route::get('/messages', [MessageController::class, 'inbox'])
        ->name('messages.index');

    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount'])
        ->middleware('throttle:ajax')
        ->name('messages.unread-count');

    Route::get('/messages/{repairRequest}', [MessageController::class, 'inbox'])
        ->name('messages.show');

    /*
    | Chat (Module C3)
    */
    Route::get('/repair-requests/{repairRequest}/messages', [MessageController::class, 'index'])
        ->middleware('throttle:ajax')
        ->name('repair-requests.messages.index');

    Route::post('/repair-requests/{repairRequest}/messages', [MessageController::class, 'store'])
        ->middleware('throttle:messages')
        ->name('repair-requests.messages.store');

    Route::post('/repair-requests/{repairRequest}/messages/read', [MessageController::class, 'markRead'])
        ->middleware('throttle:ajax')
        ->name('repair-requests.messages.read');

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

    Route::patch('/invoices/{invoice}', [InvoiceController::class, 'update'])
        ->middleware('role:admin')
        ->name('invoices.update');

    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('invoices.destroy');

    Route::post('/invoices/{invoice}/send', [InvoiceController::class, 'send'])
        ->middleware('role:admin')
        ->name('invoices.send');

    Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])
        ->middleware('role:admin')
        ->name('invoices.mark-paid');

    Route::post('/invoices/{invoice}/pay', [InvoicePaymentController::class, 'checkout'])
        ->middleware('role:customer')
        ->name('invoices.pay');

    Route::get('/invoices/{invoice}/payment/success', [InvoicePaymentController::class, 'success'])
        ->middleware('role:customer')
        ->name('invoices.payment.success');

    Route::get('/invoices/{invoice}/payment/cancel', [InvoicePaymentController::class, 'cancel'])
        ->middleware('role:customer')
        ->name('invoices.payment.cancel');

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
    | Reports (Module 6)
    */
    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('role:admin')
        ->name('reports.index');

    /*
    | User management (admin)
    */
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('role:admin')
        ->name('users.index');

    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])
        ->middleware('role:admin')
        ->name('users.update-role');
});
