<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_entry_items', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->after('quantity')->default(0)->comment('Precio unitario del producto');
            $table->decimal('total', 10, 2)->after('unit_price')->default(0)->comment('Total calculado (quantity * unit_price)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_entry_items', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'total']);
        });
    }
};
