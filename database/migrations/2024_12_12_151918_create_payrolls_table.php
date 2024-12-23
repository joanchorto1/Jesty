<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');

            // Periodo de pago
            $table->date('start_date');
            $table->date('end_date');
            $table->date('payment_date');
            $table->integer('days_worked');
            $table->integer('days_absent')->default(0);

            // Ingresos del empleado
            $table->decimal('base_salary', 10, 2);
            $table->decimal('overtime', 10, 2)->default(0);
            $table->decimal('bonuses', 10, 2)->default(0);
            $table->decimal('commissions', 10, 2)->default(0);
            $table->decimal('vacation_bonus', 10, 2)->default(0);
            $table->decimal('annual_bonus', 10, 2)->default(0);
            $table->decimal('other_earnings', 10, 2)->default(0);

            // Deducciones
            $table->decimal('income_tax', 10, 2)->default(0);
            $table->decimal('social_security', 10, 2)->default(0);
            $table->decimal('housing_fund', 10, 2)->default(0);
            $table->decimal('loans', 10, 2)->default(0);
            $table->decimal('savings_fund', 10, 2)->default(0);
            $table->decimal('union_dues', 10, 2)->default(0);
            $table->decimal('other_deductions', 10, 2)->default(0);

            // IRPF
            $table->decimal('irpf_percentage', 5, 2)->default(0); // Porcentaje de retención IRPF
            $table->decimal('irpf_deduction', 10, 2)->default(0); // Monto de la retención IRPF

            // Totales
            $table->decimal('total_earnings', 10, 2);
            $table->decimal('total_deductions', 10, 2);
            $table->decimal('net_pay', 10, 2);

            $table->timestamps();

            // Relaciones
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
};
