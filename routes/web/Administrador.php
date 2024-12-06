<?php


use App\Http\Controllers\CompanyController;
use App\Models\Feature;
use App\Models\RoleFeature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware(['route.features.access:7'])->group(function () {
// Routes for Companies
    Route::resource('companies', CompanyController::class)
        ->names([
//            'index' => 'companies.index',
//            'create' => 'companies.create',
//            'store' => 'companies.store',
//            'show' => 'companies.show',
            'edit' => 'companies.edit',
            'update' => 'companies.update',
            'destroy' => 'companies.destroy',
        ]);

    Route::get('/company/changePlan/{company}', [CompanyController::class, 'changePlan'])->name('company.changePlan');
    Route::post('/company/updatePlan/{company}', [CompanyController::class, 'updatePlan'])->name('company.updatePlan');
    Route::resource('users', \App\Http\Controllers\UserController::class)
        ->names([
            'index' => 'users.index',
            'create' => 'users.create',
            'store' => 'users.store',
            'show' => 'users.show',
            'edit' => 'users.edit',
            'update' => 'users.update',
            'destroy' => 'users.destroy',
        ]);

    Route::resource('roles', \App\Http\Controllers\RoleController::class)
        ->names([
            'index' => 'roles.index',
            'create' => 'roles.create',
            'store' => 'roles.store',
            'show' => 'roles.show',
            'edit' => 'roles.edit',
            'update' => 'roles.update',
            'destroy' => 'roles.destroy',
        ]);

    Route::get('/dashboard/admin', function () {
        $roleFeatures = RoleFeature::where('role_id', Auth::user()->role_id)->get();
        $features = Feature::whereIn('id', $roleFeatures->pluck('feature_id'))->get();
        $company = \App\Models\Company::where('id', Auth::user()->company_id)->first();

        return Inertia::render('DashboardAdmin', [
            'company' => \App\Models\Company::where('id', Auth::user()->company_id)->first(),
            'users' => \App\Models\User::where('company_id', Auth::user()->company_id)->get(),
            'plan' => \App\Models\Plan::where('id', $company->plan_id)->first(),
            'features' =>$features,
            'roles'=> \App\Models\Role::where('company_id', Auth::user()->company_id)->get(),
        ]);
    })->name('dashboard.admin');

    Route::get('/company/{company}/settings', function ($company) {
        return Inertia::render('CompanySettings', [
            'company' => \App\Models\Company::where('id', $company)->first(),
            'plan' => \App\Models\Plan::where('company_id', Auth::user()->company_id)->get(),
        ]);
    })->name('company.settings');





});
