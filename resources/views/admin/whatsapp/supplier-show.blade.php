@extends('layouts.admin')

@section('title', 'تفاصيل محادثة واتس أب')
@section('page-title', 'تفاصيل محادثة واتس أب')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fab fa-whatsapp text-success me-2"></i>
                        محادثة: {{ $conversation['name'] }}
                    </h5>
                    <a href="{{ route('admin.whatsapp.supplier.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>عودة
                    </a>
                </div>
                <div class="card-body">
                    <div class="waw-right" style="display: flex; height: calc(100vh - 200px); background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                        <!-- عمود اختيار المحادثة -->
                        <div style="width: 350px; border-left: 1px solid #eee; display: flex; flex-direction: column; background: #f8f9fa;">
                            <!-- رأس العمود - تعيين المورد -->
                            <div style="padding: 20px; border-bottom: 1px solid #eee; background: #fff;">
                                <h6 style="margin: 0 0 15px 0; color: var(--primary-color); font-weight: 700;">
                                    <i class="fas fa-user-check me-2"></i>تعيين مورد
                                </h6>
                                <select id="assignSupplier" class="form-select" style="border-radius: 10px;">
                                    <option value="">-- اختر مورد --</option>
                                    @foreach($approvedSuppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ $conversation['assigned_supplier'] && $conversation['assigned_supplier']['id'] == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @if($conversation['assigned_supplier'])
                                <div style="margin-top: 10px; padding: 10px; background: #d4edda; border-radius: 8px; color: #155724; font-size: 0.85rem;">
                                    <i class="fas fa-check-circle me-1"></i>
                                    معين حالياً: {{ $conversation['assigned_supplier']['name'] }}
                                </div>
                                @else
                                <div style="margin-top: 10px; padding: 10px; background: #fff3cd; border-radius: 8px; color: #856404; font-size: 0.85rem;">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    غير معين - ستظهر المحادثة لجميع الموردين
                                </div>
                                @endif
                            </div>
                            
                            <!-- معلومات العميل -->
                            <div style="padding: 20px; border-bottom: 1px solid #eee; background: #fff;">
                                <h6 style="margin: 0 0 15px 0; color: var(--primary-color); font-weight: 700;">
                                    <i class="fas fa-user me-2"></i>معلومات العميل
                                </h6>
                                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), #3d2a7a); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 1.5rem; margin-left: 15px;">
                                        {{ $conversation['avatar'] }}
                                    </div>
                                    <div>
                                        <h6 style="margin: 0; font-weight: 600;">{{ $conversation['name'] }}</h6>
                                        <p style="margin: 0; color: #6c757d; font-size: 0.9rem;">{{ $conversation['phone'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- منطقة المحادثة -->
                        <div class="waw-chat-area" style="flex: 1; display: flex; flex-direction: column; background: #fff;">
                            <!-- رأس المحادثة -->
                            <div style="padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: space-between; background: #f8f9fa;">
                                <div style="display: flex; align-items: center;">
                                    <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), #3d2a7a); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 1.1rem; margin-left: 12px;">
                                        {{ $conversation['avatar'] }}
                                    </div>
                                    <div>
                                        <h6 style="margin: 0; font-weight: 600; color: var(--primary-color);">{{ $conversation['name'] }}</h6>
                                        <p style="margin: 0; font-size: 0.8rem; color: #25D366;">
                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> متصل
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- منطقة الرسائل -->
                            <div style="flex: 1; overflow-y: auto; padding: 20px; background: #e5ddd5;">
                                @foreach($messages as $message)
                                <div style="display: flex; margin-bottom: 15px; {{ $message['sender'] === 'customer' ? 'justify-content: flex-start;' : 'justify-content: flex-end;' }}">
                                    <div style="max-width: 70%; padding: 12px 16px; border-radius: 12px; {{ $message['sender'] === 'customer' ? 'background: #fff; border-top-right-radius: 2px;' : 'background: #dcf8c6; border-top-left-radius: 2px;' }} box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
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
                                @endforeach
                            </div>
                            
                            <!-- منطقة الكتابة -->
                            <div style="padding: 15px 20px; border-top: 1px solid #eee; background: #f8f9fa;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <button class="btn btn-outline-secondary" style="border-radius: 10px; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-smile"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" style="border-radius: 10px; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                    <input type="text" class="form-control" placeholder="اكتب رسالتك..." style="border-radius: 20px; border: 1px solid #ddd;" disabled>
                                    <button class="btn" style="background: #25D366; color: #fff; border-radius: 10px; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;" disabled>
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const assignSelect = document.getElementById('assignSupplier');
    const conversationId = {{ $conversation['id'] }};
    
    assignSelect.addEventListener('change', function() {
        const supplierId = this.value;
        
        fetch(`/admin/whatsapp/supplier/${conversationId}/assign`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                supplier_id: supplierId || null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء تعيين المورد');
        });
    });
});
</script>

<style>
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
