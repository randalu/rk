<?php

namespace App\Http\Controllers;

use App\Models\Salesperson;
use Illuminate\Http\Request;

class SalespersonController extends Controller
{
    public function index()
    {
        $salespeople = Salesperson::with('user')
            ->latest()
            ->paginate(15);

        return view('salespeople.index', compact('salespeople'));
    }

    public function edit(Salesperson $salesperson)
    {
        return view('salespeople.edit', compact('salesperson'));
    }

    public function update(Request $request, Salesperson $salesperson)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'commission_type' => 'required|in:qty_based,value_based',
            'target_period'   => 'required|in:monthly,quarterly,yearly',
            'is_active'       => 'nullable|boolean',
        ]);

        $salesperson->update([
            'name'            => $request->name,
            'phone'           => $request->phone,
            'commission_type' => $request->commission_type,
            'target_period'   => $request->target_period,
            'is_active'       => $request->boolean('is_active'),
        ]);

        return redirect()->route('salespeople.index')
                         ->with('success', 'Salesperson updated.');
    }

    public function destroy(Salesperson $salesperson)
    {
        if (!$salesperson->user_id) {
            return redirect()->route('salespeople.index')
                             ->with('error', 'Cannot delete the Direct Sale record.');
        }

        $salesperson->delete();

        return redirect()->route('salespeople.index')
                         ->with('success', 'Salesperson deleted.');
    }

    public function create() {}
    public function store(Request $request) {}
    public function show(Salesperson $salesperson) {}
}