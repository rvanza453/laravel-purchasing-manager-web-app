<?php

use Illuminate\Support\Facades\Route;
use Modules\SystemSupport\Http\Controllers\TicketController;

Route::middleware(['web', 'auth'])->prefix('system-support')->name('systemsupport.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('systemsupport.tickets.index');
    })->name('dashboard');

    Route::resource('tickets', TicketController::class);

    // Announcements
    Route::resource('announcements', \Modules\SystemSupport\Http\Controllers\AnnouncementController::class)->only(['index', 'store', 'destroy']);
    Route::post('announcements/{id}/toggle', [\Modules\SystemSupport\Http\Controllers\AnnouncementController::class, 'toggleActive'])->name('announcements.toggle');
});
