<?php

namespace App\Http\Controllers;

use App\Models\SmsRecipient;
use App\Models\SmsLog;
use Illuminate\Http\Request;

class SmsRecipientController extends Controller
{
    public function index()
    {
        $recipients = SmsRecipient::latest()->get();
        $recentLogs = SmsLog::latest()->take(20)->get();

        return view('sms.index', compact('recipients', 'recentLogs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'phone'               => 'required|string|max:20',
            'notify_low_stock'    => 'nullable|boolean',
            'notify_due_payments' => 'nullable|boolean',
        ]);

        SmsRecipient::create([
            'name'                => $request->name,
            'phone'               => $request->phone,
            'notify_low_stock'    => $request->boolean('notify_low_stock'),
            'notify_due_payments' => $request->boolean('notify_due_payments'),
            'is_active'           => true,
        ]);

        return redirect()->route('sms-recipients.index')
                         ->with('success', 'Recipient added.');
    }

    public function update(Request $request, SmsRecipient $smsRecipient)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'phone'               => 'required|string|max:20',
            'notify_low_stock'    => 'nullable|boolean',
            'notify_due_payments' => 'nullable|boolean',
            'is_active'           => 'nullable|boolean',
        ]);

        $smsRecipient->update([
            'name'                => $request->name,
            'phone'               => $request->phone,
            'notify_low_stock'    => $request->boolean('notify_low_stock'),
            'notify_due_payments' => $request->boolean('notify_due_payments'),
            'is_active'           => $request->boolean('is_active'),
        ]);

        return redirect()->route('sms-recipients.index')
                         ->with('success', 'Recipient updated.');
    }

    public function destroy(SmsRecipient $smsRecipient)
    {
        $smsRecipient->delete();

        return redirect()->route('sms-recipients.index')
                         ->with('success', 'Recipient deleted.');
    }

    public function create() {}
    public function show(SmsRecipient $smsRecipient) {}
    public function edit(SmsRecipient $smsRecipient) {}
}