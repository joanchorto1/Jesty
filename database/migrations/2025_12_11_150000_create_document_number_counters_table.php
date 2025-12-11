<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_number_counters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedInteger('year');
            $table->string('prefix', 20);
            $table->unsignedInteger('last_sequence')->default(0);
            $table->timestamps();

            $table->unique(['company_id', 'year', 'prefix']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_number_counters');
    }
};
