<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'due_date')) {
                $table->date('due_date')->nullable()->after('date');
            }

            if (! Schema::hasColumn('invoices', 'notes')) {
                $table->text('notes')->nullable()->after('state');
            }

            if (! Schema::hasColumn('invoices', 'irpf_tax')) {
                $table->decimal('irpf_tax', 5, 2)->default(0)->after('iva');
            }

            if (! Schema::hasColumn('invoices', 'total_irpf')) {
                $table->decimal('total_irpf', 10, 2)->default(0)->after('irpf_tax');
            }

            if (! Schema::hasColumn('invoices', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('total');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'pdf_path')) {
                $table->dropColumn('pdf_path');
            }

            if (Schema::hasColumn('invoices', 'total_irpf')) {
                $table->dropColumn('total_irpf');
            }

            if (Schema::hasColumn('invoices', 'irpf_tax')) {
                $table->dropColumn('irpf_tax');
            }

            if (Schema::hasColumn('invoices', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('invoices', 'due_date')) {
                $table->dropColumn('due_date');
            }
        });
    }
};
