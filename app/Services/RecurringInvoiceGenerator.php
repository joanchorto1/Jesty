<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\RecurringInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecurringInvoiceGenerator
{
    public function generate(RecurringInvoice $template, ?Carbon $issueDate = null): Invoice
    {
        $issueDate = $issueDate ? $issueDate->copy() : Carbon::now();

        return DB::transaction(function () use ($template, $issueDate) {
            $template->loadMissing('items');

            if ($template->items->isEmpty()) {
                throw new \RuntimeException('Recurring invoice templates must contain at least one item.');
            }

            $itemsForTotals = $template->items->map(function ($item) {
                return [
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'iva' => $item->iva,
                ];
            })->toArray();

            $totals = DocumentTotalsCalculator::calculate($itemsForTotals);

            $invoice = Invoice::create([
                'recurring_invoice_id' => $template->id,
                'company_id' => $template->company_id,
                'client_id' => $template->client_id,
                'date' => $issueDate->toDateString(),
                'state' => $template->invoice_state,
                'base_imponible' => $totals['base'],
                'monto_iva' => $totals['tax'],
                'total' => $totals['total'],
                'iva' => $totals['effectiveRate'],
                'name' => DocumentNumberGenerator::generate(
                    Invoice::class,
                    'name',
                    'FA',
                    $template->company_id,
                    $issueDate
                ),
            ]);

            foreach ($template->items as $item) {
                $lineBase = $item->quantity * $item->unit_price;
                if ($item->discount > 0) {
                    $lineBase -= ($lineBase * $item->discount) / 100;
                }
                $lineBase = round($lineBase, 2);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'total' => $lineBase,
                    'iva' => $item->iva,
                ]);
            }

            $template->expected_total = $totals['total'];
            $template->last_run_at = $issueDate;
            $template->markNextRunFrom($issueDate);

            if ($template->ends_at && $template->next_run_at && $template->next_run_at->greaterThan($template->ends_at)) {
                $template->active = false;
                $template->status = 'completed';
                $template->next_run_at = null;
            }

            $template->save();

            return $invoice;
        });
    }
}
