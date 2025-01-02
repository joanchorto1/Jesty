<?php
namespace App\Http\Controllers;

use App\Models\EmailConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EmailConfigurationController extends Controller
{
    public function index()
    {
        $emailConfigurations = EmailConfiguration::all();
        return response()->json($emailConfigurations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|string',
            'smtp_username' => 'required|string',
            'smtp_password' => 'required|string',
            'smtp_encryption' => 'nullable|string',
            'from_email' => 'required|email',
            'from_name' => 'required|string',
        ]);

        $request['comapny_id'] = Auth::user()->company_id;
        $emailConfiguration = EmailConfiguration::create($request->all());
        return Inertia::location(route('dashboard.admin'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'smtp_host' => 'sometimes|required|string',
            'smtp_port' => 'sometimes|required|string',
            'smtp_username' => 'sometimes|required|string',
            'smtp_password' => 'sometimes|required|string',
            'smtp_encryption' => 'nullable|string',
            'from_email' => 'sometimes|required|email',
            'from_name' => 'sometimes|required|string',
        ]);

        $emailConfiguration = EmailConfiguration::findOrFail($id);
        $emailConfiguration->update($request->all());
        return Inertia::location(route('dashboard.admin'));
    }

    public function edit($id)
    {
        $emailConfiguration = EmailConfiguration::findOrFail($id);
        return Inertia::render('EmailConfiguration/Edit', [
            'emailConfiguration' => $emailConfiguration
        ]);
    }

    public function destroy($id)
    {
        $emailConfiguration = EmailConfiguration::findOrFail($id);
        $emailConfiguration->delete();
        return response()->json(null, 204);
    }
}
