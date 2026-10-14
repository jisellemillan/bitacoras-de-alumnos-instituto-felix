<?php

use App\Livewire\Agreements\AgreementsIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'section:acuerdos'])->group(function () {
    Route::get('/acuerdos', AgreementsIndex::class)->name('agreements.index');
});
