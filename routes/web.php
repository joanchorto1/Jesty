<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BudgetItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TiketController;
use App\Models\Invoice;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'check.company.plan', // Middleware personalizado
])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'user' => \Illuminate\Support\Facades\Auth::user(),
            'role' => \App\Models\Role::where('id', \Illuminate\Support\Facades\Auth::user()->role_id)->first(),
        ]);
    })->name('dashboard');

    // Aquí puedes añadir otras rutas protegidas
});


//Register route:

Route::post('/register/company/user', [\App\Http\Controllers\AuthController::class, 'store'])->name('auth.register');



Route::middleware('check.company.plan')->group(function () {

    require __DIR__ . '/web/Inventario.php';
    require __DIR__ . '/web/CRM.php';
    require __DIR__ . '/web/Contabilidad.php';
    require __DIR__ . '/web/TPV.php';
    require __DIR__ . '/web/Clients.php';
    require __DIR__ . '/web/Facturacion.php';


});
// Agrupación de rutas
    require __DIR__ . '/web/Administrador.php';







////outes for AdminUser
//
//Route::get('/jctagency/erp/superadmin/login', [AdminController::class, 'showLoginForm'])->name('admin.loginForm');
//Route::post('/jctagency/erp/superadmin/login', [AdminController::class, 'login'])->name('admin.login');
//
//// Ruta para el logout del administrador
//Route::post('/jctagency/erp/superadmin/logout', [AdminController::class, 'logout'])->name('admin.logout');
//
//// Rutas para el registro del administrador
//Route::get('/jctagency/erp/superadmin/register', [AdminController::class, 'showRegisterForm'])->name('admin.registerForm');
//Route::post('/jctagency/erp/superadmin/register', [AdminController::class, 'register'])->name('admin.register');
//
//// Ruta para el dashboard del administrador
//Route::get('/jctagency/erp/superadmin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware('auth:admin');
//
////Users routes
//
//Route::get('/jctagency/erp/superadmin/userAdmin/create', [AdminController::class, 'userAdminCreate'])->name('users.adminCreate');
//Route::post('/jctagency/erp/superadmin/userAdmin/store', [AdminController::class, 'storeAdminUser'])->name('users.adminStore');
