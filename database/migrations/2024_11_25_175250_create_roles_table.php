<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del rol (ej. Administrador, Manager)
            $table->text('description')->nullable(); // Descripción del rol
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // Relación con las compañías
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
}
