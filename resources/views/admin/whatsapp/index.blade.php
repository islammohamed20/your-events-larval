@extends('layouts.admin')

@section('title', 'لوحة واتساب')
@section('page-title', 'واتساب')
@section('page-description', 'إدارة المحادثات')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/waw.css') }}?v={{ filemtime(public_path('css/waw.css')) }}">
<style>
/* تحسين مساحة الشاشة بالكامل للمحادثات */
.main-content { padding: 0 !important; }
.admin-header { margin: 0 !important; }

@media (min-width: 993px) {
    /* إخفاء الهيدر العلوي في الكمبيوتر لإعطاء 100% للواتساب */
    .admin-header { display: none !important; }
    .waw-shell {
        height: 100vh !important;
        border-radius: 0 !important;
        border-left: 1px solid var(--waw-border);
        box-shadow: none !important;
    }
}

@media (max-width: 992px) {
    /* في الموبايل، تقليص الهيدر العلوي لإبقاء زر القائمة متاحاً */
    .admin-header {
        padding: 8px 16px !important;
        box-shadow: none !important;
        border-bottom: 1px solid var(--waw-border) !important;
        background: var(--waw-panel) !important;
    }
    .admin-header h3, .admin-header small { display: none !important; }
    
    .waw-shell {
        height: calc(100vh - 54px) !important;
        border-radius: 0 !important;
    }
}
</style>
@endsection

@section('content')
<div class="waw-shell"
    id="whatsappDashboard"
    data-conversations-url="{{ route('admin.whatsapp.conversations') }}"
    data-poll-url="{{ route('admin.whatsapp.poll') }}"
    data-message-url-template="{{ route('admin.whatsapp.messages', ['conversation' => '__ID__']) }}"
    data-send-url-template="{{ route('admin.whatsapp.send', ['conversation' => '__ID__']) }}"
    data-assign-url-template="{{ route('admin.whatsapp.assign', ['conversation' => '__ID__']) }}"
    data-status-url-template="{{ route('admin.whatsapp.status', ['conversation' => '__ID__']) }}"
    data-start-url="{{ route('admin.whatsapp.start') }}"
