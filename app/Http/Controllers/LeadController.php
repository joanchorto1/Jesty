<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::where('company_id', Auth::user()->company_id)->get();

        return Inertia::render('Leads/Index', [
            'leads' => $leads,
            'importSummary' => session('importSummary'),
            'importError' => session('importError'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Leads/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
        ]);

        $company = Company::where('id',Auth::user()->company_id)->first();
        $lead = $request->all();
        $lead['company_id'] = $company->id;




        Lead::create($lead);


return Inertia::location(route('leads.index'));
    }

    public function show(Lead $lead)
    {
        $opportunities = Opportunity::where('lead_id', $lead->id)->get();
        return Inertia::render('Leads/Show', ['lead' => $lead, 'opportunities' => $opportunities]);
    }

    public function edit(Lead $lead)
    {
        return Inertia::render('Leads/Edit', ['lead' => $lead]);
    }

    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email,' . $lead->id,
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
        ]);

        $lead->update($request->all());
        return Inertia::location(route('leads.index'));
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return Inertia::location(route('leads.index'));
    }

    public function convertToClient(Lead $lead)
    {
        $client= [];
        $client['name'] = $lead->name;
        $client['phone'] = $lead->phone;
        $client['email'] = $lead->email;
        $client['address'] = '.';
        $client['nif']= '.';
        $client['bank']= '.';
        $client['company_id'] = $lead->company_id;

        $client=Client::create($client);
        $lead->delete();
        return Inertia::location(route('clients.show', $client->id));
    }

    public function exportCsv()
    {
        $companyId = Auth::user()->company_id;
        $leads = Lead::where('company_id', $companyId)->get($this->exportableColumns());

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="leads-' . now()->format('YmdHis') . '.csv"',
        ];

        $columns = $this->columnLabels();

        $callback = static function () use ($columns, $leads) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, array_values($columns));

            foreach ($leads as $lead) {
                $row = [];
                foreach (array_keys($columns) as $key) {
                    $row[] = $lead->{$key} ?? '';
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, 'leads-' . now()->format('YmdHis') . '.csv', $headers);
    }

    public function exportExcel()
    {
        $companyId = Auth::user()->company_id;
        $leads = Lead::where('company_id', $companyId)->get($this->exportableColumns());

        $columns = $this->columnLabels();
        $filename = 'leads-' . now()->format('YmdHis') . '.xls';

        $content = '<table border="1"><thead><tr>';
        foreach ($columns as $label) {
            $content .= '<th>' . e($label) . '</th>';
        }
        $content .= '</tr></thead><tbody>';

        foreach ($leads as $lead) {
            $content .= '<tr>';
            foreach (array_keys($columns) as $key) {
                $value = $lead->{$key} ?? '';
                $content .= '<td>' . e($value) . '</td>';
            }
            $content .= '</tr>';
        }

        $content .= '</tbody></table>';

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'records' => 'required|array',
            'records.*.name' => 'nullable|string|max:255',
            'records.*.company_name' => 'nullable|string|max:255',
            'records.*.email' => 'nullable|email',
            'records.*.phone' => 'nullable|string|max:50',
            'records.*.position' => 'nullable|string|max:255',
            'records.*.source' => 'nullable|string|max:255',
            'records.*.status' => 'nullable|string|max:255',
        ]);

        $companyId = Auth::user()->company_id;

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($validated['records'] as $record) {
            $cleanRecord = $this->sanitizeRecord($record);

            if (empty($cleanRecord) || (!$cleanRecord['name'] && !$cleanRecord['email'])) {
                $skipped++;
                continue;
            }

            $cleanRecord['company_id'] = $companyId;

            if (!empty($cleanRecord['email'])) {
                $existing = Lead::where('company_id', $companyId)
                    ->where('email', $cleanRecord['email'])
                    ->first();

                if ($existing) {
                    $existing->update($cleanRecord);
                    $updated++;
                    continue;
                }
            }

            Lead::create($cleanRecord);
            $created++;
        }

        if ($created === 0 && $updated === 0) {
            return redirect()->route('leads.index')->with('importError', 'No se encontraron registros válidos para importar.');
        }

        return redirect()->route('leads.index')->with('importSummary', [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ]);
    }

    private function sanitizeRecord(array $record): array
    {
        $clean = [];

        foreach ($this->exportableColumns() as $column) {
            $value = $record[$column] ?? null;

            if (is_string($value)) {
                $value = trim($value);
            }

            $clean[$column] = $value === '' ? null : $value;
        }

        return $clean;
    }

    private function columnLabels(): array
    {
        return [
            'name' => 'Nombre',
            'company_name' => 'Empresa',
            'email' => 'Correo electrónico',
            'phone' => 'Teléfono',
            'position' => 'Cargo',
            'source' => 'Fuente',
            'status' => 'Estado',
        ];
    }

    private function exportableColumns(): array
    {
        return array_keys($this->columnLabels());
    }
}
