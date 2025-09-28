<?php


use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmailConfigurationController;
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
        $company = \App\Models\Company::where('id', Auth::user()->company_id)->first();
        $emailConfig = \App\Models\EmailConfiguration::where('company_id', Auth::user()->company_id)->first();

        return Inertia::render('DashboardAdmin', [
            'company' => \App\Models\Company::where('id', Auth::user()->company_id)->first(),
            'users' => \App\Models\User::where('company_id', Auth::user()->company_id)->get(),
            'roles'=> \App\Models\Role::where('company_id', Auth::user()->company_id)->get(),
            'emailConfig' => $emailConfig,
            'modules' => config('modules.modules', []),
        ]);
    })->name('dashboard.admin');

    //Rutas de EmailConfig:

    Route::get('/email-configurations', [EmailConfigurationController::class, 'index'])->name('email-configurations.index');

    // Crear una nueva configuración de correo
    Route::post('/email-configurations', [EmailConfigurationController::class, 'store'])->name('email-configurations.store');

    // Mostrar una configuración de correo
    Route::get('/email-configurations/{id}', [EmailConfigurationController::class, 'show'])->name('email-configurations.show');

    //Ir a la vista de edit
    Route::get('/email-configurations/{id}/edit', [EmailConfigurationController::class, 'edit'])->name('email-configurations.edit');

    // Actualizar una configuración existente
    Route::put('/email-configurations/{id}', [EmailConfigurationController::class, 'update'])->name('email-configurations.update');

    // Eliminar una configuración de correo
    Route::delete('/email-configurations/{id}', [EmailConfigurationController::class, 'destroy'])->name('email-configurations.destroy');

    //Rutas de Keys
    Route::get('/company/{company}/keys', [CompanyController::class , 'showKeys'])->name('companies.showKeys');
    Route::put('/company/{company}/keys', [CompanyController::class, 'updateKeys'])->name('companies.updateKeys');
    Route::get('/company/{company}/keys/new', [CompanyController::class, 'generateKeys'])->name('companies.generateKeys');


    //Rutas de tareas de usuario

    Route::get('/user_tasks', [\App\Http\Controllers\UserTaskController::class, 'index'])->name('user_tasks.index');
    Route::get('/user_tasks/creat/admin', [\App\Http\Controllers\UserTaskController::class, 'adminCreate'])->name('user_tasks.adminCreate');
    Route::get('/user_tasks/edit/admin/{user_task}', [\App\Http\Controllers\UserTaskController::class, 'adminEdit'])->name('user_tasks.adminEdit');
    Route::post('/user_tasks/admin', [\App\Http\Controllers\UserTaskController::class, 'adminStore'])->name('user_tasks.adminStore');
    Route::put('/user_tasks/admin/{user_task}', [\App\Http\Controllers\UserTaskController::class, 'adminUpdate'])->name('user_tasks.adminUpdate');
});







