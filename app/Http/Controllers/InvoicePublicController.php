<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class InvoicePublicController extends Controller
{
    public function __invoke(string $token)
    {
        $invoice = Invoice::whereNotNull('public_token')
            ->where('public_token', $token)
            ->firstOrFail();

        if (! $invoice->pdf_path || ! Storage::disk('public')->exists($invoice->pdf_path)) {
            abort(404);
        }

        $fileName = sprintf('%s.pdf', $invoice->number ?? $invoice->name ?? $invoice->id);

        return new Response(
            Storage::disk('public')->get($invoice->pdf_path),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('inline; filename="%s"', $fileName),
            ]
        );
    }
}
