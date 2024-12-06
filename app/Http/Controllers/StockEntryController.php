<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\In;
use Inertia\Inertia;

class StockEntryController extends Controller
{
    // Mostrar todos los registros de entrada de stock
    public function index()
    {
        $stockEntries = StockEntry::where('company_id',Auth::user()->company_id)->get();




        $suppliers=Supplier::where('company_id',Auth::user()->company_id)->get();

        return Inertia::render('StockEntries/Index', [
            'stockEntries' => $stockEntries,
            'suppliers'=>$suppliers
        ]);
    }

    public function  goToShow (StockEntry $stockEntry)
    {
        return Inertia::location(route('stockEntries.show',$stockEntry->id));

    }


    // Mostrar un registro de entrada de stock específico
    public function show(StockEntry $stockEntry)
    {
        $id=$stockEntry->id;
        $stockEntryItems=StockEntryItem::where('stock_entry_id',$id)->get();
        $products=Product::where('company_id',Auth::user()->company_id)->get();
        $supplier=Supplier::where('id',$stockEntry->supplier_id)->first();


        return Inertia::render('StockEntries/Show', [
            'stockEntry' => $stockEntry,
            'stockEntryItems'=>$stockEntryItems,
            'products'=>$products,
            'supplier'=>$supplier
        ]);
    }


    // Mostrar el formulario para crear una nueva entrada de stock
    public function create(Supplier $supplier)
    {
        $products = Product::where('supplier_id',$supplier->id)->get();
        $categories = Category::where('company_id',Auth::user()->company_id)->get();
        return Inertia::render('StockEntries/Create',
            [
                'supplier' => $supplier,
                'products' => $products,
                'categories' => $categories,
            ]);
    }

