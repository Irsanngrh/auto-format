<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DirectorController; 

Route::get('/', function () {
    return redirect()->route('reports.index');
});

Route::resource('reports', ReportController::class)->except(['show']);

Route::get('/reports/view/{slug}/{month}/{year}/{card_last_digits}', [ReportController::class, 'show'])
    ->name('reports.show')
    ->where([
        'slug' => '[a-z0-9\-]+',
        'month' => '[0-9]+',
        'year' => '[0-9]{4}',
        'card_last_digits' => '[0-9]{4}'
    ]);

Route::post('/reports/{id}/transactions', [ReportController::class, 'storeTransaction'])
    ->name('reports.transactions.store')
    ->whereNumber('id');

Route::put('/transactions/{id}', [ReportController::class, 'updateTransaction'])
    ->name('reports.transactions.update')
    ->whereNumber('id');

Route::delete('/transactions/{id}', [ReportController::class, 'destroyTransaction'])
    ->name('reports.transactions.destroy')
    ->whereNumber('id');

Route::get('/reports/{id}/export/pdf', [ReportController::class, 'exportPdf'])
    ->name('reports.export_pdf')
    ->whereNumber('id');

Route::get('/reports/{id}/export/excel', [ReportController::class, 'exportExcel'])
    ->name('reports.export_excel')
    ->whereNumber('id');

Route::get('/reports/{id}/preview', [ReportController::class, 'previewPdf'])
    ->name('reports.preview')
    ->whereNumber('id');

Route::resource('directors', DirectorController::class);