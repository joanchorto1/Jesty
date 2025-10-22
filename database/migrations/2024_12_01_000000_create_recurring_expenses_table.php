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
        Schema::create('recurring_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('iva', 5, 2)->default(0);
            $table->enum('frequency_type', ['daily', 'weekly', 'monthly', 'yearly']);
            $table->unsignedInteger('frequency_interval')->default(1);
            $table->timestamp('next_run_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('last_generated_at')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('recurring_expense_id')
                ->nullable()
                ->after('expense_category_id')
                ->constrained('recurring_expenses')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['recurring_expense_id']);
            $table->dropColumn('recurring_expense_id');
        });

        Schema::dropIfExists('recurring_expenses');
    }
};
