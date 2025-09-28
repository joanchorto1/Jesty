<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PerformanceReviewController;
use App\Models\Project;
use Inertia\Inertia;


Route::middleware(['route.features.access:8'])->group(function () {


    Route::get('/dashboard/rrhh', function () {
        return Inertia::render('DashboardRRHH',[
            'employees' => \App\Models\Employee::where('company_id', Auth::user()->company_id)->with('department')->get(),
            'departments' => \App\Models\Department::where('company_id', Auth::user()->company_id)
                ->with('projects')
                ->get(),
            'projects' => Project::where('company_id', Auth::user()->company_id)
                ->with(['department', 'phases', 'responsibles.employee'])
                ->get(),
        ]);
    })->name('dashboard.rrhh');

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

    //payroll, send and print routes

    Route::get('payrolls/{payroll}/send', [PayrollController::class, 'send'])->name('payrolls.send');
    Route::get('payrolls/{payroll}/print', [PayrollController::class, 'print'])->name('payrolls.print');
    Route::resource('leaves', LeaveController::class)
        ->names([
            'index' => 'leaves.index',
            'create' => 'leaves.create',
            'store' => 'leaves.store',
            'show' => 'leaves.show',
            'edit'=>'leaves.edit',
            'update' => 'leaves.update',
            'destroy' => 'leaves.destroy',
        ]);

Route::get('/leaves/{leave}/edit2',[LeaveController::class,'edit2'])->name('leaves.edit2');
    Route::get('leaves/{leave}/goToEdit', [LeaveController::class, 'goToEdit'])->name('leaves.goToEdit');
    Route::put('leaves/{leave}/update2', [LeaveController::class, 'update2'])->name('leaves.update2');


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

    Route::get('performance-reviews/{review}/edit2', [PerformanceReviewController::class, 'edit2'])->name('performance-reviews.edit2');
    Route::put('performance-reviews/{review}/update2', [PerformanceReviewController::class, 'update2'])->name('performance-reviews.update2');



});
