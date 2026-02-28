<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\CartItem;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\TapPayment;
use App\Services\N8nNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            ->withCount([
                'bookings as paid_bookings_count' => function ($q) {
                    $q->where('payment_status', 'paid');
                },
            ])
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

        $quote->load([
            'items.service.thumbnailImage',
            'items.service.images',
        ]);
        $quote->loadCount([
            'bookings as paid_bookings_count' => function ($q) {
                $q->where('payment_status', 'paid');
            },
        ]);

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
            if (! $cartItem->service) {
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
                    'error' => $e->getMessage(),
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
            Log::error('Failed to send quote email to customer: '.$e->getMessage());
        }

        // إرسال إشعار n8n للإدارة (Gmail + WhatsApp)
        try {
            $this->n8nService->sendNewQuoteNotification($quote->fresh(['items', 'user']));
        } catch (\Exception $e) {
            // لا تفشل العملية إذا فشل الإشعار
            Log::error('Failed to send n8n notification: '.$e->getMessage());
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
        if ($quote->user_id !== $user->id && ! $user->is_admin) {
            abort(403, 'غير مصرح لك بتحميل هذا العرض');
        }

        $quote->load([
            'items.service.thumbnailImage',
            'items.service.images',
            'user',
        ]);

        $html = view('quotes.pdf', compact('quote'))->render();

        $tempDir = storage_path('app/mpdf');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

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
            'tempDir' => $tempDir,
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
        return response($mpdf->Output('quote-'.$quote->quote_number.'.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="quote-'.$quote->quote_number.'.pdf"');
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
        if (! in_array($quote->status, ['pending', 'under_review'])) {
            return back()->with('error', 'لا يمكن تعديل عرض السعر بعد الموافقة عليه');
        }

        $validated = $request->validate([
            'customer_notes' => 'nullable|string|max:2000',
        ]);

        $quote->update($validated);

        return back()->with('success', 'تم تحديث الملاحظات بنجاح');
    }

    /**
     * Show combined booking and payment page for approved quote
     */
    public function showCompleteBookingPayment(Quote $quote)
    {
        if ($quote->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        if ($quote->status !== 'approved') {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'يجب أن يتم الموافقة على عرض السعر أولاً قبل استكمال بيانات الحجز');
        }

        if ($quote->items()->count() === 0) {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'هذا العرض لا يحتوي على أي خدمات. يرجى إنشاء عرض جديد أو إضافة خدمات قبل استكمال بيانات الحجز.');
        }

        $quote->load('items.service', 'user');
        $bookingData = session('quotes.complete_booking.'.$quote->id, []);

        return view('quotes.complete-booking-payment', compact('quote', 'bookingData'));
    }

    public function processCompleteBookingPayment(Request $request, Quote $quote)
    {
        if ($quote->user_id !== Auth::id()) {
            abort(403);
        }

        if ($quote->status !== 'approved') {
            return back()->with('error', 'يجب أن يتم الموافقة على عرض السعر أولاً');
        }

        if ($quote->items()->count() === 0) {
            return back()->with('error', 'لا يمكن استكمال بيانات الحجز لهذا العرض لأنه لا يحتوي على خدمات.');
        }

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'event_date' => 'required|date|after:today',
            'event_location' => 'required|string|max:255',
            'event_lat' => 'required|numeric|between:-90,90',
            'event_lng' => 'required|numeric|between:-180,180',
            'guests_count' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
            'payment_method' => 'required|in:mada,visa,mastercard,applepay,stcpay',
        ]);

        // Store booking data in session for payment processing
        $request->session()->put('quotes.complete_booking.'.$quote->id, $validated);

        return $this->startTapPayment($quote, $validated);
    }

    /**
     * @deprecated Use showCompleteBookingPayment instead
     */
    public function showCompleteBooking(Quote $quote)
    {
        return redirect()->route('quotes.complete-booking', $quote);
    }

    /**
     * @deprecated Use processCompleteBookingPayment instead
     */
    public function storeCompleteBooking(Request $request, Quote $quote)
    {
        if ($quote->user_id !== Auth::id()) {
            abort(403);
        }

        if ($quote->status !== 'approved') {
            return back()->with('error', 'يجب أن يتم الموافقة على عرض السعر أولاً');
        }

        if ($quote->items()->count() === 0) {
            return back()->with('error', 'لا يمكن استكمال بيانات الحجز لهذا العرض لأنه لا يحتوي على خدمات.');
        }

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'event_date' => 'required|date|after:today',
            'event_location' => 'required|string|max:255',
            'event_lat' => 'required|numeric|between:-90,90',
            'event_lng' => 'required|numeric|between:-180,180',
            'guests_count' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        $request->session()->put('quotes.complete_booking.'.$quote->id, $validated);

        return redirect()->route('quotes.complete-booking', $quote);
    }

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

        if ($quote->items()->count() === 0) {
            return redirect()->route('quotes.show', $quote)
                ->with('error', 'هذا العرض لا يحتوي على أي خدمات. يرجى إنشاء عرض جديد أو إضافة خدمات قبل الدفع.');
        }

        return redirect()->route('quotes.complete-booking', $quote);
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

        if ($quote->items()->count() === 0) {
            return back()->with('error', 'لا يمكن إتمام الدفع لهذا العرض لأنه لا يحتوي على خدمات.');
        }

        $rules = [
            'payment_method' => 'required|in:mada,visa,mastercard,applepay,stcpay',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'event_date' => 'required|date|after:today',
            'event_location' => 'required|string|max:255',
            'event_lat' => 'nullable|numeric|between:-90,90|required_with:event_lng',
            'event_lng' => 'nullable|numeric|between:-180,180|required_with:event_lat',
            'guests_count' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        return $this->startTapPayment($quote, $validated);
    }

    public function tapCallback(Request $request, Quote $quote)
    {
        $tapChargeId = $request->query('tap_id') ?: $request->query('charge_id') ?: $request->query('id');
        if (! $tapChargeId) {
            return redirect()->route('quotes.show', $quote)->with('error', 'لم يتم استلام معرف عملية الدفع من Tap.');
        }

        $payment = \App\Models\Payment::where('gateway', 'tap')
            ->where('gateway_payment_id', $tapChargeId)
            ->latest()
            ->first();

        $tapPayment = TapPayment::where('tap_charge_id', $tapChargeId)->latest()->first();

        $charge = $this->fetchTapCharge($tapChargeId);
        if (! $charge) {
            return redirect()->route('quotes.show', $quote)->with('error', 'تعذر التحقق من حالة الدفع من Tap.');
        }

        $status = strtoupper((string) ($charge['status'] ?? ''));

        if ($status === 'CAPTURED') {
            if ($payment) {
                $payment->update([
                    'status' => 'paid',
                    'gateway_transaction_id' => $charge['transaction']['id'] ?? $payment->gateway_transaction_id,
                    'paid_at' => now(),
                    'invoice_url' => $charge['receipt']['url'] ?? $payment->invoice_url,
                    'metadata' => array_merge((array) ($payment->metadata ?? []), ['tap_charge' => $charge]),
                ]);
            }

            if ($tapPayment) {
                $tapPayment->update([
                    'tap_transaction_id' => $charge['transaction']['id'] ?? $tapPayment->tap_transaction_id,
                    'amount' => $charge['amount'] ?? $tapPayment->amount,
                    'currency' => $charge['currency'] ?? $tapPayment->currency,
                    'status' => $charge['status'] ?? $tapPayment->status,
                    'customer_email' => $charge['customer']['email'] ?? $tapPayment->customer_email,
                    'customer_phone' => $charge['customer']['phone']['number'] ?? $tapPayment->customer_phone,
                    'charge_data' => $charge,
                ]);
            } elseif ($payment) {
                $bookingIdForTap = (int) (($payment->metadata['booking_id'] ?? 0) ?: 0);
                $bookingForTap = $bookingIdForTap ? Booking::find($bookingIdForTap) : null;

                TapPayment::create([
                    'payment_id' => $payment->id,
                    'booking_id' => $bookingForTap?->id,
                    'quote_id' => $quote->id,
                    'tap_charge_id' => $tapChargeId,
                    'tap_transaction_id' => $charge['transaction']['id'] ?? null,
                    'amount' => $charge['amount'] ?? $payment->amount,
                    'currency' => $charge['currency'] ?? $payment->currency,
                    'status' => $charge['status'] ?? null,
                    'customer_email' => $charge['customer']['email'] ?? $bookingForTap?->client_email,
                    'customer_phone' => $charge['customer']['phone']['number'] ?? $bookingForTap?->client_phone,
                    'charge_data' => $charge,
                ]);
            }

            $bookingId = (int) (($payment->metadata['booking_id'] ?? 0) ?: 0);
            $booking = $bookingId ? Booking::find($bookingId) : null;
            if ($booking) {
                $booking->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'card',
                    'status' => 'awaiting_supplier',
                ]);

                if ((int) ($booking->notified_suppliers_count ?? 0) === 0) {
                    try {
                        $booking->notifyEligibleSuppliers();
                    } catch (\Throwable $e) {
                        Log::warning('Supplier notification failed: '.$e->getMessage());
                    }
                }

                try {
                    Mail::to($booking->user?->email)->send(new \App\Mail\QuotePaymentConfirmationMail($quote->fresh()));
                } catch (\Throwable $e) {
                    Log::error('Failed to send quote payment confirmation email: '.$e->getMessage());
                }

                try {
                    Mail::to($booking->user?->email)->send(new BookingConfirmation($booking->fresh()));
                } catch (\Throwable $e) {
                    Log::error('Failed to send confirmation email: '.$e->getMessage());
                }
            }

            $quote->update([
                'status' => 'paid',
                'payment_status' => 'paid',
                'payment_date' => now(),
                'payment_method' => 'card',
                'payment_reference' => $tapChargeId,
            ]);

            return redirect()->route('quotes.show', $quote)->with('success', 'تم استلام الدفع بنجاح عبر Tap.');
        }

        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $charge['response']['message'] ?? $payment->failure_reason,
                'failed_at' => now(),
                'metadata' => array_merge((array) ($payment->metadata ?? []), ['tap_charge' => $charge]),
            ]);
        }

        if ($payment) {
            $bookingId = (int) (($payment->metadata['booking_id'] ?? 0) ?: 0);
            $booking = $bookingId ? Booking::find($bookingId) : null;
            if ($booking && $booking->payment_status === 'pending') {
                $booking->delete();
            }
        }

        return redirect()->route('quotes.show', $quote)->with('error', 'لم يكتمل الدفع. يرجى المحاولة مرة أخرى.');
    }

    public function tapWebhook(Request $request)
    {
        $webhookSecret = (string) config('services.tap.webhook_secret');

        if ($webhookSecret !== '') {
            $signature = (string) $request->header('X-Tap-Signature', '');
            if ($signature === '') {
                $signature = (string) $request->header('X-Tap-Webhook-Secret', '');
            }

            if ($signature === '' || ! hash_equals($webhookSecret, $signature)) {
                return response()->json(['ok' => false], 401);
            }
        }

        $payload = $request->all();
        $tapChargeId = $payload['id'] ?? ($payload['charge_id'] ?? ($payload['data']['id'] ?? null));
        if (! $tapChargeId) {
            return response()->json(['ok' => true]);
        }

        $payment = \App\Models\Payment::where('gateway', 'tap')
            ->where('gateway_payment_id', $tapChargeId)
            ->latest()
            ->first();

        $tapPayment = TapPayment::where('tap_charge_id', $tapChargeId)->latest()->first();

        if (! $payment) {
            return response()->json(['ok' => true]);
        }

        $charge = $this->fetchTapCharge($tapChargeId);
        if (! $charge) {
            return response()->json(['ok' => true]);
        }

        $status = strtoupper((string) ($charge['status'] ?? ''));
        if ($status === 'CAPTURED' && $payment->status !== 'paid') {
            $payment->update([
                'status' => 'paid',
                'gateway_transaction_id' => $charge['transaction']['id'] ?? $payment->gateway_transaction_id,
                'paid_at' => now(),
                'invoice_url' => $charge['receipt']['url'] ?? $payment->invoice_url,
                'metadata' => array_merge((array) ($payment->metadata ?? []), ['tap_charge' => $charge]),
            ]);

            if ($tapPayment) {
                $tapPayment->update([
                    'tap_transaction_id' => $charge['transaction']['id'] ?? $tapPayment->tap_transaction_id,
                    'amount' => $charge['amount'] ?? $tapPayment->amount,
                    'currency' => $charge['currency'] ?? $tapPayment->currency,
                    'status' => $charge['status'] ?? $tapPayment->status,
                    'customer_email' => $charge['customer']['email'] ?? $tapPayment->customer_email,
                    'customer_phone' => $charge['customer']['phone']['number'] ?? $tapPayment->customer_phone,
                    'charge_data' => $charge,
                ]);
            } else {
                $bookingIdForTap = (int) (($payment->metadata['booking_id'] ?? 0) ?: 0);
                $bookingForTap = $bookingIdForTap ? Booking::find($bookingIdForTap) : null;

                TapPayment::create([
                    'payment_id' => $payment->id,
                    'booking_id' => $bookingForTap?->id,
                    'quote_id' => $bookingForTap?->quote_id,
                    'tap_charge_id' => $tapChargeId,
                    'tap_transaction_id' => $charge['transaction']['id'] ?? null,
                    'amount' => $charge['amount'] ?? $payment->amount,
                    'currency' => $charge['currency'] ?? $payment->currency,
                    'status' => $charge['status'] ?? null,
                    'customer_email' => $charge['customer']['email'] ?? $bookingForTap?->client_email,
                    'customer_phone' => $charge['customer']['phone']['number'] ?? $bookingForTap?->client_phone,
                    'charge_data' => $charge,
                ]);
            }

            $bookingId = (int) (($payment->metadata['booking_id'] ?? 0) ?: 0);
            $booking = $bookingId ? Booking::find($bookingId) : null;
            if ($booking && $booking->payment_status !== 'paid') {
                $booking->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'card',
                    'status' => 'awaiting_supplier',
                ]);

                if ((int) ($booking->notified_suppliers_count ?? 0) === 0) {
                    try {
                        $booking->notifyEligibleSuppliers();
                    } catch (\Throwable $e) {
                        Log::warning('Supplier notification failed: '.$e->getMessage());
                    }
                }
            }

            $quoteId = (int) (($payment->metadata['quote_id'] ?? 0) ?: 0);
            if (! $quoteId && $booking) {
                $quoteId = (int) ($booking->quote_id ?: 0);
            }
            if ($quoteId) {
                $quoteForUpdate = Quote::find($quoteId);
                if ($quoteForUpdate && $quoteForUpdate->payment_status !== 'paid') {
                    $quoteForUpdate->update([
                        'status' => 'paid',
                        'payment_status' => 'paid',
                        'payment_date' => now(),
                        'payment_method' => 'card',
                        'payment_reference' => $tapChargeId,
                    ]);
                }
            }
        } elseif (in_array($status, ['FAILED', 'DECLINED', 'CANCELLED'], true) && $payment->status !== 'failed') {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $charge['response']['message'] ?? $payment->failure_reason,
                'failed_at' => now(),
                'metadata' => array_merge((array) ($payment->metadata ?? []), ['tap_charge' => $charge]),
            ]);

            if ($tapPayment) {
                $tapPayment->update([
                    'tap_transaction_id' => $charge['transaction']['id'] ?? $tapPayment->tap_transaction_id,
                    'amount' => $charge['amount'] ?? $tapPayment->amount,
                    'currency' => $charge['currency'] ?? $tapPayment->currency,
                    'status' => $charge['status'] ?? $tapPayment->status,
                    'customer_email' => $charge['customer']['email'] ?? $tapPayment->customer_email,
                    'customer_phone' => $charge['customer']['phone']['number'] ?? $tapPayment->customer_phone,
                    'charge_data' => $charge,
                ]);
            } elseif ($payment) {
                $bookingIdForTap = (int) (($payment->metadata['booking_id'] ?? 0) ?: 0);
                $bookingForTap = $bookingIdForTap ? Booking::find($bookingIdForTap) : null;

                TapPayment::create([
                    'payment_id' => $payment->id,
                    'booking_id' => $bookingForTap?->id,
                    'quote_id' => $bookingForTap?->quote_id,
                    'tap_charge_id' => $tapChargeId,
                    'tap_transaction_id' => $charge['transaction']['id'] ?? null,
                    'amount' => $charge['amount'] ?? $payment->amount,
                    'currency' => $charge['currency'] ?? $payment->currency,
                    'status' => $charge['status'] ?? null,
                    'customer_email' => $charge['customer']['email'] ?? $bookingForTap?->client_email,
                    'customer_phone' => $charge['customer']['phone']['number'] ?? $bookingForTap?->client_phone,
                    'charge_data' => $charge,
                ]);
            }

            $bookingId = (int) (($payment->metadata['booking_id'] ?? 0) ?: 0);
            $booking = $bookingId ? Booking::find($bookingId) : null;
            if ($booking && $booking->payment_status === 'pending') {
                $booking->delete();
            }
        }

        return response()->json(['ok' => true]);
    }

    private function startTapPayment(Quote $quote, array $validated)
    {
        $tapSecret = (string) config('services.tap.secret_key');
        $tapBaseUrl = (string) config('services.tap.base_url');

        if ($tapSecret === '') {
            return back()->with('error', 'Tap غير مُفعّل حالياً: لم يتم ضبط TAP_SECRET_KEY.');
        }

        $quote->load('items.service', 'user');

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'quote_id' => $quote->id,
            'activity_name' => $quote->items->first()->service_name ?? 'فعالية',
            'service_id' => $quote->items->first()->service_id ?? null,
            'client_name' => $validated['client_name'],
            'client_email' => Auth::user()->email,
            'client_phone' => $validated['client_phone'],
            'event_date' => $validated['event_date'],
            'event_location' => $validated['event_location'],
            'event_lat' => $validated['event_lat'] ?? null,
            'event_lng' => $validated['event_lng'] ?? null,
            'guests_count' => $validated['guests_count'],
            'special_requests' => $validated['special_requests'] ?? null,
            'total_amount' => $quote->total,
            'payment_method' => 'card',
            'payment_status' => 'pending',
            'status' => 'pending',
            'booking_reference' => 'BOOK-YE-'.str_pad(Booking::count() + 1, 6, '0', STR_PAD_LEFT),
            'expires_at' => now()->addHours(24),
        ]);

        $selectedPaymentMethod = (string) ($validated['payment_method'] ?? 'card');
        $paymentMethodForPaymentRow = in_array($selectedPaymentMethod, ['mada', 'visa', 'mastercard', 'applepay', 'stcpay'], true)
            ? $selectedPaymentMethod
            : null;

        $payment = \App\Models\Payment::create([
            'user_id' => Auth::id(),
            'booking_id' => $booking->id,
            'gateway' => 'tap',
            'amount' => $quote->total,
            'currency' => 'SAR',
            'status' => 'processing',
            'payment_method' => $paymentMethodForPaymentRow,
            'metadata' => [
                'source' => 'quotes.startTapPayment',
                'quote_id' => $quote->id,
                'quote_number' => $quote->quote_number,
                'booking_id' => $booking->id,
                'selected_payment_method' => $selectedPaymentMethod,
            ],
        ]);

        $payload = [
            'amount' => (float) $quote->total,
            'currency' => 'SAR',
            'threeDSecure' => true,
            'save_card' => false,
            'source' => [
                'id' => 'src_all',
            ],
            'description' => 'Quote '.$quote->quote_number,
            'metadata' => [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'quote_id' => $quote->id,
            ],
            'customer' => [
                'first_name' => $validated['client_name'],
                'email' => Auth::user()->email,
                'phone' => [
                    'country_code' => '966',
                    'number' => $validated['client_phone'],
                ],
            ],
            'redirect' => [
                'url' => route('tap.callback', ['quote' => $quote->id]),
            ],
            'post' => [
                'url' => route('tap.webhook'),
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$tapSecret,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(rtrim($tapBaseUrl, '/').'/charges', $payload);

        if (! $response->successful()) {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => 'Tap API error',
                'failed_at' => now(),
                'metadata' => array_merge((array) ($payment->metadata ?? []), [
                    'tap_response' => $response->json(),
                    'tap_status' => $response->status(),
                ]),
            ]);

            return back()->with('error', 'تعذر إنشاء عملية الدفع عبر Tap. يرجى المحاولة لاحقاً.');
        }

        $charge = (array) $response->json();
        $tapChargeId = $charge['id'] ?? null;
        $transactionUrl = $charge['transaction']['url'] ?? null;

        if (! $tapChargeId || ! $transactionUrl) {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => 'Tap returned invalid response',
                'failed_at' => now(),
                'metadata' => array_merge((array) ($payment->metadata ?? []), ['tap_charge' => $charge]),
            ]);

            return back()->with('error', 'تعذر بدء عملية الدفع عبر Tap بسبب استجابة غير مكتملة.');
        }

        $payment->update([
            'gateway_payment_id' => $tapChargeId,
            'invoice_url' => $transactionUrl,
            'metadata' => array_merge((array) ($payment->metadata ?? []), ['tap_charge' => $charge]),
        ]);

        TapPayment::updateOrCreate(
            ['tap_charge_id' => $tapChargeId],
            [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'quote_id' => $quote->id,
                'tap_transaction_id' => $charge['transaction']['id'] ?? null,
                'amount' => $charge['amount'] ?? $payment->amount,
                'currency' => $charge['currency'] ?? $payment->currency,
                'status' => $charge['status'] ?? null,
                'customer_email' => $charge['customer']['email'] ?? $booking->client_email,
                'customer_phone' => $charge['customer']['phone']['number'] ?? $booking->client_phone,
                'charge_data' => $charge,
            ]
        );

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect_url' => $transactionUrl,
                'tap_charge_id' => $tapChargeId,
            ]);
        }

        $redirectResponse = redirect()->away($transactionUrl, 303);
        $redirectResponse->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $redirectResponse->headers->set('Pragma', 'no-cache');

        return $redirectResponse;
    }

    private function fetchTapCharge(string $tapChargeId): ?array
    {
        $tapSecret = (string) config('services.tap.secret_key');
        $tapBaseUrl = (string) config('services.tap.base_url');

        if ($tapSecret === '') {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$tapSecret,
                'Accept' => 'application/json',
            ])->get(rtrim($tapBaseUrl, '/').'/charges/'.$tapChargeId);

            if (! $response->successful()) {
                return null;
            }

            return (array) $response->json();
        } catch (\Throwable $e) {
            Log::warning('Tap charge fetch failed: '.$e->getMessage());

            return null;
        }
    }
}
