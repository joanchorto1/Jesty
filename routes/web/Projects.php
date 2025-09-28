<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectPhaseController;
use App\Http\Controllers\ProjectResponsibleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['route.features.access:4', 'auth'])->group(function () {
    Route::resource('projects', ProjectController::class);

    Route::post('projects/{project}/phases', [ProjectPhaseController::class, 'store'])->name('projects.phases.store');
    Route::put('projects/{project}/phases/{phase}', [ProjectPhaseController::class, 'update'])->name('projects.phases.update');
    Route::delete('projects/{project}/phases/{phase}', [ProjectPhaseController::class, 'destroy'])->name('projects.phases.destroy');

    Route::post('projects/{project}/responsibles', [ProjectResponsibleController::class, 'store'])->name('projects.responsibles.store');
    Route::put('projects/{project}/responsibles/{responsible}', [ProjectResponsibleController::class, 'update'])->name('projects.responsibles.update');
    Route::delete('projects/{project}/responsibles/{responsible}', [ProjectResponsibleController::class, 'destroy'])->name('projects.responsibles.destroy');
});
