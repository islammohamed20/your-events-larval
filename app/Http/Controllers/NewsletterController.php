<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Mail\NewsletterSubscriptionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $data = $validator->validated();
            
            // Check if already subscribed
            $existing = Newsletter::where('email', $data['email'])->first();
            if ($existing && $existing->status === 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'أنت مشترك بالفعل في النشرة الإخبارية',
                ]);
            }

            // Create or update subscription
            $newsletter = Newsletter::updateOrCreate(
                ['email' => $data['email']],
                [
                    'status' => 'active',
                    'name' => $data['name'] ?? null,
                    'source' => 'footer',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'subscribed_at' => now(),
                    'unsubscribed_at' => null,
                ]
            );

            // Send welcome email using sales mailer
            try {
                Mail::to($data['email'])->send(new NewsletterSubscriptionMail($data['email'], $data['name'] ?? null));
            } catch (\Exception $e) {
                Log::error('Failed to send newsletter welcome email', [
                    'email' => $data['email'],
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'شكراً لاشتراكك! تم إرسال رسالة تأكيد إلى بريدك الإلكتروني',
            ]);

        } catch (\Exception $e) {
            Log::error('Newsletter subscription error', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الاشتراك. يرجى المحاولة مرة أخرى',
            ], 500);
        }
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني غير صحيح',
            ]);
        }

        try {
            $newsletter = Newsletter::where('email', $validator->validated()['email'])->first();
            
            if (!$newsletter) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا البريد الإلكتروني غير مشترك في النشرة الإخبارية',
                ]);
            }

            $newsletter->unsubscribe();

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء اشتراكك بنجاح',
            ]);

        } catch (\Exception $e) {
            Log::error('Newsletter unsubscribe error', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ. يرجى المحاولة مرة أخرى',
            ], 500);
        }
    }
}
