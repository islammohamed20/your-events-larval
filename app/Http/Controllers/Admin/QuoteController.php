<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Mail\QuoteMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
        ];
        
        return view('admin.quotes.index', compact('quotes', 'stats'));
    }
    
    /**
     * Show quote details
     */
    public function show(Quote $quote)
    {
        $quote->load('user', 'items.service');
        
        return view('admin.quotes.show', compact('quote'));
    }
    
    /**
     * Update quote status
     */
    public function updateStatus(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,approved,rejected,completed',
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
                $quote->load('items.service', 'user');
                Mail::to($quote->user->email)->send(new QuoteMail($quote));
            } catch (\Exception $e) {
                Log::error('Failed to send approval email: ' . $e->getMessage());
            }
        } elseif ($validated['status'] === 'rejected') {
            $quote->rejected_at = now();
            // يمكن إرسال إيميل بالرفض
            try {
                $quote->load('items.service', 'user');
                Mail::to($quote->user->email)->send(new QuoteMail($quote));
            } catch (\Exception $e) {
                Log::error('Failed to send rejection email: ' . $e->getMessage());
            }
        }
        
        $quote->save();
        
        // Recalculate totals if discount was applied
        if (isset($validated['discount'])) {
            $quote->calculateTotals();
        }
        
        return redirect()->back()->with('success', 'تم تحديث حالة عرض السعر بنجاح');
    }
    
    /**
     * Delete quote
     */
    public function destroy(Quote $quote)
    {
        $quote->delete();
        
        return redirect()->route('admin.quotes.index')->with('success', 'تم حذف عرض السعر بنجاح');
    }

    /**
     * Send quote email to customer (manual action)
     */
    public function sendEmail(Quote $quote)
    {
        try {
            \Mail::to($quote->user->email)->send(new \App\Mail\QuoteMail($quote));
            return back()->with('success', 'تم إرسال البريد الإلكتروني للعميل بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إرسال البريد: ' . $e->getMessage());
        }
    }
}
