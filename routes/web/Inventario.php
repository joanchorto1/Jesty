<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockEntryController;
use App\Http\Controllers\SupplierController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['route.features.access:3'])->group(function () {

//products
    Route::get('/dashboard/products', function () {
        return Inertia::render('DashboardProductos', ['categories' => Category::where('company_id', Auth::user()->company_id)->get(), 'products' => Product::where('company_id', Auth::user()->company_id)->get()]);
    })->name('dashboard.products');


    Route::resource('categories', CategoryController::class)
        ->names([
            'index' => 'categories.index',
            'create' => 'categories.create',
            'store' => 'categories.store',
            'show' => 'categories.show',
            'edit' => 'categories.edit',
            'update' => 'categories.update',
            'destroy' => 'categories.destroy',
        ]);


// Routes for Products
    Route::resource('products', ProductController::class)
        ->names([
            'index' => 'products.index',
            'create' => 'products.create',
            'store' => 'products.store',
            'show' => 'products.show',
            'edit' => 'products.edit',
            'update' => 'products.update',
            'destroy' => 'products.destroy',
        ]);


    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');

// Create: Mostrar formulario de creación de proveedor
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');

// Store: Guardar nuevo proveedor
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');

// Show: Mostrar detalles de un proveedor específico
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');

// Edit: Mostrar formulario de edición de proveedor
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');

// Update: Actualizar proveedor específico
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');

// Delete: Eliminar un proveedor específico
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'delete'])->name('suppliers.delete');

// GoToCreate: Ruta personalizada para redirigir al formulario de creación
    Route::get('/suppliers/goToCreate', [SupplierController::class, 'goToCreate'])->name('suppliers.goToCreate');


// Rutas para Entradas de Stock
    Route::get('/stock-entries', [StockEntryController::class, 'index'])->name('stockEntries.index');
    Route::get('/stock-entries/create/{supplier}', [StockEntryController::class, 'create'])->name('stockEntries.create');
    Route::post('/stock-entries', [StockEntryController::class, 'store'])->name('stockEntries.store');
//storewhititems
    Route::post('/stock-entries/store-with-items', [StockEntryController::class, 'storeWithItems'])->name('stockEntries.storeWithItems');
    Route::get('/stock-entries/{stockEntry}', [StockEntryController::class, 'show'])->name('stockEntries.show');
    Route::get('/stock-entries/{stockEntry}/goToShow', [StockEntryController::class, 'goToShow'])->name('stockEntries.goToShow');
    Route::get('/stock-entries/{stockEntry}/edit', [StockEntryController::class, 'edit'])->name('stockEntries.edit');
    Route::get('/stock-entries/{stockEntry}/gotoedit', [StockEntryController::class, 'goToEdit'])->name('stockEntries.goToEdit');
    Route::put('/stock-entries/{stockEntry}', [StockEntryController::class, 'update'])->name('stockEntries.update');
    Route::delete('/stock-entries/{stockEntry}', [StockEntryController::class, 'destroy'])->name('stockEntries.destroy');


});
