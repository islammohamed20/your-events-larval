@extends('layouts.admin')

@section('title', 'تعديل بيانات العميل')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>تعديل بيانات العميل: {{ $customer->name }}</h2>
                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة لتفاصيل العميل
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-edit text-primary"></i>
                                تعديل المعلومات الأساسية
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle"></i>
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>يرجى تصحيح الأخطاء التالية:</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.customers.update', $customer->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-user text-primary"></i>
                                            اسم العميل <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope text-primary"></i>
                                            البريد الإلكتروني <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $customer->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">
                                            <i class="fas fa-phone text-primary"></i>
                                            رقم الهاتف
                                        </label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $customer->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="company_name" class="form-label">
                                            <i class="fas fa-building text-primary"></i>
                                            اسم الشركة <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                               id="company_name" name="company_name" value="{{ old('company_name', $customer->company_name) }}" required>
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tax_number" class="form-label">
                                            <i class="fas fa-receipt text-primary"></i>
                                            الرقم الضريبي
                                        </label>
                                        <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                                               id="tax_number" name="tax_number" value="{{ old('tax_number', $customer->tax_number) }}">
                                        @error('tax_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">
                                            <i class="fas fa-toggle-on text-primary"></i>
                                            حالة العميل
                                        </label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                            <option value="active" {{ old('status', $customer->status ?? 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                                            <option value="inactive" {{ old('status', $customer->status ?? 'active') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                            <option value="suspended" {{ old('status', $customer->status ?? 'active') == 'suspended' ? 'selected' : '' }}>معلق</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="mb-3">
                                    <i class="fas fa-credit-card text-primary"></i>
                                    معلومات الدفع (اختيارية)
                                </h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="card_type" class="form-label">نوع البطاقة</label>
                                        <select class="form-select @error('card_type') is-invalid @enderror" id="card_type" name="card_type">
                                            <option value="">اختر نوع البطاقة</option>
                                            <option value="visa" {{ old('card_type', $customer->card_type) == 'visa' ? 'selected' : '' }}>Visa</option>
                                            <option value="mastercard" {{ old('card_type', $customer->card_type) == 'mastercard' ? 'selected' : '' }}>Mastercard</option>
                                            <option value="mada" {{ old('card_type', $customer->card_type) == 'mada' ? 'selected' : '' }}>مدى</option>
                                        </select>
                                        @error('card_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="card_holder_name" class="form-label">اسم حامل البطاقة</label>
                                        <input type="text" class="form-control @error('card_holder_name') is-invalid @enderror" 
                                               id="card_holder_name" name="card_holder_name" value="{{ old('card_holder_name', $customer->card_holder_name) }}">
                                        @error('card_holder_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="card_last_four" class="form-label">آخر 4 أرقام</label>
                                        <input type="text" class="form-control @error('card_last_four') is-invalid @enderror" 
                                               id="card_last_four" name="card_last_four" value="{{ old('card_last_four', $customer->card_last_four) }}" 
                                               maxlength="4" placeholder="1234">
                                        @error('card_last_four')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="card_expiry_month" class="form-label">شهر الانتهاء</label>
                                        <select class="form-select @error('card_expiry_month') is-invalid @enderror" id="card_expiry_month" name="card_expiry_month">
                                            <option value="">الشهر</option>
                                            @for($i = 1; $i <= 12; $i++)
                                                <option value="{{ sprintf('%02d', $i) }}" {{ old('card_expiry_month', $customer->card_expiry_month) == sprintf('%02d', $i) ? 'selected' : '' }}>
                                                    {{ sprintf('%02d', $i) }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('card_expiry_month')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="card_expiry_year" class="form-label">سنة الانتهاء</label>
                                        <select class="form-select @error('card_expiry_year') is-invalid @enderror" id="card_expiry_year" name="card_expiry_year">
                                            <option value="">السنة</option>
                                            @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                                <option value="{{ $i }}" {{ old('card_expiry_year', $customer->card_expiry_year) == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('card_expiry_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="mb-3">
                                    <label for="notes" class="form-label">
                                        <i class="fas fa-sticky-note text-primary"></i>
                                        ملاحظات إدارية
                                    </label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="4" 
                                              placeholder="أضف ملاحظات حول العميل...">{{ old('notes', $customer->notes ?? '') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">هذه الملاحظات مرئية للإدارة فقط</div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> حفظ التغييرات
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle text-info"></i>
                                معلومات العميل
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">رقم العميل</small>
                                <div class="fw-bold">#{{ $customer->id }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">تاريخ التسجيل</small>
                                <div class="fw-bold">{{ $customer->created_at->format('d/m/Y') }}</div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">آخر تحديث</small>
                                <div class="fw-bold">{{ $customer->updated_at->format('d/m/Y H:i') }}</div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">عدد عروض الأسعار</small>
                                <div class="fw-bold">{{ $customer->quotes_count ?? 0 }}</div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">عدد الحجوزات</small>
                                <div class="fw-bold">{{ $customer->bookings_count ?? 0 }}</div>
                            </div>

                            <hr>

                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-outline-primary btn-sm text-white">
                                    <i class="fas fa-eye"></i> عرض التفاصيل
                                </a>
                                <a href="{{ route('admin.customers.export-detail', $customer->id) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-excel"></i> تصدير بيانات العميل
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-shield-alt text-warning"></i>
                                إجراءات متقدمة
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning mb-3">
                                <small>
                                    <i class="fas fa-exclamation-triangle"></i>
                                    تأكد من صحة البيانات قبل الحفظ. بعض التغييرات قد تؤثر على عروض الأسعار والحجوزات الحالية.
                                </small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <button type="button" class="btn btn-warning w-100" onclick="resetPassword()">
                                        <i class="fas fa-key"></i> إعادة تعيين كلمة المرور
                                    </button>
                                    <form id="resetPasswordForm" action="{{ route('admin.customers.reset-password', $customer->id) }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <button type="button" class="btn btn-info w-100" onclick="sendWelcomeEmail()">
                                        <i class="fas fa-envelope"></i> إرسال بريد ترحيبي
                                    </button>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}#activity-log" class="btn btn-secondary w-100">
                                        <i class="fas fa-history"></i> سجل الأنشطة
                                    </a>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <button type="button" class="btn btn-danger w-100" onclick="confirmDelete()">
                                        <i class="fas fa-trash"></i> حذف الحساب
                                    </button>
                                    <form id="deleteCustomerForm" action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 0.5rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 0.5rem 0.5rem 0 0 !important;
}

.form-label {
    font-weight: 600;
    color: #495057;
}

.btn {
    border-radius: 0.375rem;
    font-weight: 500;
}

.alert {
    border-radius: 0.5rem;
}

.text-danger {
    color: #dc3545 !important;
}

.text-primary {
    color: #0d6efd !important;
}
</style>

<script>
function resetPassword() {
    if (confirm('هل تريد إرسال رمز إعادة تعيين كلمة المرور إلى بريد هذا العميل؟')) {
        const form = document.getElementById('resetPasswordForm');
        if (form) {
            form.submit();
        }
    }
}

function sendWelcomeEmail() {
    if (confirm('هل تريد إرسال بريد ترحيبي لهذا العميل؟')) {
        alert('سيتم تنفيذ هذه الميزة قريباً');
    }
}

// تم ربط زر "سجل الأنشطة" مباشرةً بقسم activity-log في صفحة العرض

function confirmDelete() {
    if (confirm('تحذير: هل أنت متأكد من حذف هذا الحساب؟ لا يمكن التراجع عن هذا الإجراء!')) {
        if (confirm('تأكيد نهائي: سيتم حذف جميع البيانات المرتبطة بهذا العميل (الحجوزات، عروض الأسعار، إلخ)')) {
            const form = document.getElementById('deleteCustomerForm');
            if (form) {
                form.submit();
            }
        }
    }
}
</script>
@endsection
