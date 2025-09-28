<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\EmailConfiguration;
use App\Models\Feature;
use App\Models\Role;
use App\Models\RoleFeature;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_nif' => ['required', 'string', 'max:255'],
            'company_address' => ['required', 'string', 'max:255'],
            'company_phone' => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'email', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
        ]);

        Log::info('Registro manual de compañía iniciado', ['company' => $data['company_name']]);

        $company = Company::create([
            'name' => $data['company_name'],
            'address' => $data['company_address'],
            'phone' => $data['company_phone'],
            'email' => $data['company_email'],
            'nif' => $data['company_nif'],
        ]);

        $adminRole = Role::create([
            'name' => 'Administrador',
            'description' => 'Rol con acceso completo al sistema',
            'company_id' => $company->id,
        ]);

        $featureIds = Feature::pluck('id');
        foreach ($featureIds as $featureId) {
            RoleFeature::create([
                'role_id' => $adminRole->id,
                'feature_id' => $featureId,
            ]);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'company_id' => $company->id,
            'role_id' => $adminRole->id,
            'address' => $data['address'],
            'phone' => $data['phone'],
        ]);

        $smtp = config('mail.mailers.smtp');

        EmailConfiguration::create([
            'company_id' => $company->id,
            'smtp_host' => $smtp['host'] ?? '',
            'smtp_port' => (string) ($smtp['port'] ?? ''),
            'smtp_username' => $smtp['username'] ?? '',
            'smtp_password' => $smtp['password'] ?? '',
            'smtp_encryption' => $smtp['encryption'] ?? null,
            'from_email' => config('mail.from.address', $data['company_email']),
            'from_name' => config('mail.from.name', $company->name),
        ]);

        Log::info('Compañía y administrador registrados manualmente', [
            'company_id' => $company->id,
            'user_id' => $user->id,
        ]);

        return redirect()->route('login')->with('status', __('Registration completed successfully.'));
    }
}
