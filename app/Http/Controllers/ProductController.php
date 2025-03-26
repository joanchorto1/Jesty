<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::where('company_id',Auth::user()->company_id)->get();
        $products = Product::where('company_id',Auth::user()->company_id)->where('disabled', false)->get();
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
        Log::info('Request: '.$request);
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'supplier_id' => 'required',
            'cost_price' => 'required|numeric',
            'stock' => 'required_if:is_stackable,true',
            'is_stackable' => 'required'
        ]);

        $data = $request->all();
        $data['company_id'] = Auth::user()->company_id;
        if (!$request->is_stackable) {
            $data['stock'] = 0;
        }

        Log::info('Data: ', $data);
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
            'stock' => 'required_if:is_stackable,true',
            'cost_price' => 'required',
            'supplier_id' => 'required',
            'is_stackable' => 'required'
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


        $product->update(['disabled' => true]);
        return Inertia::location(route('products.index'));
    }


}
