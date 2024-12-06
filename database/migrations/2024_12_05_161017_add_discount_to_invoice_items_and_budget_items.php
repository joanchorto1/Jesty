<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AddDiscountToInvoiceItemsAndBudgetItems extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('discount', 10, 2)->default(0)->after('unit_price')->comment('Descuento aplicado al ítem');
        });

        Schema::table('budget_items', function (Blueprint $table) {
            $table->decimal('discount', 10, 2)->default(0)->after('unit_price')->comment('Descuento aplicado al ítem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('discount');
        });

        Schema::table('budget_items', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
}
