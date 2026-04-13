<?php

use Illuminate\Support\Facades\Route;
use Modules\AgriFlow\Http\Controllers\AgriFlowController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('agriflow')->name('agriflow.')->group(function () {
        Route::get('/', [AgriFlowController::class, 'index'])->name('index');
        Route::get('/panen', [AgriFlowController::class, 'panen'])->name('panen');
        Route::get('/pengiriman', [AgriFlowController::class, 'pengiriman'])->name('pengiriman');
        Route::get('/monitoring-restan', [AgriFlowController::class, 'monitoringRestan'])->name('monitoring-restan');
    });
});
