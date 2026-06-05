<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────────────────
Route::get('/', fn () => view('pages.home'))->name('home');
Route::get('/book', \App\Livewire\BookingWizard::class)->name('book');

// ── Auth ──────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    \App\Livewire\Auth\Login::class)->name('login');
    Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// ── Customer ──────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Customer\BookingHistory::class)->name('customer.dashboard');
});

// ── Admin ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', \App\Http\Middleware\EnsureAdmin::class])
    ->prefix('admin')
    ->group(function () {
        Route::get('/', \App\Livewire\Admin\BookingDashboard::class)->name('admin.dashboard');
    });
