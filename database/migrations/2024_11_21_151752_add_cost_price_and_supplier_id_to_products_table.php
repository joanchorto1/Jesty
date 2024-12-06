<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostPriceAndSupplierIdToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->after('price')->default(0)->comment('Precio de costo del producto');
            $table->unsignedBigInteger('supplier_id')->nullable()->after('cost_price')->comment('ID del proveedor');

            // Si la tabla suppliers existe, añadimos la relación con clave foránea
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
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']); // Eliminamos la clave foránea
            $table->dropColumn(['cost_price', 'supplier_id']); // Eliminamos las columnas
        });
    }
}
