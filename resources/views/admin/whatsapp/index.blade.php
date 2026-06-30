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
    data-panel-url-template="{{ route('admin.whatsapp.panel', ['conversation' => '__ID__']) }}"
    data-send-url-template="{{ route('admin.whatsapp.send', ['conversation' => '__ID__']) }}"
    data-assign-url-template="{{ route('admin.whatsapp.assign', ['conversation' => '__ID__']) }}"
    data-status-url-template="{{ route('admin.whatsapp.status', ['conversation' => '__ID__']) }}"
    data-pause-bot-url-template="{{ route('admin.whatsapp.pause-bot', ['conversation' => '__ID__']) }}"
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
                <button type="button" class="waw-chd-info waw-customer-trigger" id="chatCustomerTrigger">
                    <div class="waw-chd-name" id="chatCustomerName">اختر محادثة</div>
                    <div class="waw-chd-sub"  id="chatCustomerMeta"></div>
                </button>
                <div class="waw-chd-actions">
                    <select id="conversationAgent" class="waw-agent-select" disabled>
                        <option value="">تعيين جهة...</option>
                        <optgroup label="الفريق">
                            @foreach($agents as $agent)
                                <option value="user:{{ $agent->id }}">
                                    {{ $agent->name }}{{ $agent->is_online ? ' 🟢' : '' }}
                                </option>
                            @endforeach
                        </optgroup>
                        @if(isset($suppliers) && $suppliers->count())
                            <optgroup label="الموردون">
                                @foreach($suppliers as $supplier)
                                    <option value="supplier:{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                    <button type="button" class="waw-status-btn" id="btnReopenConv" disabled>
                        إغلاق
                    </button>
                </div>
            </div>

            {{-- Messages --}}
            <div class="waw-messages w-full p-0 chat-panel-height overflow-y-auto container-scroll h-100 no-footer" id="messageList">
                <div class="waw-empty-chat">
                    <i class="fab fa-whatsapp"></i>
                    <span>لا توجد رسائل بعد</span>
                </div>
            </div>

            <aside class="waw-customer-panel d-none" id="customerInfoPanel" aria-hidden="true">
                <div class="waw-panel-tabs" id="customerPanelTabs">
                    <button type="button" class="waw-panel-tab active" data-panel-tab="profile" title="الملف الشخصي"><i class="fas fa-user"></i></button>
                    <button type="button" class="waw-panel-tab" data-panel-tab="summary" title="ملخص"><i class="fas fa-expand"></i><span class="waw-panel-badge d-none" id="customerPanelSummaryBadge">0</span></button>
                    <button type="button" class="waw-panel-tab" data-panel-tab="notes" title="آخر الرسائل"><i class="fas fa-book"></i></button>
                    <button type="button" class="waw-panel-tab" data-panel-tab="tags" title="الوسوم"><i class="fas fa-tag"></i></button>
                    <button type="button" class="waw-panel-tab" data-panel-tab="meta" title="البيانات"><i class="fas fa-tags"></i></button>
                    <button type="button" class="waw-panel-tab" data-panel-tab="actions" title="إجراءات"><i class="fas fa-copy"></i></button>
                    <button type="button" class="waw-panel-tab" data-panel-tab="links" title="روابط"><i class="fas fa-shopping-cart"></i></button>
                    <button type="button" class="waw-panel-tab ms-auto" id="refreshCustomerPanel" title="تحديث"><i class="fas fa-sync-alt"></i></button>
                </div>
                <div class="waw-customer-panel-hd">
                    <div>
                        <div class="waw-customer-panel-title">بيانات المحادثة</div>
                        <div class="waw-customer-panel-sub">عرض سريع لمعلومات العميل والمحادثة</div>
                    </div>
                    <button type="button" class="waw-icon-btn" id="closeCustomerPanel" title="إغلاق">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="waw-customer-panel-body w-full p-0 chat-panel-height overflow-y-auto container-scroll h-100 no-footer" id="customerInfoBody">
                    <div class="waw-panel-view" data-panel-view="profile">
                        <div class="waw-panel-actions-row">
                            <button type="button" class="waw-action-chip" id="customerPanelStatusButton">فتح</button>
                            <button type="button" class="waw-action-chip" id="customerPanelExportButton">تنزيل بيانات المستخدم</button>
                            <button type="button" class="waw-action-chip" id="customerPanelCopyLinkButton">نسخ رابط الدردشة</button>
                        </div>

                        <div class="waw-customer-card">
                            <div class="waw-customer-avatar" id="customerPanelAvatar">؟</div>
                            <div class="waw-customer-title" id="customerPanelName">اختر محادثة</div>
                            <div class="waw-customer-phone" id="customerPanelPhone">-</div>
                        </div>

                        <div class="waw-info-group">
                            <div class="waw-info-row">
                                <span>الحالة</span>
                                <strong id="customerPanelStatus">-</strong>
                            </div>
                            <div class="waw-info-row">
                                <span>الموظف المسؤول</span>
                                <strong id="customerPanelAgent">غير معين</strong>
                            </div>
                            <div class="waw-info-row">
                                <span>آخر تحديث</span>
                                <strong id="customerPanelUpdated">-</strong>
                            </div>
                            <div class="waw-info-row">
                                <span>غير مقروء</span>
                                <strong id="customerPanelUnread">0</strong>
                            </div>
                        </div>

                        <div class="waw-info-group">
                            <div class="waw-info-label">إيقاف تلقائي مؤقتًا</div>
                            <div class="waw-pause-actions">
                                <button type="button" class="waw-pause-btn" data-pause-minutes="30">+ 30 دقائق</button>
                                <button type="button" class="waw-pause-btn" data-pause-minutes="5">+ 5 دقائق</button>
                                <button type="button" class="waw-pause-btn" data-pause-minutes="60">+ 1 ساعة</button>
                                <button type="button" class="waw-pause-btn" data-pause-resume="1">استئناف البوت</button>
                            </div>
                        </div>
                    </div>

                    <div class="waw-panel-view d-none" data-panel-view="summary">
                        <div class="waw-info-group">
                            <div class="waw-info-row"><span>عدد الرسائل</span><strong id="customerPanelMessagesCount">0</strong></div>
                            <div class="waw-info-row"><span>رسائل العميل</span><strong id="customerPanelCustomerCount">0</strong></div>
                            <div class="waw-info-row"><span>رسائل الفريق</span><strong id="customerPanelAgentCount">0</strong></div>
                            <div class="waw-info-row"><span>يمكن الإرسال الآن</span><strong id="customerPanelSendAllowed">-</strong></div>
                        </div>
                    </div>

                    <div class="waw-panel-view d-none" data-panel-view="notes">
                        <div class="waw-info-group">
                            <div class="waw-info-label">آخر رسالة</div>
                            <div class="waw-info-message" id="customerPanelLastMessage">لا توجد رسائل بعد</div>
                        </div>
                        <div class="waw-transcript-list" id="customerPanelTranscript"></div>
                    </div>

                    <div class="waw-panel-view d-none" data-panel-view="tags">
                        <div class="waw-info-group">
                            <div class="waw-info-label">الوسوم / التصنيفات</div>
                            <div class="waw-tag-list" id="customerPanelTags"></div>
                        </div>
                    </div>

                    <div class="waw-panel-view d-none" data-panel-view="meta">
                        <div class="waw-info-group">
                            <div class="waw-info-row"><span>رقم المستخدم</span><strong id="customerPanelUserId">-</strong></div>
                            <div class="waw-info-row"><span>مستخدم Ns</span><strong id="customerPanelUserNs">-</strong></div>
                            <div class="waw-info-row"><span>تم الاشتراك</span><strong id="customerPanelSubscribed">-</strong></div>
                            <div class="waw-info-row"><span>آخر تفاعل</span><strong id="customerPanelInteraction">-</strong></div>
                            <div class="waw-info-row"><span>آخر نوع رسالة</span><strong id="customerPanelLastType">-</strong></div>
                            <div class="waw-info-row"><span>إيقاف البوت</span><strong id="customerPanelPaused">0</strong></div>
                        </div>
                    </div>

                    <div class="waw-panel-view d-none" data-panel-view="actions">
                        <div class="waw-info-group">
                            <div class="waw-panel-actions-stack">
                                <button type="button" class="waw-action-chip w-100" id="customerPanelCopyPhoneButton">نسخ الرقم</button>
                                <button type="button" class="waw-action-chip w-100" id="customerPanelCopyNsButton">نسخ مستخدم Ns</button>
                                <button type="button" class="waw-action-chip w-100" id="customerPanelReloadButton">تحديث البيانات</button>
                            </div>
                        </div>
                    </div>

                    <div class="waw-panel-view d-none" data-panel-view="links">
                        <div class="waw-info-group">
                            <div class="waw-info-label">عنوان URL للدردشة المباشرة</div>
                            <div class="waw-info-message" id="customerPanelLivechatUrl">-</div>
                        </div>
                    </div>
                </div>
            </aside>

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
                                        data-type="{{ $template->type }}"
                                        data-namespace="{{ $template->faalwa_namespace }}"
                                        data-language="{{ $template->language_code ?? 'ar' }}"
                                        data-params-schema='@json($template->params_schema ?? [])'>
                                    {{ $template->name }}
                                </option>
                            @endforeach
                        </select>
                        <div id="templateModeHint" class="small px-1 mt-2 d-none" style="color:var(--waw-text-2);"></div>
                        <div id="templateParamsPanel" class="mt-2 d-none"></div>
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