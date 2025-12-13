@extends('layouts.app')

@section('title', 'إنشاء طلب خدمة جديد')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white p-4">
                    <h3 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>إنشاء طلب خدمة جديد
                    </h3>
                </div>
                <div class="card-body p-5">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('admin.service-requests.store') }}">
                        @csrf

                        <!-- الحجز -->
                        <div class="mb-4">
                            <label for="booking_id" class="form-label fw-bold">الحجز *</label>
                            <select class="form-select @error('booking_id') is-invalid @enderror" 
                                    name="booking_id" id="booking_id" required>
                                <option value="">-- اختر الحجز --</option>
                                @foreach($bookings as $booking)
                                <option value="{{ $booking->id }}" {{ old('booking_id') == $booking->id ? 'selected' : '' }}>
                                    {{ $booking->client_name }} - {{ $booking->created_at->format('Y-m-d') }}
                                </option>
                                @endforeach
                            </select>
                            @error('booking_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- الفئة -->
                        <div class="mb-4">
                            <label for="category_id" class="form-label fw-bold">الفئة *</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    name="category_id" id="category_id" required>
                                <option value="">-- اختر الفئة --</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- الخدمة -->
                        <div class="mb-4">
                            <label for="service_id" class="form-label fw-bold">الخدمة *</label>
                            <select class="form-select @error('service_id') is-invalid @enderror" 
                                    name="service_id" id="service_id" required>
                                <option value="">-- اختر الخدمة --</option>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('service_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- الكمية -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="quantity" class="form-label fw-bold">الكمية *</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                       name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" required>
                                @error('quantity')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- السعر -->
                            <div class="col-md-6 mb-4">
                                <label for="price" class="form-label fw-bold">السعر *</label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                       name="price" id="price" value="{{ old('price') }}" step="0.01" min="0" required>
                                @error('price')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- ملاحظات العميل -->
                        <div class="mb-4">
                            <label for="customer_notes" class="form-label fw-bold">ملاحظات العميل</label>
                            <textarea class="form-control @error('customer_notes') is-invalid @enderror" 
                                      name="customer_notes" id="customer_notes" rows="3">{{ old('customer_notes') }}</textarea>
                            @error('customer_notes')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- ملاحظات عامة -->
                        <div class="mb-4">
                            <label for="admin_notes" class="form-label fw-bold">ملاحظات عامة</label>
                            <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                                      name="admin_notes" id="admin_notes" rows="3">{{ old('admin_notes') }}</textarea>
                            @error('admin_notes')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- الأزرار -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>حفظ الطلب
                            </button>
                            <a href="{{ route('admin.service-requests.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
