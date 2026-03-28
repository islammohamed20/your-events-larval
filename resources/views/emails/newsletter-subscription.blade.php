@extends('layouts.email')

@section('content')
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif; direction: rtl; text-align: right;">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #2c3e50; margin: 0;">🎉 شكراً لاشتراكك!</h1>
            <p style="color: #7f8c8d; margin: 10px 0 0 0;">مرحباً بك في عائلة فعالياتك</p>
        </div>

        <!-- Main Content -->
        <div style="background: #f8f9fa; padding: 30px; border-radius: 10px; margin-bottom: 20px;">
            <h2 style="color: #2c3e50; margin-top: 0;">أهلاً بك {{ $name ?: 'صديقنا' }}!</h2>
            
            <p style="color: #34495e; line-height: 1.6;">
                يسعدنا انضمامك إلى نشرتنا الإخبارية. الآن ستحصل على:
            </p>
            
            <ul style="color: #34495e; line-height: 1.8; padding-right: 20px;">
                <li>🎉 آخر العروض الحصرية على خدماتنا وباقاتنا</li>
                <li>📅 أخبار الفعاليات والمناسبات القادمة</li>
                <li>💡 نصائح احترافية لتنظيم فعاليات ناجحة</li>
                <li>🎁 خصومات خاصة للمشتركين فقط</li>
            </ul>
        </div>

        <!-- CTA Buttons -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('services.index') }}" 
               style="display: inline-block; background: #3498db; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 5px;">
                استكشف خدماتنا
            </a>
            <a href="{{ route('packages.index') }}" 
               style="display: inline-block; background: #e74c3c; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 5px;">
                تصفح الباقات
            </a>
        </div>

        <!-- Social Links -->
        <div style="text-align: center; margin: 30px 0; padding: 20px; background: #ecf0f1; border-radius: 10px;">
            <p style="color: #7f8c8d; margin: 0 0 15px 0;">تابعنا على وسائل التواصل الاجتماعي</p>
            <div>
                <a href="#" style="margin: 0 10px; color: #3498db; text-decoration: none;">
                    <i class="fab fa-facebook fa-lg"></i>
                </a>
                <a href="#" style="margin: 0 10px; color: #1da1f2; text-decoration: none;">
                    <i class="fab fa-twitter fa-lg"></i>
                </a>
                <a href="#" style="margin: 0 10px; color: #e4405f; text-decoration: none;">
                    <i class="fab fa-instagram fa-lg"></i>
                </a>
                <a href="#" style="margin: 0 10px; color: #25d366; text-decoration: none;">
                    <i class="fab fa-whatsapp fa-lg"></i>
                </a>
            </div>
        </div>

        <!-- Unsubscribe -->
        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ecf0f1;">
            <p style="color: #95a5a6; font-size: 12px; margin: 0;">
                إذا لم تعد ترغب في استلام رسائلنا، يمكنك 
                <a href="#" style="color: #95a5a6; text-decoration: underline;">إلغاء الاشتراك</a>
            </p>
        </div>

    </div>
@endsection
