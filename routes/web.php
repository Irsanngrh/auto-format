<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('reports.create');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/create', [ReportController::class, 'create'])->name('create');
    Route::post('/', [ReportController::class, 'store'])->name('store');
    Route::get('/{id}', [ReportController::class, 'show'])->name('show');
    Route::post('/{id}/transaction', [ReportController::class, 'storeTransaction'])->name('transaction.store');
    Route::delete('/transaction/{id}', [ReportController::class, 'destroyTransaction'])->name('transaction.destroy');
    Route::get('/{id}/pdf', [ReportController::class, 'exportPdf'])->name('pdf');
    Route::get('/{id}/excel', [ReportController::class, 'exportExcel'])->name('excel');
});