<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        'user_agent',
        'channel',
        'phone',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempts' => 'integer',
    ];

    protected $attributes = [
        'channel' => 'email',
    ];

    /**
     * Generate and send OTP
     */
    public static function generate(string $email, string $type = 'email_verification', int $length = 6, int $expiryMinutes = 10, bool $sendEmail = true, ?string $channel = 'email', ?string $phone = null)
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
            'channel' => $channel ?? 'email',
            'phone' => $phone,
        ]);

        // إرسال OTP عبر القناة المختارة
        if ($channel === 'whatsapp' && $phone) {
            try {
                self::sendOtpWhatsApp($phone, $otp, $type, $expiryMinutes);
            } catch (\Exception $e) {
                Log::warning('OTP WhatsApp failed, falling back to email: '.$e->getMessage());
                self::sendOtpEmail($email, $otp, $type, $expiryMinutes);
            }
        } elseif ($sendEmail) {
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

        if (! $record) {
            return [
                'success' => false,
                'message' => 'كود التحقق غير صحيح أو منتهي الصلاحية',
            ];
        }

        // التحقق من عدد المحاولات
        if ($record->attempts >= 5) {
            $record->update(['status' => 'failed']);

            return [
                'success' => false,
                'message' => 'تم تجاوز الحد الأقصى للمحاولات. يرجى طلب كود جديد',
            ];
        }

        // التحقق من انتهاء الصلاحية
        if (Carbon::now()->isAfter($record->expires_at)) {
            $record->update(['status' => 'expired']);

            return [
                'success' => false,
                'message' => 'انتهت صلاحية كود التحقق. يرجى طلب كود جديد',
            ];
        }

        // تحديث الحالة
        $record->update([
            'status' => 'verified',
            'verified_at' => Carbon::now(),
        ]);

        return [
            'success' => true,
            'message' => 'تم التحقق بنجاح',
            'record' => $record,
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
        return $this->status === 'pending' && ! $this->isExpired() && $this->attempts < 5;
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
            'two_factor' => 'التحقق بخطوتين (2FA)',
        ];

        $subject = 'رمز التحقق - منصة فعالياتك';
        $typeLabel = $typeLabels[$type] ?? 'التحقق';
        // إرسال باستخدام قالب Blade الموحد (emails.supplier-otp)
        try {
            $mailData = [
                'otp' => $otp,
                'supplierName' => null,
                'typeLabel' => $typeLabel,
                'expiryMinutes' => $expiryMinutes,
                'email' => $email,
            ];

            Mail::mailer('hello')->send('emails.supplier-otp', $mailData, function ($message) use ($email, $subject, $mailData) {
                $message->to($email)
                        ->subject($subject)
                        ->from(
                            env('HELLO_MAIL_USERNAME', 'hello@yourevents.sa'),
                            env('HELLO_MAIL_FROM_NAME', 'Your Events')
                        );

                // Plain text alternative for copyable OTP
                $message->text(view('emails.supplier-otp-plain', $mailData)->render());
            });
        } catch (\Exception $e) {
            Log::error('OTP Email Error: '.$e->getMessage());
        }
    }

    /**
     * Normalize phone number to international format (no +, no spaces, no leading 00)
     * - Saudi local: 05XXXXXXXX → 9665XXXXXXXX
     * - Egyptian local: 01XXXXXXXXX → 201XXXXXXXXX
     * - Already international: kept as-is
     */
    private static function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '00')) {
            return substr($phone, 2);
        }

        if (str_starts_with($phone, '966') || str_starts_with($phone, '20')) {
            return $phone;
        }

        if (str_starts_with($phone, '05') && strlen($phone) === 10) {
            return '966' . substr($phone, 1);
        }

        if (str_starts_with($phone, '01') && strlen($phone) === 11) {
            return '20' . substr($phone, 1);
        }

        if (str_starts_with($phone, '0')) {
            return '966' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Send OTP via WhatsApp using FaalwaService
     */
    private static function sendOtpWhatsApp(string $phone, string $otp, string $type, int $expiryMinutes)
    {
        $typeLabels = [
            'email_verification' => 'التحقق من البريد الإلكتروني',
            'login' => 'تسجيل الدخول',
            'supplier_login' => 'تسجيل دخول المورد',
            'password_reset' => 'إعادة تعيين كلمة المرور',
            'booking_confirmation' => 'تأكيد الحجز',
            'payment_confirmation' => 'تأكيد الدفع',
            'two_factor' => 'التحقق بخطوتين (2FA)',
        ];

        $typeLabel = $typeLabels[$type] ?? 'التحقق';

        $message = "🔐 *منصة فعالياتك Your Events*\n\n";
        $message .= "رمز {$typeLabel}: *{$otp}*\n\n";
        $message .= "الكود صالح لمدة {$expiryMinutes} دقائق.\n";
        $message .= "لا تشارك هذا الكود مع أي شخص.";

        $normalizedPhone = self::normalizePhone($phone);

        Log::info('OTP WhatsApp sending', [
            'original_phone' => $phone,
            'normalized_phone' => $normalizedPhone,
            'type' => $type,
        ]);

        try {
            $result = app(\App\Services\FaalwaService::class)->sendTextMessage($normalizedPhone, $message);

            if (! ($result['success'] ?? false)) {
                Log::error('OTP WhatsApp failed, falling back to email', [
                    'phone' => $normalizedPhone,
                    'error' => $result['message'] ?? 'Unknown error',
                ]);
                throw new \Exception('WhatsApp send failed: ' . ($result['message'] ?? 'Unknown error'));
            }

            Log::info('OTP WhatsApp sent successfully', [
                'phone' => $normalizedPhone,
                'status' => $result['status'] ?? 'sent',
            ]);
        } catch (\Exception $e) {
            Log::error('OTP WhatsApp Error: '.$e->getMessage());
            throw $e;
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
