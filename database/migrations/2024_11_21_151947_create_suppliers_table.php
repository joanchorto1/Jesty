<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->comment('Referencia a la compañía');
            $table->string('name')->comment('Nombre del proveedor');
            $table->string('email')->nullable()->comment('Correo electrónico del proveedor');
            $table->string('phone')->nullable()->comment('Teléfono del proveedor');
            $table->string('address')->nullable()->comment('Dirección del proveedor');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}
