<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('features', function (Blueprint $table) {
            if (!Schema::hasColumn('features', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });

        $featureId = DB::table('features')->where('name', 'RRHH')->value('id');

        if ($featureId) {
            DB::table('features')->where('id', $featureId)->update(['is_active' => false]);
            DB::table('plan_feature')->where('feature_id', $featureId)->delete();
            DB::table('role_feature')->where('feature_id', $featureId)->delete();
        }
    }

    public function down(): void
    {
        Schema::table('features', function (Blueprint $table) {
            if (Schema::hasColumn('features', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
