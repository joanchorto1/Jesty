<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $featureId = DB::table('features')->where('name', 'Agenda')->value('id');

        if (!$featureId) {
            $featureId = DB::table('features')->insertGetId([
                'id' => 9,
                'name' => 'Agenda',
                'description' => 'Planificació de cites i reunions',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $planIds = DB::table('plans')->whereIn('name', ['Básico', 'Estándar', 'Premium', 'FirstMonthFree'])->pluck('id');

        foreach ($planIds as $planId) {
            $exists = DB::table('plan_feature')
                ->where('plan_id', $planId)
                ->where('feature_id', $featureId)
                ->exists();

            if (!$exists) {
                DB::table('plan_feature')->insert([
                    'plan_id' => $planId,
                    'feature_id' => $featureId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $roles = DB::table('roles')->pluck('id');

        foreach ($roles as $roleId) {
            $roleHasFeature = DB::table('role_features')
                ->where('role_id', $roleId)
                ->where('feature_id', $featureId)
                ->exists();

            if (!$roleHasFeature) {
                DB::table('role_features')->insert([
                    'role_id' => $roleId,
                    'feature_id' => $featureId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $featureId = DB::table('features')->where('name', 'Agenda')->value('id');

        if ($featureId) {
            DB::table('plan_feature')->where('feature_id', $featureId)->delete();
            DB::table('role_features')->where('feature_id', $featureId)->delete();
            DB::table('features')->where('id', $featureId)->delete();
        }
    }
};
