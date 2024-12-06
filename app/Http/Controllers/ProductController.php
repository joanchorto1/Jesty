<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::where('company_id',Auth::user()->company_id)->get();
        $products = Product::where('company_id',Auth::user()->company_id)->get();
        $categories = Category::where('company_id',Auth::user()->company_id)->get();
        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }

    public function create()
    {
        $companies = Company::all();
        $categories = Category::where('company_id',Auth::user()->company_id)->get();
        $suppliers = Supplier::where('company_id',Auth::user()->company_id)->get();
        return Inertia::render('Products/Create', [
            'companies' => $companies,
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'supplier_id' => 'required',
            'cost_price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        $data = $request->all();
        $data['company_id'] = Auth::user()->company_id;
        Product::create($data);


        return Inertia::location(route('products.index'));
    }

    public function edit(Product $product)
    {
        $companies = Company::all();
        $suppliers = Supplier::where('company_id',Auth::user()->company_id)->get();
        $categories = Category::where('company_id',Auth::user()->company_id)->get();
        return Inertia::render('Products/Edit', [
            'product' => $product,
            'companies' => $companies,
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'category_id' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'cost_price' => 'required',
            'supplier_id' => 'required',
        ]);

        $product->update($request->all());

        return Inertia::location(route('products.index'));
    }

    //show

    public function show()
    {

    }

    public function destroy(Product $product)
    {
        $product->delete();
        print('Esti  elimina');
        return Inertia::location(route('products.index'));
    }
}
