<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of email templates
     */
    public function index()
    {
        $this->syncSystemEmailTemplates();

        $templates = EmailTemplate::orderBy('type')->orderBy('name')->get();

        return view('admin.email-templates.index', compact('templates'));
    }

    /**
     * Ensure all system email blade views are represented in email_templates table.
     */
    protected function syncSystemEmailTemplates(): void
    {
        $known = [
            'booking-confirmation' => [
                'name' => 'تأكيد الحجز',
                'subject' => 'تأكيد الحجز - {{booking_number}}',
                'type' => 'booking',
                'description' => 'قالب تأكيد الحجز للعميل.',
            ],
            'quote' => [
                'name' => 'عرض السعر',
                'subject' => 'عرض سعر جديد - {{quote_number}}',
                'type' => 'custom',
                'description' => 'قالب إرسال عرض السعر للعميل.',
            ],
            'quote-payment-confirmation' => [
                'name' => 'تأكيد دفع عرض السعر',
                'subject' => 'تم استلام دفعتك - {{quote_number}}',
                'type' => 'custom',
                'description' => 'قالب تأكيد الدفع لعرض السعر.',
            ],
            'booking-notification' => [
                'name' => 'إشعار حجز',
                'subject' => 'طلب حجز جديد - {{booking_number}}',
                'type' => 'booking',
                'description' => 'قالب إشعار داخلي/للمورد عند وجود حجز.',
            ],
            'booking-accepted-by-supplier' => [
                'name' => 'قبول الحجز من المورد',
                'subject' => 'تم قبول الحجز من المورد - {{booking_number}}',
                'type' => 'custom',
                'description' => 'قالب إشعار قبول المورد للحجز.',
            ],
            'supplier-approval' => [
                'name' => 'قبول المورد',
                'subject' => 'تم قبولك كمورد - {{company_name}}',
                'type' => 'supplier_approval',
                'description' => 'قالب اعتماد المورد بعد الموافقة.',
            ],
            'supplier-created-pending' => [
                'name' => 'إنشاء مورد (قيد المراجعة)',
                'subject' => 'تم إنشاء حساب مورد لك - بانتظار المراجعة',
                'type' => 'custom',
                'description' => 'قالب إشعار المورد الذي يُنشأ من لوحة الإدارة وحالته pending.',
            ],
            'supplier-otp' => [
                'name' => 'رمز تحقق المورد',
                'subject' => 'رمز التحقق - منصة فعالياتك',
                'type' => 'custom',
                'description' => 'قالب رمز التحقق OTP للمورد.',
            ],
            'supplier-quote-approved' => [
                'name' => 'إشعار مورد بعرض سعر موافق عليه',
                'subject' => 'فرصة عمل جديدة - عرض سعر موافق عليه',
                'type' => 'custom',
                'description' => 'قالب إشعار المورد عند موافقة العميل على عرض السعر.',
            ],
            'supplier-accepted-quote' => [
                'name' => 'إشعار العميل بقبول المورد',
                'subject' => 'تم قبول عرض السعر من المورد',
                'type' => 'custom',
                'description' => 'قالب إشعار العميل بعد قبول المورد للعرض.',
            ],
            'admin-supplier-accepted' => [
                'name' => 'إشعار الإدارة بقبول المورد',
                'subject' => 'قبول عرض السعر من المورد',
                'type' => 'custom',
                'description' => 'قالب إشعار الإدارة عند قبول المورد لعرض سعر.',
            ],
            'order-request' => [
                'name' => 'طلب جديد للمورد',
                'subject' => 'طلب جديد - {{order_number}}',
                'type' => 'custom',
                'description' => 'قالب إشعار المورد بطلب/منافسة جديدة.',
            ],
            'competitive-order-notification' => [
                'name' => 'إشعار طلب تنافسي',
                'subject' => 'طلب تنافسي جديد - {{order_number}}',
                'type' => 'custom',
                'description' => 'قالب إشعار الطلبات التنافسية.',
            ],
        ];

        $files = glob(resource_path('views/emails/*.blade.php')) ?: [];

        foreach ($files as $file) {
            $slug = str_replace('.blade.php', '', basename($file));
            $meta = $known[$slug] ?? [
                'name' => 'قالب: '.str_replace('-', ' ', $slug),
                'subject' => 'قالب بريد: '.$slug,
                'type' => 'custom',
                'description' => 'قالب نظام تمت مزامنته تلقائياً من resources/views/emails.',
            ];

            EmailTemplate::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $meta['name'],
                    'subject' => $meta['subject'],
                    'body' => '<p>هذا القالب مرتبط بملف Blade: <strong>emails.'.$slug.'</strong></p>',
                    'type' => $meta['type'],
                    'description' => $meta['description'],
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Show the form for creating a new template
     */
    public function create()
    {
        return view('admin.email-templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:email_templates,slug',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:booking,welcome,reset_password,invoice,custom,supplier_approval',
            'description' => 'nullable|string',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        EmailTemplate::create($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'تم إنشاء قالب البريد الإلكتروني بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailTemplate $emailTemplate)
    {
        $variables = $emailTemplate->getDefaultVariables();
        $sampleData = [];

        foreach ($variables as $key => $label) {
            $sampleData[$key] = $label;
        }

        $rendered = $emailTemplate->render($sampleData);

        return view('admin.email-templates.preview', [
            'template' => $emailTemplate,
            'rendered' => $rendered,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        return view('admin.email-templates.edit', compact('emailTemplate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:email_templates,slug,'.$emailTemplate->id,
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:booking,welcome,reset_password,invoice,custom,supplier_approval',
            'description' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $emailTemplate->update($validated);

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'تم تحديث قالب البريد الإلكتروني بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'تم حذف قالب البريد الإلكتروني بنجاح');
    }

    /**
     * Toggle template active status
     */
    public function toggleActive(EmailTemplate $emailTemplate)
    {
        $emailTemplate->update(['is_active' => ! $emailTemplate->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $emailTemplate->is_active,
            'message' => $emailTemplate->is_active ? 'تم تفعيل القالب' : 'تم تعطيل القالب',
        ]);
    }

    /**
     * Send test email
     */
    public function sendTest(Request $request, EmailTemplate $emailTemplate)
    {
        $request->validate([
            'to_email' => 'required|email',
        ]);

        $variables = $emailTemplate->getDefaultVariables();
        $sampleData = [];

        foreach ($variables as $key => $label) {
            $sampleData[$key] = $label;
        }

        $rendered = $emailTemplate->render($sampleData);

        try {
            Mail::send([], [], function ($message) use ($request, $rendered) {
                $message->to($request->to_email)
                    ->subject($rendered['subject'])
                    ->html($rendered['body']);
            });

            return back()->with('success', 'تم إرسال البريد التجريبي بنجاح! ✅');
        } catch (\Exception $e) {
            return back()->with('error', 'فشل إرسال البريد: '.$e->getMessage());
        }
    }

    /**
     * Duplicate template
     */
    public function duplicate(EmailTemplate $emailTemplate)
    {
        $newTemplate = $emailTemplate->replicate();
        $newTemplate->name = $emailTemplate->name.' (نسخة)';
        $newTemplate->slug = $emailTemplate->slug.'-copy-'.time();
        $newTemplate->is_active = false;
        $newTemplate->save();

        return redirect()->route('admin.email-templates.edit', $newTemplate)
            ->with('success', 'تم نسخ القالب بنجاح');
    }
}
