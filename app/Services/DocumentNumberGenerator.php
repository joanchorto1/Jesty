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
        $yearSuffix = substr((string) $year, -2);
        $pattern = sprintf('%s-%s-', $prefix, $yearSuffix);

        $latestDocument = $modelClass::where('company_id', $companyId)
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

        return sprintf('%s-%s-%04d', $prefix, $yearSuffix, $nextSequence);
    }
}
