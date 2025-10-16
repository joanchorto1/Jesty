<?php

namespace App\Services;

use Carbon\Carbon;

class DocumentNumberGenerator
{
    /**
     * Generate the next document number for the given model.
     */
    public static function generate(string $modelClass, string $numberColumn, string $prefix, int $companyId, Carbon $date): string
    {
        $year = $date->year;
        $pattern = sprintf('%s-%d-', $prefix, $year);

        $latestDocument = $modelClass::where('company_id', $companyId)
            ->whereYear('date', $year)
            ->where($numberColumn, 'like', $pattern . '%')
            ->orderBy($numberColumn, 'desc')
            ->first();

        $nextSequence = 1;

        if ($latestDocument) {
            $currentValue = $latestDocument->{$numberColumn};

            if (preg_match('/(\d+)$/', $currentValue, $matches)) {
                $nextSequence = (int) $matches[1] + 1;
            }
        }

        return sprintf('%s-%d-%04d', $prefix, $year, $nextSequence);
    }
}
