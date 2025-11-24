<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->string('guest_name');
            $table->string('subject');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'scheduled_date', 'scheduled_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
