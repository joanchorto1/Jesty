<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Part;
use App\Models\PartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PartController extends Controller
{
    public function index()
    {
        $parts = Part::with(['client', 'items.product'])
            ->where('company_id', Auth::user()->company_id)
            ->orderByDesc('date')
            ->get();

        $clients = Client::where('company_id', Auth::user()->company_id)->get();

        return Inertia::render('Parts/Index', [
            'parts' => $parts,
            'clients' => $clients,
        ]);
    }

    public function create()
    {
        $clients = Client::where('company_id', Auth::user()->company_id)->get();
        $products = Product::where('company_id', Auth::user()->company_id)
            ->where('disabled', false)
            ->with('category')
            ->get();

        return Inertia::render('Parts/Create', [
            'clients' => $clients,
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:255',
            'date' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $client = Client::where('company_id', Auth::user()->company_id)
            ->where('id', $validated['client_id'])
            ->firstOrFail();

        $productIds = collect($validated['items'])->pluck('product_id');
        $products = Product::where('company_id', Auth::user()->company_id)
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        if ($products->count() !== $productIds->unique()->count()) {
            return back()->withErrors(['items' => 'Se han seleccionado productos no disponibles.']);
        }

        $part = Part::create([
            'company_id' => Auth::user()->company_id,
            'client_id' => $client->id,
            'reference' => $validated['reference'],
            'date' => $validated['date'],
            'status' => 'pending',
            'total' => 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        $total = 0;
        foreach ($validated['items'] as $itemData) {
            $product = $products[$itemData['product_id']];
            $unitPrice = $product->price;
            $quantity = $itemData['quantity'];
            $lineTotal = round($unitPrice * $quantity, 2);

            PartItem::create([
                'part_id' => $part->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total' => $lineTotal,
            ]);

            $total += $lineTotal;
        }

        $part->update(['total' => round($total, 2)]);

        return redirect()->route('parts.index');
    }

    public function destroy(Part $part)
    {
        abort_unless($part->company_id === Auth::user()->company_id, 403);

        if ($part->status !== 'pending') {
            return back()->withErrors(['parts' => 'No es posible eliminar un parte ya facturado.']);
        }

        $part->delete();

        return redirect()->route('parts.index');
    }

    public function convertToInvoice(Request $request)
    {
        $validated = $request->validate([
            'parts' => 'required|array|min:1',
            'parts.*' => 'exists:parts,id',
            'invoice.name' => 'required|string|max:255',
            'invoice.date' => 'required|date',
            'invoice.state' => 'required|in:pending,paid,cancelled',
            'invoice.iva' => 'required|numeric|min:0',
        ]);

        $parts = Part::with('items')
            ->whereIn('id', $validated['parts'])
            ->where('company_id', Auth::user()->company_id)
            ->get();

        if ($parts->isEmpty() || $parts->count() !== count($validated['parts'])) {
            return back()->withErrors(['parts' => 'No se han encontrado todos los partes seleccionados.']);
        }

        if ($parts->pluck('status')->contains('invoiced')) {
            return back()->withErrors(['parts' => 'Alguno de los partes seleccionados ya ha sido facturado.']);
        }

        $clientIds = $parts->pluck('client_id')->unique();
        if ($clientIds->count() !== 1) {
            return back()->withErrors(['parts' => 'Los partes seleccionados deben pertenecer al mismo cliente.']);
        }

        $baseImponible = round($parts->sum('total'), 2);
        $ivaRate = (float) $validated['invoice']['iva'];
        $montoIva = round($baseImponible * ($ivaRate / 100), 2);
        $total = round($baseImponible + $montoIva, 2);

        $invoice = Invoice::create([
            'company_id' => Auth::user()->company_id,
            'client_id' => $clientIds->first(),
            'date' => $validated['invoice']['date'],
            'name' => $validated['invoice']['name'],
            'base_imponible' => $baseImponible,
            'iva' => $ivaRate,
            'monto_iva' => $montoIva,
            'total' => $total,
            'state' => $validated['invoice']['state'],
        ]);

        foreach ($parts as $part) {
            foreach ($part->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => 0,
                    'total' => $item->total,
                ]);

                $this->updateStockProduct($item->product_id, $item->quantity);
            }

            $part->update([
                'status' => 'invoiced',
                'invoice_id' => $invoice->id,
            ]);
        }

        if ($invoice->state === 'paid') {
            $this->createIncomeFromInvoice($invoice);
        }

        app('App\\Http\\Controllers\\UserNotificationController')->createNotification(
            'Nueva factura',
            'Se ha creado una nueva factura a partir de partes de trabajo',
            'Facturación'
        );

        return redirect()->route('invoices.show', $invoice->id);
    }

    private function createIncomeFromInvoice(Invoice $invoice): void
    {
        Income::create([
            'source' => 'invoice',
            'name' => $invoice->name,
            'company_id' => $invoice->company_id,
            'tax_base' => $invoice->base_imponible,
            'tax_rate' => $invoice->iva,
            'external_id' => $invoice->id,
            'tax_amount' => $invoice->monto_iva,
            'total_amount' => $invoice->total,
            'date' => $invoice->date,
        ]);

        app('App\\Http\\Controllers\\UserNotificationController')->createNotification(
            'Nueva factura pagada',
            'Se ha pagado una factura',
            'Facturación'
        );
    }

    private function updateStockProduct(int $productId, int $quantity): void
    {
        $product = Product::find($productId);

        if (!$product || !$product->is_stackable) {
            return;
        }

        $product->stock -= $quantity;
        $product->save();

        if ($product->stock < 5) {
            $this->sendLowStockNotification($product);
        }
    }

    private function sendLowStockNotification(Product $product): void
    {
        app('App\\Http\\Controllers\\UserNotificationController')->createNotification(
            'Stock bajo',
            'El producto ' . $product->name . ' tiene un stock bajo',
            'Inventario'
        );
    }
}
