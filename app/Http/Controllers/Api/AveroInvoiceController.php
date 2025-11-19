<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Services\DocumentTotalsCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class AveroInvoiceController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document_number' => 'required|string|max:255',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'client.name' => 'required|string|max:255',
            'client.nif' => 'required|string|max:255',
            'client.email' => 'nullable|email',
            'client.address' => 'nullable|string',
            'client.city' => 'nullable|string',
            'client.postal_code' => 'nullable|string',
            'client.country' => 'nullable|string',
            'client.phone' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric',
            'items.*.tax_rate' => 'nullable|numeric',
            'items.*.discount' => 'nullable|numeric',
            'items.*.total' => 'nullable|numeric',
            'summary.base_imponible' => 'required|numeric',
            'summary.irpf_tax' => 'nullable|numeric',
            'summary.total_irpf' => 'nullable|numeric',
            'summary.total_iva' => 'required|numeric',
            'summary.total' => 'required|numeric',
        ]);

        try {
            $invoice = DB::transaction(function () use ($validated) {
                $company = $this->resolveCompany();
                $client = $this->findOrCreateClient($company->id, $validated['client']);

                $itemsPayload = collect($validated['items'])->map(function (array $item) {
                    $quantity = (float) $item['quantity'];
                    $unitPrice = (float) $item['unit_price'];
                    $discount = (float) ($item['discount'] ?? 0);
                    $taxRate = (float) ($item['tax_rate'] ?? 0);
                    $lineTotal = $item['total'] ?? $this->calculateLineTotal($quantity, $unitPrice, $discount);

                    return [
                        'description' => $item['description'],
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'discount' => $discount,
                        'iva' => $taxRate,
                        'total' => $lineTotal,
                    ];
                })->all();

                $totals = DocumentTotalsCalculator::calculate(array_map(function ($item) {
                    return [
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'discount' => $item['discount'],
                        'iva' => $item['iva'],
                    ];
                }, $itemsPayload));

                $summary = $validated['summary'];
                $invoice = Invoice::create([
                    'company_id' => $company->id,
                    'client_id' => $client->id,
                    'date' => $validated['date'],
                    'due_date' => $validated['due_date'] ?? null,
                    'name' => $validated['document_number'],
                    'state' => 'pending',
                    'base_imponible' => $summary['base_imponible'],
                    'iva' => $totals['effectiveRate'],
                    'monto_iva' => $summary['total_iva'],
                    'total' => $summary['total'],
                    'irpf_tax' => $summary['irpf_tax'] ?? 0,
                    'total_irpf' => $summary['total_irpf'] ?? 0,
                    'notes' => $validated['notes'] ?? null,
                ]);

                $category = $this->resolveCategory($company->id);

                foreach ($itemsPayload as $item) {
                    $product = $this->findOrCreateProduct($company->id, $category->id, $item);
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'discount' => $item['discount'],
                        'total' => $item['total'],
                        'iva' => $item['iva'],
                    ]);
                }

                return $invoice;
            });

            $pdfUrl = $this->generateInvoicePdf($invoice->fresh(['client', 'company', 'items.product']));

            return response()->json([
                'status' => 'success',
                'message' => null,
                'pdf_url' => $pdfUrl,
                'invoice_id' => $invoice->id,
            ]);
        } catch (Throwable $exception) {
            Log::error('Error importing invoice from Avero', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    protected function resolveCompany(): Company
    {
        $companyId = config('services.avero.company_id');

        if ($companyId) {
            return Company::findOrFail($companyId);
        }

        $company = Company::first();

        if (! $company) {
            abort(422, 'No hi ha cap empresa configurada per rebre factures.');
        }

        return $company;
    }

    protected function findOrCreateClient(int $companyId, array $clientData): Client
    {
        $addressParts = collect([
            $clientData['address'] ?? null,
            $clientData['postal_code'] ?? null,
            $clientData['city'] ?? null,
            $clientData['country'] ?? null,
        ])->filter()->implode(', ');

        return Client::updateOrCreate(
            [
                'company_id' => $companyId,
                'nif' => $clientData['nif'],
            ],
            [
                'name' => $clientData['name'],
                'email' => $clientData['email'] ?? null,
                'phone' => $clientData['phone'] ?? null,
                'address' => $addressParts ?: ($clientData['address'] ?? null),
            ]
        );
    }

    protected function resolveCategory(int $companyId): Category
    {
        $categoryName = config('services.avero.default_category', 'Avero');

        return Category::firstOrCreate(
            [
                'company_id' => $companyId,
                'name' => $categoryName,
            ],
            [
                'description' => 'Productes importats des d\'Avero',
            ]
        );
    }

    protected function findOrCreateProduct(int $companyId, int $categoryId, array $item): Product
    {
        $product = Product::firstOrNew([
            'company_id' => $companyId,
            'name' => $item['description'],
        ]);

        if (! $product->exists) {
            $product->category_id = $categoryId;
        }

        $product->description = $item['description'];
        $product->price = $item['unit_price'];
        $product->iva = $item['iva'] ?? 0;
        $product->save();

        return $product;
    }

    protected function generateInvoicePdf(Invoice $invoice): string
    {
        $pdf = Pdf::loadView('pdfs.invoice', [
            'invoice' => $invoice,
            'client' => $invoice->client,
            'company' => $invoice->company,
        ]);

        $fileName = sprintf('invoices/%s-%s.pdf', $invoice->name, Str::uuid());
        Storage::disk('public')->put($fileName, $pdf->output());

        $invoice->forceFill(['pdf_path' => $fileName])->save();

        return Storage::disk('public')->url($fileName);
    }

    protected function calculateLineTotal(float $quantity, float $unitPrice, float $discount): float
    {
        $lineBase = $quantity * $unitPrice;

        if ($discount > 0) {
            $lineBase -= ($lineBase * $discount) / 100;
        }

        return round($lineBase, 2);
    }
}
