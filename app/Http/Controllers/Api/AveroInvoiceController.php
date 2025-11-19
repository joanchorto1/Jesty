<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Services\DocumentNumberGenerator;
use App\Services\DocumentTotalsCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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
        Log::info('Starting invoice import from Avero', [
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
        ]);

        $validated = $request->validate([
            'document_number' => 'nullable|string|max:255',
            'source_reference' => 'nullable|string|max:255',
            'date' => 'required|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'client.name' => 'required|string|max:255',
            'client.nif' => 'required|string|max:255',
            'client.email' => 'required|email',
            'client.address' => 'required|string',
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

        Log::info('Payload validated for invoice import from Avero', [
            'document_number' => $validated['document_number'],
            'date' => $validated['date'],
            'client' => $validated['client'],
            'items_count' => count($validated['items']),
        ]);

        $externalReference = $validated['document_number'] ?? ($validated['source_reference'] ?? null);

        try {
            $company = $this->resolveCompany();
            Log::info('Company resolved for Avero invoice import', ['company_id' => $company->id]);

            if (! empty($externalReference)) {
                $existingInvoice = $this->findInvoiceByExternalReference($company->id, $externalReference);

                if ($existingInvoice) {
                    $existingInvoice = $existingInvoice->fresh(['client', 'company', 'items.product']);
                    Log::info('Invoice already imported from Avero, returning existing data', [
                        'invoice_id' => $existingInvoice->id,
                        'external_reference' => $externalReference,
                    ]);

                    $pdfUrls = $this->ensureInvoicePdf($existingInvoice);

                    return response()->json($this->formatInvoiceResponse($existingInvoice, $pdfUrls));
                }
            }

            $invoice = DB::transaction(function () use ($validated, $company, $externalReference) {
                Log::debug('Resolving client for Avero invoice import', ['client' => $validated['client']]);
                $client = $this->findOrCreateClient($company->id, $validated['client']);
                Log::info('Client resolved for Avero invoice import', ['client_id' => $client->id]);

                Log::debug('Normalizing invoice items from Avero payload', ['items' => $validated['items']]);
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
                Log::debug('Normalized Avero items payload', ['items_payload' => $itemsPayload]);

                $totals = DocumentTotalsCalculator::calculate(array_map(function ($item) {
                    return [
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'discount' => $item['discount'],
                        'iva' => $item['iva'],
                    ];
                }, $itemsPayload));

                Log::info('Calculated totals for Avero invoice import', ['totals' => $totals]);

                $summary = $validated['summary'];
                $invoiceNumber = DocumentNumberGenerator::generate(
                    Invoice::class,
                    'number',
                    config('services.avero.invoice_prefix', 'FA'),
                    $company->id,
                    Carbon::parse($validated['date'])
                );

                Log::debug('Creating invoice from Avero payload', [
                    'summary' => $summary,
                    'invoice_number' => $invoiceNumber,
                ]);

                $invoice = Invoice::create([
                    'company_id' => $company->id,
                    'client_id' => $client->id,
                    'date' => $validated['date'],
                    'due_date' => $validated['due_date'] ?? null,
                    'name' => $invoiceNumber,
                    'number' => $invoiceNumber,
                    'external_reference' => $externalReference,
                    'state' => 'pending',
                    'base_imponible' => $summary['base_imponible'],
                    'iva' => $totals['effectiveRate'],
                    'monto_iva' => $summary['total_iva'],
                    'total' => $summary['total'],
                    'irpf_tax' => $summary['irpf_tax'] ?? 0,
                    'total_irpf' => $summary['total_irpf'] ?? 0,
                    'notes' => $validated['notes'] ?? null,
                ]);
                Log::info('Invoice created from Avero payload', ['invoice_id' => $invoice->id]);

                $category = $this->resolveCategory($company->id);
                Log::info('Category resolved for imported products', ['category_id' => $category->id]);

                foreach ($itemsPayload as $item) {
                    $product = $this->findOrCreateProduct($company->id, $category->id, $item);
                    Log::debug('Creating invoice item from Avero payload', [
                        'invoice_id' => $invoice->id,
                        'product_id' => $product->id,
                        'item' => $item,
                    ]);
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

            $invoice = $invoice->fresh(['client', 'company', 'items.product']);
            $pdfUrls = $this->ensureInvoicePdf($invoice);
            Log::info('PDF generated for Avero invoice', [
                'invoice_id' => $invoice->id,
                'public_url' => $pdfUrls['public_url'] ?? null,
                'storage_url' => $pdfUrls['storage_url'] ?? null,
            ]);

            Log::info('Invoice import from Avero completed successfully', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->number,
            ]);

            return response()->json($this->formatInvoiceResponse($invoice, $pdfUrls));
        } catch (Throwable $exception) {
            Log::error('Error importing invoice from Avero', [
                'message' => $exception->getMessage(),
                'payload' => $request->all(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Internal server error.',
            ], 500);
        }
    }

    protected function resolveCompany(): Company
    {
        $companyId = config('services.avero.company_id');

        if ($companyId) {
            Log::debug('Resolving company for Avero using configured company_id', ['company_id' => $companyId]);
            return Company::findOrFail($companyId);
        }

        Log::debug('No company_id configured for Avero import, using first company');
        $company = Company::first();

        if (! $company) {
            Log::warning('Attempted to import invoice from Avero without any company configured');
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

        $client = Client::updateOrCreate(
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

        Log::debug('Resolved client for Avero import', [
            'client_id' => $client->id,
            'company_id' => $companyId,
        ]);

        return $client;
    }

    protected function resolveCategory(int $companyId): Category
    {
        $categoryName = config('services.avero.default_category', 'Avero');

        $category = Category::firstOrCreate(
            [
                'company_id' => $companyId,
                'name' => $categoryName,
            ],
            [
                'description' => 'Productes importats des d\'Avero',
            ]
        );

        Log::debug('Resolved category for Avero import', ['category_id' => $category->id]);

        return $category;
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

        Log::debug('Resolved product for Avero import', [
            'product_id' => $product->id,
            'company_id' => $companyId,
            'category_id' => $categoryId,
        ]);

        return $product;
    }

    protected function ensureInvoicePdf(Invoice $invoice): array
    {
        $invoice->loadMissing('client', 'company', 'items.product');

        $disk = Storage::disk('public');
        $shouldGenerate = empty($invoice->pdf_path) || ! $disk->exists($invoice->pdf_path);

        if ($shouldGenerate) {
            Log::debug('Rendering invoice PDF for Avero import', ['invoice_id' => $invoice->id]);
            $pdf = Pdf::loadView('pdfs.invoice', [
                'invoice' => $invoice,
                'client' => $invoice->client,
                'company' => $invoice->company,
            ]);

            $fileName = sprintf(
                'invoices/%s-%s.pdf',
                Str::slug($invoice->number ?? $invoice->name ?? (string) $invoice->id),
                Str::uuid()
            );

            Log::debug('Storing invoice PDF for Avero import', ['invoice_id' => $invoice->id, 'file_name' => $fileName]);
            $disk->put($fileName, $pdf->output());

            $invoice->forceFill(['pdf_path' => $fileName])->save();
        }

        if (! $invoice->public_token) {
            $invoice->forceFill(['public_token' => (string) Str::uuid()])->save();
        }

        $storageUrl = $invoice->pdf_path ? $disk->url($invoice->pdf_path) : null;

        return [
            'public_url' => route('invoices.public.show', ['token' => $invoice->public_token]),
            'storage_url' => $storageUrl,
        ];
    }

    protected function findInvoiceByExternalReference(int $companyId, ?string $reference): ?Invoice
    {
        if (! $reference) {
            return null;
        }

        return Invoice::where('company_id', $companyId)
            ->where('external_reference', $reference)
            ->first();
    }

    protected function formatInvoiceResponse(Invoice $invoice, array $pdfUrls): array
    {
        return [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->number ?? $invoice->name,
            'invoice_date' => $invoice->date ? $invoice->date->toDateString() : null,
            'invoice_total' => (float) $invoice->total,
            'invoice_url' => $pdfUrls['public_url'] ?? null,
            'invoice_pdf_url' => $pdfUrls['storage_url'] ?? null,
        ];
    }

    protected function calculateLineTotal(float $quantity, float $unitPrice, float $discount): float
    {
        $lineBase = $quantity * $unitPrice;

        if ($discount > 0) {
            $lineBase -= ($lineBase * $discount) / 100;
        }

        $lineTotal = round($lineBase, 2);

        Log::debug('Calculated line total for Avero invoice item', [
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount' => $discount,
            'line_total' => $lineTotal,
        ]);

        return $lineTotal;
    }
}
