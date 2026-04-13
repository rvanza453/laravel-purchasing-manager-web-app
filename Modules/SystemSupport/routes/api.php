<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemSupport\Http\Controllers\SystemSupportController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('systemsupports', SystemSupportController::class)->names('systemsupport');
});
