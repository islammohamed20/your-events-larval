<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Service;
use App\Models\Category;
use App\Models\SupplierService;
use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    /**
     * Show supplier registration form
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        // احصل على جميع الخدمات مع معلومات الفئة
        $allServices = Service::where('is_active', true)
            ->with('category')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'subtitle' => $service->subtitle,
                    'category_id' => $service->category_id,
                ];
            });

        return view('suppliers.register', compact('categories', 'allServices'));
    }

    /**
     * Store supplier registration
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_type' => 'required|in:individual,company',
            'name' => 'required|string|max:255',
            'commercial_register' => 'required_if:supplier_type,company|nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'headquarters_city' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
            'primary_phone' => 'required|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:suppliers,email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|max:500',
            'social_media' => 'nullable|array',
            'social_media.twitter' => 'nullable|url',
            'social_media.instagram' => 'nullable|url',
            'social_media.snapchat' => 'nullable|url',
            'social_media.tiktok' => 'nullable|url',
            'terms_accepted' => 'required|accepted',
            'privacy_accepted' => 'required|accepted',
            
            // Files
            'commercial_register_file' => 'required_if:supplier_type,company|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tax_certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'company_profile_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'portfolio_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // تحقق من أن جميع الخدمات تنتمي إلى الفئات المختارة
        $selectedServiceIds = $validated['services'];
        $selectedCategoryIds = $validated['categories'];
        
        $services = Service::whereIn('id', $selectedServiceIds)->get();
        $invalidServices = $services->whereNotIn('category_id', $selectedCategoryIds);
        
        if ($invalidServices->count() > 0) {
            return back()->withInput()->withErrors([
                'services' => 'بعض الخدمات المختارة لا تنتمي إلى الفئات المحددة'
            ]);
        }

        // Ensure boolean fields and default status
        $validated['terms_accepted'] = $request->boolean('terms_accepted');
        $validated['privacy_accepted'] = $request->boolean('privacy_accepted');
        $validated['status'] = 'pending';
        
        // حول الفئات والخدمات إلى JSON للتوافقية
        $validated['services_offered'] = $selectedCategoryIds;
        
        // أزل الحقول غير المطلوبة
        unset($validated['categories']);
        unset($validated['services']);

        // Handle file uploads
        if ($request->hasFile('commercial_register_file')) {
            $validated['commercial_register_file'] = $request->file('commercial_register_file')->store('suppliers/documents', 'public');
        }

        if ($request->hasFile('tax_certificate_file')) {
            $validated['tax_certificate_file'] = $request->file('tax_certificate_file')->store('suppliers/documents', 'public');
        }

        if ($request->hasFile('company_profile_file')) {
            $validated['company_profile_file'] = $request->file('company_profile_file')->store('suppliers/documents', 'public');
        }

        if ($request->hasFile('portfolio_files')) {
            $portfolioFiles = [];
            foreach ($request->file('portfolio_files') as $file) {
                $portfolioFiles[] = $file->store('suppliers/portfolio', 'public');
            }
            $validated['portfolio_files'] = $portfolioFiles;
        }

        // Hash supplier password before create
        if (isset($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        // Create supplier
        $supplier = Supplier::create($validated);

        // حفظ الخدمات في جدول SupplierServices (Many-to-Many)
        foreach ($selectedServiceIds as $serviceId) {
            $service = Service::find($serviceId);
            if ($service) {
                SupplierService::create([
                    'supplier_id' => $supplier->id,
                    'service_id' => $service->id,
                    'category_id' => $service->category_id,
                    'is_available' => true,
                ]);
            }
        }

        // إرسال OTP للبريد الإلكتروني باستخدام القالب الموحد عبر OtpVerification
        try {
            OtpVerification::generate($supplier->email, 'email_verification');
        } catch (\Exception $e) {
            Log::error('Failed to send supplier OTP: ' . $e->getMessage());
        }

        session(['supplier_email' => $supplier->email]);

        return redirect()->route('suppliers.verify-otp')->with('success', 'تم التسجيل بنجاح! يرجى التحقق من بريدك الإلكتروني لإدخال رمز التحقق.');
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyOtp(Request $request)
    {
        // Prefer session email; fallback to query param then set session
        $email = $request->session()->get('supplier_email');
        if (!$email) {
            $email = $request->query('email');
            if ($email) {
                $request->session()->put('supplier_email', $email);
            }
        }

        if (!$email) {
            return redirect()->route('suppliers.register')->with('error', 'يرجى التسجيل أولاً');
        }

        return view('suppliers.verify-otp', ['email' => $email]);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        // Prefer session email; fallback to request payload
        $email = $request->session()->get('supplier_email') ?: $request->input('email');
        
        if (!$email) {
            return redirect()->route('suppliers.register')->with('error', 'انتهت صلاحية الجلسة');
        }

        $supplier = Supplier::where('email', $email)->first();

        if (!$supplier) {
            return back()->with('error', 'البريد الإلكتروني غير صحيح');
        }

        // Verify OTP
        $result = OtpVerification::verify($email, $request->otp, 'email_verification');

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        $supplier->update(['email_verified_at' => now()]);

        return redirect()->route('suppliers.success')->with('success', 'تم ارسال طلبك بنجاح لادارة الموقع.');
    }

    /**
     * Show success page
     */
    public function success()
    {
        return view('suppliers.success');
    }

    /**
     * Resend OTP for supplier email
     */
    public function resendOtp(Request $request)
    {
        // Get email either from request body or session
        $email = $request->input('email') ?: $request->session()->get('supplier_email');

        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني غير موجود في الجلسة. يرجى المحاولة مرة أخرى.',
            ], 422);
        }

        $supplier = Supplier::where('email', $email)->first();
        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => 'هذا البريد الإلكتروني غير مسجل كمورد.',
            ], 404);
        }

        try {
            OtpVerification::generate($email, 'email_verification');
            // Refresh session email to ensure continuity
            $request->session()->put('supplier_email', $email);
        } catch (\Throwable $e) {
            Log::error('Failed to resend supplier OTP: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الرمز. حاول لاحقًا.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رمز تحقق جديد إلى بريدك الإلكتروني.',
            'expires_in' => 600,
        ]);
    }
}
