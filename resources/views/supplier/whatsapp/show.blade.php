@extends('supplier.layouts.app')

@section('title', __('common.whatsapp_conversation'))

@section('page-title', __('common.whatsapp_conversation'))

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
                <span class="badge bg-success">{{ count($conversations ?? []) }}</span>
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
            @forelse($conversations ?? [] as $conv)
            <a href="{{ route('supplier.whatsapp.show', $conv['id']) }}" 
               class="conversation-item {{ $conv['id'] == $conversation['id'] ? 'active' : '' }}" 
               style="display: flex; align-items: center; padding: 15px 20px; text-decoration: none; color: inherit; border-bottom: 1px solid #eee; transition: all 0.3s ease; cursor: pointer; {{ $conv['id'] == $conversation['id'] ? 'background: #e3f2fd;' : '' }}"
               onmouseover="this.style.background='#f0f0f0'" 
               onmouseout="this.style.background='transparent'">
                <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), #3d2a7a); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 1.2rem; margin-left: 15px; flex-shrink: 0;">
                    {{ $conv['avatar'] }}
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px;">
                        <h6 style="margin: 0; font-weight: 600; color: var(--primary-color);">{{ $conv['name'] }}</h6>
                        <span style="font-size: 0.75rem; color: #6c757d;">{{ $conv['time'] }}</span>
                    </div>
                    <p style="margin: 0; font-size: 0.85rem; color: #6c757d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $conv['last_message'] }}
                    </p>
                </div>
                @if($conv['unread'] > 0)
                <div style="width: 24px; height: 24px; border-radius: 50%; background: #25D366; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; margin-right: 10px;">
                    {{ $conv['unread'] }}
                </div>
                @endif
            </a>
            @empty
            <div style="padding: 40px 20px; text-align: center; color: #6c757d;">
                <i class="fab fa-whatsapp" style="font-size: 3rem; margin-bottom: 15px; color: #ddd;"></i>
                <p>لا توجد محادثات معينة لك</p>
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- منطقة المحادثة -->
    <div class="waw-chat-area" style="flex: 1; display: flex; flex-direction: column; background: #fff;">
        <!-- رأس المحادثة -->
        <div style="padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: space-between; background: #f8f9fa;">
            <div style="display: flex; align-items: center;">
                <a href="{{ route('supplier.whatsapp.index') }}" class="btn btn-outline-secondary d-lg-none me-2" style="border-radius: 10px;">
                    <i class="fas fa-arrow-right"></i>
                </a>
                <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), #3d2a7a); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 1.1rem; margin-left: 12px;">
                    {{ $conversation['avatar'] }}
                </div>
                <div>
                    <h6 style="margin: 0; font-weight: 600; color: var(--primary-color);">{{ $conversation['name'] }}</h6>
                    <p style="margin: 0; font-size: 0.8rem; color: #25D366;">
                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> {{ __('common.online') }}
                    </p>
                </div>
            </div>
            <div style="display: flex; gap: 10px;">
                <button class="btn btn-outline-secondary" style="border-radius: 10px; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-phone"></i>
                </button>
                <button class="btn btn-outline-secondary" style="border-radius: 10px; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-video"></i>
                </button>
                <button class="btn btn-outline-secondary" style="border-radius: 10px; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>
        
        <!-- منطقة الرسائل -->
        <div style="flex: 1; overflow-y: auto; padding: 20px; background: #e5ddd5;" id="messagesContainer">
            @forelse($messages as $message)
            <div style="display: flex; margin-bottom: 15px; {{ $message['sender'] === 'supplier' ? 'justify-content: flex-end;' : 'justify-content: flex-start;' }}">
                <div style="max-width: 70%; padding: 12px 16px; border-radius: 12px; {{ $message['sender'] === 'supplier' ? 'background: #dcf8c6; border-top-left-radius: 2px;' : 'background: #fff; border-top-right-radius: 2px;' }} box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                    <p style="margin: 0; color: #333; font-size: 0.95rem; line-height: 1.5;">
                        {{ $message['text'] }}
                    </p>
                    <div style="display: flex; align-items: center; justify-content: flex-end; margin-top: 5px;">
                        <span style="font-size: 0.7rem; color: #6c757d;">{{ $message['time'] }}</span>
                        @if($message['sender'] === 'supplier')
                        <i class="fas fa-check-double ms-1" style="font-size: 0.7rem; color: #34b7f1;"></i>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align: center; color: #6c757d; padding: 40px;">
                <i class="fab fa-whatsapp" style="font-size: 3rem; margin-bottom: 15px; color: #ddd;"></i>
                <p>ابدأ المحادثة بإرسال رسالة</p>
            </div>
            @endforelse
        </div>
        
        <!-- منطقة الكتابة -->
        <div style="padding: 15px 20px; border-top: 1px solid #eee; background: #f8f9fa;">
            <form id="messageForm" data-conversation-id="{{ $conversation['id'] }}">
                @csrf
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button type="button" class="btn btn-outline-secondary" style="border-radius: 10px; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-smile"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" style="border-radius: 10px; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <input type="text" id="messageInput" name="message" class="form-control" placeholder="{{ __('common.type_message') }}" style="border-radius: 20px; border: 1px solid #ddd;" required>
                    <button type="submit" class="btn" id="sendButton" style="background: #25D366; color: #fff; border-radius: 10px; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const messagesContainer = document.getElementById('messagesContainer');
    
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const conversationId = messageForm.dataset.conversationId;
        const message = messageInput.value.trim();
        
        if (!message) return;
        
        sendButton.disabled = true;
        sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        fetch(`/supplier/whatsapp/${conversationId}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                // إضافة الرسالة إلى الواجهة
                const now = new Date();
                const time = now.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' });
                
                const messageDiv = document.createElement('div');
                messageDiv.style.cssText = 'display: flex; margin-bottom: 15px; justify-content: flex-end;';
                messageDiv.innerHTML = `
                    <div style="max-width: 70%; padding: 12px 16px; border-radius: 12px; background: #dcf8c6; border-top-left-radius: 2px; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                        <p style="margin: 0; color: #333; font-size: 0.95rem; line-height: 1.5;">
                            ${message}
                        </p>
                        <div style="display: flex; align-items: center; justify-content: flex-end; margin-top: 5px;">
                            <span style="font-size: 0.7rem; color: #6c757d;">${time}</span>
                            <i class="fas fa-check-double ms-1" style="font-size: 0.7rem; color: #34b7f1;"></i>
                        </div>
                    </div>
                `;
                messagesContainer.appendChild(messageDiv);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            } else {
                alert('فشل إرسال الرسالة: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إرسال الرسالة');
        })
        .finally(() => {
            sendButton.disabled = false;
            sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
        });
    });
});
</script>

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

.waw-chat-area > div:nth-child(2)::-webkit-scrollbar {
    width: 6px;
}

.waw-chat-area > div:nth-child(2)::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.waw-chat-area > div:nth-child(2)::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.waw-chat-area > div:nth-child(2)::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection
