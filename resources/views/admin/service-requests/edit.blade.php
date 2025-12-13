@extends('layouts.app')

@section('title', 'تعديل طلب الخدمة')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white p-4">
                    <h3 class="mb-0">
                        <i class="fas fa-edit me-2"></i>تعديل الطلب #{{ $serviceRequest->id }}
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

                    <form method="POST" action="{{ route('admin.service-requests.update', $serviceRequest->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- الخدمة (للقراءة فقط) -->
                        <div class="mb-4">
                            <label for="service" class="form-label fw-bold">الخدمة</label>
                            <input type="text" class="form-control" value="{{ $serviceRequest->service->name }}" disabled>
                        </div>

                        <!-- الفئة (للقراءة فقط) -->
                        <div class="mb-4">
                            <label for="category" class="form-label fw-bold">الفئة</label>
                            <input type="text" class="form-control" value="{{ $serviceRequest->category->name }}" disabled>
                        </div>

                        <!-- الكمية -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="quantity" class="form-label fw-bold">الكمية *</label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                       name="quantity" id="quantity" value="{{ old('quantity', $serviceRequest->quantity) }}" min="1" required>
                                @error('quantity')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- السعر -->
                            <div class="col-md-6 mb-4">
                                <label for="price" class="form-label fw-bold">السعر *</label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                       name="price" id="price" value="{{ old('price', $serviceRequest->price) }}" step="0.01" min="0" required>
                                @error('price')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- ملاحظات العميل -->
                        <div class="mb-4">
                            <label for="customer_notes" class="form-label fw-bold">ملاحظات العميل</label>
                            <textarea class="form-control @error('customer_notes') is-invalid @enderror" 
                                      name="customer_notes" id="customer_notes" rows="3">{{ old('customer_notes', $serviceRequest->customer_notes) }}</textarea>
                            @error('customer_notes')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- ملاحظات عامة -->
                        <div class="mb-4">
                            <label for="admin_notes" class="form-label fw-bold">ملاحظات عامة</label>
                            <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                                      name="admin_notes" id="admin_notes" rows="3">{{ old('admin_notes', $serviceRequest->admin_notes) }}</textarea>
                            @error('admin_notes')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- الحالة -->
                        <div class="mb-4">
                            <label for="status" class="form-label fw-bold">الحالة *</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" id="status" required>
                                <option value="pending" {{ old('status', $serviceRequest->status) === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="accepted" {{ old('status', $serviceRequest->status) === 'accepted' ? 'selected' : '' }}>مقبول</option>
                                <option value="rejected" {{ old('status', $serviceRequest->status) === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                <option value="completed" {{ old('status', $serviceRequest->status) === 'completed' ? 'selected' : '' }}>مكتمل</option>
                            </select>
                            @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- الأزرار -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>حفظ التعديلات
                            </button>
                            <a href="{{ route('admin.service-requests.show', $serviceRequest->id) }}" class="btn btn-secondary btn-lg">
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
