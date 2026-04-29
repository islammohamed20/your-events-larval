@extends('layouts.admin')

@section('title', 'عملاء الواتساب')
@section('page-title', 'عملاء الواتساب')
@section('page-description', 'قائمة العملاء المتزامنة مع نظام Faalwa')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-users text-primary me-2"></i> قائمة العملاء (Bot Users)</h5>
                
                <form action="{{ route('admin.whatsapp.customers.index') }}" method="GET" class="d-flex" style="width: 300px;">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو الرقم..." value="{{ $search }}">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>العميل</th>
                                <th>رقم الهاتف</th>
                                <th>القنوات (Channels)</th>
                                <th>الاشتراك (Opt-in)</th>
                                <th>تاريخ الانضمام</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:35px; height:35px;">
                                            {{ mb_substr($customer['name'] ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $customer['name'] ?? 'بدون اسم' }}</h6>
                                            <small class="text-muted">{{ $customer['user_ns'] ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span dir="ltr">{{ $customer['phone'] ?? $customer['user_id'] ?? 'غير متوفر' }}</span>
                                </td>
                                <td>
                                    @php
                                        $channels = $customer['channels'] ?? [];
                                        if (is_string($channels)) $channels = [$channels];
                                    @endphp
                                    @if(!empty($channels))
                                        @foreach($channels as $channel)
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                @if(str_contains(strtolower($channel), 'whatsapp'))
                                                    <i class="fab fa-whatsapp text-success"></i>
                                                @endif
                                                {{ $channel }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($customer['is_opt_in_sms']) && $customer['is_opt_in_sms'] == 1)
                                        <span class="badge bg-success-subtle text-success"><i class="fas fa-check-circle"></i> مشترك</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger"><i class="fas fa-times-circle"></i> غير مشترك</span>
                                    @endif
                                </td>
                                <td>
                                    {{ isset($customer['created_at']) ? \Carbon\Carbon::parse($customer['created_at'])->format('Y-m-d h:i A') : '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                    <h5>لا يوجد عملاء</h5>
                                    <p>لم يتم العثور على أي عملاء متزامنين مع نظام Faalwa.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if(($pagination['total_pages'] ?? 1) > 1)
            <div class="card-footer d-flex justify-content-center">
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        @php
                            $currentPage = $pagination['current_page'] ?? 1;
                            $totalPages = $pagination['total_pages'] ?? 1;
                        @endphp
                        
                        <li class="page-item {{ $currentPage <= 1 ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}">السابق</a>
                        </li>
                        
                        <li class="page-item disabled">
                            <span class="page-link">صفحة {{ $currentPage }} من {{ $totalPages }}</span>
                        </li>
                        
                        <li class="page-item {{ $currentPage >= $totalPages ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}">التالي</a>
                        </li>
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
