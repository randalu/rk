<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::withCount('expenses')->get();
        return view('expenses.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string',
        ]);

        ExpenseCategory::create($request->only('name', 'description'));

        return redirect()->route('expense-categories.index')
                         ->with('success', 'Category created.');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id,
            'description' => 'nullable|string',
        ]);

        $expenseCategory->update($request->only('name', 'description'));

        return redirect()->route('expense-categories.index')
                         ->with('success', 'Category updated.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->expenses()->count() > 0) {
            return redirect()->route('expense-categories.index')
                             ->with('error', 'Cannot delete a category that has expenses.');
        }

        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')
                         ->with('success', 'Category deleted.');
    }

    public function create() {}
    public function edit(ExpenseCategory $expenseCategory) {}
    public function show(ExpenseCategory $expenseCategory) {}
}