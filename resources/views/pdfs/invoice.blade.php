<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $invoice->number ?? $invoice->name ?? $invoice->id }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 28px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
        }

        .page {
            max-width: 960px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 20px 55px rgba(15, 23, 42, 0.12);
        }

        header {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 16px;
        }

        h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
            color: #0f172a;
        }

        .meta {
            margin-top: 12px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            font-size: 12px;
            color: #475569;
        }

        .meta span {
            display: inline-flex;
            gap: 6px;
            align-items: center;
        }

        .grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            margin-top: 32px;
        }

        .card {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 18px 20px;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);
        }

        .eyebrow {
            margin: 0;
            font-size: 10px;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: #1d4ed8;
            font-weight: 700;
        }

        .card ul {
            list-style: none;
            padding: 0;
            margin: 12px 0 0;
            color: #475569;
            font-size: 13px;
        }

        .card ul li + li {
            margin-top: 4px;
        }

        .card .title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 28px;
            border-radius: 16px;
            overflow: hidden;
            font-size: 12px;
        }

        thead {
            background: #e2e8f0;
            color: #1d4ed8;
            text-transform: uppercase;
            letter-spacing: 0.2em;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            font-size: 11px;
            font-weight: 700;
        }

        tbody tr {
            border-top: 1px solid #e2e8f0;
        }

        tbody tr:first-child {
            border-top: none;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        td {
            color: #334155;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #94a3b8;
        }

        .subtotal {
            font-weight: 700;
            color: #0f172a;
        }

        .totals-grid {
            margin-top: 28px;
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .totals-card {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);
            height: 100%;
        }

        .totals-card dl {
            margin: 16px 0 0;
            padding: 0;
        }

        .totals-card dt,
        .totals-card dd {
            font-size: 13px;
            margin: 0;
            padding: 8px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
        }

        .totals-card dt:last-of-type,
        .totals-card dd:last-of-type {
            border-bottom: none;
        }

        .totals-card dd {
            font-weight: 700;
            color: #0f172a;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 12px;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
            font-size: 11px;
        }

        .notes {
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            padding: 18px 20px;
            color: #475569;
            font-size: 13px;
            line-height: 1.5;
        }

        .notes h3 {
            margin: 0 0 8px;
            font-size: 11px;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        footer {
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
        }

        .no-data {
            padding: 18px;
            text-align: center;
            color: #94a3b8;
        }
    </style>
</head>
<body>
@php
    $items = $invoice->items ?? [];

    $formatCurrency = fn($value) => '$' . number_format((float) $value, 2, '.', ',');
    $formatRate = fn($value) => number_format((float) $value, 2) . '%';

    $taxBreakdown = [];
    foreach ($items as $item) {
        $base = (float) ($item->total ?? 0);
        $rate = (float) ($item->iva ?? 0);

        if ($base <= 0) {
            continue;
        }

        $tax = round($base * $rate / 100, 2);
        $key = number_format($rate, 2);

        if (! isset($taxBreakdown[$key])) {
            $taxBreakdown[$key] = [
                'rate' => $rate,
                'base' => 0,
                'tax' => 0,
            ];
        }

        $taxBreakdown[$key]['base'] += $base;
        $taxBreakdown[$key]['tax'] += $tax;
    }

    ksort($taxBreakdown, SORT_NUMERIC);
    $taxBreakdown = array_values($taxBreakdown);

    $irpfRate = (float) ($invoice->irpf_tax ?? 0);
    $retencionIrpf = $invoice->total_irpf ?? ($irpfRate > 0 ? round(($invoice->base_imponible ?? 0) * $irpfRate / 100, 2) : 0);
    $totalFinal = ($invoice->base_imponible ?? 0) + ($invoice->monto_iva ?? 0) - $retencionIrpf;
@endphp

<div class="page">
    <header>
        <h1>Factura</h1>
        <div class="meta">
            <span><strong>Data:</strong> {{ $invoice->date ?? '—' }}</span>
            @if($invoice->due_date)
                <span><strong>Venciment:</strong> {{ $invoice->due_date }}</span>
            @endif
            @if($invoice->name || $invoice->number)
                <span><strong>Núm. de factura:</strong> {{ $invoice->name ?? $invoice->number }}</span>
            @endif
        </div>
    </header>

    <section class="grid">
        <article class="card">
            <p class="eyebrow">Detalls de l'empresa</p>
            <h2 class="title">{{ $company->name }}</h2>
            <ul>
                @if($company->address)<li>{{ $company->address }}</li>@endif
                @if($company->phone)<li>Tel. {{ $company->phone }}</li>@endif
                @if($company->email)<li>{{ $company->email }}</li>@endif
                @if($company->nif)<li>NIF: {{ $company->nif }}</li>@endif
            </ul>
        </article>
        <article class="card">
            <p class="eyebrow">Detalls del client</p>
            <h2 class="title">{{ $client->name }}</h2>
            <ul>
                @if($client->address)<li>{{ $client->address }}</li>@endif
                @if($client->phone)<li>Tel. {{ $client->phone }}</li>@endif
                @if($client->email)<li>{{ $client->email }}</li>@endif
                @if($client->nif)<li>NIF: {{ $client->nif }}</li>@endif
            </ul>
        </article>
    </section>

    <section>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 28px;">
            <p class="eyebrow" style="color: #64748b;">Detall de línies</p>
            <span class="badge">{{ count($items) }} productes</span>
        </div>

        <div style="border: 1px solid #e2e8f0; border-radius: 18px; overflow: hidden; margin-top: 12px;">
            <table>
                <thead>
                    <tr>
                        <th>Producte</th>
                        <th class="text-center">Quantitat</th>
                        <th class="text-center">Preu unitari</th>
                        <th class="text-center">Descompte</th>
                        <th class="text-center">IVA</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: #0f172a;">{{ optional($item->product)->name ?? '—' }}</div>
                            @if(!empty($item->description))
                                <div class="muted" style="font-size: 11px; margin-top: 4px;">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">{{ $formatCurrency($item->unit_price) }}</td>
                        <td class="text-center">
                            @if(($item->discount ?? 0) > 0)
                                {{ $formatRate($item->discount) }}
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div style="font-weight: 600; color: #0f172a;">{{ $formatRate($item->iva) }}</div>
                            <div class="muted" style="font-size: 11px;">{{ $formatCurrency(($item->total ?? 0) * ($item->iva ?? 0) / 100) }}</div>
                        </td>
                        <td class="text-right subtotal">{{ $formatCurrency($item->total) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="no-data">Encara no hi ha línies associades a aquesta factura.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="totals-grid">
        <article class="totals-card">
            <p class="eyebrow" style="color: #64748b;">Resum econòmic</p>
            <dl>
                <dt>Base imposable</dt>
                <dd>{{ $formatCurrency($invoice->base_imponible ?? 0) }}</dd>

                <dt>IVA desglossat</dt>
                <dd style="border-bottom: none; justify-content: flex-start; display: block; padding: 0;">
                    @if(count($taxBreakdown))
                        @foreach($taxBreakdown as $tier)
                            <div style="margin: 10px 0; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc;">
                                <div style="display: flex; justify-content: space-between; font-weight: 700; color: #0f172a;">
                                    <span>IVA {{ $formatRate($tier['rate']) }}</span>
                                    <span>{{ $formatCurrency($tier['tax']) }}</span>
                                </div>
                                <div class="muted" style="font-size: 11px; margin-top: 4px;">Base: {{ $formatCurrency($tier['base']) }}</div>
                            </div>
                        @endforeach
                    @else
                        <p class="muted" style="padding: 10px 0 0 0;">Sense IVA aplicat a les línies actuals.</p>
                    @endif
                </dd>

                @if(($retencionIrpf ?? 0) > 0)
                    <dt>Retenció IRPF ({{ $formatRate($irpfRate) }})</dt>
                    <dd style="color: #e11d48;">− {{ $formatCurrency($retencionIrpf) }}</dd>
                @endif

                <dt style="border-bottom: none; font-size: 15px; font-weight: 700;">Total a pagar</dt>
                <dd style="border-bottom: none; font-size: 15px;">{{ $formatCurrency($invoice->total ?? $totalFinal) }}</dd>
            </dl>
        </article>

        <article class="notes">
            <h3>Observacions</h3>
            @if($invoice->notes)
                <p style="margin: 0;">{{ $invoice->notes }}</p>
            @else
                <p style="margin: 0;" class="muted">No hi ha observacions addicionals per a aquesta factura.</p>
            @endif
        </article>
    </section>

    <footer>
        <p>Gràcies per confiar en nosaltres.</p>
        <p>© {{ date('Y') }} {{ $company->name }}</p>
    </footer>
</div>
</body>
</html>
