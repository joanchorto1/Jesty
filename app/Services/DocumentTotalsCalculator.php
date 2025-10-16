<?php

namespace App\Services;

class DocumentTotalsCalculator
{
    /**
     * Calculate base amount, tax amount and total from a collection of items.
     *
     * Each item must contain quantity, unit_price, optional discount and iva rate.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array{
     *     base: float,
     *     tax: float,
     *     total: float,
     *     effectiveRate: float,
     *     taxBreakdown: array<int, array{rate: float, base: float, tax: float}>
     * }
     */
    public static function calculate(array $items): array
    {
        $base = 0.0;
        $tax = 0.0;
        $breakdown = [];

        foreach ($items as $item) {
            $quantity = self::toFloat($item['quantity'] ?? 0);
            $unitPrice = self::toFloat($item['unit_price'] ?? 0);
            $discount = self::toFloat($item['discount'] ?? 0);
            $ivaRate = self::toFloat($item['iva'] ?? 0);

            $lineBase = $quantity * $unitPrice;
            if ($discount > 0) {
                $lineBase -= ($lineBase * $discount) / 100;
            }

            $lineBase = round($lineBase, 2);
            $lineTax = round(($lineBase * $ivaRate) / 100, 2);

            $base += $lineBase;
            $tax += $lineTax;

            if ($lineBase > 0 || $lineTax > 0) {
                $rateKey = number_format($ivaRate, 2, '.', '');

                if (! isset($breakdown[$rateKey])) {
                    $breakdown[$rateKey] = [
                        'rate' => round($ivaRate, 2),
                        'base' => 0.0,
                        'tax' => 0.0,
                    ];
                }

                $breakdown[$rateKey]['base'] += $lineBase;
                $breakdown[$rateKey]['tax'] += $lineTax;
            }
        }

        $base = round($base, 2);
        $tax = round($tax, 2);
        $total = round($base + $tax, 2);
        $effectiveRate = $base > 0 ? round(($tax / $base) * 100, 2) : 0.0;

        ksort($breakdown, SORT_NUMERIC);

        $taxBreakdown = array_map(function (array $entry) {
            return [
                'rate' => $entry['rate'],
                'base' => round($entry['base'], 2),
                'tax' => round($entry['tax'], 2),
            ];
        }, array_values($breakdown));

        return [
            'base' => $base,
            'tax' => $tax,
            'total' => $total,
            'effectiveRate' => $effectiveRate,
            'taxBreakdown' => $taxBreakdown,
        ];
    }

    private static function toFloat(mixed $value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }
}
