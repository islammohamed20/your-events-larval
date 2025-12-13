<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\CartItem;
use App\Models\Booking;
use App\Services\N8nNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Mail\BookingConfirmation;
use Mpdf\Mpdf;

class QuoteController extends Controller
{
    protected $n8nService;

    public function __construct(N8nNotificationService $n8nService)
    {
        $this->middleware('auth');
        $this->n8nService = $n8nService;
    }

    /**
     * Display user's quotes
     */
    public function index()
    {
        $quotes = Quote::where('user_id', Auth::id())
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('quotes.index', compact('quotes'));
    }

    /**
     * Show single quote
     */
    public function show(Quote $quote)
    {
        // Ensure user can only view their own quotes
        if ($quote->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بعرض هذا العرض');
        }

        $quote->load('items.service');

        return view('quotes.show', compact('quote'));
    }

    /**
     * Create quote from cart (checkout)
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'customer_notes' => 'nullable|string|max:2000',
        ]);

        // Get cart items
        $cartItems = CartItem::getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'السلة فارغة');
        }

        // Create quote with status 'under_review'
        $quote = Quote::create([
            'user_id' => Auth::id(),
            'quote_number' => Quote::generateQuoteNumber(),
            'status' => 'under_review', // الحالة الافتراضية: قيد المراجعة
            'customer_notes' => $validated['customer_notes'] ?? null,
        ]);

        // Create quote items from cart
        $itemsCreated = 0;
        foreach ($cartItems as $cartItem) {
            // تجاوز العناصر التي ليس لها خدمة (الخدمة محذوفة)
            if (!$cartItem->service) {
                Log::warning('Cart item skipped - service not found', ['cart_item_id' => $cartItem->id, 'service_id' => $cartItem->service_id]);
                continue;
            }
            
            try {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'service_id' => $cartItem->service_id,
                    'service_name' => $cartItem->service->name,
                    'service_description' => $cartItem->service->description ?? '',
                    'price' => $cartItem->price,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $cartItem->price * $cartItem->quantity,
                    'customer_notes' => $cartItem->customer_notes,
                    'selections' => $cartItem->selections,
                ]);
                $itemsCreated++;
            } catch (\Exception $e) {
                Log::error('Failed to create quote item', [
                    'cart_item_id' => $cartItem->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // إذا لم يتم إنشاء أي عناصر، احذف العرض وأرجع خطأ
        if ($itemsCreated === 0) {
            $quote->delete();
            return redirect()->route('cart.index')->with('error', 'لم نتمكن من إنشاء عرض السعر. يرجى التحقق من السلة والمحاولة مرة أخرى.');
        }

        // Calculate totals
        $quote->calculateTotals();

        // Clear cart
        CartItem::clearCart();

        // إرسال إيميل فوري للعميل بعرض السعر
        try {
            $quote->load('items.service', 'user');
            Mail::to(Auth::user()->email)->send(new \App\Mail\QuoteMail($quote));
        } catch (\Exception $e) {
            Log::error('Failed to send quote email to customer: ' . $e->getMessage());
        }

        // إرسال إشعار n8n للإدارة (Gmail + WhatsApp)
        try {
            $this->n8nService->sendNewQuoteNotification($quote->fresh(['items', 'user']));
        } catch (\Exception $e) {
            // لا تفشل العملية إذا فشل الإشعار
            Log::error('Failed to send n8n notification: ' . $e->getMessage());
        }

        return redirect()->route('quotes.show', $quote)->with('success', 'تم إنشاء عرض السعر بنجاح! تم إرسال نسخة إلى بريدك الإلكتروني وسيتم مراجعته من قبل فريقنا قريباً.');
    }

    /**
     * Download quote as PDF
     */
    public function downloadPdf(Quote $quote)
    {
        // Ensure user can only download their own quotes (or admin can download any)
        $user = Auth::user();
        if ($quote->user_id !== $user->id && !$user->is_admin) {
            abort(403, 'غير مصرح لك بتحميل هذا العرض');
        }

        $quote->load('items.service', 'user');

        // Generate HTML content
        $html = view('quotes.pdf', compact('quote'))->render();

        // Create mPDF instance with Arabic support
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'default_font' => 'dejavusans',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'autoArabic' => true,
        ]);

        // Set letter head PNG as full page background
        $letterHeadPath = public_path('storage/extra/letter head.png');
        if (file_exists($letterHeadPath)) {
            $mpdf->SetWatermarkImage(
                $letterHeadPath,
                1,        // Opacity (1 = full opacity)
                [210, 297],  // Size in mm (A4 full size)
                [0, 0]    // Position
            );
            $mpdf->showWatermarkImage = true;
        }

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        // Output PDF as download
        return response($mpdf->Output('quote-' . $quote->quote_number . '.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="quote-' . $quote->quote_number . '.pdf"');
    }

    /**
     * Update quote notes (customer can edit before approval)
     */
    public function updateNotes(Request $request, Quote $quote)
    {
        // Ensure user owns the quote
        if ($quote->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow editing if quote is still pending or under review
        if (!in_array($quote->status, ['pending', 'under_review'])) {
            return back()->with('error', 'لا يمكن تعديل عرض السعر بعد الموافقة عليه');
        }

        $validated = $request->validate([
            'customer_notes' => 'nullable|string|max:2000',
        ]);

        $quote->update($validated);

        return back()->with('success', 'تم تحديث الملاحظات بنجاح');
    }

    /**
     * Show payment page for approved quote
     */
    public function showPayment(Quote $quote)
    {
        // Ensure user owns the quote
        if ($quote->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // Only allow payment for approved quotes
        if ($quote->status !== 'approved') {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'يجب أن يتم الموافقة على عرض السعر أولاً قبل الدفع');
        }

        $quote->load('items.service', 'user');

        return view('quotes.payment', compact('quote'));
    }

    /**
     * Process payment and create booking
     */
    public function processPayment(Request $request, Quote $quote)
    {
        // Ensure user owns the quote
        if ($quote->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow payment for approved quotes
        if ($quote->status !== 'approved') {
            return back()->with('error', 'يجب أن يتم الموافقة على عرض السعر أولاً');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:card,bank_transfer,cash',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'event_date' => 'required|date|after:today',
            'event_location' => 'required|string|max:255',
            'guests_count' => 'required|integer|min:1',
            'card_type' => 'required_if:payment_method,card|nullable|in:visa,mastercard,mada',
            'card_holder_name' => 'required_if:payment_method,card|nullable|string|max:255',
            'card_last_four' => 'required_if:payment_method,card|nullable|size:4|regex:/^[0-9]{4}$/',
            'card_expiry_month' => 'required_if:payment_method,card|nullable|numeric|between:1,12',
            'card_expiry_year' => 'required_if:payment_method,card|nullable|numeric|min:' . date('Y'),
            'special_requests' => 'nullable|string',
        ]);

        // Create booking from quote
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'quote_id' => $quote->id,
            'client_name' => $validated['client_name'],
            'client_email' => Auth::user()->email,
            'client_phone' => $validated['client_phone'],
            'event_date' => $validated['event_date'],
            'event_location' => $validated['event_location'],
            'guests_count' => $validated['guests_count'],
            'special_requests' => $validated['special_requests'] ?? null,
            'total_amount' => $quote->total,
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending', // In real app, this would be 'paid' after payment gateway
            'status' => 'confirmed',
            'booking_reference' => 'BOOK-YE-' . str_pad(Booking::count() + 1, 6, '0', STR_PAD_LEFT),
        ]);

        // Update payment notes with card info if card payment
        if ($validated['payment_method'] === 'card') {
            $booking->payment_notes = "Card Type: {$validated['card_type']}, Holder: {$validated['card_holder_name']}, Last 4: {$validated['card_last_four']}, Expiry: {$validated['card_expiry_month']}/{$validated['card_expiry_year']}";
            $booking->save();
        }

        // Update quote status to booked
        $quote->update(['status' => 'booked']);

        try {
            \App\Models\Payment::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'quote_id' => $quote->id,
                'booking_id' => $booking->id,
                'amount' => $quote->total,
                'currency' => 'SAR',
                'method' => $validated['payment_method'],
                'status' => 'pending',
                'provider' => 'manual',
                'notes' => $validated['payment_method'] === 'card' ? $booking->payment_notes : null,
                'metadata' => [
                    'source' => 'quotes.processPayment',
                ],
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Payment record creation failed: ' . $e->getMessage());
        }

        // Send confirmation email
        try {
            Mail::to(Auth::user()->email)->send(new BookingConfirmation($booking));
        } catch (\Exception $e) {
            Log::error('Failed to send confirmation email: ' . $e->getMessage());
        }

        return redirect()->route('quotes.show', $quote)->with('success', 'تم تأكيد عرض السعر وإنشاء الحجز بنجاح!');
    }
}
