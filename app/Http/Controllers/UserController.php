<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = \App\Models\User::where('company_id', Auth::user()->company_id)->get();
        $roles = \App\Models\Role::where('company_id', Auth::user()->company_id)->get();
        $company = \App\Models\Company::where('id', Auth::user()->company_id)->first();
        $plan = \App\Models\Plan::where('id', $company->plan_id)->first();
        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'plan' => $plan,
            'company' => $company,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Users/Create', [
            'roles' => \App\Models\Role::where('company_id', Auth::user()->company_id)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user=$request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'phone' => 'required',
            'role_id' => 'required',
            'password' => 'required',
        ]);
        $user['company_id'] = Auth::user()->company_id;
        $user['password'] = bcrypt($user['password']);
        \App\Models\User::create($user);
        return Inertia::location(route('users.index'));
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
        $user = \App\Models\User::find($id);
        $roles = \App\Models\Role::where('company_id', Auth::user()->company_id)->get();
        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => $roles,
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = \App\Models\User::find($id);
        $user->update($request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'password' ,
            'address' => 'required',
            'role_id' => 'required',
        ]));
        return Inertia::location(route('users.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = \App\Models\User::find($id);
        $user->delete();
    }
}
