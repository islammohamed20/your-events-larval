@extends('layouts.admin')

@section('title', 'الإشعارات - لوحة التحكم')
@section('page-title', 'الإشعارات')
@section('page-description', 'جميع إشعارات النظام')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-bell me-2"></i>الإشعارات</span>
        @if($notifications->where('is_read', false)->count() > 0)
        <form action="{{ route('admin.notifications.read-all') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-light">
                <i class="fas fa-check-double me-1"></i>تحديد الكل كمقروء
            </button>
        </form>
        @endif
    </div>
    <div class="card-body p-0">
        @forelse($notifications as $notification)
            <div class="d-flex align-items-start p-3 border-bottom {{ $notification->is_read ? '' : 'bg-light' }}" style="transition:background 0.2s;">
                {{-- Icon --}}
                <div class="flex-shrink-0 me-3" style="width:44px;height:44px;border-radius:50%;background:{{ $notification->color ?? '#1f144a' }}15;display:flex;align-items:center;justify-content:center;">
                    <i class="{{ $notification->icon ?? 'fas fa-bell' }}" style="color:{{ $notification->color ?? '#1f144a' }};font-size:18px;"></i>
                </div>
                {{-- Content --}}
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 fw-bold" style="font-size:14px;">
                                @if(!$notification->is_read)
                                    <span class="badge bg-primary rounded-pill me-1" style="font-size:9px;">جديد</span>
                                @endif
                                {{ $notification->title }}
                            </h6>
                            <p class="mb-1 text-muted" style="font-size:13px;">{{ $notification->message }}</p>
                            <small class="text-muted"><i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="d-flex gap-1">
                            @if($notification->link)
                                <a href="{{ $notification->link }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endif
                            @if(!$notification->is_read)
                                <form action="{{ route('admin.notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="تحديد كمقروء">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف هذا الإشعار؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-bell-slash fa-3x mb-3 d-block" style="opacity:0.3;"></i>
                <h5>لا توجد إشعارات</h5>
                <p>ستظهر الإشعارات هنا عند حدوث أحداث جديدة في النظام</p>
            </div>
        @endforelse
    </div>
    @if($notifications->hasPages())
    <div class="card-footer d-flex justify-content-center">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
