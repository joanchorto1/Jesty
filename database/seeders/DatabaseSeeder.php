<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Plan;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\Income;

class DatabaseSeeder extends Seeder
{  public function run()
{
    // Crear Features
    $features = [
        ['name' => 'Facturación', 'description' => 'Gestión de facturas'],
        ['name' => 'Contabilidad', 'description' => 'Gestión contable'],
        ['name' => 'Inventario', 'description' => 'Gestión de inventarios'],
        ['name' => 'CRM', 'description' => 'Gestión de relaciones con clientes'],
        ['name' => 'TPV', 'description' => 'Gestión de puntos de venta'],
        ['name' => 'Clientes', 'description' => 'Gestión de clientes'],
        ['name' => 'Administradores', 'description' => 'Gestión de administradores'],
        ['name' => 'RRHH', 'description' => 'Gestión de recursos humanos'],
    ];

    foreach ($features as $feature) {
        Feature::create($feature);
    }

    // Crear Planes
    $plans = [
        ['name' => 'Básico', 'description' => 'Plan básico con funcionalidades limitadas', 'price' => 10, 'user_limit' => 1],
        ['name' => 'Estándar', 'description' => 'Plan estándar con más funcionalidades', 'price' => 25, 'user_limit' => 3],
        ['name' => 'Premium', 'description' => 'Plan premium con todas las funcionalidades', 'price' => 50, 'user_limit' => 10],
        ['name'=>'FirstMonthFree','description'=>'Primer mes gratis','price'=>0,'user_limit'=>1]

    ];

    foreach ($plans as $plan) {
        Plan::create($plan);
    }

    // Asignar Features a Planes
    $basicPlan = Plan::where('name', 'Básico')->first();
    $standardPlan = Plan::where('name', 'Estándar')->first();
    $premiumPlan = Plan::where('name', 'Premium')->first();
    $firstMonthFree = Plan::where('name','FirstMonthFree')->first();

    $facturacion = Feature::where('name', 'Facturación')->first();
    $contabilidad = Feature::where('name', 'Contabilidad')->first();
    $inventario = Feature::where('name', 'Inventario')->first();
    $crm = Feature::where('name', 'CRM')->first();
    $tpv = Feature::where('name', 'TPV')->first();
    $clientes = Feature::where('name', 'Clientes')->first();
    $administradores = Feature::where('name', 'Administradores')->first();
    $rrhh = Feature::where('name', 'RRHH')->first();


    $basicPlan->features()->attach([$facturacion->id,$administradores->id,$inventario->id,$clientes->id]);
    $standardPlan->features()->attach([$facturacion->id, $inventario->id, $crm->id, $clientes->id, $administradores->id]);
    $premiumPlan->features()->attach([$facturacion->id, $inventario->id, $crm->id, $contabilidad->id, $tpv->id, $clientes->id, $administradores->id, $rrhh->id]);
    $firstMonthFree->features()->attach([$facturacion->id,$administradores->id,$inventario->id,$clientes->id,$contabilidad->id,$tpv->id,$crm->id,$rrhh->id]);

//
//    // Crear Company
//    $company = Company::create([
//        'name' => 'Mi Empresa',
//        'address' => 'Calle Falsa, 123',
//        'phone' => '555-1234',
//        'email' => 'company@example.com',
//        'nif' => '12345678A',
//        'plan_id' => $premiumPlan->id,
//    ]);
//
//    //Crea role administador
//    $role = Role::create([
//        'name' => 'Administrador',
//        'description' => 'Rol de administrador con todos los permisos',
//        'company_id' => $company->id,
//    ]);
//
//    foreach (Feature::all() as $feature) {
//        $role->features()->attach($feature->id);
//    }
//
//    // Crear Usuario Administrador
//    $admin = User::create([
//        'name' => 'Administrador',
//        'email' => 'admin@example.com',
//        'password' => Hash::make('password'),
//        'company_id' => $company->id,
//        'role_id' => $role->id,
//    ]);

}
}
