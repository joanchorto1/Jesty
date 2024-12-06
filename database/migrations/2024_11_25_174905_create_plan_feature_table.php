<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanFeatureTable extends Migration
{
    public function up(): void
    {
        Schema::create('plan_feature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->onDelete('cascade'); // Relación con los planes
            $table->foreignId('feature_id')->constrained('features')->onDelete('cascade'); // Relación con los features
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_feature');
    }
}
