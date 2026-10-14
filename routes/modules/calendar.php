<?php

use App\Livewire\Appointments\CalendarPage;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'section:calendario'])->group(function () {
    Route::get('/calendario', CalendarPage::class)->name('calendar.index');
});
