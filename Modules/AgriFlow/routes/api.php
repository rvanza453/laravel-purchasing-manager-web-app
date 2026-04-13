<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('agriflow/ping', function () {
        return response()->json([
            'module' => 'AgriFlow',
            'status' => 'ok',
        ]);
    })->name('agriflow.ping');
});
