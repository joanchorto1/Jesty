<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'iva')) {
                $table->decimal('iva', 5, 2)->default(0)->after('discount');
            }
        });

        Schema::table('budget_items', function (Blueprint $table) {
            if (!Schema::hasColumn('budget_items', 'iva')) {
                $table->decimal('iva', 5, 2)->default(0)->after('discount');
            }
        });

        Schema::table('part_items', function (Blueprint $table) {
            if (!Schema::hasColumn('part_items', 'iva')) {
                $table->decimal('iva', 5, 2)->default(0)->after('unit_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_items', 'iva')) {
                $table->dropColumn('iva');
            }
        });

        Schema::table('budget_items', function (Blueprint $table) {
            if (Schema::hasColumn('budget_items', 'iva')) {
                $table->dropColumn('iva');
            }
        });

        Schema::table('part_items', function (Blueprint $table) {
            if (Schema::hasColumn('part_items', 'iva')) {
                $table->dropColumn('iva');
            }
        });
    }
};