    // Guardar una nueva entrada de stock
    public function store(Request $request)
    {

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'entry_date' => 'required|date',
            'reference' => 'required|string',
            'base_amount' => 'required|numeric',
            'tax_rate' => 'required|numeric',
            'tax_amount' => 'required|numeric',
            'total' => 'required|numeric',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'stock_entry_items' => 'required|array',  // Los items deben ser un array
        ]);



        // Crear la entrada de stock
        $stockEntry = StockEntry::create([
            'company_id' => Auth::user()->company_id,
            'supplier_id' => $validated['supplier_id'],
            'entry_date' => $validated['entry_date'],
            'reference' => $validated['reference'],
            'base_amount' => $validated['base_amount'],
            'tax_rate' => $validated['tax_rate'],
            'tax_amount' => $validated['tax_amount'],
            'total' => $validated['total'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        // Crear los items de la entrada de stock
        foreach ($validated['stock_entry_items'] as $item) {
            StockEntryItem::create([
                'stock_entry_id' => $stockEntry->id,
                'unit_price' => $item['unit_price'],
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'total'=>$item['total']
                ]);

            $this->IncreaceStockOfProduct($item['product_id'],$item['quantity']);
        }

        $this->createExpenseFromStockEntry($stockEntry);
        return Inertia::location(route('stockEntries.index'));}


    public function goToEdit(StockEntry $stockEntry)
    {
        return Inertia::location(route('stockEntries.edit',$stockEntry));
    }

    // Mostrar el formulario para editar una entrada de stock
    public function edit(StockEntry $stockEntry)
    {
        $id=$stockEntry->id;
        $stockEntryItems=StockEntryItem::where('stock_entry_id',$id)->get();
        $products=Product::where('company_id',Auth::user()->company_id)->get();
        $supplier=Supplier::where('id',$stockEntry->supplier_id)->first();


        return Inertia::render('StockEntries/Edit', [
            'stockEntry' => $stockEntry,
            'stockEntryItems'=>$stockEntryItems,
            'products'=>$products,
            'supplier'=>$supplier
        ]);
    }

    // Actualizar una entrada de stock
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'entry_date' => 'required|date',
            'reference' => 'required|string',
            'base_amount' => 'required|numeric',
            'tax_rate' => 'required|numeric',
            'tax_amount' => 'required|numeric',
            'total' => 'required|numeric',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'stock_entry_items' => 'required|array',
        ]);

        // Encontrar la entrada de stock y actualizarla
        $stockEntry = StockEntry::findOrFail($id);
        $stockEntry->update([
            'supplier_id' => $validated['supplier_id'],
            'entry_date' => $validated['entry_date'],
            'reference' => $validated['reference'],
            'base_amount' => $validated['base_amount'],
            'tax_rate' => $validated['tax_rate'],
            'tax_amount' => $validated['tax_amount'],
            'total' => $validated['total'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        // Eliminar los items antiguos y agregar los nuevos

        $stockEntryItems=StockEntryItem::where('stock_entry_id',$id)->get();
        $stockEntryItemsOld=StockEntryItem::where('stock_entry_id',$id)->get();

        foreach ($stockEntryItems as $item ){

            $item->delete();
        }


        foreach ($validated['stock_entry_items'] as $item) {
            StockEntryItem::create([
                'stock_entry_id' => $stockEntry->id,
                'unit_price' => $item['unit_price'],
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'total'=>$item['total']
            ]);

            $this->updateStockOfProduct($item['product_id'],$item['quantity'],$stockEntryItemsOld);
        }


        return Inertia::location(route('stockEntries.index'));
    }

    // Eliminar una entrada de stock
    public function destroy($id)
    {
        $stockEntry = StockEntry::findOrFail($id);
        $stockEntry->delete();
        $this->deleteExpenseFromStockEntry($stockEntry);

        return Inertia::location(route('stockEntries.index'));
    }


    private function createExpenseFromStockEntry(StockEntry $stockEntry)
    {
        // Verificar si el método de pago "Otros" no existe
        if (PaymentMethod::where('name', 'Otros')->where('company_id', $stockEntry->company_id)->doesntExist()) {
            $payment_method = PaymentMethod::create([
                'name' => 'Otros',
                'description' => 'Otro método de pago no definido',
                'company_id' => $stockEntry->company_id
            ]);
        } else {
            $payment_method = PaymentMethod::where('name', 'Otros')->where('company_id', $stockEntry->company_id)->first();
        }

        // Verificar si la categoría de gasto "Entrada de Stock" no existe
        if (ExpenseCategory::where('name', 'Entrada de Stock')->where('company_id', $stockEntry->company_id)->doesntExist()) {
            $expenseCategory = ExpenseCategory::create([
                'name' => 'Entrada de Stock',
                'description' => 'Entrada de Stock',
                'company_id' => $stockEntry->company_id
            ]);
        } else {
            $expenseCategory = ExpenseCategory::where('name', 'Entrada de Stock')->where('company_id', $stockEntry->company_id)->first();
        }

        $supplier = Supplier::where('id', $stockEntry->supplier_id)->first();
        // Crear el gasto
        Expense::create([
            'name' => 'Entrada de stock de '.$supplier->name,
            'description' => 'Entrada de stock',
            'amount' => $stockEntry->total,
            'iva' => $stockEntry->tax_rate,
            'date' => $stockEntry->created_at,
            'external_id' => $stockEntry->id,
            'payment_method_id' => $payment_method->id,
            'expense_category_id' => $expenseCategory->id,
            'company_id' => $stockEntry->company_id, // Nota: corregí 'comapny_id' a 'company_id'
        ]);
    }

    private function updateExpenseFromStockEntry(StockEntry $stockEntry)
    {
        $category = ExpenseCategory::where('name', 'Entrada de Stock')
            ->where('company_id', $stockEntry->company_id)
            ->first();

        if ($category) {
            Expense::where('external_id', $stockEntry->id)
                ->where('company_id', $stockEntry->company_id)
                ->where('expense_category_id', $category->id) // Corregido el nombre de la columna
                ->delete();
        }

        $this->createExpenseFromStockEntry($stockEntry);
    }

    private function deleteExpenseFromStockEntry(StockEntry $stockEntry)
    {
        $category = ExpenseCategory::where('name', 'Entrada de Stock')
            ->where('company_id', $stockEntry->company_id)
            ->first();

        if ($category) {
            Expense::where('external_id', $stockEntry->id)
                ->where('company_id', $stockEntry->company_id)
                ->where('expense_category_id', $category->id) // Corregido el nombre de la columna
                ->delete();
        }
    }

    private function IncreaceStockOfProduct($product_id, $quantity)
    {

        $product = Product::findOrFail($product_id);
        $product->stock += $quantity;
        $product->save();
    }
    private function updateStockOfProduct($product_id, $quantity, $stockEntryItemsOld)
    {
        foreach ($stockEntryItemsOld as $item){
            $product = Product::findOrFail($item->product_id);
            $product->stock -= $item->quantity;
            $product->save();
        }
        $product = Product::findOrFail($product_id);
        $product->stock += $quantity;
        $product->save();
    }


}
