<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('suppliers')) {
            return;
        }

        $duplicates = DB::table('suppliers')
            ->select('company_id', 'name', DB::raw('MIN(id) as keeper_id'), DB::raw('COUNT(*) as total'))
            ->groupBy('company_id', 'name')
            ->having('total', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('suppliers')
                ->where('company_id', $duplicate->company_id)
                ->where('name', $duplicate->name)
                ->where('id', '!=', $duplicate->keeper_id)
                ->delete();
        }

        if (! $this->hasCompanyNameUniqueIndex()) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->unique(['company_id', 'name']);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('suppliers')) {
            return;
        }

        if ($this->hasCompanyNameUniqueIndex()) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropUnique('suppliers_company_id_name_unique');
            });
        }
    }

    private function hasCompanyNameUniqueIndex(): bool
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'sqlite') {
            $indexes = $connection->select("PRAGMA index_list('suppliers')");

            foreach ($indexes as $index) {
                if (! empty($index->unique)) {
                    $indexInfo = $connection->select("PRAGMA index_info('{$index->name}')");
                    $columns = collect($indexInfo)->pluck('name')->all();

                    if ($columns === ['company_id', 'name']) {
                        return true;
                    }
                }
            }

            return false;
        }

        if ($driver === 'mysql') {
            $schema = $connection->getDatabaseName();
            $result = $connection->select(
                "SELECT COUNT(*) as count FROM information_schema.statistics WHERE table_schema = ? AND table_name = 'suppliers' AND non_unique = 0 AND index_name = 'suppliers_company_id_name_unique'",
                [$schema]
            );

            return ! empty($result) && (int) $result[0]->count > 0;
        }

        return false;
    }
};
