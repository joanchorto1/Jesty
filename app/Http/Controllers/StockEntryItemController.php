<?php

namespace App\Http\Controllers;

use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StockEntryItemController extends Controller
{
    public function create(StockEntry $stockEntry)
    {
        $this->authorizeAccess($stockEntry);

        $products = Product::where('company_id', Auth::user()->company_id)->get();

        return Inertia::render('StockEntryItems/Create', [
            'stockEntry' => $stockEntry,
            'products' => $products,
        ]);
    }

    public function store(Request $request, StockEntry $stockEntry)
    {
        $this->authorizeAccess($stockEntry);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $stockEntry->items()->create($request->all());

        return Inertia::location(route('stock-entries.edit', $stockEntry->id));
    }

    public function destroy(StockEntryItem $stockEntryItem)
    {
        $stockEntry = $stockEntryItem->stockEntry;
        $this->authorizeAccess($stockEntry);

        $stockEntryItem->delete();

        return Inertia::location(route('stock-entries.edit', $stockEntry->id));
    }

    private function authorizeAccess(StockEntry $stockEntry)
    {
        if ($stockEntry->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized access');
        }
    }
}
