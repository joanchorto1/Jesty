<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::all();
        return Inertia::render('ExpenseCategories/Index', [
            'categories' => $categories
        ]);
    }

    public function create()
    {
        return Inertia::render('ExpenseCategories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data = $validated;
        $data['company_id'] = Auth::user()->company_id;

        ExpenseCategory::create($data);

        return Inertia::location(route('expenseCategories.index'));
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        return Inertia::render('ExpenseCategories/Show', [
            'expenseCategory' => $expenseCategory
        ]);
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return Inertia::render('ExpenseCategories/Edit', [
            'category' => $expenseCategory
        ]);
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
        ]);

        $expenseCategory->update($validated);

        return Inertia::location(route('expenseCategories.index'));
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();

        return Inertia::location(route('expenseCategories.index'));
    }
}
