<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Inertia\Inertia;
use phpseclib3\Crypt\RSA;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        return Inertia::render('Companies/Index', [
            'companies' => $companies
        ]);
    }

    public function create()
    {
        return Inertia::render('Companies/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'nif' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'address' => 'required',
        ]);

        Company::create($request->only(['name', 'nif', 'phone', 'email', 'address']));

        return Inertia::location(route('admin.dashboard'));
    }

    public function edit(Company $company)
    {
        return Inertia::render('Companies/Edit', [
            'company' => $company
        ]);
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required',
            'nif' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'address' => 'required',
        ]);

        $company->update($request->only(['name', 'nif', 'phone', 'email', 'address']));

        return Inertia::location(route('dashboard.admin'));
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('companies.index');
    }


  
    public function updateKeys(Request $request, Company $company)
    {
        $request->validate([
            'public_key' => 'required',
            'private_key' => 'required',
        ]);

        // Encriptar las claves
        $encryptedData = [
            'public_key' => Crypt::encrypt($request->input('public_key')),
            'private_key' => Crypt::encrypt($request->input('private_key')),
        ];

        $company->update($encryptedData);

        return Inertia::location(route('dashboard.admin'));
    }


    public function showKeys(Company $company)
    {

        if (!$company->public_key || !$company->private_key) {
            return Inertia::location(route('companies.generateKeys', $company));
        }else{
            // Deserialitzar i desxifrar la clau pública i privada
            $publicKey = unserialize(Crypt::decryptString($company->public_key));
            $privateKey = unserialize(Crypt::decryptString($company->private_key));

            return Inertia::render('Companies/ShowKeys', [
                'company' => $company,
                'public_key' => $publicKey,
                'private_key' => $privateKey
            ]);
        }

    }


    public function generateKeys(Company $company)
    {
        // Generate new RSA keys
        $rsa = RSA::createKey(2048);

        // Update the company's keys
        $publicKey = $rsa->getPublicKey()->toString('PKCS8');
         $privateKey= $rsa->toString('PKCS1'); // Optional: Store securely if needed

        $encryptedData = [
            'public_key' => Crypt::encrypt($publicKey),
            'private_key' => Crypt::encrypt($privateKey),
        ];

        $company->update($encryptedData);

        return Inertia::location(route('companies.showKeys', $company));
    }


}
