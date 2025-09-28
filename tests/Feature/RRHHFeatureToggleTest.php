<?php

namespace Tests\Feature;

use App\Http\Middleware\HandleInertiaRequests;
use App\Models\Company;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RRHHFeatureToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_rrhh_routes_are_unavailable_when_feature_flag_is_disabled(): void
    {
        $this->assertFalse(Route::has('dashboard.rrhh'));

        $this->get('/dashboard/rrhh')->assertNotFound();
    }

    public function test_inactive_rrhh_feature_is_not_shared_with_navigation(): void
    {
        $plan = Plan::create([
            'name' => 'Test',
            'description' => 'Test plan',
            'price' => 0,
        ]);

        $company = Company::create([
            'name' => 'Test Co',
            'nif' => 'B12345678',
            'phone' => '123456789',
            'email' => 'company@example.com',
            'address' => 'Address 123',
            'plan_id' => $plan->id,
        ]);

        $role = Role::create([
            'name' => 'Administrador',
            'description' => 'Admin role',
            'company_id' => $company->id,
        ]);

        $feature = Feature::create([
            'name' => 'RRHH',
            'description' => 'Recursos humanos',
            'is_active' => false,
        ]);

        $role->features()->attach($feature->id);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id,
        ]);

        $this->actingAs($user);

        /** @var HandleInertiaRequests $middleware */
        $middleware = app(HandleInertiaRequests::class);

        $shared = $middleware->share(request());

        $this->assertFalse(
            collect($shared['features'])
                ->pluck('name')
                ->contains('RRHH'),
            'Inactive RRHH feature should not appear in shared navigation data.'
        );
    }
}