>

    {{-- ══════════════ COLUMN 1: Sidebar Filters ══════════════ --}}
    <div class="waw-sidebar">
        <div class="waw-sb-header-top">
            <span class="waw-sb-title">عدد مرات التحديث</span>
            <div class="d-flex align-items-center">
                <span class="waw-poll-dot me-2" id="pollStatusDot" title="المزامنة تعمل"></span>
                <button class="waw-icon-btn" id="refreshSpinIcon" title="تحديث"><i class="fas fa-sync-alt"></i></button>
                <button class="waw-icon-btn ms-1" id="themeToggleBtn" title="تغيير المظهر"><i class="fas fa-moon" id="themeIcon"></i></button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="waw-sb-section">
            <button class="waw-sb-item active" data-filter-tab="all">
                <i class="fas fa-users text-primary"></i> الكل
            </button>
            <button class="waw-sb-item" data-filter-tab="my">
                <i class="fas fa-user text-dark"></i> مُخصص لي
            </button>
            <button class="waw-sb-item" data-filter-tab="unassigned">
                <i class="fas fa-user-circle text-secondary"></i> غير معين
            </button>
        </div>

        <div class="waw-sb-divider"></div>
        
        <div class="waw-sb-header">
            <span>الحالة</span>
            <i class="fas fa-chevron-up"></i>
        </div>

        {{-- Statuses --}}
        <div class="waw-sb-section">
            <button class="waw-sb-item active" data-status-tab="">
                <i class="far fa-list-alt text-primary"></i> الكل
                @if(($stats['open_chats'] ?? 0) + ($stats['pending_chats'] ?? 0) > 0)
                    <span class="waw-badge">{{ ($stats['open_chats'] ?? 0) + ($stats['pending_chats'] ?? 0) }}</span>
                @endif
            </button>
            <button class="waw-sb-item" data-status-tab="open">
                <i class="fas fa-comment-dots" style="color:#d9534f;"></i> فتح
                @if(($stats['open_chats'] ?? 0) > 0)
                    <span class="waw-badge">{{ $stats['open_chats'] }}</span>
                @endif
            </button>
            <button class="waw-sb-item" data-status-tab="pending">
                <i class="fas fa-exclamation-circle" style="color:#102a43;"></i> قيد الانتظار
                @if(($stats['pending_chats'] ?? 0) > 0)
                    <span class="waw-badge">{{ $stats['pending_chats'] }}</span>
                @endif
            </button>
            <button class="waw-sb-item" data-status-tab="closed">
                <i class="fas fa-check-square text-success"></i> تم
            </button>
        </div>
    </div>

    {{-- ══════════════ COLUMN 2: Conversation List ══════════════ --}}
    <div class="waw-left" id="wawLeft">
        
        {{-- Search and Unanswered --}}
        <div class="waw-search">
            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                <div class="d-flex align-items-center">
                    <span class="me-2" style="font-size: 0.85rem; font-weight: 600; color: var(--waw-text);">لم يتم الرد عليها</span>
                    <div class="form-check form-switch m-0 p-0" style="padding-right: 2.5em !important;">
                        <input class="form-check-input" type="checkbox" id="unansweredFilter">
                    </div>
                </div>
                <button class="waw-icon-btn text-success" title="محادثة جديدة" data-bs-toggle="modal" data-bs-target="#newChatModal">
                    <i class="fas fa-comment-medical"></i>
                </button>
            </div>
            
            <div class="d-flex gap-2">
                <button class="waw-icon-btn" style="border:1px solid var(--waw-border); border-radius:6px; width:36px; height:36px;"><i class="fas fa-filter"></i></button>
                <button class="waw-icon-btn" style="border:1px solid var(--waw-border); border-radius:6px; width:36px; height:36px;"><i class="fas fa-sort"></i></button>
                <div class="waw-search-inner" style="flex:1;">
                    <i class="fas fa-search"></i>
                    <input type="search" id="conversationSearch" placeholder="بحث">
                </div>
            </div>
        </div>

        {{-- Hidden filters for JS --}}
        <select id="conversationFilter" class="d-none">
            <option value="all">الكل</option>
            <option value="my">محادثاتي</option>
            <option value="unassigned">غير مسندة</option>
        </select>
        <select id="statusFilter" class="d-none">
            <option value="">كل الحالات</option>
            <option value="open">مفتوحة</option>
            <option value="pending">معلقة</option>
            <option value="closed">مغلقة</option>
        </select>

        {{-- Conversation list (el-scrollbar__view) --}}
        <div class="waw-conv-list el-scrollbar__view" id="conversationList">
            <div class="waw-loading mb-1">
                <i class="fas fa-spinner fa-spin"></i>
                <span>جارٍ التحميل...</span>
            </div>
        </div>
    </div>

    {{-- ══════════════ RIGHT: Chat area ══════════════ --}}
    <div class="waw-right">

        {{-- Intro (no conversation selected) --}}
        <div class="waw-intro" id="wawIntro">
            <div class="waw-intro-icon">
                <i class="fab fa-whatsapp"></i>
            </div>
            <h2>واتساب للأعمال</h2>
            <p>أداة إدارة محادثات واتساب الاحترافية. اختر محادثة من القائمة أو ابدأ محادثة جديدة.</p>
            <div class="waw-intro-line"></div>
            <button class="waw-intro-btn" data-bs-toggle="modal" data-bs-target="#newChatModal">
                <i class="fas fa-plus me-2"></i>محادثة جديدة
            </button>
        </div>

        {{-- Chat pane --}}
        <div class="waw-chat d-none" id="wawChat">

            {{-- Chat header --}}
            <div class="waw-chat-hd">
                <button class="waw-icon-btn waw-back-btn" id="btnBack" title="رجوع">
                    <i class="fas fa-arrow-right"></i>
                </button>
                <div class="waw-chd-avatar" id="chatHeaderAvatar">؟</div>
                <div class="waw-chd-info">
                    <div class="waw-chd-name" id="chatCustomerName">اختر محادثة</div>
                    <div class="waw-chd-sub"  id="chatCustomerMeta"></div>
                </div>
                <div class="waw-chd-actions">
                    <select id="conversationAgent" class="waw-agent-select" disabled>
                        <option value="">تعيين وكيل...</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">
                                {{ $agent->name }}{{ $agent->is_online ? ' 🟢' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" class="waw-status-btn" id="btnReopenConv" disabled>
                        إغلاق
                    </button>
                    <button type="button" class="waw-icon-btn" title="المزيد">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>

            {{-- Messages --}}
            <div class="waw-messages" id="messageList">
                <div class="waw-empty-chat">
                    <i class="fab fa-whatsapp"></i>
                    <span>لا توجد رسائل بعد</span>
                </div>
            </div>

            {{-- Compose --}}
            <div class="waw-compose">
                <form id="chatForm">
                    <input type="hidden" id="messageType" value="text">
                    <input type="hidden" id="selectedTemplateId" value="">
                    <select id="conversationStatus" class="d-none">
                        <option value="open">open</option>
                        <option value="pending">pending</option>
                        <option value="closed">closed</option>
                    </select>

                    <div class="waw-compose-icons">
                        <button type="button" class="waw-icon-btn" title="إيموجي">
                            <i class="far fa-smile"></i>
                        </button>
                        <button type="button" class="waw-icon-btn" title="إرفاق">
                            <i class="fas fa-paperclip"></i>
                        </button>
                    </div>

                    <div class="waw-compose-body">
                        <textarea id="messageInput" rows="1" placeholder="اكتب رسالة..." disabled></textarea>
                        <select id="templateSelect" class="waw-tpl-select">
                            <option value="">📋 قوالب</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}"
                                        data-content="{{ e($template->content) }}"
                                        data-type="{{ $template->type }}">
                                    {{ $template->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="waw-send-btn" id="sendMessageButton" disabled>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>

        </div>{{-- end waw-chat --}}
    </div>{{-- end waw-right --}}

</div>{{-- end waw-shell --}}


{{-- ═══ New Chat Modal ═══ --}}
<div class="modal fade waw-modal" id="newChatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fab fa-whatsapp text-success me-2"></i>محادثة جديدة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="newChatForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:var(--waw-hover);border-color:var(--waw-border);color:var(--waw-text-2);">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="tel" id="newChatPhone" class="form-control"
                                   placeholder="966501234567" required>
                        </div>
                        <div class="form-text" style="color:var(--waw-text-2);">أدخل الرقم مع رمز الدولة بدون + (مثال: 966501234567)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الرسالة الأولى <span class="text-danger">*</span></label>
                        <textarea id="newChatMessage" class="form-control" rows="3"
                                  placeholder="اكتب رسالتك هنا..." required></textarea>
                    </div>
                    <div id="newChatError" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success btn-sm" id="newChatSubmit">
                        <i class="fas fa-paper-plane me-1"></i>إرسال
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/chat.js') }}?v={{ filemtime(public_path('js/chat.js')) }}"></script>
@endsection