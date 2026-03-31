<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('category', 'createdBy')
            ->latest()
            ->paginate(15);

        $totalThisMonth = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        return view('expenses.index', compact('expenses', 'totalThisMonth'));
    }

    public function create()
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'  => 'required|exists:expense_categories,id',
            'amount'       => 'required|numeric|min:0.01',
            'description'  => 'nullable|string',
            'expense_date' => 'required|date',
        ]);

        Expense::create([
            'category_id'  => $request->category_id,
            'amount'       => $request->amount,
            'description'  => $request->description,
            'expense_date' => $request->expense_date,
            'created_by'   => auth()->id(),
        ]);

        return redirect()->route('expenses.index')
                         ->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense)
    {
        $expense->load('category', 'createdBy');
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'category_id'  => 'required|exists:expense_categories,id',
            'amount'       => 'required|numeric|min:0.01',
            'description'  => 'nullable|string',
            'expense_date' => 'required|date',
        ]);

        $expense->update($request->only(
            'category_id', 'amount', 'description', 'expense_date'
        ));

        return redirect()->route('expenses.index')
                         ->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')
                         ->with('success', 'Expense deleted.');
    }
}