<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'quote', 'booking'])->orderByDesc('created_at');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        if ($request->has('method') && $request->method !== '') {
            $query->where('method', $request->method);
        }
        if ($request->has('provider') && $request->provider !== '') {
            $query->where('provider', $request->provider);
        }

        $payments = $query->paginate(20)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user', 'quote', 'booking']);

        return view('admin.payments.show', compact('payment'));
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,paid,failed,refunded,cancelled',
            'notes' => 'nullable|string',
        ]);

        $payment->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $payment->notes,
            'captured_at' => $validated['status'] === 'paid' ? now() : $payment->captured_at,
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة الدفع بنجاح');
    }
}
