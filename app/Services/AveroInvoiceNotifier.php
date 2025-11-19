<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AveroInvoiceNotifier
{
    /**
     * Send the invoice to the configured Avero endpoint (if any) so they can
     * synchronise the information on their side.
     *
     * @return array{status: string, reason?: string, message?: string, status_code?: int, body?: mixed, response?: mixed}
     */
    public function notify(Invoice $invoice, string $pdfUrl): array
    {
        $endpoint = config('services.avero.webhook_url');

        if (! $endpoint) {
            Log::info('Avero webhook URL not configured. Skipping outbound notification.', [
                'invoice_id' => $invoice->id,
            ]);

            return [
                'status' => 'skipped',
                'reason' => 'missing_webhook',
            ];
        }

        $invoice->loadMissing(['client', 'company', 'items.product']);

        $payload = $this->buildPayload($invoice, $pdfUrl);

        $request = Http::timeout(15)->acceptJson();

        if ($token = config('services.avero.api_token')) {
            $request = $request->withToken($token);
        }

        try {
            $response = $request->post($endpoint, $payload);
        } catch (Throwable $exception) {
            Log::error('Failed to notify Avero about invoice.', [
                'invoice_id' => $invoice->id,
                'message' => $exception->getMessage(),
            ]);

            return [
                'status' => 'failed',
                'reason' => 'exception',
                'message' => $exception->getMessage(),
            ];
        }

        if ($response->failed()) {
            Log::error('Avero responded with an error when receiving the invoice.', [
                'invoice_id' => $invoice->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'status' => 'failed',
                'reason' => 'http_error',
                'status_code' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ];
        }

        $responseData = $response->json();

        Log::info('Invoice synchronised with Avero successfully.', [
            'invoice_id' => $invoice->id,
            'endpoint' => $endpoint,
        ]);

        return [
            'status' => 'sent',
            'response' => $responseData,
        ];
    }

    /**
     * Build the payload expected by the external service.
     */
    protected function buildPayload(Invoice $invoice, string $pdfUrl): array
    {
        $client = $invoice->client;
        $company = $invoice->company;

        return [
            'invoice' => [
                'id' => $invoice->id,
                'number' => $invoice->name,
                'date' => $this->formatDate($invoice->date),
                'due_date' => $this->formatDate($invoice->due_date),
                'state' => $invoice->state,
                'notes' => $invoice->notes,
                'totals' => [
                    'base_imponible' => (float) $invoice->base_imponible,
                    'iva_rate' => (float) $invoice->iva,
                    'total_iva' => (float) $invoice->monto_iva,
                    'irpf_tax' => (float) ($invoice->irpf_tax ?? 0),
                    'total_irpf' => (float) ($invoice->total_irpf ?? 0),
                    'total' => (float) $invoice->total,
                ],
            ],
            'client' => $client ? [
                'id' => $client->id,
                'name' => $client->name,
                'nif' => $client->nif,
                'email' => $client->email,
                'phone' => $client->phone,
                'address' => $client->address,
            ] : null,
            'company' => $company ? [
                'id' => $company->id,
                'name' => $company->name,
                'nif' => $company->nif,
                'email' => $company->email,
                'phone' => $company->phone,
                'address' => $company->address,
            ] : null,
            'items' => $invoice->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => optional($item->product)->name,
                    'description' => $item->getAttribute('description') ?? optional($item->product)->description,
                    'quantity' => (float) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'discount' => (float) ($item->discount ?? 0),
                    'iva' => (float) ($item->iva ?? 0),
                    'total' => (float) $item->total,
                ];
            })->toArray(),
            'pdf_url' => $pdfUrl,
        ];
    }

    protected function formatDate(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->toDateString();
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (Throwable) {
            return (string) $value;
        }
    }
}
