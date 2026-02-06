<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('reports.index');
});

Route::resource('directors', DirectorController::class);

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
Route::get('/reports/{id}/edit', [ReportController::class, 'edit'])->name('reports.edit');
Route::put('/reports/{id}', [ReportController::class, 'update'])->name('reports.update');
Route::delete('/reports/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');

Route::get('/reports/{slug}/{month}/{year}/{card_last_digits}', [ReportController::class, 'show'])->name('reports.show');

Route::get('/reports/{id}/pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
Route::get('/reports/{id}/excel', [ReportController::class, 'exportExcel'])->name('reports.exportExcel');

Route::post('/reports/{id}/transactions', [ReportController::class, 'storeTransaction'])->name('transactions.store');
Route::put('/transactions/{id}', [ReportController::class, 'updateTransaction'])->name('transactions.update');
Route::delete('/transactions/{id}', [ReportController::class, 'destroyTransaction'])->name('transactions.destroy');