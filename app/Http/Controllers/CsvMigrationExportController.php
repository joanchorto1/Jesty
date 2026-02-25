<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvMigrationExportController extends Controller
{
    public function clients(): StreamedResponse
    {
        $companyId = Auth::user()->company_id;
        $clients = Client::where('company_id', $companyId)->orderBy('id')->get();

        $headers = [
            'migration_version',
            'entity',
            'legacy_id',
            'name',
            'nif',
            'bank',
            'phone',
            'email',
            'address',
            'created_at',
            'updated_at',
        ];

        return $this->streamCsv('jesty_clients.csv', $headers, function ($handle) use ($clients) {
            foreach ($clients as $client) {
                fputcsv($handle, [
                    '1.0',
                    'clients',
                    (string) $client->id,
                    (string) ($client->name ?? ''),
                    (string) ($client->nif ?? ''),
                    (string) ($client->bank ?? ''),
                    (string) ($client->phone ?? ''),
                    (string) ($client->email ?? ''),
                    (string) ($client->address ?? ''),
                    optional($client->created_at)->toDateTimeString(),
                    optional($client->updated_at)->toDateTimeString(),
                ]);
            }
        });
    }

    public function products(): StreamedResponse
    {
        $companyId = Auth::user()->company_id;
        $products = Product::where('company_id', $companyId)->orderBy('id')->get();

        $headers = [
            'migration_version',
            'entity',
            'legacy_id',
            'name',
            'description',
            'price',
            'iva',
            'stock',
            'category_id',
            'supplier_id',
            'cost_price',
            'is_stackable',
            'disabled',
            'codebar',
            'created_at',
            'updated_at',
        ];

        return $this->streamCsv('jesty_products.csv', $headers, function ($handle) use ($products) {
            foreach ($products as $product) {
                fputcsv($handle, [
                    '1.0',
                    'products',
                    (string) $product->id,
                    (string) ($product->name ?? ''),
                    (string) ($product->description ?? ''),
                    $this->decimal($product->price),
                    $this->decimal($product->iva),
                    (string) ($product->stock ?? '0'),
                    (string) ($product->category_id ?? ''),
                    (string) ($product->supplier_id ?? ''),
                    $this->decimal($product->cost_price),
                    $product->is_stackable ? '1' : '0',
                    $product->disabled ? '1' : '0',
                    (string) ($product->codebar ?? ''),
                    optional($product->created_at)->toDateTimeString(),
                    optional($product->updated_at)->toDateTimeString(),
                ]);
            }
        });
    }

    public function budgets(): StreamedResponse
    {
        $companyId = Auth::user()->company_id;
        $budgets = Budget::with(['client', 'items.product'])
            ->where('company_id', $companyId)
            ->orderBy('id')
            ->get();

        $headers = [
            'migration_version',
            'entity',
            'legacy_id',
            'client_legacy_id',
            'client_name',
            'client_nif',
            'client_email',
            'date',
            'name',
            'state',
            'base_imponible',
            'iva',
            'monto_iva',
            'total',
            'items_json',
            'created_at',
            'updated_at',
        ];

        return $this->streamCsv('jesty_budgets.csv', $headers, function ($handle) use ($budgets) {
            foreach ($budgets as $budget) {
                $items = $budget->items->map(function ($item) {
                    return [
                        'legacy_id' => $item->id,
                        'product_legacy_id' => $item->product_id,
                        'product_name' => $item->product?->name,
                        'product_description' => $item->product?->description,
                        'quantity' => $this->decimal($item->quantity),
                        'unit_price' => $this->decimal($item->unit_price),
                        'discount' => $this->decimal($item->discount),
                        'iva' => $this->decimal($item->iva),
                        'total' => $this->decimal($item->total),
                    ];
                })->values()->toJson(JSON_UNESCAPED_UNICODE);

                fputcsv($handle, [
                    '1.0',
                    'budgets',
                    (string) $budget->id,
                    (string) ($budget->client_id ?? ''),
                    (string) ($budget->client?->name ?? ''),
                    (string) ($budget->client?->nif ?? ''),
                    (string) ($budget->client?->email ?? ''),
                    optional($budget->date)->format('Y-m-d'),
                    (string) ($budget->name ?? ''),
                    (string) ($budget->state ?? ''),
                    $this->decimal($budget->base_imponible),
                    $this->decimal($budget->iva),
                    $this->decimal($budget->monto_iva),
                    $this->decimal($budget->total),
                    $items,
                    optional($budget->created_at)->toDateTimeString(),
                    optional($budget->updated_at)->toDateTimeString(),
                ]);
            }
        });
    }

    public function invoices(): StreamedResponse
    {
        $companyId = Auth::user()->company_id;
        $invoices = Invoice::with(['client', 'items.product'])
            ->where('company_id', $companyId)
            ->orderBy('id')
            ->get();

        $headers = [
            'migration_version',
            'entity',
            'legacy_id',
            'client_legacy_id',
            'client_name',
            'client_nif',
            'client_email',
            'date',
            'due_date',
            'name',
            'number',
            'external_reference',
            'state',
            'base_imponible',
            'iva',
            'monto_iva',
            'irpf_tax',
            'total_irpf',
            'total',
            'notes',
            'items_json',
            'created_at',
            'updated_at',
        ];

        return $this->streamCsv('jesty_invoices.csv', $headers, function ($handle) use ($invoices) {
            foreach ($invoices as $invoice) {
                $items = $invoice->items->map(function ($item) {
                    return [
                        'legacy_id' => $item->id,
                        'product_legacy_id' => $item->product_id,
                        'product_name' => $item->product?->name,
                        'product_description' => $item->product?->description,
                        'quantity' => $this->decimal($item->quantity),
                        'unit_price' => $this->decimal($item->unit_price),
                        'discount' => $this->decimal($item->discount),
                        'iva' => $this->decimal($item->iva),
                        'total' => $this->decimal($item->total),
                    ];
                })->values()->toJson(JSON_UNESCAPED_UNICODE);

                fputcsv($handle, [
                    '1.0',
                    'invoices',
                    (string) $invoice->id,
                    (string) ($invoice->client_id ?? ''),
                    (string) ($invoice->client?->name ?? ''),
                    (string) ($invoice->client?->nif ?? ''),
                    (string) ($invoice->client?->email ?? ''),
                    optional($invoice->date)->format('Y-m-d'),
                    optional($invoice->due_date)->format('Y-m-d'),
                    (string) ($invoice->name ?? ''),
                    (string) ($invoice->number ?? ''),
                    (string) ($invoice->external_reference ?? ''),
                    (string) ($invoice->state ?? ''),
                    $this->decimal($invoice->base_imponible),
                    $this->decimal($invoice->iva),
                    $this->decimal($invoice->monto_iva),
                    $this->decimal($invoice->irpf_tax),
                    $this->decimal($invoice->total_irpf),
                    $this->decimal($invoice->total),
                    (string) ($invoice->notes ?? ''),
                    $items,
                    optional($invoice->created_at)->toDateTimeString(),
                    optional($invoice->updated_at)->toDateTimeString(),
                ]);
            }
        });
    }

    private function streamCsv(string $filename, array $headers, callable $writer): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $writer) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            $writer($handle);
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function decimal(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '0.00';
        }

        return number_format((float) $value, 2, '.', '');
    }
}
