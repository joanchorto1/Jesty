<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TiketController;


Route::middleware(['route.features.access:5'])->group(function(){


//Dashboard TPV
Route::get('/dashboard/tpv', [TiketController::class, 'dashboard'])->name('dashboard.tpv');

// Mostrar todos los tikets
Route::get('/tikets', [TiketController::class, 'index'])->name('tikets.index');

// Mostrar el formulario para crear un nuevo tiket
Route::get('/tikets/create', [TiketController::class, 'create'])->name('tikets.create');

// Almacenar un nuevo tiket con ítems
Route::post('/tikets', [TiketController::class, 'storeWithItems'])->name('tikets.store');

// Mostrar el formulario para editar un tiket existente
Route::get('/tikets/{tiket}/edit', [TiketController::class, 'edit'])->name('tikets.edit');
Route::get('/tikets/{tiket}/goToEdit', [TiketController::class, 'goToEdit'])->name('tikets.goToEdit');

// Actualizar un tiket existente
Route::put('/tikets/{tiket}', [TiketController::class, 'update'])->name('tikets.update');

// Eliminar un tiket existente
Route::delete('/tikets/{tiket}', [TiketController::class, 'destroy'])->name('tikets.destroy');

//Reuta para print con la funcion directamente en el ruter
Route::get('tikets/print', [TiketController::class, 'printNoID'])->name('tickets.printNoID');
Route::get('tikets/{tiket}/print', [TiketController::class, 'print'])->name('tikets.print');
Route::get('tikets/{tiket}/imprimir', [TiketController::class, 'imprimir'])->name('tikets.imprimir');
Route::get('tikets/report', [TiketController::class, 'reportProducts'])->name('tikets.productReport');
Route::post('tikets/call/store', [TiketController::class, 'callStore'])->name('tikets.callStore');
Route::get('tikets/call/create', [TiketController::class, 'goToCreate'])->name('tikets.goToCreate');


});
