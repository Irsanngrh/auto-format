<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('reports.index');
});

Route::resource('directors', DirectorController::class);

Route::post('/reports/bulk-action', [ReportController::class, 'bulkAction'])->name('reports.bulkAction');
Route::get('/reports/{id}/pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
Route::get('/reports/{id}/excel', [ReportController::class, 'exportExcel'])->name('reports.exportExcel');

Route::post('/reports/{id}/transaction', [ReportController::class, 'storeTransaction'])->name('transactions.store');
Route::put('/transactions/{id}', [ReportController::class, 'updateTransaction'])->name('transactions.update');
Route::delete('/transactions/{id}', [ReportController::class, 'destroyTransaction'])->name('transactions.destroy');

Route::get('/report/{slug}/{month}/{year}', [ReportController::class, 'show'])->name('reports.show');

Route::resource('reports', ReportController::class)->except(['show']);