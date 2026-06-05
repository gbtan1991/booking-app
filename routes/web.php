<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────────────────
Route::get('/', \App\Livewire\BookingWizard::class)->name('home');

// ── Auth ──────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// ── Admin ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', \App\Http\Middleware\EnsureAdmin::class])
    ->prefix('admin')
    ->group(function () {
        Route::get('/',         \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');
        Route::get('/users',    \App\Livewire\Admin\UserManager::class)->name('admin.users');
        Route::get('/services', \App\Livewire\Admin\ServiceManager::class)->name('admin.services');
    });
