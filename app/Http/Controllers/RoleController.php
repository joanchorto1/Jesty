<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Roles/Index', [
            'roles' => \App\Models\Role::where('company_id', Auth::user()->company_id)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Roles/Create', [
            'features' => \App\Models\Feature::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'feature_ids' => 'required',
        ]);

        $role = $request->only('name', 'description');
        $role['company_id'] = Auth::user()->company_id;
        $role = \App\Models\Role::create($role);

        foreach ($request->feature_ids as $feature_id) {
            \App\Models\RoleFeature::create([
                'role_id' => $role->id,
                'feature_id' => $feature_id,
            ]);
        }
        return Inertia::location(route('roles.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = \App\Models\Role::find($id);
        $roleFeatures = \App\Models\RoleFeature::where('role_id', $id)->get();
        $features =[];
        $activeFeatures = \App\Models\Feature::where('is_active', true)->get();
        foreach ($activeFeatures as $feature) {
            $features[] = [
                'id' => $feature->id,
                'name' => $feature->name,
                'checked' => $roleFeatures->contains('feature_id', $feature->id),
            ];
        }
        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'features' => $features,
            'roleFeatures' => $roleFeatures,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = \App\Models\Role::find($id);
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'feature_ids' => 'required',
        ]);

        $role->update($request->only('name', 'description'));

        \App\Models\RoleFeature::where('role_id', $id)->delete();
        foreach ($request->feature_ids as $feature_id) {
            \App\Models\RoleFeature::create([
                'role_id' => $role->id,
                'feature_id' => $feature_id,
            ]);
        }
        return Inertia::location(route('roles.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return Inertia::location(route('roles.index'));

    }
}
