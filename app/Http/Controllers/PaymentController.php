<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Bill;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('bill.customer', 'receivedBy')
            ->latest()
            ->paginate(15);

        return view('payments.index', compact('payments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bill_id'      => 'required|exists:bills,id',
            'amount'       => 'required|numeric|min:0.01',
            'payment_type' => 'required|in:cash,card,online',
            'notes'        => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $bill = Bill::find($request->bill_id);

            Payment::create([
                'bill_id'      => $bill->id,
                'amount'       => $request->amount,
                'payment_type' => $request->payment_type,
                'received_by'  => auth()->id(),
                'notes'        => $request->notes,
                'paid_at'      => Carbon::now(),
            ]);

            // Check if bill is now fully paid
            $totalPaid = $bill->payments()->sum('amount') + $request->amount;

            if ($totalPaid >= $bill->total) {
                // Flip commission to payable
                Commission::where('bill_id', $bill->id)
                          ->where('status', 'pending')
                          ->update(['status' => 'payable']);
            }
        });

        return redirect()->route('bills.show', $request->bill_id)
                         ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment) {}
    public function create() {}
    public function edit(Payment $payment) {}
    public function update(Request $request, Payment $payment) {}

    public function destroy(Payment $payment)
    {
        $billId = $payment->bill_id;
        $payment->delete();

        // If bill no longer fully paid, revert commission to pending
        $bill      = Bill::find($billId);
        $totalPaid = $bill->payments()->sum('amount');

        if ($totalPaid < $bill->total) {
            Commission::where('bill_id', $billId)
                      ->where('status', 'payable')
                      ->update(['status' => 'pending']);
        }

        return redirect()->route('bills.show', $billId)
                         ->with('success', 'Payment deleted.');
    }
}