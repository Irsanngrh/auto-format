<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DirectorController; 

Route::get('/', function () {
    return redirect()->route('reports.index');
});

Route::resource('reports', ReportController::class)->except(['show']);

Route::get('/reports/view/{slug}/{month}/{year}/{card_last_digits}', [ReportController::class, 'show'])
    ->name('reports.show');

Route::post('/reports/{id}/transactions', [ReportController::class, 'storeTransaction'])
    ->name('reports.transactions.store');

Route::put('/transactions/{id}', [ReportController::class, 'updateTransaction'])
    ->name('reports.transactions.update');

Route::delete('/transactions/{id}', [ReportController::class, 'destroyTransaction'])
    ->name('reports.transactions.destroy');

Route::get('/reports/{id}/export/pdf', [ReportController::class, 'exportPdf'])
    ->name('reports.export_pdf');

Route::get('/reports/{id}/export/excel', [ReportController::class, 'exportExcel'])
    ->name('reports.export_excel');

Route::get('/reports/{id}/preview', [ReportController::class, 'previewPdf'])
    ->name('reports.preview');

Route::resource('directors', DirectorController::class);