<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OtpVerification extends Model
{
    protected $fillable = [
        'email',
        'otp',
        'type',
        'status',
        'expires_at',
        'verified_at',
        'attempts',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempts' => 'integer',
    ];

    /**
     * Generate and send OTP
     */
    public static function generate(string $email, string $type = 'email_verification', int $length = 6, int $expiryMinutes = 10, bool $sendEmail = true)
    {
        // حذف OTP القديمة لنفس البريد والنوع
        self::where('email', $email)
            ->where('type', $type)
            ->where('status', 'pending')
            ->delete();

        // توليد OTP عشوائي
        $otp = self::generateOtpCode($length);

        // إنشاء سجل جديد
        $otpRecord = self::create([
            'email' => $email,
            'otp' => $otp,
            'type' => $type,
            'status' => 'pending',
            'expires_at' => Carbon::now()->addMinutes($expiryMinutes),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // إرسال البريد الإلكتروني (يمكن تعطيله عند الحاجة)
        if ($sendEmail) {
            self::sendOtpEmail($email, $otp, $type, $expiryMinutes);
        }

        return $otpRecord;
    }

    /**
     * Verify OTP
     */
    public static function verify(string $email, string $otp, string $type = 'email_verification')
    {
        $record = self::where('email', $email)
            ->where('type', $type)
            ->where('status', 'pending')
            ->where('otp', $otp)
            ->first();

        if (!$record) {
            return [
                'success' => false,
                'message' => 'كود التحقق غير صحيح أو منتهي الصلاحية'
            ];
        }

        // التحقق من عدد المحاولات
        if ($record->attempts >= 5) {
            $record->update(['status' => 'failed']);
            return [
                'success' => false,
                'message' => 'تم تجاوز الحد الأقصى للمحاولات. يرجى طلب كود جديد'
            ];
        }

        // التحقق من انتهاء الصلاحية
        if (Carbon::now()->isAfter($record->expires_at)) {
            $record->update(['status' => 'expired']);
            return [
                'success' => false,
                'message' => 'انتهت صلاحية كود التحقق. يرجى طلب كود جديد'
            ];
        }

        // تحديث الحالة
        $record->update([
            'status' => 'verified',
            'verified_at' => Carbon::now()
        ]);

        return [
            'success' => true,
            'message' => 'تم التحقق بنجاح',
            'record' => $record
        ];
    }

    /**
     * Increment attempts
     */
    public function incrementAttempts()
    {
        $this->increment('attempts');
        
        if ($this->attempts >= 5) {
            $this->update(['status' => 'failed']);
        }
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired()
    {
        return Carbon::now()->isAfter($this->expires_at);
    }

    /**
     * Check if OTP is valid
     */
    public function isValid()
    {
        return $this->status === 'pending' && !$this->isExpired() && $this->attempts < 5;
    }

    /**
     * Generate random OTP code
     */
    private static function generateOtpCode(int $length = 6)
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return (string) random_int($min, $max);
    }

    /**
     * Send OTP via email
     */
    private static function sendOtpEmail(string $email, string $otp, string $type, int $expiryMinutes)
    {
        $typeLabels = [
            'email_verification' => 'التحقق من البريد الإلكتروني',
            'login' => 'تسجيل الدخول',
            'supplier_login' => 'تسجيل دخول المورد',
            'password_reset' => 'إعادة تعيين كلمة المرور',
            'booking_confirmation' => 'تأكيد الحجز',
            'payment_confirmation' => 'تأكيد الدفع',
        ];

        $subject = 'رمز التحقق - منصة فعالياتك';
        $typeLabel = $typeLabels[$type] ?? 'التحقق';
        // إرسال باستخدام قالب Blade الموحد (emails.supplier-otp)
        try {
            Mail::send('emails.supplier-otp', [
                'otp' => $otp,
                'supplierName' => null,
                'typeLabel' => $typeLabel,
                'expiryMinutes' => $expiryMinutes,
                'email' => $email,
            ], function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });
        } catch (\Exception $e) {
            Log::error('OTP Email Error: ' . $e->getMessage());
        }
    }

    /**
     * Clean expired OTPs (can be scheduled)
     */
    public static function cleanExpired()
    {
        self::where('expires_at', '<', Carbon::now())
            ->where('status', 'pending')
            ->update(['status' => 'expired']);
    }
}
