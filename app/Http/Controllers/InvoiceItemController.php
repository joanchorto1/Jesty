<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceItemController extends Controller
{
    public function index()
    {
        $invoiceItems = InvoiceItem::all();
        return Inertia::render('InvoiceItems/Index', [
            'invoiceItems' => $invoiceItems
        ]);
    }

    public function create()
    {
        $invoices = Invoice::all();
        $products = Product::all();
        return Inertia::render('InvoiceItems/Create', [
            'invoices' => $invoices,
            'products' => $products
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|integer',
            'unit_price' => 'required|numeric',
        ]);

        InvoiceItem::create($request->all());

        return redirect()->route('invoiceItems.index');
    }

    public function edit(InvoiceItem $invoiceItem)
    {
        $invoices = Invoice::all();
        $products = Product::all();
        return Inertia::render('InvoiceItems/Edit', [
            'invoiceItem' => $invoiceItem,
            'invoices' => $invoices,
            'products' => $products
        ]);
    }

    public function update(Request $request, InvoiceItem $invoiceItem)
    {
        $request->validate([
            'invoice_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|integer',
            'unit_price' => 'required|numeric',
        ]);

        $invoiceItem->update($request->all());

        return redirect()->route('invoiceItems.index');
    }

    public function destroy(InvoiceItem $invoiceItem)
    {
        $invoiceItem->delete();

        return redirect()->route('invoiceItems.index');
    }
}
