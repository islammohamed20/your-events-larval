@extends('layouts.admin')

@section('title', 'تعديل قالب البريد')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit me-2"></i>
            تعديل قالب: {{ $emailTemplate->name }}
        </h1>
        <div>
            <a href="{{ route('admin.email-templates.show', $emailTemplate) }}" class="btn btn-info me-2">
                <i class="fas fa-eye me-2"></i>معاينة
            </a>
            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    <form action="{{ route('admin.email-templates.update', $emailTemplate) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.email-templates.form')
    </form>
</div>
@endsection
