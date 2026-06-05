<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('pages.home'));

// Admin routes — protected by basic auth middleware for MVP simplicity
// In production, swap with Spatie permissions or Laravel Sanctum
Route::middleware(['auth.basic'])->prefix('admin')->group(function () {
    Route::get('/', \App\Livewire\Admin\BookingDashboard::class)->name('admin.dashboard');
});
