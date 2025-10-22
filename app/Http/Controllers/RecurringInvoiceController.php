<?php

namespace App\Http\Controllers;

use App\Models\RecurringInvoice;
use App\Models\RecurringInvoiceItem;
use App\Services\DocumentTotalsCalculator;
use App\Services\RecurringInvoiceGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RecurringInvoiceController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;

        $templates = RecurringInvoice::with(['client', 'items', 'generatedInvoices' => function ($query) {
            $query->latest('date')->limit(5);
        }])
            ->where('company_id', $companyId)
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Invoices/Recurring/Index', [
            'templates' => $templates,
            'frequencyUnits' => ['day', 'week', 'month', 'year'],
            'statusOptions' => ['draft', 'active', 'paused', 'completed', 'archived'],
            'defaultInvoiceState' => 'pending',
        ]);
    }

    public function store(Request $request, RecurringInvoiceGenerator $generator)
    {
        $validated = $this->validateTemplate($request);

        [$template, $shouldGenerate] = $this->persistTemplate($validated, $request->boolean('generate_now'));

        if ($shouldGenerate) {
            $generator->generate($template, Carbon::parse($validated['first_issue_on']));
        }

        app('App\\Http\\Controllers\\UserNotificationController')->createNotification(
            'Nueva factura recurrente',
            'Se ha creado una plantilla de facturación recurrente',
            'Facturación'
        );

        return Inertia::location(route('recurring-invoices.index'));
    }

    public function update(Request $request, RecurringInvoice $recurringInvoice, RecurringInvoiceGenerator $generator)
    {
        $this->authorizeTemplate($recurringInvoice);

        $validated = $this->validateTemplate($request);

        $recurringInvoice->items()->delete();

        [$template, $shouldGenerate] = $this->persistTemplate($validated, $request->boolean('generate_now'), $recurringInvoice);

        if ($shouldGenerate) {
            $generator->generate($template, Carbon::parse($validated['first_issue_on']));
        }

        app('App\\Http\\Controllers\\UserNotificationController')->createNotification(
            'Plantilla recurrente actualizada',
            'Se han actualizado los parámetros de una factura recurrente',
            'Facturación'
        );

        return Inertia::location(route('recurring-invoices.index'));
    }

    public function destroy(RecurringInvoice $recurringInvoice)
    {
        $this->authorizeTemplate($recurringInvoice);

        $recurringInvoice->update([
            'active' => false,
            'status' => 'archived',
            'next_run_at' => null,
        ]);

        app('App\\Http\\Controllers\\UserNotificationController')->createNotification(
            'Plantilla recurrente archivada',
            'Se ha archivado una plantilla de facturación recurrente',
            'Facturación'
        );

        return Inertia::location(route('recurring-invoices.index'));
    }

    public function status(Request $request, RecurringInvoice $recurringInvoice)
    {
        $this->authorizeTemplate($recurringInvoice);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'paused'])],
        ]);

        $recurringInvoice->update([
            'status' => $validated['status'],
            'active' => $validated['status'] === 'active',
        ]);

        return response()->json([
            'status' => 'ok',
            'template' => $recurringInvoice->fresh(),
        ]);
    }

    private function validateTemplate(Request $request): array
    {
        return $request->validate([
            'client_id' => 'required|exists:clients,id',
            'frequency_unit' => 'required|in:day,week,month,year',
            'frequency_interval' => 'required|integer|min:1',
            'first_issue_on' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:first_issue_on',
            'status' => 'required|string|in:draft,active,paused,completed',
            'invoice_state' => 'required|string|in:pending,paid,cancelled',
            'generate_now' => 'sometimes|boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.iva' => 'nullable|numeric|min:0',
        ]);
    }

    private function persistTemplate(array $validated, bool $generateNow, ?RecurringInvoice $existing = null): array
    {
        $companyId = Auth::user()->company_id;
        $items = collect($validated['items'])->map(function (array $item) {
            return [
                'product_id' => $item['product_id'],
                'quantity' => (int) $item['quantity'],
                'unit_price' => (float) $item['unit_price'],
                'discount' => (float) ($item['discount'] ?? 0),
                'iva' => (float) ($item['iva'] ?? 0),
            ];
        });

        $totals = DocumentTotalsCalculator::calculate($items->toArray());

        $firstIssue = Carbon::parse($validated['first_issue_on']);
        $endsAt = isset($validated['ends_at']) ? Carbon::parse($validated['ends_at']) : null;

        $data = [
            'company_id' => $companyId,
            'client_id' => $validated['client_id'],
            'status' => $validated['status'],
            'invoice_state' => $validated['invoice_state'],
            'frequency_unit' => $validated['frequency_unit'],
            'frequency_interval' => (int) $validated['frequency_interval'],
            'first_issue_on' => $firstIssue->toDateString(),
            'next_run_at' => $firstIssue->copy()->startOfDay(),
            'ends_at' => $endsAt,
            'expected_total' => $totals['total'],
            'active' => $validated['status'] === 'active',
        ];

        if ($existing) {
            $existing->update($data);
            $template = $existing;
        } else {
            $template = RecurringInvoice::create($data);
        }

        foreach ($items as $item) {
            RecurringInvoiceItem::create([
                'recurring_invoice_id' => $template->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'],
                'iva' => $item['iva'],
            ]);
        }

        return [$template->fresh(['items']), $generateNow && $validated['status'] === 'active'];
    }

    private function authorizeTemplate(RecurringInvoice $recurringInvoice): void
    {
        if ($recurringInvoice->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}
