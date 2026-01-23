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
            padding: 18px;
            font-family: 'Inter', 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
            background: #f3f4f6;
            color: #111827;
            line-height: 1.45;
            font-size: 11px;
        }

        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 400;
            src: local('Inter'), local('Inter-Regular');
        }

        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 600;
            src: local('Inter'), local('Inter-SemiBold');
        }

        @page {
            margin: 16px;
        }

        .page {
            max-width: 960px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            page-break-inside: avoid;
        }

        header {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 12px;
            gap: 8px;
        }

        h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }

        .meta {
            margin-top: 12px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            font-size: 10px;
            color: #6b7280;
        }

        .meta span {
            display: inline-flex;
            gap: 6px;
            align-items: center;
        }

        .grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            margin-top: 16px;
            page-break-inside: avoid;
        }

        .card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 16px;
            page-break-inside: avoid;
        }

        .eyebrow {
            margin: 0;
            font-size: 9px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
        }

        .card ul {
            list-style: none;
            padding: 0;
            margin: 8px 0 0;
            color: #6b7280;
            font-size: 10px;
        }

        .card ul li + li {
            margin-top: 4px;
        }

        .card .title {
            font-size: 12px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            border-radius: 12px;
            overflow: hidden;
            font-size: 10px;
            page-break-inside: auto;
        }

        thead {
            background: #f3f4f6;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            display: table-header-group;
        }

        th, td {
            padding: 8px 10px;
            text-align: left;
        }

        th {
            font-size: 10px;
            font-weight: 600;
        }

        tbody tr {
            border-top: 1px solid #e5e7eb;
            page-break-inside: avoid;
        }

        tbody tr:first-child {
            border-top: none;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        td {
            color: #374151;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #9ca3af;
        }

        .subtotal {
            font-weight: 600;
            color: #111827;
        }

        .totals-grid {
            margin-top: 16px;
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            page-break-inside: avoid;
        }

        .totals-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px 16px;
            height: 100%;
            page-break-inside: avoid;
        }

        .totals-card dl {
            margin: 10px 0 0;
            padding: 0;
        }

        .totals-card dt,
        .totals-card dd {
            font-size: 10px;
            margin: 0;
            padding: 6px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e5e7eb;
        }

        .totals-card dt:last-of-type,
        .totals-card dd:last-of-type {
            border-bottom: none;
        }

        .totals-card dd {
            font-weight: 600;
            color: #111827;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #111827;
            font-weight: 600;
            font-size: 9px;
        }

        .notes {
            border: 1px dashed #e5e7eb;
            border-radius: 12px;
            padding: 12px 16px;
            color: #6b7280;
            font-size: 10px;
            line-height: 1.5;
            page-break-inside: avoid;
        }

        .notes h3 {
            margin: 0 0 8px;
            font-size: 10px;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: #9ca3af;
        }

        footer {
            margin-top: 16px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }

        .no-data {
            padding: 18px;
            text-align: center;
            color: #9ca3af;
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
                <span><strong>Núm.:</strong> {{ $invoice->name ?? $invoice->number }}</span>
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
        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 14px;">
            <p class="eyebrow" style="color: #9ca3af;">Detall de línies</p>
            <span class="badge">{{ count($items) }} productes</span>
        </div>

        <div style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; margin-top: 8px;">
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
                            <div style="font-weight: 600; color: #111827;">{{ optional($item->product)->name ?? '—' }}</div>
                            @if(!empty($item->description))
                                <div class="muted" style="font-size: 9px; margin-top: 3px;">{{ $item->description }}</div>
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
                            <div style="font-weight: 600; color: #111827;">{{ $formatRate($item->iva) }}</div>
                            <div class="muted" style="font-size: 9px;">{{ $formatCurrency(($item->total ?? 0) * ($item->iva ?? 0) / 100) }}</div>
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
            <p class="eyebrow" style="color: #9ca3af;">Resum econòmic</p>
            <dl>
                <dt>Base imposable</dt>
                <dd>{{ $formatCurrency($invoice->base_imponible ?? 0) }}</dd>

                <dt>IVA desglossat</dt>
                <dd style="border-bottom: none; justify-content: flex-start; display: block; padding: 4px 0 0;">
                    @if(count($taxBreakdown))
                        <ul style="list-style: none; margin: 0; padding: 0;">
                            @foreach($taxBreakdown as $tier)
                                <li style="display: flex; justify-content: space-between; gap: 8px; padding: 4px 0;">
                                    <span>IVA {{ $formatRate($tier['rate']) }} · Base {{ $formatCurrency($tier['base']) }}</span>
                                    <span style="font-weight: 600; color: #111827;">{{ $formatCurrency($tier['tax']) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="muted" style="padding: 4px 0 0 0;">Sense IVA aplicat a les línies actuals.</p>
                    @endif
                </dd>

                @if(($retencionIrpf ?? 0) > 0)
                    <dt>Retenció IRPF ({{ $formatRate($irpfRate) }})</dt>
                    <dd style="color: #9ca3af;">− {{ $formatCurrency($retencionIrpf) }}</dd>
                @endif

                <dt style="border-bottom: none; font-size: 12px; font-weight: 600;">Total a pagar</dt>
                <dd style="border-bottom: none; font-size: 12px;">{{ $formatCurrency($invoice->total ?? $totalFinal) }}</dd>
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
