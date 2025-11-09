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
                        <code class="text-primary">\{\{${variable.key}\}\}</code>
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
            const variable = `\{\{${this.dataset.variable}\}\}`;
            navigator.clipboard.writeText(variable).then(() => {
                // Show tooltip or notification
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
});
</script>
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
