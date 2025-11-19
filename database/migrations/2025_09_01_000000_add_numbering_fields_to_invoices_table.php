<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'number')) {
                $table->string('number')->nullable()->after('name');
                $table->index(['company_id', 'number']);
            }

            if (! Schema::hasColumn('invoices', 'external_reference')) {
                $table->string('external_reference')->nullable()->after('number');
                $table->index(['company_id', 'external_reference']);
            }

            if (! Schema::hasColumn('invoices', 'public_token')) {
                $table->string('public_token')->nullable()->after('pdf_path');
                $table->unique('public_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'public_token')) {
                $table->dropUnique('invoices_public_token_unique');
                $table->dropColumn('public_token');
            }

            if (Schema::hasColumn('invoices', 'external_reference')) {
                $table->dropIndex('invoices_company_id_external_reference_index');
                $table->dropColumn('external_reference');
            }

            if (Schema::hasColumn('invoices', 'number')) {
                $table->dropIndex('invoices_company_id_number_index');
                $table->dropColumn('number');
            }
        });
    }
};
