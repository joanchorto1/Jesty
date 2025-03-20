<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Company;
use App\Models\User;

class GastromarSeeder extends Seeder
{
    public function run()
    {
        // Crear Plan Premium con todas las funcionalidades


        // Asignar todas las características al plan

        // Crear Company
        $company = Company::create([
            'name' => 'Gastromar Tortosa',
            'address' => 'Calle Principal, 456',
            'phone' => '555-5678',
            'email' => 'gastromartortosa@gmail.com',
            'nif' => '87654321B',
            'plan_id' => 3,
        ]);

        // Crear rol de Administrador
        $role = Role::create([
            'name' => 'Administrador',
            'description' => 'Rol de administrador con todos los permisos',
            'company_id' => $company->id,
        ]);

        // Asignar todas las características al rol
        foreach (Feature::all() as $feature) {
            $role->features()->attach($feature->id);
        }

        // Crear Usuario Administrador
        User::create([
            'name' => 'Gastromar',
            'email' => 'gastromartortosa@gmail.com',
            'password' => Hash::make('password'),
            'company_id' => $company->id,
            'role_id' => $role->id,
        ]);
    }
}
