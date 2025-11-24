<?php

use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['route.features.access:9'])->group(function () {
    Route::get('/agenda', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/agenda', [AppointmentController::class, 'store'])->name('appointments.store');
});
