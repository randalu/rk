<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(15);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'phone'                => 'nullable|string|max:20',
            'email'                => 'nullable|email|max:255',
            'address'              => 'nullable|string',
            'default_payment_term' => 'required|in:cash,credit_30,credit_45,credit_60',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
                         ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['bills' => function($q) {
            $q->latest()->take(10);
        }]);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'phone'                => 'nullable|string|max:20',
            'email'                => 'nullable|email|max:255',
            'address'              => 'nullable|string',
            'default_payment_term' => 'required|in:cash,credit_30,credit_45,credit_60',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
                         ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
                         ->with('success', 'Customer deleted successfully.');
    }
}