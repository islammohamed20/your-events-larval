<div class="row">
    <!-- Main Form -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-file-alt me-2"></i>
                    معلومات القالب
                </h6>
            </div>
            <div class="card-body">
                <!-- Template Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">
                        <i class="fas fa-tag me-1"></i>
                        اسم القالب <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $emailTemplate->name ?? '') }}"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Slug -->
                <div class="mb-3">
                    <label for="slug" class="form-label">
                        <i class="fas fa-link me-1"></i>
                        المعرف (Slug)
                    </label>
                    <input type="text" 
                           class="form-control @error('slug') is-invalid @enderror" 
                           id="slug" 
                           name="slug" 
                           value="{{ old('slug', $emailTemplate->slug ?? '') }}"
                           placeholder="يتم إنشاؤه تلقائياً إذا ترك فارغاً">
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">المعرف الفريد للقالب (مثال: booking-confirmation)</small>
                </div>

                <!-- Type -->
                <div class="mb-3">
                    <label for="type" class="form-label">
                        <i class="fas fa-list me-1"></i>
                        نوع القالب <span class="text-danger">*</span>
                    </label>
                    <select class="form-select @error('type') is-invalid @enderror" 
                            id="type" 
                            name="type" 
                            required>
                        <option value="">-- اختر النوع --</option>
                        <option value="booking" {{ old('type', $emailTemplate->type ?? '') == 'booking' ? 'selected' : '' }}>
                            📅 تأكيد حجز
                        </option>
                        <option value="welcome" {{ old('type', $emailTemplate->type ?? '') == 'welcome' ? 'selected' : '' }}>
                            👋 رسالة ترحيب
                        </option>
                        <option value="reset_password" {{ old('type', $emailTemplate->type ?? '') == 'reset_password' ? 'selected' : '' }}>
                            🔐 إعادة تعيين كلمة المرور
                        </option>
                        <option value="invoice" {{ old('type', $emailTemplate->type ?? '') == 'invoice' ? 'selected' : '' }}>
                            💰 فاتورة
                        </option>
                        <option value="supplier_approval" {{ old('type', $emailTemplate->type ?? '') == 'supplier_approval' ? 'selected' : '' }}>
                            ✅ قبول المورد
                        </option>
                        <option value="custom" {{ old('type', $emailTemplate->type ?? '') == 'custom' ? 'selected' : '' }}>
                            ⚙️ مخصص
                        </option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Subject -->
                <div class="mb-3">
                    <label for="subject" class="form-label">
                        <i class="fas fa-heading me-1"></i>
                        موضوع الإيميل <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control @error('subject') is-invalid @enderror" 
                           id="subject" 
                           name="subject" 
                           value="{{ old('subject', $emailTemplate->subject ?? '') }}"
                           placeholder="مثال: تأكيد حجزك في Your Events"
                           required>
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">يمكنك استخدام المتغيرات مثل: @{{customer_name}}</small>
                    
                    <!-- Quick Fill Supplier Approval -->
                    <div class="mt-3">
                        <button type="button" id="fill-supplier-approval" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-magic me-1"></i>
                            ملء بقالب قبول المورد الاحترافي
                        </button>
                        <small class="text-muted ms-2">يضبط الموضوع والمحتوى بنمط الهوية البصرية الحالية</small>
                    </div>
                </div>

                <!-- Body -->
                <div class="mb-3">
                    <label for="body" class="form-label">
                        <i class="fas fa-align-left me-1"></i>
                        محتوى الإيميل <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('body') is-invalid @enderror" 
                              id="body" 
                              name="body" 
                              rows="15"
                              required>{{ old('body', $emailTemplate->body ?? '') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label">
                        <i class="fas fa-info-circle me-1"></i>
                        وصف القالب
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="3"
                              placeholder="وصف مختصر لاستخدام هذا القالب...">{{ old('description', $emailTemplate->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        {{ isset($emailTemplate) ? 'تحديث القالب' : 'حفظ القالب' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-toggle-on me-2"></i>
                    حالة القالب
                </h6>
            </div>
            <div class="card-body">
                <div class="form-check form-switch">
                    <input class="form-check-input" 
                           type="checkbox" 
                           id="is_active" 
                           name="is_active"
                           {{ old('is_active', $emailTemplate->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        تفعيل القالب
                    </label>
                </div>
                <small class="text-muted d-block mt-2">
                    <i class="fas fa-info-circle me-1"></i>
                    عند التفعيل، سيتم استخدام هذا القالب لإرسال الإيميلات
                </small>
            </div>
        </div>

        <!-- Variables Card -->
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-code me-2"></i>
                    المتغيرات المتاحة
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>استخدم هذه المتغيرات في الموضوع والمحتوى:</strong></p>
                
                <div id="variables-list" class="variable-list">
                    <!-- سيتم ملؤها ديناميكياً حسب النوع -->
                </div>

                <hr>
                
                <p class="mb-2"><strong>طريقة الاستخدام:</strong></p>
                <div class="alert alert-light mb-0">
                    <code>@{{customer_name}}</code>
                    <br>
                    <small class="text-muted">سيتم استبداله تلقائياً بقيمة المتغير</small>
                </div>
            </div>
        </div>

        <!-- Test Email Card -->
        @if(isset($emailTemplate) && $emailTemplate->exists)
        <div class="card shadow mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-paper-plane me-2"></i>
                    إرسال بريد تجريبي
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.email-templates.send-test', $emailTemplate) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="to_email" class="form-label">البريد الإلكتروني:</label>
                        <input type="email" 
                               class="form-control form-control-sm" 
                               id="to_email" 
                               name="to_email"
                               placeholder="test@example.com"
                               required>
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm w-100">
                        <i class="fas fa-paper-plane me-2"></i>
                        إرسال اختبار
                    </button>
                </form>
                <small class="text-muted d-block mt-2">
                    سيتم إرسال البريد بالبيانات التجريبية
                </small>
            </div>
        </div>
        @endif

        <!-- Help Card -->
        <div class="card shadow mb-4">
            <div class="card-header bg-secondary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-question-circle me-2"></i>
                    مساعدة
                </h6>
            </div>
            <div class="card-body">
                <h6 class="fw-bold">نصائح:</h6>
                <ul class="small mb-0">
                    <li>استخدم HTML لتنسيق المحتوى</li>
                    <li>يمكنك إضافة صور وروابط</li>
                    <li>اختبر القالب قبل التفعيل</li>
                    <li>استخدم المتغيرات لجعل المحتوى ديناميكي</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
@verbatim
<script>
// Initialize TinyMCE
tinymce.init({
    selector: '#body',
    height: 500,
    directionality: 'rtl',
    language: 'ar',
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | code preview fullscreen',
    menubar: 'file edit view insert format tools table help',
    content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; direction: rtl; }',
    setup: function(editor) {
        editor.on('init', function() {
            this.getDoc().body.style.fontFamily = 'Arial, sans-serif';
        });
    }
});

// Variables by Type
const variablesByType = {
    booking: [
        { key: 'customer_name', label: 'اسم العميل' },
        { key: 'booking_number', label: 'رقم الحجز' },
        { key: 'service_name', label: 'اسم الخدمة' },
        { key: 'booking_date', label: 'تاريخ الحجز' },
        { key: 'booking_time', label: 'وقت الحجز' },
        { key: 'total_amount', label: 'المبلغ الإجمالي' },
        { key: 'company_name', label: 'اسم الشركة' },
        { key: 'company_phone', label: 'رقم الهاتف' },
        { key: 'company_email', label: 'البريد الإلكتروني' },
    ],
    welcome: [
        { key: 'customer_name', label: 'اسم العميل' },
        { key: 'customer_email', label: 'البريد الإلكتروني' },
        { key: 'company_name', label: 'اسم الشركة' },
        { key: 'website_url', label: 'رابط الموقع' },
    ],
    reset_password: [
        { key: 'customer_name', label: 'اسم العميل' },
        { key: 'reset_link', label: 'رابط إعادة التعيين' },
        { key: 'expiry_time', label: 'مدة الصلاحية' },
    ],
    invoice: [
        { key: 'customer_name', label: 'اسم العميل' },
        { key: 'invoice_number', label: 'رقم الفاتورة' },
        { key: 'invoice_date', label: 'تاريخ الفاتورة' },
        { key: 'total_amount', label: 'المبلغ الإجمالي' },
        { key: 'payment_method', label: 'طريقة الدفع' },
        { key: 'invoice_url', label: 'رابط الفاتورة' },
    ],
    supplier_approval: [
        { key: 'supplier_name', label: 'اسم المورد' },
        { key: 'supplier_email', label: 'البريد الإلكتروني للمورد' },
        { key: 'approval_date', label: 'تاريخ الموافقة' },
        { key: 'dashboard_url', label: 'رابط لوحة المورد' },
        { key: 'company_name', label: 'اسم الشركة' },
        { key: 'support_email', label: 'بريد الدعم' },
    ],
    custom: []
};

// Update variables list based on selected type
function updateVariablesList() {
    const type = document.getElementById('type').value;
    const variablesList = document.getElementById('variables-list');
    const variables = variablesByType[type] || [];

    if (variables.length === 0) {
        variablesList.innerHTML = '<p class="text-muted small mb-0">لا توجد متغيرات محددة لهذا النوع</p>';
        return;
    }

    let html = '<div class="list-group list-group-flush">';
    variables.forEach(variable => {
        html += `
            <div class="list-group-item px-0 py-2 border-0">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <code class="text-primary">{{'{{'}}${variable.key}{{'}}'}}</code>
                        <br>
                        <small class="text-muted">${variable.label}</small>
                    </div>
                    <button type="button" 
                            class="btn btn-sm btn-outline-primary copy-variable" 
                            data-variable="${variable.key}"
                            title="نسخ">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        `;
    });
    html += '</div>';

    variablesList.innerHTML = html;

    // Add copy functionality
    document.querySelectorAll('.copy-variable').forEach(btn => {
        btn.addEventListener('click', function() {
            const variable = '{{' + this.dataset.variable + '}}';
            navigator.clipboard.writeText(variable).then(() => {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i>';
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 1000);
            });
        });
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateVariablesList();
    
    document.getElementById('type').addEventListener('change', updateVariablesList);

    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        const slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\u0600-\u06FF\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
        
        if (!document.getElementById('slug').value || document.getElementById('slug').dataset.auto !== 'false') {
            document.getElementById('slug').value = slug;
            document.getElementById('slug').dataset.auto = 'true';
        }
    });

    document.getElementById('slug').addEventListener('input', function() {
        this.dataset.auto = 'false';
    });

    // Fill supplier approval template
    const fillBtn = document.getElementById('fill-supplier-approval');
    if (fillBtn) {
        fillBtn.addEventListener('click', function() {
            // Force type to supplier_approval
            const typeSelect = document.getElementById('type');
            if (typeSelect) {
                typeSelect.value = 'supplier_approval';
                updateVariablesList();
            }

            // Set subject using literal Blade-safe braces via @verbatim
            const subjectInput = document.getElementById('subject');
            subjectInput.value = '🎉 تم قبولك كمورد لدى {{company_name}} – ابدأ الآن';

            // Compose modern HTML body using brand style
            const html = `<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>قبول المورد</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background:#f8f9fb; margin:0; padding:0; direction:rtl; }
    .container { max-width:680px; margin:32px auto; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 6px 24px rgba(0,0,0,0.08); }
    .header { background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 50%, #A855F7 100%); color:#fff; text-align:center; padding:36px 24px; }
    .header .brand { font-size:22px; font-weight:700; margin-bottom:8px; color:#FDE68A; }
    .header .title { font-size:26px; font-weight:800; margin:0; }
    .header .subtitle { font-size:15px; opacity:0.9; margin-top:10px; }
    .content { padding:32px 28px; color:#374151; line-height:1.8; }
    .greeting { font-size:18px; margin-bottom:14px; }
    .panel { background:#f8f4ff; border:2px solid #e9e3ff; border-radius:12px; padding:18px; margin:18px 0; }
    .btn { display:inline-block; padding:12px 18px; background:#7C3AED; color:#fff; text-decoration:none; border-radius:10px; font-weight:600; box-shadow:0 4px 12px rgba(124,58,237,0.35); }
    .btn:hover { background:#6D28D9; }
    .note { font-size:14px; color:#6b7280; margin-top:12px; }
    .footer { background: linear-gradient(135deg, #1f2937 0%, #374151 100%); color:#d1d5db; text-align:center; padding:24px; }
    .footer .brand { color:#A855F7; font-weight:700; }
    .contact { display:flex; justify-content:center; gap:16px; flex-wrap:wrap; margin-top:12px; }
    .divider { height:1px; background:linear-gradient(to left, transparent, #e9ecef, transparent); margin:22px 0; }
  </style>
  </head>
<body>
  <div class="container">
    <div class="header">
      <div class="brand">{{company_name}}</div>
      <h1 class="title">تم قبولك كمورد ✅</h1>
      <p class="subtitle">نرحّب بك ضمن شبكة شركائنا – جاهزين ننطلق!</p>
    </div>
    <div class="content">
      <p class="greeting">مرحباً {{supplier_name}},</p>
      <p>يسعدنا إبلاغك أنه تم قبول طلبك كمورد لدى {{company_name}} بتاريخ {{approval_date}}. تم مراجعة بياناتك والتأكد من توافقها مع معاييرنا المهنية.</p>
      <div class="panel">
        <strong>الخطوة التالية:</strong>
        <ul style="margin:10px 0 0; padding-right:18px;">
          <li>أكمل ملفك التعريفي وخدماتك من لوحة المورد.</li>
          <li>أضف نماذج أعمالك لتزيد فرص ظهورك للعملاء.</li>
          <li>فعّل الإشعارات لتتابع الطلبات والعروض أولاً بأول.</li>
        </ul>
      </div>
      <p style="text-align:center; margin:24px 0;">
        <a class="btn" href="{{dashboard_url}}" target="_blank">الذهاب إلى لوحة المورد</a>
      </p>
      <div class="divider"></div>
      <p class="note">لو لديك أي استفسار، فريق الدعم جاهز دائماً لمساعدتك عبر البريد: <a href="mailto:{{support_email}}" style="color:#7C3AED; text-decoration:none;">{{support_email}}</a>.</p>
    </div>
    <div class="footer">
      <div><span class="brand">{{company_name}}</span></div>
      <div class="contact">
        <span>✉️ <a href="mailto:{{support_email}}" style="color:#A855F7; text-decoration:none;">{{support_email}}</a></span>
        <span>🌐 <a href="{{dashboard_url}}" style="color:#A855F7; text-decoration:none;">لوحة المورد</a></span>
      </div>
      <p style="margin-top: 10px; font-size: 12px; color: #9ca3af;">هذه رسالة تلقائية – لا ترد عليها.</p>
    </div>
  </div>
</body>
</html>`;

            // Put HTML into TinyMCE or textarea
            if (window.tinymce?.get('body')) {
                tinymce.get('body').setContent(html);
            } else {
                document.getElementById('body').value = html;
            }
        });
    }
});
</script>
@endverbatim
@endpush

<style>
.variable-list {
    max-height: 300px;
    overflow-y: auto;
}

.variable-list code {
    font-size: 0.85rem;
    padding: 2px 6px;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.copy-variable {
    font-size: 0.75rem;
    padding: 2px 8px;
}
</style>
