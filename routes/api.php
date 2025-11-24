<?php

use App\Http\Controllers\Api\AveroInvoiceController;
use App\Http\Controllers\Api\AppointmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('invoices/from-avero', AveroInvoiceController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('appointments/availability', [AppointmentController::class, 'availability'])->name('api.appointments.availability');
    Route::post('appointments', [AppointmentController::class, 'store'])->name('api.appointments.store');
});
