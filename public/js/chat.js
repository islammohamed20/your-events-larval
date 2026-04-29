/* ==============================================
   WAW Chat JS – Premium WhatsApp Web Clone
   ============================================== */
'use strict';

document.addEventListener('DOMContentLoaded', function () {

    /* ── DOM refs ── */
    const root               = document.getElementById('whatsappDashboard');
    if (!root) return;

    const csrfToken          = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const conversationList   = document.getElementById('conversationList');
    const searchInput        = document.getElementById('conversationSearch');
    const filterInput        = document.getElementById('conversationFilter');
    const statusFilterInput  = document.getElementById('statusFilter');
    const filterTabs         = document.querySelectorAll('.waw-sb-item[data-filter-tab]');
    const statusTabs         = document.querySelectorAll('.waw-sb-item[data-status-tab]');
    const unansweredToggle   = document.getElementById('unansweredFilter');
    const messageList        = document.getElementById('messageList');
    const messageInput       = document.getElementById('messageInput');
    const sendButton         = document.getElementById('sendMessageButton');
    const chatForm           = document.getElementById('chatForm');
    const templateSelect     = document.getElementById('templateSelect');
    const messageTypeInput   = document.getElementById('messageType');
    const templateIdInput    = document.getElementById('selectedTemplateId');
    const customerNameEl     = document.getElementById('chatCustomerName');
    const customerMetaEl     = document.getElementById('chatCustomerMeta');
    const chatHeaderAvatar   = document.getElementById('chatHeaderAvatar');
    const contactAvatar      = document.getElementById('contactAvatar');
    const wawIntro           = document.getElementById('wawIntro');
    const wawChat            = document.getElementById('wawChat');
    const agentSelect        = document.getElementById('conversationAgent');
    const conversationStatus = document.getElementById('conversationStatus');
    const pollStatusDot      = document.getElementById('pollStatusDot');
    const newChatForm        = document.getElementById('newChatForm');
    const newChatPhone       = document.getElementById('newChatPhone');
    const newChatMessage     = document.getElementById('newChatMessage');
    const newChatError       = document.getElementById('newChatError');
    const btnReopenConv      = document.getElementById('btnReopenConv');
    const refreshSpinIcon    = document.getElementById('refreshSpinIcon');
    const themeToggleBtn     = document.getElementById('themeToggleBtn');
    const themeIcon          = document.getElementById('themeIcon');
    const wawLeft            = document.getElementById('wawLeft');
    const btnBack            = document.getElementById('btnBack');

    /* ── State ── */
    const state = {
        conversations: [],
        selectedConversationId: null,
        lastMessageId: 0,
        pollTimer: null,
    };

    /* ── API endpoints ── */
    const api = {
        conversations: root.dataset.conversationsUrl,
        poll:          root.dataset.pollUrl,
        messages:      root.dataset.messageUrlTemplate,
        send:          root.dataset.sendUrlTemplate,
        assign:        root.dataset.assignUrlTemplate,
        status:        root.dataset.statusUrlTemplate,
        start:         root.dataset.startUrl,
    };

    const baseHeaders = {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
    };
    const jsonHeaders = { ...baseHeaders, 'Content-Type': 'application/json' };

    /* ── Helpers ── */
    function buildUrl(tpl, id) { return tpl.replace('__ID__', String(id)); }

    function escapeHtml(v) {
        return String(v ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function initials(name) { return String(name || '?').trim().charAt(0).toUpperCase(); }

    function formatTime(ts) {
        if (!ts) return '';
        const d = new Date(ts);
        if (isNaN(d)) return ts;
        const now = new Date();
        if (d.toDateString() === now.toDateString()) {
            return d.toLocaleTimeString('ar', { hour: '2-digit', minute: '2-digit' });
        }
        const diff = Math.floor((now - d) / 86400000);
        if (diff < 7) return d.toLocaleDateString('ar', { weekday: 'short' });
        return d.toLocaleDateString('ar', { day: 'numeric', month: 'short' });
    }

    function statusLabel(s) { return { open: 'مفتوحة', pending: 'معلقة', closed: 'مغلقة' }[s] || s; }

    function tickIcon(status) {
        if (status === 'read')      return '<span class="msg-tick read" title="مقروءة">&#10003;&#10003;</span>';
        if (status === 'delivered') return '<span class="msg-tick sent" title="تم التوصيل">&#10003;&#10003;</span>';
        if (status === 'sent')      return '<span class="msg-tick sent" title="مرسلة">&#10003;</span>';
        if (status === 'pending')   return '<span class="msg-tick" title="جار الإرسال">&#8987;</span>';
        if (status === 'failed')    return '<span class="msg-tick" style="color:#f15c6d;" title="فشل">&#10005;</span>';
        return '';
    }

    function setPollDot(online) {
        if (!pollStatusDot) return;
        pollStatusDot.classList.toggle('offline', !online);
        pollStatusDot.title = online ? 'المزامنة تعمل' : 'انقطع الاتصال';
    }

    function syncStatusTabs() {
        statusTabs.forEach(tab => {
            const t = tab.getAttribute('data-status-tab') || '';
            tab.classList.toggle('active', t === (statusFilterInput.value || ''));
        });
        filterTabs.forEach(tab => {
            const t = tab.getAttribute('data-filter-tab') || 'all';
            tab.classList.toggle('active', t === (filterInput.value || 'all'));
        });
    }

    function autoResize(el) {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 120) + 'px';
    }

    function debounce(fn, ms) {
        let t;
        return function () { clearTimeout(t); t = setTimeout(() => fn.apply(this, arguments), ms || 300); };
    }

    /* ── Avatar color ── */
    function avatarColor(name) {
        const colors = [
            'linear-gradient(135deg,#00a884,#005c4b)',
            'linear-gradient(135deg,#7b68ee,#4a3ab5)',
            'linear-gradient(135deg,#ff6b6b,#c0392b)',
            'linear-gradient(135deg,#f39c12,#e67e22)',
            'linear-gradient(135deg,#3498db,#1a5276)',
            'linear-gradient(135deg,#e91e63,#880e4f)',
        ];
        let hash = 0;
        for (let i = 0; i < (name || '').length; i++) hash += name.charCodeAt(i);
        return colors[hash % colors.length];
    }

    /* ── Mobile: back button ── */
    if (btnBack) {
        btnBack.addEventListener('click', () => {
            wawLeft && wawLeft.classList.remove('mobile-hidden');
        });
    }

    /* ── Theme Toggle ── */
    const savedTheme = localStorage.getItem('waw-theme') || 'dark';
    if (savedTheme === 'light') {
        document.body.setAttribute('data-waw-theme', 'light');
        if (themeIcon) themeIcon.classList.replace('fa-moon', 'fa-sun');
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const isLight = document.body.getAttribute('data-waw-theme') === 'light';
            if (isLight) {
                document.body.removeAttribute('data-waw-theme');
                localStorage.setItem('waw-theme', 'dark');
                if (themeIcon) themeIcon.classList.replace('fa-sun', 'fa-moon');
            } else {
                document.body.setAttribute('data-waw-theme', 'light');
                localStorage.setItem('waw-theme', 'light');
                if (themeIcon) themeIcon.classList.replace('fa-moon', 'fa-sun');
            }
        });
    }

    /* ── Render Conversations ── */
    function renderConversations() {
        if (!state.conversations.length) {
            conversationList.innerHTML = '<div class="waw-empty"><i class="fas fa-comment-slash"></i><span>لا توجد محادثات مطابقة</span></div>';
            return;
        }

        conversationList.innerHTML = state.conversations.map(c => {
            const active    = c.id === state.selectedConversationId ? 'active' : '';
            const hasUnread = c.unread_count > 0;
            const unreadBadge = hasUnread ? `<span class="waw-unread-badge">${c.unread_count}</span>` : '';
            const timeClass = hasUnread ? 'unread-time' : '';
            const avatarCls = hasUnread ? 'waw-conv-avatar unread' : 'waw-conv-avatar';
            const tickHtml  = c.last_sender_type === 'agent' ? '<span class="conv-tick">&#10003;&#10003;</span>' : '';
            const timeStr   = escapeHtml(formatTime(c.last_message_at_iso || c.last_message_at));

            return `
<button type="button" class="waw-conv-item mb-1 ${active}" data-conversation-id="${c.id}">
    <div class="waw-conv-avatar-wrap">
        <div class="${avatarCls}" style="background:${avatarColor(c.customer_name)}">
            ${escapeHtml(initials(c.customer_name))}
        </div>
        <span class="waw-wa-dot"><i class="fab fa-whatsapp"></i></span>
    </div>
    <div class="waw-conv-body">
        <div class="waw-conv-row1">
            <span class="waw-conv-name">${escapeHtml(c.customer_name)}</span>
            <span class="waw-conv-time ${timeClass}">${timeStr}</span>
        </div>
        <div class="waw-conv-row2">
            ${tickHtml}
            <span class="waw-conv-preview">${escapeHtml(c.last_message || 'لا توجد رسائل بعد')}</span>
            ${unreadBadge}
        </div>
        <div style="margin-top:3px;">
            <span class="waw-status-pill ${c.status}">${statusLabel(c.status)}</span>
            ${c.assigned_agent ? `<span style="font-size:.65rem;color:var(--waw-text-2);margin-right:4px;">👤 ${escapeHtml(c.assigned_agent)}</span>` : ''}
        </div>
    </div>
</button>`;
        }).join('');

        conversationList.querySelectorAll('.waw-conv-item').forEach(btn => {
            btn.addEventListener('click', () => selectConversation(Number(btn.dataset.conversationId)));
        });
    }

    /* ── Render Messages ── */
    function renderMessages(messages, append = false) {
        const isEmpty = messageList.querySelector('.waw-empty-chat');
        if (isEmpty) isEmpty.remove();

        if (!append) {
            messageList.innerHTML = '';
            state.lastMessageId = 0;
        }

        let lastDate = null;

        messages.forEach(msg => {
            if (msg.id > state.lastMessageId) state.lastMessageId = msg.id;

            // Date separator
            const msgDate = new Date(msg.created_at_iso || msg.created_at);
            const dateStr = isNaN(msgDate) ? '' : msgDate.toLocaleDateString('ar', { day: 'numeric', month: 'long', year: 'numeric' });
            if (dateStr && dateStr !== lastDate) {
                lastDate = dateStr;
                const sep = document.createElement('div');
                sep.className = 'waw-date-sep';
                sep.innerHTML = `<span>${dateStr}</span>`;
                messageList.appendChild(sep);
            }

            const isOut   = msg.sender_type === 'agent';
            const tplBadge = msg.message_type === 'template' ? '<span class="waw-tpl-badge">قالب</span>' : '';
            const timeDisp = (() => {
                const d = new Date(msg.created_at_iso || msg.created_at);
                return isNaN(d) ? (msg.created_at || '') : d.toLocaleTimeString('ar', { hour: '2-digit', minute: '2-digit' });
            })();

            const wrap = document.createElement('div');
            wrap.className = `waw-msg-wrap ${isOut ? 'out' : 'in'}`;
            if (msg.id) wrap.dataset.messageId = msg.id;

            const statusClass = ['pending', 'failed'].includes(msg.status) ? msg.status : '';
            wrap.innerHTML = `
<div class="waw-bubble ${isOut ? 'out' : 'in'} ${statusClass}">
    ${tplBadge}
    ${escapeHtml(msg.message).replace(/\n/g, '<br>')}
</div>
<div class="waw-msg-meta">
    <span>${timeDisp}</span>
    ${isOut ? tickIcon(msg.status) : ''}
</div>`;
            messageList.appendChild(wrap);
        });

        messageList.scrollTop = messageList.scrollHeight;
    }

    /* ── Set composer enabled ── */
    function setComposerEnabled(enabled) {
        messageInput.disabled  = !enabled;
        sendButton.disabled    = !enabled;
        agentSelect.disabled   = !enabled;
        if (btnReopenConv) btnReopenConv.disabled = !enabled;
        if (enabled) messageInput.focus();
    }

    /* ── Update chat header ── */
    function updateHeader(conv) {
        if (!conv) return;
        customerNameEl.textContent = conv.customer_name || conv.customer_phone;
        customerMetaEl.textContent = conv.customer_phone;
        const initChar = initials(conv.customer_name || conv.customer_phone);
        chatHeaderAvatar.textContent = initChar;
        chatHeaderAvatar.style.background = avatarColor(conv.customer_name);
        contactAvatar.textContent = initChar;

        if (agentSelect) agentSelect.value = conv.assigned_to || '';
        if (conversationStatus) conversationStatus.value = conv.status || 'open';

        if (btnReopenConv) {
            const isClosed = conv.status === 'closed';
            btnReopenConv.textContent = isClosed ? 'إعادة فتح' : 'إغلاق';
            btnReopenConv.className   = 'waw-status-btn' + (isClosed ? '' : ' is-open');
        }
    }

    /* ── Select conversation ── */
    async function selectConversation(id) {
        state.selectedConversationId = id;

        // Mobile: hide sidebar
        if (window.innerWidth <= 768 && wawLeft) {
            wawLeft.classList.add('mobile-hidden');
        }

        wawIntro && wawIntro.classList.add('d-none');
        wawChat  && wawChat.classList.remove('d-none');

        messageList.innerHTML = '<div class="waw-loading"><i class="fas fa-spinner fa-spin"></i><span>جارٍ تحميل الرسائل...</span></div>';
        setComposerEnabled(false);

        renderConversations();

        try {
            const res = await fetch(buildUrl(api.messages, id) + `?since_id=0`, { headers: baseHeaders });
            const data = await res.json();
            messageList.innerHTML = '';
            if (data.success) {
                updateHeader(data.conversation);
                if (data.data && data.data.length) {
                    renderMessages(data.data, false);
                } else {
                    messageList.innerHTML = '<div class="waw-empty-chat"><i class="fab fa-whatsapp"></i><span>لا توجد رسائل بعد</span></div>';
                }
            }
        } catch (e) {
            messageList.innerHTML = '<div class="waw-empty"><i class="fas fa-exclamation-triangle"></i><span>فشل تحميل الرسائل</span></div>';
        }

        setComposerEnabled(true);
    }

    /* ── Load conversations ── */
    async function loadConversations() {
        const params = new URLSearchParams({
            search: searchInput.value.trim(),
            filter: filterInput.value,
            status: statusFilterInput.value,
        });
        if (unansweredToggle && unansweredToggle.checked) {
            params.append('unanswered', '1');
        }
        try {
            const res  = await fetch(api.conversations + '?' + params, { headers: baseHeaders });
            const data = await res.json();
            state.conversations = data.data || [];
            renderConversations();
            setPollDot(true);
        } catch {
            setPollDot(false);
        }
    }

    /* ── Send message ── */
    async function sendMessage(e) {
        e.preventDefault();
        if (!state.selectedConversationId) return;

        const content = messageInput.value.trim();
        if (!content) return;

        const tempId = 'tmp_' + Date.now();
        const tempMsg = {
            id: tempId, sender_type: 'agent',
            message: content, message_type: messageTypeInput.value,
            status: 'pending',
            created_at: new Date().toLocaleTimeString('ar', { hour: '2-digit', minute: '2-digit' }),
            created_at_iso: new Date().toISOString(),
        };
        renderMessages([tempMsg], true);

        messageInput.value = '';
        messageInput.style.height = 'auto';
        sendButton.disabled = true;

        try {
            const res    = await fetch(buildUrl(api.send, state.selectedConversationId), {
                method: 'POST', headers: jsonHeaders,
                body: JSON.stringify({
                    message:      content,
                    message_type: messageTypeInput.value,
                    template_id:  templateIdInput.value || '',
                }),
            });
            const result = await res.json();
            const tempNode = messageList.querySelector(`[data-message-id="${tempId}"]`);

            if (!res.ok || !result.success) {
                if (tempNode) {
                    tempNode.querySelector('.waw-bubble')?.classList.add('failed');
                    tempNode.querySelector('.waw-msg-meta').innerHTML = '<span style="color:#f15c6d;">فشل الإرسال &#10005;</span>';
                }
                return;
            }
            if (tempNode) tempNode.remove();
            renderMessages([result.data], true);
            messageTypeInput.value = 'text';
            templateIdInput.value  = '';
            templateSelect.value   = '';
            await loadConversations();
        } finally {
            setComposerEnabled(!!state.selectedConversationId);
        }
    }

    /* ── Assignment / Status ── */
    async function updateAssignment() {
        if (!state.selectedConversationId) return;
        await fetch(buildUrl(api.assign, state.selectedConversationId), {
            method: 'POST', headers: jsonHeaders,
            body: JSON.stringify({ assigned_to: agentSelect.value || null }),
        });
        await loadConversations();
    }

    async function updateConversationStatus() {
        if (!state.selectedConversationId) return;
        await fetch(buildUrl(api.status, state.selectedConversationId), {
            method: 'POST', headers: jsonHeaders,
            body: JSON.stringify({ status: conversationStatus.value }),
        });
        await loadConversations();
    }

    /* ── New Chat Modal ── */
    if (newChatForm && api.start) {
        newChatForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            newChatError.classList.add('d-none');
            newChatSubmit.disabled = true;
            newChatSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>جارٍ...';
            try {
                const res    = await fetch(api.start, {
                    method: 'POST', headers: jsonHeaders,
                    body: JSON.stringify({ phone: newChatPhone.value.trim(), message: newChatMessage.value.trim() }),
                });
                const result = await res.json();
                if (!res.ok || !result.success) {
                    newChatError.textContent = result.message || 'حدث خطأ أثناء الإرسال.';
                    newChatError.classList.remove('d-none');
                    return;
                }
                bootstrap.Modal.getInstance(document.getElementById('newChatModal'))?.hide();
                newChatForm.reset();
                await loadConversations();
                if (result.conversation_id) selectConversation(result.conversation_id);
            } catch {
                newChatError.textContent = 'تعذّر الاتصال بالخادم.';
                newChatError.classList.remove('d-none');
            } finally {
                newChatSubmit.disabled = false;
                newChatSubmit.innerHTML = '<i class="fas fa-paper-plane me-1"></i>إرسال';
            }
        });
    }

    /* ── Poll ── */
    async function poll() {
        const params = new URLSearchParams({
            search: searchInput.value.trim(),
            filter: filterInput.value,
            status: statusFilterInput.value,
        });
        if (unansweredToggle && unansweredToggle.checked) {
            params.append('unanswered', '1');
        }
        if (state.selectedConversationId) {
            params.append('conversation_id', String(state.selectedConversationId));
            params.append('since_id', String(state.lastMessageId));
        }
        try {
            const res     = await fetch(api.poll + '?' + params, { headers: baseHeaders });
            const payload = await res.json();
            state.conversations = payload.conversations || [];
            renderConversations();
            const selected = state.conversations.find(c => c.id === state.selectedConversationId) || null;
            if (selected) updateHeader(selected);
            if (Array.isArray(payload.messages) && payload.messages.length) {
                renderMessages(payload.messages, true);
            }
            setPollDot(true);
        } catch {
            setPollDot(false);
        }
    }

    /* ── Event Listeners ── */
    messageInput.addEventListener('input', () => autoResize(messageInput));
    messageInput.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
        }
    });

    searchInput.addEventListener('input', debounce(loadConversations, 350));
    filterInput.addEventListener('change', loadConversations);
    statusFilterInput.addEventListener('change', loadConversations);

    statusTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            statusFilterInput.value = tab.getAttribute('data-status-tab') || '';
            syncStatusTabs();
            loadConversations();
        });
    });

    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            filterInput.value = tab.getAttribute('data-filter-tab') || 'all';
            syncStatusTabs();
            loadConversations();
        });
    });

    if (unansweredToggle) {
        unansweredToggle.addEventListener('change', loadConversations);
    }

    if (refreshSpinIcon) {
        refreshSpinIcon.addEventListener('click', () => {
            const icon = refreshSpinIcon.querySelector('i');
            icon && icon.classList.add('fa-spin');
            loadConversations().finally(() => {
                setTimeout(() => icon && icon.classList.remove('fa-spin'), 600);
            });
        });
    }

    if (btnReopenConv) {
        btnReopenConv.addEventListener('click', () => {
            if (!state.selectedConversationId) return;
            conversationStatus.value = conversationStatus.value === 'closed' ? 'open' : 'closed';
            updateConversationStatus();
        });
    }

    chatForm.addEventListener('submit', sendMessage);
    agentSelect.addEventListener('change', updateAssignment);
    conversationStatus.addEventListener('change', updateConversationStatus);

    templateSelect.addEventListener('change', function () {
        const opt = templateSelect.selectedOptions[0];
        if (!opt || !opt.value) {
            messageTypeInput.value = 'text';
            templateIdInput.value  = '';
            return;
        }
        messageTypeInput.value = 'template';
        templateIdInput.value  = opt.value;
        messageInput.value     = opt.dataset.content || '';
        autoResize(messageInput);
        messageInput.focus();
    });

    /* ── Init ── */
    loadConversations().then(() => {
        syncStatusTabs();
        if (state.selectedConversationId) selectConversation(state.selectedConversationId);
    });

    state.pollTimer = setInterval(poll, 3000);
});
