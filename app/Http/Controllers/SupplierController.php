<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::where('company_id', Auth::user()->company_id)->get();

        return Inertia::render('Suppliers/Index', [
            'suppliers' => $suppliers,
        ]);
    }

    public function create()
    {
        return Inertia::render('Suppliers/Create');
    }

    public function goToCreate()
    {
        return Inertia::location(route('suppliers.create'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        Supplier::create(array_merge($request->all(), [
            'company_id' => Auth::user()->company_id,
        ]));

        return Inertia::location(route('suppliers.index'));
    }

    public function edit(Supplier $supplier)
    {

        return Inertia::render('Suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request, Supplier $supplier)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $supplier->update($request->all());

        return Inertia::location(route('suppliers.index'));
    }


    public function delete(Supplier $supplier)
    {

        $supplier->delete();

        return Inertia::location(route('suppliers.index'));
    }


    public function show(Supplier $supplier)
    {

        $products = Product::where('supplier_id', $supplier->id)->get();
        $stockEntries = StockEntry::where('supplier_id', $supplier->id)->get();
        return Inertia::render('Suppliers/Show', [
            'supplier' => $supplier,
            'products' => $products,
            'stockEntries' => $stockEntries,
        ]);
    }
}
