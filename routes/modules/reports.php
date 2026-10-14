<?php

use App\Livewire\Reports\ReportsIndex;
use App\Http\Controllers\Pdf\ReportPdfController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'section:reportes'])->group(function () {
    Route::get('/reportes', ReportsIndex::class)->name('reports.index');
    Route::get('/reportes/pdf/casos', [ReportPdfController::class, 'casesList'])->name('reports.pdf.cases');
    Route::get('/reportes/pdf/estadisticas', [ReportPdfController::class, 'statistics'])->name('reports.pdf.statistics');
    Route::get('/reportes/pdf/alumno/{student}', [ReportPdfController::class, 'studentFile'])->name('reports.pdf.student');
});
