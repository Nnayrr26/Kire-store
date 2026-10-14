<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JokiOrderController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\WorkerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1')->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/topup/mobile-legends', [TopUpController::class, 'create'])
    ->middleware(['auth', 'role:user'])
    ->name('topup.mobile-legends');

Route::post('/topup/mobile-legends', [TopUpController::class, 'store'])
    ->middleware(['auth', 'role:user', 'throttle:10,1'])
    ->name('topup.mobile-legends.store');

Route::get('/joki/mobile-legends', [JokiOrderController::class, 'create'])
    ->middleware(['auth', 'role:user'])
    ->name('joki.mobile-legends');

Route::post('/joki/mobile-legends', [JokiOrderController::class, 'store'])
    ->middleware(['auth', 'role:user', 'throttle:10,1'])
    ->name('joki.mobile-legends.store');

Route::get('/dashboard', function (Request $request) {
    return match ($request->user()->role) {
        'admin' => redirect()->route('dashboard.admin'),
        'worker' => redirect()->route('dashboard.worker'),
        'user' => redirect()->route('dashboard.user'),
        default => abort(403),
    };
})->middleware('auth')->name('dashboard');

Route::get('/dashboard/user', [DashboardController::class, 'user'])
    ->middleware(['auth', 'role:user'])
    ->name('dashboard.user');

Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
    ->middleware(['auth', 'role:admin'])
    ->name('dashboard.admin');

Route::get('/dashboard/worker', [DashboardController::class, 'worker'])
    ->middleware(['auth', 'role:worker'])
    ->name('dashboard.worker');

Route::post('/dashboard/admin/workers', [WorkerController::class, 'store'])
    ->middleware(['auth', 'role:admin'])
    ->name('dashboard.admin.workers.store');

Route::patch('/dashboard/admin/joki-orders/{order}/assign', [WorkerController::class, 'assign'])
    ->middleware(['auth', 'role:admin'])
    ->name('dashboard.admin.joki-orders.assign');

Route::patch('/dashboard/worker/joki-orders/{order}', [WorkerController::class, 'updateTask'])
    ->middleware(['auth', 'role:worker'])
    ->name('dashboard.worker.joki-orders.update');

Route::patch('/dashboard/admin/orders/{order}', [TopUpController::class, 'updateStatus'])
    ->middleware(['auth', 'role:admin'])
    ->name('dashboard.admin.orders.update');

Route::patch('/dashboard/admin/joki-orders/{order}', [JokiOrderController::class, 'update'])
    ->middleware(['auth', 'role:admin'])
    ->name('dashboard.admin.joki-orders.update');
