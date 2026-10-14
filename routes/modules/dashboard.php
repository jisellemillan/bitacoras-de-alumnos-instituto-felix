<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\DashboardPage;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', DashboardPage::class)->name('dashboard');
});
