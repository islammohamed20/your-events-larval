@extends('layouts.app')

@section('title', 'تعديل البيانات الشخصية') 'تعديل البيانات الشخصية')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-primary rounded-circle" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 32px; margin: 0 auto;">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    </div>
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted small">{{ $user->email }}</p>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('profile.show') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-user me-2"></i> البيانات الشخصية
                    </a>
                    <a href="{{ route('profile.edit') }}" class="list-group-item list-group-item-action active">
                        <i class="fas fa-edit me-2"></i> تعديل البيانات
                    </a>
                    <a href="{{ route('profile.password') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-key me-2"></i> تغيير كلمة المرور
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-heart me-2"></i> قائمة المفضلة
                        @if(auth()->user()->wishlists->count() > 0)
                            <span class="badge bg-danger rounded-pill float-end">{{ auth()->user()->wishlists->count() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('quotes.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-invoice me-2"></i> عروض الأسعار
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i> تعديل البيانات الشخصية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning d-flex justify-content-between align-items-center" role="alert">
                        <div>
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            حذف الحساب سيؤدي إلى إزالة جميع بياناتك نهائياً.
                        </div>
                        <form action="{{ route('profile.destroy') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف الحساب نهائياً؟ لا يمكن التراجع عن هذا الإجراء.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt me-1"></i> حذف الحساب
                            </button>
                        </form>
                    </div>
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Personal Information -->
                        <h6 class="mb-3 text-primary">
                            <i class="fas fa-user-circle me-2"></i> البيانات الشخصية
                        </h6>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                </div>
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Company Information -->
                        <h6 class="mb-3 text-primary">
                            <i class="fas fa-building me-2"></i> بيانات الجهة
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">اسم الجهة <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" name="company_name" value="{{ old('company_name', $user->company_name) }}" required>
                                </div>
                                @error('company_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tax_number" class="form-label">الرقم الضريبي</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-receipt"></i></span>
                                    <input type="text" class="form-control @error('tax_number') is-invalid @enderror" 
                                           id="tax_number" name="tax_number" value="{{ old('tax_number', $user->tax_number) }}">
                                </div>
                                <small class="text-muted">اختياري</small>
                                @error('tax_number')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <h6 class="mb-3 text-primary">
                            <i class="fas fa-credit-card me-2"></i> معلومات الدفع
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="card_type" class="form-label">نوع البطاقة</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                    <select class="form-select @error('card_type') is-invalid @enderror" 
                                            id="card_type" name="card_type">
                                        <option value="">اختر نوع البطاقة</option>
                                        <option value="visa" {{ old('card_type', $user->card_type) == 'visa' ? 'selected' : '' }}>
                                            فيزا (Visa)
                                        </option>
                                        <option value="mastercard" {{ old('card_type', $user->card_type) == 'mastercard' ? 'selected' : '' }}>
                                            ماستر كارد (Mastercard)
                                        </option>
                                        <option value="mada" {{ old('card_type', $user->card_type) == 'mada' ? 'selected' : '' }}>
                                            مدى (Mada)
                                        </option>
                                    </select>
                                </div>
                                <small class="text-muted">اختياري</small>
                                @error('card_type')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="card_holder_name" class="form-label">اسم حامل البطاقة</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('card_holder_name') is-invalid @enderror" 
                                           id="card_holder_name" name="card_holder_name" 
                                           value="{{ old('card_holder_name', $user->card_holder_name) }}"
                                           placeholder="الاسم كما في البطاقة">
                                </div>
                                <small class="text-muted">اختياري</small>
                                @error('card_holder_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="card_number" class="form-label">رقم البطاقة</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                    <input type="text" class="form-control" id="card_number" name="card_number"
                                           value="{{ old('card_number') }}" inputmode="numeric" autocomplete="off"
                                           placeholder="•••• •••• •••• ••••">
                                </div>
                                <small class="text-muted">لن يتم حفظ رقم البطاقة الكامل</small>
                            </div>

                            <div class="col-md-4">
                                <label for="card_last_four" class="form-label">آخر 4 أرقام</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                    <input type="text" class="form-control @error('card_last_four') is-invalid @enderror" 
                                           id="card_last_four" name="card_last_four" 
                                           value="{{ old('card_last_four', $user->card_last_four) }}"
                                           placeholder="1234" maxlength="4" pattern="[0-9]{4}">
                                </div>
                                <small class="text-muted">آخر 4 أرقام فقط</small>
                                @error('card_last_four')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="card_expiry_month" class="form-label">شهر الانتهاء</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <select class="form-select @error('card_expiry_month') is-invalid @enderror" 
                                            id="card_expiry_month" name="card_expiry_month">
                                        <option value="">شهر</option>
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ sprintf('%02d', $m) }}" 
                                                {{ old('card_expiry_month', $user->card_expiry_month) == sprintf('%02d', $m) ? 'selected' : '' }}>
                                                {{ sprintf('%02d', $m) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <small class="text-muted">MM</small>
                                @error('card_expiry_month')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="card_expiry_year" class="form-label">سنة الانتهاء</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <select class="form-select @error('card_expiry_year') is-invalid @enderror" 
                                            id="card_expiry_year" name="card_expiry_year">
                                        <option value="">سنة</option>
                                        @for($y = date('Y'); $y <= date('Y') + 10; $y++)
                                            <option value="{{ $y }}" 
                                                {{ old('card_expiry_year', $user->card_expiry_year) == $y ? 'selected' : '' }}>
                                                {{ $y }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <small class="text-muted">YYYY</small>
                                @error('card_expiry_year')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>ملاحظة:</strong> يمكنك إدخال رقم البطاقة للتأكد، لكن لن يتم حفظ رقم البطاقة الكامل أو CVC. نحفظ فقط آخر 4 أرقام للتعريف.
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i> إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-title {
    color: white;
    font-weight: bold;
}
.list-group-item.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cardNumberInput = document.getElementById('card_number');
    const lastFourInput = document.getElementById('card_last_four');
    if (!cardNumberInput || !lastFourInput) return;

    cardNumberInput.addEventListener('input', function () {
        const digits = (this.value || '').replace(/\D/g, '');
        if (digits.length >= 4) {
            lastFourInput.value = digits.slice(-4);
        }
    });
});
</script>
@endsection
