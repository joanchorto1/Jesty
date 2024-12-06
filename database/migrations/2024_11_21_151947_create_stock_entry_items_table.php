<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockEntryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_entry_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_entry_id')->comment('Referencia a la entrada de stock');
            $table->unsignedBigInteger('product_id')->comment('Referencia al producto');
            $table->integer('quantity')->default(0)->comment('Cantidad ingresada');
            $table->timestamps();

            $table->foreign('stock_entry_id')->references('id')->on('stock_entries')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_entry_items');
    }
}
