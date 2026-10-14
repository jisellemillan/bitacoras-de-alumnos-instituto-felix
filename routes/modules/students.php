<?php

use App\Livewire\Students\StudentsIndex;
use App\Livewire\StudentFiles\StudentFilePage;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'section:alumnos,configuracion'])->group(function () {
    Route::get('/alumnos', StudentsIndex::class)->name('students.index');
    Route::get('/alumnos/{student}', StudentFilePage::class)->name('students.show');
});
