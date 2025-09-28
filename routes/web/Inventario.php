<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['route.features.access:3'])->group(function () {

// serveis
    Route::get('/dashboard/services', function () {
        return Inertia::render('DashboardServeis', [
            'categories' => Category::where('company_id', Auth::user()->company_id)->get(),
            'products' => Product::where('company_id', Auth::user()->company_id)->get(),
        ]);
    })->name('dashboard.services');


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


});
