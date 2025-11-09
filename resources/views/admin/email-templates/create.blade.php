@extends('layouts.admin')

@section('title', 'إضافة قالب بريد إلكتروني')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle me-2"></i>
            إضافة قالب بريد إلكتروني جديد
        </h1>
        <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right me-2"></i>رجوع
        </a>
    </div>

    <form action="{{ route('admin.email-templates.store') }}" method="POST">
        @csrf
        @include('admin.email-templates.form')
    </form>
</div>
@endsection
