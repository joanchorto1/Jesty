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

//ruta para el registro de usuario
Route::get('/register', [\App\Http\Controllers\AuthController::class, 'create'])->name('auth.register.create');





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
            'notifications' => \App\Models\UserNotification::where('user_id', \Illuminate\Support\Facades\Auth::user()->id)->where('read',false)->orderBy('created_at', 'desc')->get(),
            'tasks' => \App\Models\UserTask::where('user_id', \Illuminate\Support\Facades\Auth::user()->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->orderBy('created_at', 'desc')
                ->get(),
        ]);
    })->name('dashboard');

    // Aquí puedes añadir otras rutas protegidas
});



//Register route:

Route::post('/register/company/user', [\App\Http\Controllers\AuthController::class, 'store'])->name('auth.register');

//Rutas basicas de qualquier usuario
use App\Http\Controllers\UserTaskController;

Route::middleware(['auth'])->group(function () {
    Route::resource('user_tasks', UserTaskController::class);
    Route::get('user_tasks/{user_task}/complete', [UserTaskController::class, 'markAsCompleted'])->name('user_tasks.mark_as_completed');
    Route::get('user_tasks/{user_task}/in_progress', [UserTaskController::class, 'markAsInProgress'])->name('user_tasks.mark_as_in_progress');
    Route::get('user_tasks/{user_task}', [UserTaskController::class, 'show'])->name('user_tasks.show');
});



Route::middleware('check.company.plan')->group(function () {

    require __DIR__ . '/web/Inventario.php';
    require __DIR__ . '/web/CRM.php';
    require __DIR__ . '/web/Contabilidad.php';
    require __DIR__ . '/web/TPV.php';
    require __DIR__ . '/web/Clients.php';
    require __DIR__ . '/web/Facturacion.php';
//    require __DIR__ . '/web/Proyectos.php';
    require __DIR__ . '/web/RRHH.php';
    require __DIR__ . '/web/Notifications.php';


});



// Agrupación de rutas
    require __DIR__ . '/web/Administrador.php';
    require __DIR__ . '/web/Stripe.php';





