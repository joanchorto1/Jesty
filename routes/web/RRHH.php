<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PerformanceReviewController;



Route::middleware(['route.features.access:8'])->group(function () {

    Route::resource('payrolls', PayrollController::class)
        ->names([
            'index' => 'payrolls.index',
            'create' => 'payrolls.create',
            'store' => 'payrolls.store',
            'show' => 'payrolls.show',
            'edit' => 'payrolls.edit',
            'update' => 'payrolls.update',
            'destroy' => 'payrolls.destroy',
        ]);
    Route::resource('leaves', LeaveController::class)
        ->names([
            'index' => 'leaves.index',
            'create' => 'leaves.create',
            'store' => 'leaves.store',
            'show' => 'leaves.show',
            'edit' => 'leaves.edit',
            'update' => 'leaves.update',
            'destroy' => 'leaves.destroy',
        ]);
    Route::resource('trainings', TrainingController::class)
        ->names([
            'index' => 'trainings.index',
            'create' => 'trainings.create',
            'store' => 'trainings.store',
            'show' => 'trainings.show',
            'edit' => 'trainings.edit',
            'update' => 'trainings.update',
            'destroy' => 'trainings.destroy',
        ]);
    Route::resource('employees', EmployeeController::class)
        ->names([
            'index' => 'employees.index',
            'create' => 'employees.create',
            'store' => 'employees.store',
            'show' => 'employees.show',
            'edit' => 'employees.edit',
            'update' => 'employees.update',
            'destroy' => 'employees.destroy',
        ]);
    Route::resource('departments', DepartmentController::class)
        ->names([
            'index' => 'departments.index',
            'create' => 'departments.create',
            'store' => 'departments.store',
            'show' => 'departments.show',
            'edit' => 'departments.edit',
            'update' => 'departments.update',
            'destroy' => 'departments.destroy',
        ]);

    Route::resource('attendances', AttendanceController::class)
        ->names([
            'index' => 'attendances.index',
            'create' => 'attendances.create',
            'store' => 'attendances.store',
            'show' => 'attendances.show',
            'edit' => 'attendances.edit',
            'update' => 'attendances.update',
            'destroy' => 'attendances.destroy',
        ]);
    Route::resource('performance-reviews', PerformanceReviewController::class)
        ->names([
            'index' => 'performance-reviews.index',
            'create' => 'performance-reviews.create',
            'store' => 'performance-reviews.store',
            'show' => 'performance-reviews.show',
            'edit' => 'performance-reviews.edit',
            'update' => 'performance-reviews.update',
            'destroy' => 'performance-reviews.destroy',
        ]);



});
