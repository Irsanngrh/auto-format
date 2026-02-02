<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('reports.index');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/create', [ReportController::class, 'create'])->name('create');
    Route::post('/', [ReportController::class, 'store'])->name('store');
    
    Route::post('/bulk-action', [ReportController::class, 'bulkAction'])->name('bulk_action');
    Route::delete('/{id}', [ReportController::class, 'destroy'])->name('destroy');

    Route::get('/{year}/{month}/{slug}', [ReportController::class, 'show'])->name('show');
    Route::get('/{year}/{month}/{slug}/pdf', [ReportController::class, 'exportPdf'])->name('pdf');
    Route::get('/{year}/{month}/{slug}/excel', [ReportController::class, 'exportExcel'])->name('excel');

    Route::post('/{id}/transaction', [ReportController::class, 'storeTransaction'])->name('transaction.store');
    Route::put('/transaction/{id}', [ReportController::class, 'updateTransaction'])->name('transaction.update');
    Route::delete('/transaction/{id}', [ReportController::class, 'destroyTransaction'])->name('transaction.destroy');
});