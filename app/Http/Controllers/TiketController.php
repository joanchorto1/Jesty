<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Validation\Rules\In;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Tiket;
use App\Models\TiketItem;
use App\Models\Product;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TiketController extends Controller
{

    public  function dashboard()
    {
        $tikets = Tiket::where('company_id', Auth::user()->company_id)->get();
        $tiketItems = TiketItem::all()  ;
        $company = Company::where('id', Auth::user()->company_id)->first(); // Cambiado a first() para obtener un solo registro
        $products = Product::where('company_id', Auth::user()->company_id)->get();

        //Bucle que recorra los tikets y los tiketItems y guarda unicamente los items que tengan como tiket_id el id del tiket
        $tiketItemsReal = [];
        foreach ($tikets as $tiket) {
            $tiketItemsReal[$tiket->id] = [];
            foreach ($tiketItems as $tiketItem) {
                if ($tiketItem->tiket_id == $tiket->id) {
                    $tiketItemsReal[$tiket->id][] = $tiketItem;
                }
            }
        }

        return Inertia::render('DashboardTPV', [
            'tickets' => $tikets,
            'ticketItems' => $tiketItemsReal,
            'company' => $company,
            'products' => $products,
            'categories' => Category::where('company_id', Auth::user()->company_id)->get(),
        ]);
    }

    public function storeWithItems(Request $request)
    {

        // Validar los datos del request
        $validated = $request->validate([
            'name' => 'required|string',
            'base_imponible' => 'required|numeric',
            'iva' => 'required|numeric',
            'monto_iva' => 'required|numeric',
            'total' => 'required|numeric',
            'tiketItems' => 'required|array',
            'tiketItems.*.product_id' => 'required|exists:products,id',
            'tiketItems.*.quantity' => 'required|integer',
            'tiketItems.*.unit_price' => 'required|numeric',
            'tiketItems.*.total' => 'required|numeric',
        ]);



        // Crear el tiket
        $data = $request->only('name', 'base_imponible', 'iva', 'monto_iva', 'total');
        $data['company_id'] = Auth::user()->company_id;



        $tiket = Tiket::create($data);

        $this->createIncomeFromTiket($tiket);

        // Crear los ítems del tiket
        foreach ($validated['tiketItems'] as $item) {
            TiketItem::create([
                'tiket_id' => $tiket->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['total'],
            ]);
            $this->updateStockProducts($item['product_id'], $item['quantity']);
        }

        return null;



    }

    public function edit(Tiket $tiket)
    {
        $tiketItems = TiketItem::where('tiket_id', $tiket->id)->get();


        return Inertia::render('Tikets/Edit', [
                'ticket' => $tiket,
                'ticketItems' => $tiketItems,
                'products' => Product::where('company_id', Auth::user()->company_id)->get(),
                'categories' => Category::where('company_id', Auth::user()->company_id)->get(),
            ]);


    }

    public function goToEdit(Tiket $tiket)
    {
        return Inertia::location(route('tikets.edit', $tiket->id));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'base_imponible' => 'required|numeric|min:0',
            'iva' => 'nullable|numeric',
            'total' => 'required|numeric',
            'monto_iva' => 'required|numeric',
            'ticketItems' => 'required|array',
            'ticketItems.*.product_id' => 'required|exists:products,id',
            'ticketItems.*.quantity' => 'required|integer|min:1',
            'ticketItems.*.unit_price' => 'required|numeric|min:0',
            'ticketItems.*.total' => 'required|numeric|min:0',
        ]);

        $tiket = Tiket::findOrFail($id);

        // Actualizar el tiket
        $data = $request->only(['name', 'base_imponible', 'iva', 'total', 'monto_iva']);
        $tiket->update($data);

        // Obtener IDs de los items existentes para compararlos
        $existingItemIds = TiketItem::where('tiket_id', $id)->pluck('id')->toArray();
        $oldItems = TiketItem::where('tiket_id', $id)->get();

        foreach ($request->ticketItems as $item) {
            // Si el ítem tiene un ID, lo actualizamos, si no, lo creamos
            if (isset($item['id'])) {
                // Actualizar ítem existente
                TiketItem::where('id', $item['id'])->update([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                ]);

                $this->updateStockProductFromItems($item['product_id'], $item['quantity'], $oldItems);

                // Quitamos el ID del array de existentes
                $existingItemIds = array_diff($existingItemIds, [$item['id']]);
            } else {
                // Crear nuevo ítem
                TiketItem::create([
                    'tiket_id' => $id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                ]);

                $this->updateStockProducts($item['product_id'], $item['quantity']);
            }
        }

        $this->updateIncomeFromTiket($tiket);


        // Eliminar los ítems que no están en la nueva solicitud
        if (!empty($existingItemIds)) {
            TiketItem::whereIn('id', $existingItemIds)->delete();
        }

        return null;
    }

    public function destroy(Tiket $tiket)
    {
        //eliminar los items del tiket
        $this->deleteIncomeFromTiket($tiket);
        TiketItem::where('tiket_id', $tiket->id)->delete();
        //eliminar el tiket
        $tiket->delete();
        return Inertia::location(route('tikets.index'));
    }

    public function index()
    {
        $tikets = Tiket::where('company_id', Auth::user()->company_id)->get();
        return Inertia::render('Tikets/Index', [
            'tickets' => $tikets,
        ]);
    }
    public function goToCreate()
    {
        return Inertia::location(route('tikets.create'));
    }

    public function create()
    {
        return Inertia::render('Tikets/Create', [
            'products' => Product::where('company_id', Auth::user()->company_id)->where('disabled', false)->get(),
            'categories' => Category::where('company_id', Auth::user()->company_id)->get(),
        ]);
    }
    public function printNoID(Request $request)
    {
        // Asegúrate de que el nombre se recibe en la solicitud

        $request->validate([
            'name' => 'required|string|max:255',
        ]);


        // Busca el ticket por el nombre y por company_id
        $tiketReal = Tiket::where('name', $request->name)->where('company_id', Auth::user()->company_id)->first();

        return Inertia::location(route('tikets.imprimir', $tiketReal->id));

    }


    public function imprimir(Tiket $tiket)
    {
        $tiketItems = TiketItem::where('tiket_id', $tiket->id)->get();
        $company = Company::where('id', Auth::user()->company_id)->first(); // Cambiado a first() para obtener un solo registro
        $products = Product::where('company_id', Auth::user()->company_id)->get();


        return Inertia::render('Tikets/Imprimir', [
            'tiket' => $tiket,
            'tiketItems' => $tiketItems,
            'company' => $company,
            'products' => $products,
        ]);
    }

    public function print(Tiket $tiket)
    {

        return Inertia::location(route('tikets.imprimir', $tiket->id));

    }

    public function reportProducts(){
        $company = Company::where('id', Auth::user()->company_id)->first(); // Cambiado a first() para obtener un solo registro
        $tikets = Tiket::where('company_id', $company->id)->get();

        // Obtiene todos los IDs de los tickets para usarlos en la consulta de tiketItems
        $tiketIds = $tikets->pluck('id');

        // Obtiene los TiketItem correspondientes a todos los tickets
        $tiketItems = TiketItem::whereIn('tiket_id', $tiketIds)->get();

        return Inertia::render('Tikets/ProductReport', [
            'tickets' => $tikets,
            'ticketItems' => $tiketItems,
            'products' => Product::where('company_id', Auth::user()->company_id)->get(),
        ]);
    }

    private function createIncomeFromTiket(Tiket $tiket)
    {

        Income::create(
            [
                'name' => $tiket->name,
                'source' => 'TPV',
                'tax_base' => $tiket->base_imponible,
                'tax_rate' => $tiket->iva,
                'tax_amount' => $tiket->monto_iva,
                'external_id'=> $tiket->id,
                'total_amount' => $tiket->total,
                'company_id' => Auth::user()->company_id,
                'date' => now(),
            ]
        );
    }
    private function updateIncomeFromTiket(Tiket $tiket)
    {
        $income = Income::where('name', $tiket->name)->where('external_id',$tiket->id)->first();

        $income->update(
            [
                'tax_base' => $tiket->base_imponible,
                'tax_rate' => $tiket->iva,
                'tax_amount' => $tiket->monto_iva,
                'total_amount' => $tiket->total,
            ]
        );

    }

    private function deleteIncomeFromTiket(Tiket $tiket)
    {
        $income = Income::where('name', $tiket->name)->where('external_id',$tiket->id)->first();
        $income->delete();
    }

    private function updateStockProducts($product_id, $quantity)
    {

        $product = Product::where('id', $product_id)->first();
        $product->stock = $product->stock - $quantity;
        $product->save();
    }
    private function updateStockProductFromItems($product_id, $quantity, $oldItems)
    {
        foreach ($oldItems as $item){
            $product = Product::findOrFail($item->product_id);
            $product->stock += $item->quantity;
            $product->save();
        }
        $product = Product::findOrFail($product_id);
        $product->stock -= $quantity;
        $product->save();

    }



}
