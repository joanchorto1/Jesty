<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Part;
use App\Models\PartItem;
use App\Models\Product;
use App\Models\Company;
use App\Services\DocumentNumberGenerator;
use App\Services\DocumentTotalsCalculator;
use Carbon\Carbon;
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

        $nextInvoiceNumber = DocumentNumberGenerator::generate(
            Invoice::class,
            'name',
            'FA',
            Auth::user()->company_id,
            Carbon::now()
        );

        return Inertia::render('Parts/Index', [
            'parts' => $parts,
            'clients' => $clients,
            'nextInvoiceNumber' => $nextInvoiceNumber,
        ]);
    }

    public function create()
    {
        $clients = Client::where('company_id', Auth::user()->company_id)->get();
        $products = Product::where('company_id', Auth::user()->company_id)
            ->where('disabled', false)
            ->with('category')
            ->get();

        $nextPartReference = DocumentNumberGenerator::generate(
            Part::class,
            'reference',
            'AB',
            Auth::user()->company_id,
            Carbon::now()
        );

        return Inertia::render('Parts/Create', [
            'clients' => $clients,
            'products' => $products,
            'nextPartReference' => $nextPartReference,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:255',
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

        $issueDate = Carbon::parse($validated['date']);
        $part = Part::create([
            'company_id' => Auth::user()->company_id,
            'client_id' => $client->id,
            'reference' => DocumentNumberGenerator::generate(
                Part::class,
                'reference',
                'AB',
                Auth::user()->company_id,
                $issueDate
            ),
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
                'iva' => $product->iva ?? 0,
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

    public function print(Part $part)
    {
        abort_unless($part->company_id === Auth::user()->company_id, 403);

        $part->load(['items', 'client']);

        $company = Company::find(Auth::user()->company_id);
        $products = Product::where('company_id', Auth::user()->company_id)->get();

        return Inertia::render('Parts/Print', [
            'part' => $part,
            'partItems' => $part->items,
            'client' => $part->client,
            'company' => $company,
            'products' => $products,
        ]);
    }

    public function convertToInvoice(Request $request)
    {
        $validated = $request->validate([
            'parts' => 'required|array|min:1',
            'parts.*' => 'exists:parts,id',
            'invoice.date' => 'required|date',
            'invoice.state' => 'required|in:pending,paid,cancelled',
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

        $itemsForTotals = $parts->flatMap(function (Part $part) {
            return $part->items->map(function (PartItem $item) {
                $ivaRate = $item->iva ?? $item->product->iva ?? 0;

                return [
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => 0,
                    'iva' => $ivaRate,
                ];
            });
        })->toArray();

        $totals = DocumentTotalsCalculator::calculate($itemsForTotals);

        $invoiceDate = Carbon::parse($validated['invoice']['date']);
        $invoice = Invoice::create([
            'company_id' => Auth::user()->company_id,
            'client_id' => $clientIds->first(),
            'date' => $validated['invoice']['date'],
            'name' => DocumentNumberGenerator::generate(
                Invoice::class,
                'name',
                'FA',
                Auth::user()->company_id,
                $invoiceDate
            ),
            'base_imponible' => $totals['base'],
            'iva' => $totals['effectiveRate'],
            'monto_iva' => $totals['tax'],
            'total' => $totals['total'],
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
                    'iva' => $item->iva ?? 0,
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
