@extends('layouts.app')

@section('title', 'تقييم الحجز - Your Events')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="text-center mb-4" data-aos="fade-up">
                    <h1 class="h3 fw-bold text-primary mb-2">تقييم تجربتك</h1>
                    <p class="text-muted mb-0">رقم الحجز: <strong dir="ltr">{{ $booking->booking_reference }}</strong></p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success text-center" data-aos="fade-up">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger text-center" data-aos="fade-up">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if($existingReview)
                    <div class="card shadow" data-aos="fade-up">
                        <div class="card-body p-5 text-center">
                            <i class="fas fa-star fa-3x text-warning mb-3"></i>
                            <h4 class="mb-3">تم استلام تقييمك مسبقاً</h4>
                            <div class="mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star fa-lg {{ $i <= $existingReview->rating ? 'text-warning' : 'text-secondary' }}"></i>
                                @endfor
                            </div>
                            <p class="text-muted mb-0">{{ $existingReview->comment ?: 'بدون تعليق' }}</p>
                        </div>
                    </div>
                @else
                    <div class="card shadow" data-aos="fade-up">
                        <div class="card-body p-4 p-md-5">
                            <h4 class="mb-4">شاركنا رأيك</h4>

                            <div class="bg-light rounded p-3 mb-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">اسم العميل</small>
                                        <strong>{{ $booking->client_name }}</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">تاريخ الفعالية</small>
                                        <strong>{{ $booking->event_date?->format('d/m/Y') ?: '-' }}</strong>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('booking.review.submit', $booking->booking_reference) }}">
                                @csrf

                                <div class="mb-4">
                                    <label class="form-label fw-bold">اسمك</label>
                                    <input type="text" name="client_name" class="form-control @error('client_name') is-invalid @enderror" value="{{ old('client_name', $booking->client_name) }}" required>
                                    @error('client_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4 text-center">
                                    <label class="form-label fw-bold d-block">تقييمك</label>
                                    <div class="rating-stars" dir="ltr">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required>
                                            <label for="star{{ $i }}" title="{{ $i }} نجوم"><i class="fas fa-star"></i></label>
                                        @endfor
                                    </div>
                                    @error('rating')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">تعليقك</label>
                                    <textarea name="comment" class="form-control @error('comment') is-invalid @enderror" rows="4" placeholder="اكتب تجربتك مع الخدمة...">{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>إرسال التقييم
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
.rating-stars {
    display: inline-flex;
    flex-direction: row-reverse;
    gap: 0.25rem;
    font-size: 2rem;
}
.rating-stars input {
    display: none;
}
.rating-stars label {
    color: #dee2e6;
    cursor: pointer;
    transition: color 0.15s;
}
.rating-stars input:checked ~ label,
.rating-stars label:hover,
.rating-stars label:hover ~ label {
    color: #ffc107;
}
</style>
@endpush
@endsection
