@extends('supplier.layouts.app')

@section('title', __('common.whatsapp_conversations'))

@section('page-title', __('common.whatsapp_conversations'))

@section('content')
<div class="waw-right" style="display: flex; height: calc(100vh - 140px); background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <!-- عمود اختيار المحادثة -->
    <div style="width: 350px; border-left: 1px solid #eee; display: flex; flex-direction: column; background: #f8f9fa;">
        <!-- رأس العمود -->
        <div style="padding: 20px; border-bottom: 1px solid #eee; background: #fff;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                <h5 style="margin: 0; color: var(--primary-color); font-weight: 700;">
                    <i class="fab fa-whatsapp me-2" style="color: #25D366;"></i>
                    {{ __('common.whatsapp_conversations') }}
                </h5>
                <span class="badge bg-success">{{ count($conversations) }}</span>
            </div>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="{{ __('common.search_conversations') }}" style="border-radius: 10px 0 0 10px;">
                <button class="btn btn-outline-secondary" type="button" style="border-radius: 0 10px 10px 0;">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        
        <!-- قائمة المحادثات -->
        <div style="flex: 1; overflow-y: auto;">
            @forelse($conversations as $conversation)
            <a href="{{ route('supplier.whatsapp.show', $conversation['id']) }}" 
               class="conversation-item" 
               style="display: flex; align-items: center; padding: 15px 20px; text-decoration: none; color: inherit; border-bottom: 1px solid #eee; transition: all 0.3s ease; cursor: pointer; {{ $loop->first ? 'background: #e3f2fd;' : '' }}"
               onmouseover="this.style.background='#f0f0f0'" 
               onmouseout="this.style.background='{{ $loop->first ? '#e3f2fd' : 'transparent' }}'">
                <!-- الصورة الرمزية -->
                <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), #3d2a7a); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 1.2rem; margin-left: 15px; flex-shrink: 0;">
                    {{ $conversation['avatar'] }}
                </div>
                
                <!-- معلومات المحادثة -->
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
                        <h6 style="margin: 0; font-weight: 600; color: var(--primary-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $conversation['name'] }}
                        </h6>
                        <span style="font-size: 0.75rem; color: #6c757d;">{{ $conversation['time'] }}</span>
                    </div>
                    <p style="margin: 0; font-size: 0.85rem; color: #6c757d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $conversation['last_message'] }}
                    </p>
                </div>
                
                <!-- عدد الرسائل غير المقروءة -->
                @if($conversation['unread'] > 0)
                <div style="width: 24px; height: 24px; border-radius: 50%; background: #25D366; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; margin-right: 10px;">
                    {{ $conversation['unread'] }}
                </div>
                @endif
            </a>
            @empty
            <div style="padding: 40px 20px; text-align: center; color: #6c757d;">
                <i class="fab fa-whatsapp" style="font-size: 3rem; margin-bottom: 15px; color: #ddd;"></i>
                <p>لا توجد محادثات معينة لك</p>
                <p style="font-size: 0.85rem; margin-top: 10px;">سيتم إشعارك عند تعيين محادثة جديدة</p>
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- منطقة المحادثة -->
    <div class="waw-chat-area" style="flex: 1; display: flex; flex-direction: column; background: #fff;">
        <!-- رسالة ترحيبية -->
        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; text-align: center;">
            <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #25D366, #128C7E); display: flex; align-items: center; justify-content: center; margin-bottom: 30px;">
                <i class="fab fa-whatsapp" style="font-size: 4rem; color: #fff;"></i>
            </div>
            <h4 style="color: var(--primary-color); font-weight: 700; margin-bottom: 15px;">
                {{ __('common.welcome_to_whatsapp') }}
            </h4>
            <p style="color: #6c757d; font-size: 1rem; max-width: 400px;">
                {{ __('common.select_conversation_to_start') }}
            </p>
        </div>
    </div>
</div>

<style>
.conversation-item:hover {
    background: #f0f0f0 !important;
}

.conversation-item.active {
    background: #e3f2fd !important;
}

.waw-sidebar::-webkit-scrollbar {
    width: 6px;
}

.waw-sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.waw-sidebar::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.waw-sidebar::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection
