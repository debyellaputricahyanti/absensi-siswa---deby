<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        if (Auth::user()->role === 'admin') {
            return redirect()->intended('admin/dashboard');
        }

        return redirect()->intended('dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AttendanceController::class, 'index'])->name('dashboard');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::put('/attendance', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::post('/permission', [AttendanceController::class, 'storePermission'])->name('permission.store');
});

Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Permissions Management
    Route::get('/permissions', [AdminController::class, 'permissions'])->name('permissions');
    Route::post('/permissions/{id}/approve', [AdminController::class, 'approvePermission'])->name('permissions.approve');
    Route::post('/permissions/{id}/reject', [AdminController::class, 'rejectPermission'])->name('permissions.reject');
    Route::get('/permissions/{id}/evidence', [AdminController::class, 'showPermissionEvidence'])->name('permissions.evidence');

    Route::get('/attendance-report', [AdminController::class, 'attendanceReport'])->name('attendance.report');

    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');

    Route::get('/time-settings', [AdminController::class, 'timeSettings'])->name('settings.time');
    Route::post('/time-settings', [AdminController::class, 'updateTimeSettings'])->name('settings.time.update');

    // Classes Management
    Route::get('/classes', [AdminController::class, 'classes'])->name('classes');
    Route::post('/classes', [AdminController::class, 'storeClass'])->name('classes.store');
    Route::delete('/classes/{id}', [AdminController::class, 'destroyClass'])->name('classes.destroy');
});
