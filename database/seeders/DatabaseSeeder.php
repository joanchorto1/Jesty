<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\EmailConfiguration;
use App\Models\Feature;
use App\Models\Role;
use App\Models\RoleFeature;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $modules = config('modules.modules', []);

        foreach ($modules as $module) {
            Feature::firstOrCreate(
                ['name' => $module['name']],
                ['description' => $module['description'] ?? null]
            );
        }

        $company = Company::create([
            'name' => 'Mi Empresa',
            'address' => 'Calle Falsa, 123',
            'phone' => '555-1234',
            'email' => 'company@example.com',
            'nif' => '12345678A',
        ]);

        $adminRole = Role::create([
            'name' => 'Administrador',
            'description' => 'Rol de administrador con todos los permisos',
            'company_id' => $company->id,
        ]);

        $featureIds = Feature::pluck('id');
        foreach ($featureIds as $featureId) {
            RoleFeature::create([
                'role_id' => $adminRole->id,
                'feature_id' => $featureId,
            ]);
        }

        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'company_id' => $company->id,
            'role_id' => $adminRole->id,
        ]);

        $smtp = config('mail.mailers.smtp');

        EmailConfiguration::create([
            'company_id' => $company->id,
            'smtp_host' => $smtp['host'] ?? '',
            'smtp_port' => (string) ($smtp['port'] ?? ''),
            'smtp_username' => $smtp['username'] ?? '',
            'smtp_password' => $smtp['password'] ?? '',
            'smtp_encryption' => $smtp['encryption'] ?? null,
            'from_email' => config('mail.from.address', $company->email),
            'from_name' => config('mail.from.name', $company->name),
        ]);
    }

}
