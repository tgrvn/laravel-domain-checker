<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DomainCheckController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function ()
{
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

});

Route::middleware('auth')->group(function ()
{
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/domains', [DomainController::class, 'index'])->name('domains.index');

    Route::resource('domains', DomainController::class);

    Route::get('/domains/{domain}/checks', [DomainCheckController::class, 'index'])->name('domains.checks');
    Route::post('/domains/{domain}/checks', [DomainCheckController::class, 'store'])->name('domains.checks.store');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});