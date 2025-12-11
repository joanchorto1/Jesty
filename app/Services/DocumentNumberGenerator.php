<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

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

        $attempts = 0;
        $lastException = null;

        while ($attempts < 5) {
            try {
                return DB::transaction(function () use ($modelClass, $numberColumn, $companyId, $prefix, $year, $yearSuffix, $pattern) {
                    $counter = DB::table('document_number_counters')
                        ->where('company_id', $companyId)
                        ->where('year', $year)
                        ->where('prefix', $prefix)
                        ->lockForUpdate()
                        ->first();

                    if (! $counter) {
                        $latestDocument = $modelClass::where('company_id', $companyId)
                            ->where($numberColumn, 'like', $pattern . '%')
                            ->lockForUpdate()
                            ->orderBy($numberColumn, 'desc')
                            ->first();

                        $lastSequence = 0;

                        if ($latestDocument && preg_match('/(\d+)$/', $latestDocument->{$numberColumn}, $matches)) {
                            $lastSequence = (int) $matches[1];
                        }

                        $nextSequence = $lastSequence + 1;

                        DB::table('document_number_counters')->insert([
                            'company_id' => $companyId,
                            'year' => $year,
                            'prefix' => $prefix,
                            'last_sequence' => $nextSequence,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        $nextSequence = $counter->last_sequence + 1;

                        DB::table('document_number_counters')
                            ->where('id', $counter->id)
                            ->update([
                                'last_sequence' => $nextSequence,
                                'updated_at' => now(),
                            ]);
                    }

                    return sprintf('%s-%s-%04d', $prefix, $yearSuffix, $nextSequence);
                });
            } catch (QueryException $exception) {
                $lastException = $exception;
                $attempts++;
                usleep(50000);
            }
        }

        throw $lastException ?? new \RuntimeException('Unable to generate document number.');
    }
}
