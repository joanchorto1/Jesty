<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BudgetItemController extends Controller
{
    public function index()
    {
        $budgetItems = BudgetItem::all();
        return Inertia::render('BudgetItems/Index', [
            'budgetItems' => $budgetItems
        ]);
    }

    public function create()
    {
        $budgets = Budget::all();
        $products = Product::all();
        return Inertia::render('BudgetItems/Create', [
            'budgets' => $budgets,
            'products' => $products
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'budget_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|integer',
            'unit_price' => 'required|numeric',
        ]);

        BudgetItem::create($request->all());

        return redirect()->route('budgetItems.index');
    }

    public function edit(BudgetItem $budgetItem)
    {
        $budgets = Budget::all();
        $products = Product::all();
        return Inertia::render('BudgetItems/Edit', [
            'budgetItem' => $budgetItem,
            'budgets' => $budgets,
            'products' => $products
        ]);
    }

    public function update(Request $request, BudgetItem $budgetItem)
    {
        $request->validate([
            'budget_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|integer',
            'unit_price' => 'required|numeric',
        ]);

        $budgetItem->update($request->all());

        return redirect()->route('budgetItems.index');
    }

    public function destroy(BudgetItem $budgetItem)
    {
        $budgetItem->delete();

        return redirect()->route('budgetItems.index');
    }

    //store in last budget with my campany
    public function storeInLastButget(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        // Obtener el ID de la empresa del usuario autenticado
        $userCompanyId = Auth::user()->company_id;

        // Obtener el último presupuesto creado en la empresa del usuario
        $budget = Budget::where('company_id', $userCompanyId)
            ->latest('created_at')
            ->first();

        if (!$budget) {
            return response()->json(['status' => 'no hi ha pressupostos'], 404);
        }

        // Crear el nuevo item de presupuesto
        $budgetItem = BudgetItem::create([
            'budget_id' => $budget->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'total' => $request->total,
        ]);

        return response()->json(['status' => 'ok', 'budgetItem' => $budgetItem], 201);
    }


    public function storeMultipleItems(Request $request)
    {
    // Validar la solicitud
    $request->validate([
        'items' => 'required|array',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
        'items.*.total' => 'required|numeric|min:0',
    ]);

    // Obtener el ID de la empresa del usuario autenticado
    $userCompanyId = Auth::user()->company_id;

    // Obtener el último presupuesto creado en la empresa del usuario
    $budget = Budget::where('company_id', $userCompanyId)
        ->latest('created_at')
        ->first();

    if (!$budget) {
        return response()->json(['status' => 'no hi ha pressupostos'], 404);
    }

    $items = $request->items;

    // Crear los nuevos ítems de presupuesto
    foreach ($items as $item) {
        BudgetItem::create([
            'budget_id' => $budget->id,
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
            'total' => $item['total'],
        ]);
    }
    return response()->json(['status' => 'ok'], 201);
}
}


