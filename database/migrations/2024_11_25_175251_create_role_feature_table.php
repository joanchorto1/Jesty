<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleFeatureTable extends Migration
{
    public function up(): void
    {
        Schema::create('role_feature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade'); // Relación con los roles
            $table->foreignId('feature_id')->constrained('features')->onDelete('cascade'); // Relación con las features
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_feature');
    }
}
