<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
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
        return view('suppliers.register');
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
            'description' => 'nullable|string',
            'services_offered' => 'required|array|min:1',
            'primary_phone' => 'required|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:suppliers,email',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string|max:500',
            'social_media' => 'nullable|array',
            'terms_accepted' => 'required|accepted',
            'privacy_accepted' => 'required|accepted',
            
            // Files
            'commercial_register_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tax_certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'company_profile_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'portfolio_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Ensure boolean fields and default status
        $validated['terms_accepted'] = $request->boolean('terms_accepted');
        $validated['privacy_accepted'] = $request->boolean('privacy_accepted');
        $validated['status'] = $validated['status'] ?? 'pending';

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

        // Create supplier
        $supplier = Supplier::create($validated);

        // Send OTP for email verification (use a DB-supported type)
        $otpRecord = OtpVerification::generate($supplier->email, 'email_verification');
        
        try {
            Mail::to($supplier->email)->send(new \App\Mail\SupplierOtpMail($otpRecord->otp, $supplier->name));
        } catch (\Exception $e) {
            Log::error('Failed to send supplier OTP: ' . $e->getMessage());
        }

        return redirect()->route('suppliers.verify-otp')->with([
            'success' => 'تم التسجيل بنجاح! يرجى التحقق من بريدك الإلكتروني لإدخال رمز التحقق.',
            'supplier_email' => $supplier->email
        ]);
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyOtp(Request $request)
    {
        $email = $request->session()->get('supplier_email');
        
        if (!$email) {
            return redirect()->route('suppliers.register')->with('error', 'يرجى التسجيل أولاً');
        }
        
        return view('suppliers.verify-otp');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $email = $request->session()->get('supplier_email');
        
        if (!$email) {
            return redirect()->route('suppliers.register')->with('error', 'انتهت صلاحية الجلسة');
        }

        $supplier = Supplier::where('email', $email)->first();

        if (!$supplier) {
            return back()->with('error', 'البريد الإلكتروني غير صحيح');
        }

        // Verify OTP using the same type used in generation
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
}
