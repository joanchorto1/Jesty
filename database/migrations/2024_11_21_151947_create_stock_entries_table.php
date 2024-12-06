<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->comment('Referencia a la compañía');
            $table->unsignedBigInteger('supplier_id')->nullable()->comment('Referencia al proveedor');
            $table->date('entry_date')->comment('Fecha de la entrada de stock');
            $table->string('reference')->nullable()->comment('Número o referencia de la entrada');
            $table->decimal('base_amount', 10, 2)->default(0)->comment('Base imponible');
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('Tasa de impuesto');
            $table->decimal('tax_amount', 10, 2)->default(0)->comment('Monto de impuesto');
            $table->decimal('total', 10, 2)->default(0)->comment('Total de la entrada');
            $table->string('status')->default('pendiente')->comment('Estado de la entrada');
            $table->text('notes')->nullable()->comment('Notas adicionales');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_entries');
    }
}
