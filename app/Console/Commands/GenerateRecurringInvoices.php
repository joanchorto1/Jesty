<?php

namespace App\Console\Commands;

use App\Models\RecurringInvoice;
use App\Services\RecurringInvoiceGenerator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRecurringInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring-invoices:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera facturas para las plantillas recurrentes con ejecución pendiente.';

    public function handle(RecurringInvoiceGenerator $generator): int
    {
        $now = Carbon::now();

        $templates = RecurringInvoice::with('items')
            ->where('active', true)
            ->where('status', 'active')
            ->whereNotNull('next_run_at')
            ->where('next_run_at', '<=', $now)
            ->get();

        $this->info(sprintf('Encontradas %d plantillas pendientes de ejecución.', $templates->count()));

        $generated = 0;

        foreach ($templates as $template) {
            try {
                $issueDate = $template->next_run_at ? $template->next_run_at->copy() : $now;
                $invoice = $generator->generate($template, $issueDate);
                $generated++;
                $this->info(sprintf('Factura %s generada para la plantilla #%d.', $invoice->name, $template->id));
            } catch (\Throwable $exception) {
                $this->error(sprintf(
                    'No se pudo generar la factura para la plantilla #%d: %s',
                    $template->id,
                    $exception->getMessage()
                ));
            }
        }

        $this->info(sprintf('Proceso completado. %d factura(s) generadas.', $generated));

        return self::SUCCESS;
    }
}
