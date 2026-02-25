<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\QuoteMail;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class QuoteController extends Controller
{
    /**
     * Display all quotes
     */
    public function index(Request $request)
    {
        $query = Quote::with('user', 'items');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $quotes = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => Quote::count(),
            'pending' => Quote::where('status', 'pending')->count(),
            'under_review' => Quote::where('status', 'under_review')->count(),
            'approved' => Quote::where('status', 'approved')->count(),
            'rejected' => Quote::where('status', 'rejected')->count(),
            'completed' => Quote::where('status', 'completed')->count(),
            'paid' => Quote::where('status', 'paid')->count(),
        ];

        return view('admin.quotes.index', compact('quotes', 'stats'));
    }

    /**
     * Show quote details
     */
    public function show(Quote $quote)
    {
        $quote->load([
            'user',
            'items.service.thumbnailImage',
            'items.service.images',
        ]);

        return view('admin.quotes.show', compact('quote'));
    }

    /**
     * Update quote status
     */
    public function updateStatus(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,approved,rejected,completed,paid',
            'admin_notes' => 'nullable|string|max:2000',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $oldStatus = $quote->status;
        $quote->status = $validated['status'];
        $quote->admin_notes = $validated['admin_notes'] ?? null;

        if (isset($validated['discount'])) {
            $quote->discount = $validated['discount'];
        }

        if ($validated['status'] === 'approved' && $oldStatus !== 'approved') {
            $quote->approved_at = now();
            // إرسال بريد الكتروني للعميل عند الموافقة
            try {
                $quote->load([
                    'items.service.thumbnailImage',
                    'items.service.images',
                    'user',
                ]);
                Mail::to($quote->user->email)->send(new QuoteMail($quote));
            } catch (\Exception $e) {
                Log::error('Failed to send approval email: '.$e->getMessage());
            }

            // Notify all suppliers whose services are in this quote (for quick approval competition)
            try {
                $serviceIds = $quote->items->pluck('service_id')->unique();
                $suppliers = \App\Models\Supplier::whereHas('services', function ($q) use ($serviceIds) {
                    $q->whereIn('services.id', $serviceIds);
                })->where('status', 'approved')->get();

                foreach ($suppliers as $supplier) {
                    Mail::to($supplier->email)->send(new \App\Mail\SupplierQuoteApprovedNotification($quote, $supplier));
                }

                Log::info('Notified '.$suppliers->count().' suppliers about approved quote', [
                    'quote_id' => $quote->id,
                    'supplier_emails' => $suppliers->pluck('email')->toArray(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to notify suppliers: '.$e->getMessage());
            }
        } elseif ($validated['status'] === 'rejected') {
            $quote->rejected_at = now();
            // يمكن إرسال إيميل بالرفض
            try {
                $quote->load('items.service', 'user');
                Mail::to($quote->user->email)->send(new QuoteMail($quote));
            } catch (\Exception $e) {
                Log::error('Failed to send rejection email: '.$e->getMessage());
            }
        } elseif ($validated['status'] === 'paid' && $oldStatus !== 'paid') {
            $quote->payment_status = 'paid';
            $quote->payment_date = now();
            if (empty($quote->payment_method)) {
                $quote->payment_method = 'card';
            }
            $quote->save();

            try {
                $quote->convertToBooking();
            } catch (\Throwable $e) {
                Log::warning('Admin set quote to paid but booking conversion failed: '.$e->getMessage(), [
                    'quote_id' => $quote->id,
                ]);
            }

            try {
                \App\Models\Payment::create([
                    'user_id' => $quote->user_id,
                    'booking_id' => optional($quote->bookings()->latest()->first())->id,
                    'gateway' => 'manual',
                    'gateway_transaction_id' => $quote->payment_reference,
                    'amount' => $quote->total,
                    'currency' => 'SAR',
                    'status' => 'paid',
                    'payment_method' => $quote->payment_method ?? 'card',
                    'description' => $quote->payment_notes,
                    'metadata' => [
                        'source' => 'admin.quotes.updateStatus',
                    ],
                    'paid_at' => now(),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to record payment on admin paid status: '.$e->getMessage());
            }
        }

        $quote->save();

        // Recalculate totals if discount was applied
        if (isset($validated['discount'])) {
            $quote->calculateTotals();
        }

        // Log status change and updates
        if ($oldStatus !== $quote->status) {
            \App\Models\ActivityLog::record($quote, 'status_changed', 'تم تغيير حالة عرض السعر', [
                'old' => $oldStatus,
                'new' => $quote->status,
                'discount' => $quote->discount,
                'admin_notes' => $quote->admin_notes,
            ]);
        } else {
            \App\Models\ActivityLog::record($quote, 'updated', 'تم تعديل عرض السعر', [
                'discount' => $quote->discount,
                'admin_notes' => $quote->admin_notes,
            ]);
        }

        return redirect()->back()->with('success', 'تم تحديث حالة عرض السعر بنجاح');
    }

    /**
     * Delete quote
     */
    public function destroy(Quote $quote)
    {
        if ($quote->bookings()->exists()) {
            return redirect()->route('admin.quotes.index')
                ->with('error', 'لا يمكن حذف عرض السعر لأنه مرتبط بحجوزات. يمكنك إلغاء الحجز أو أرشفة العرض بدلاً من حذفه.');
        }

        $quote->delete();

        return redirect()->route('admin.quotes.index')->with('success', 'تم حذف عرض السعر بنجاح');
    }

    /**
     * Send quote email to customer (manual action)
     */
    public function sendEmail(Quote $quote)
    {
        try {
            Mail::to($quote->user->email)->send(new \App\Mail\QuoteMail($quote));

            return back()->with('success', 'تم إرسال البريد الإلكتروني للعميل بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إرسال البريد: '.$e->getMessage());
        }
    }

    /**
     * Convert a PAID quote into a competitive booking if missing
     */
    public function convertPaidToBooking(Quote $quote)
    {
        if ($quote->status !== 'paid') {
            return back()->with('error', 'يجب أن تكون حالة عرض السعر: تم الدفع');
        }

        if ($quote->bookings()->exists()) {
            return back()->with('success', 'تم تحويل هذا العرض إلى حجز مسبقاً');
        }

        // Ensure payment_status consistency
        if ($quote->payment_status !== 'paid') {
            $quote->payment_status = 'paid';
            $quote->payment_date = now();
            $quote->save();
        }

        try {
            $booking = $quote->convertToBooking();
        } catch (\Throwable $e) {
            Log::error('Failed converting paid quote to booking: '.$e->getMessage(), [
                'quote_id' => $quote->id,
            ]);

            return back()->with('error', 'فشل تحويل العرض إلى حجز: '.$e->getMessage());
        }

        return back()->with('success', 'تم تحويل العرض المدفوع إلى حجز تنافسي بنجاح: #'.$booking->booking_reference);
    }
}
