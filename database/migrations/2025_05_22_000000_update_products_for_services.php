<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'periodicity')) {
                $table->string('periodicity')->nullable()->after('description');
            }
        });

        DB::statement('ALTER TABLE products MODIFY cost_price DECIMAL(10,2) NULL');
        DB::statement('ALTER TABLE products MODIFY stock INT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE products MODIFY cost_price DECIMAL(10,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE products MODIFY stock INT NOT NULL DEFAULT 0');

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'periodicity')) {
                $table->dropColumn('periodicity');
            }
        });
    }
};
