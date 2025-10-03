<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('company_id', Auth::user()->company_id)->get();
        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $companies = Company::all();
        return Inertia::render('Categories/Create', [
            'companies' => $companies,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $data = $request->all();
        $data['company_id'] = Auth::user()->company_id;
        Category::create($data);

        return Inertia::location(route('categories.index'));
    }

    public function edit(Category $category)
    {
        $companies = Company::all();
        return Inertia::render('Categories/Edit', [
            'category' => $category,
            'companies' => $companies,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $category->update($request->all());

        return Inertia::location(route('categories.index'));
    }

    public function show(Category $category)
    {
        $category->load('company');

        return Inertia::render('Categories/Show', [
            'category' => $category,
        ]);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        print('Categoría eliminada');
        return Inertia::location(route('categories.index'));
    }
}
