<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
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
    public static function generate(string $email, string $type = 'email_verification', int $length = 6, int $expiryMinutes = 10)
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

        // إرسال البريد الإلكتروني
        self::sendOtpEmail($email, $otp, $type, $expiryMinutes);

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
            'password_reset' => 'إعادة تعيين كلمة المرور',
            'booking_confirmation' => 'تأكيد الحجز',
            'payment_confirmation' => 'تأكيد الدفع',
        ];

        $subject = 'كود التحقق - Your Events';
        $typeLabel = $typeLabels[$type] ?? 'التحقق';

        $html = <<<'HTML'
        <!DOCTYPE html>
        <html dir="rtl" lang="ar">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background-color: #f4f6f9;
                    margin: 0;
                    padding: 0;
                    direction: rtl;
                }
                .container {
                    max-width: 600px;
                    margin: 40px auto;
                    background: white;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                }
                .header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    padding: 40px 20px;
                    text-align: center;
                    color: white;
                }
                .header h1 {
                    margin: 0;
                    font-size: 28px;
                    font-weight: 600;
                }
                .content {
                    padding: 40px 30px;
                }
                .otp-box {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    text-align: center;
                    padding: 30px;
                    border-radius: 10px;
                    margin: 30px 0;
                    font-size: 48px;
                    font-weight: bold;
                    letter-spacing: 10px;
                    font-family: 'Courier New', monospace;
                    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
                }
                .info {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                    border-right: 4px solid #667eea;
                    margin: 20px 0;
                }
                .info p {
                    margin: 10px 0;
                    color: #495057;
                    line-height: 1.8;
                }
                .warning {
                    background: #fff3cd;
                    border-right: 4px solid #ffc107;
                    padding: 15px;
                    border-radius: 8px;
                    margin: 20px 0;
                }
                .warning p {
                    margin: 0;
                    color: #856404;
                    font-size: 14px;
                }
                .footer {
                    background: #f8f9fa;
                    padding: 20px;
                    text-align: center;
                    color: #6c757d;
                    font-size: 14px;
                }
                .icon {
                    font-size: 60px;
                    margin-bottom: 10px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div class="icon">🔐</div>
                    <h1>كود التحقق</h1>
                </div>
                <div class="content">
                    <p style="font-size: 18px; color: #212529; margin-bottom: 20px;">
                        مرحباً،
                    </p>
                    <p style="color: #495057; line-height: 1.8; margin-bottom: 20px;">
                        تلقيت هذا البريد لأنك طلبت كود تحقق لـ <strong>typeLabel_placeholder</strong>
                    </p>
                    
                    <div class="otp-box">
                        otp_placeholder
                    </div>
                    
                    <div class="info">
                        <p><strong>صلاحية الكود:</strong> expiryMinutes_placeholder دقيقة</p>
                        <p><strong>الغرض:</strong> typeLabel_placeholder</p>
                        <p><strong>البريد الإلكتروني:</strong> email_placeholder</p>
                    </div>
                    
                    <div class="warning">
                        <p>تنبيه أمني: لا تشارك هذا الكود مع أي شخص. فريق Your Events لن يطلب منك هذا الكود أبداً.</p>
                    </div>
                    
                    <p style="color: #6c757d; font-size: 14px; margin-top: 30px;">
                        إذا لم تطلب هذا الكود، يرجى تجاهل هذا البريد أو التواصل معنا إذا كنت تعتقد أن هناك نشاط مشبوه.
                    </p>
                </div>
                <div class="footer">
                    <p style="margin: 5px 0;"><strong>Your Events</strong></p>
                    <p style="margin: 5px 0;">لا وسطاء، لا انتظار، ولا مكالمات ما تخلص. كل شي واضح، وسلس، وسريع</p>
                    <p style="margin: 5px 0; direction: rtl; text-align: center;">hello@yourevents.sa | +966 50 515 9616</p>
                    <p style="margin: 10px 0; font-size: 12px; color: #adb5bd;">
                        © 2025 Your Events. جميع الحقوق محفوظة.
                    </p>
                </div>
            </div>
        </body>
        </html>
HTML;

        $html = str_replace([
            'typeLabel_placeholder',
            'otp_placeholder',
            'expiryMinutes_placeholder',
            'email_placeholder'
        ], [
            htmlspecialchars($typeLabel),
            htmlspecialchars($otp),
            htmlspecialchars($expiryMinutes),
            htmlspecialchars($email)
        ], $html);

        try {
            Mail::send([], [], function ($message) use ($email, $subject, $html) {
                $message->to($email)
                    ->subject($subject)
                    ->html($html);
            });
        } catch (\Exception $e) {
            \Log::error('OTP Email Error: ' . $e->getMessage());
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
