<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

 class UpdateEmployeesTableAddAdditionalFields extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('dni')->unique();
            $table->string('nnss')->unique();
            $table->string('iban', 34)->unique();
            $table->date('birth_date');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['dni', 'nnss', 'iban', 'birth_date']);
        });
    }
};
