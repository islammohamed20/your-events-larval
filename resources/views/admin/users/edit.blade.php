@extends('layouts.admin')

@section('title', 'تعديل مستخدم: ' . $user->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تعديل مستخدم</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="text" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Company Information -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 text-primary"><i class="fas fa-building me-2"></i>بيانات الجهة</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">اسم الجهة <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" 
                                           name="company_name" 
                                           value="{{ old('company_name', $user->company_name) }}" 
                                           required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax_number" class="form-label">الرقم الضريبي</label>
                                    <input type="text" 
                                           class="form-control @error('tax_number') is-invalid @enderror" 
                                           id="tax_number" 
                                           name="tax_number" 
                                           value="{{ old('tax_number', $user->tax_number) }}">
                                    @error('tax_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Information -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3 text-primary"><i class="fas fa-credit-card me-2"></i>معلومات الدفع</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="card_type" class="form-label">نوع البطاقة</label>
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
                                    @error('card_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="card_holder_name" class="form-label">اسم حامل البطاقة</label>
                                    <input type="text" 
                                           class="form-control @error('card_holder_name') is-invalid @enderror" 
                                           id="card_holder_name" 
                                           name="card_holder_name" 
                                           value="{{ old('card_holder_name', $user->card_holder_name) }}"
                                           placeholder="الاسم كما في البطاقة">
                                    @error('card_holder_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="card_last_four" class="form-label">آخر 4 أرقام من البطاقة</label>
                                    <input type="text" 
                                           class="form-control @error('card_last_four') is-invalid @enderror" 
                                           id="card_last_four" 
                                           name="card_last_four" 
                                           value="{{ old('card_last_four', $user->card_last_four) }}"
                                           placeholder="1234" maxlength="4" pattern="[0-9]{4}">
                                    @error('card_last_four')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="card_expiry_month" class="form-label">شهر الانتهاء (MM)</label>
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
                                    @error('card_expiry_month')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="card_expiry_year" class="form-label">سنة الانتهاء (YYYY)</label>
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
                                    @error('card_expiry_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">كلمة المرور</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password"
                                           placeholder="اتركه فارغاً إذا كنت لا تريد تغييره">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">اتركه فارغاً إذا كنت لا تريد تغيير كلمة المرور</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation"
                                           placeholder="تأكيد كلمة المرور">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_admin" 
                                               name="is_admin" 
                                               value="1"
                                               {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_admin">
                                            مدير النظام
                                        </label>
                                        @if($user->id === Auth::id())
                                            <div class="form-text text-warning">لا يمكنك إزالة صلاحيات المدير من حسابك الخاص</div>
                                        @else
                                            <div class="form-text">المستخدمون الذين لديهم صلاحيات الإدارة يمكنهم الوصول إلى لوحة التحكم</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>معلومات إضافية:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>تاريخ التسجيل: {{ $user->created_at->format('Y-m-d H:i') }}</li>
                                        <li>آخر تحديث: {{ $user->updated_at->format('Y-m-d H:i') }}</li>
                                        <li>عدد الحجوزات: {{ $user->bookings()->count() }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> حفظ التغييرات
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
