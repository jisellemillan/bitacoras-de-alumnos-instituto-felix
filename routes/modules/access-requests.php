<?php

use App\Livewire\AccessRequests\AccessRequestsIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'section:solicitudes_acceso'])->group(function () {
    Route::get('/solicitudes-acceso', AccessRequestsIndex::class)->name('access-requests.index');
});
