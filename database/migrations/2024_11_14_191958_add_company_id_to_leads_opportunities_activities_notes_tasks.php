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
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
        });

        Schema::table('opportunities', function (Blueprint $table) {
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads_opportunities_activities_notes_tasks', function (Blueprint $table) {
            //
        });
    }
};
