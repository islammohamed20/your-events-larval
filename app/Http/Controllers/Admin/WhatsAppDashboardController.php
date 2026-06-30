<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\MessageTemplate;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WhatsAppMessage;
use App\Services\FaalwaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class WhatsAppDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->forceFill(['is_online' => true])->save();
        }

        $stats = [
            'total_conversations' => Conversation::count(),
            'total_chats'         => Conversation::count(),
            'open_chats'          => Conversation::where('status', 'open')->count(),
            'pending_chats'       => Conversation::where('status', 'pending')->count(),
            'closed_chats'        => Conversation::where('status', 'closed')->count(),
            'unassigned_chats'    => Conversation::whereNull('assigned_to')->whereNull('assigned_supplier_id')->count(),
            'messages_today'      => WhatsAppMessage::whereDate('created_at', today())->count(),
            'active_agents'       => User::where('is_online', true)->where(function ($query) {
                $query->where('role', 'agent')->orWhere('is_admin', true);
            })->count(),
        ];

        $templates = MessageTemplate::orderBy('type')->orderBy('name')->get();
        $agents = User::where(function ($query) {
            $query->where('role', 'agent')->orWhere('is_admin', true);
        })->orderBy('name')->get(['id', 'name', 'role', 'is_online']);
        $suppliers = Supplier::where('status', 'approved')->orderBy('name')->get(['id', 'name']);

        return view('admin.whatsapp.index', compact('stats', 'templates', 'agents', 'suppliers'));
    }

    public function conversations(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $canViewAllAssigned = (bool) $request->user()?->isAdmin();

        $conversations = Conversation::query()
            ->with('assignedAgent:id,name,is_online', 'assignedSupplier:id,name,status')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(function ($inner) use ($search) {
                    $inner->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhere('last_message', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->forInboxFilter($request->string('filter')->toString(), $userId, $canViewAllAssigned)
            ->orderByDesc('last_message_at')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $conversations->map(fn (Conversation $conversation) => $this->formatConversation($conversation)),
        ]);
    }

    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $query = $conversation->messages()->orderByDesc('id');

        if ($request->filled('since_id')) {
            $query->where('id', '>', (int) $request->input('since_id'));
        } else {
            $query->limit(50);
        }

        $messages = $query->get()->sortBy('id')->values();

        if ($conversation->unread_count > 0) {
            $conversation->forceFill(['unread_count' => 0])->save();
        }

        return response()->json([
            'success' => true,
            'conversation' => $this->formatConversation($conversation->fresh('assignedAgent:id,name,is_online', 'assignedSupplier:id,name,status')),
            'data' => $messages->map(fn (WhatsAppMessage $message) => $this->formatMessage($message)),
        ]);
    }

    public function sendMessage(Request $request, Conversation $conversation, FaalwaService $faalwaService): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'nullable|string',
            'message_type' => 'required|in:text,template',
            'template_id' => 'nullable|exists:message_templates,id',
            'template_params' => 'nullable|array',
            'template_params.*' => 'nullable|string|max:1000',
        ]);

        $authId = Auth::id();
        $conversation->refresh();

        if ($authId && $conversation->assigned_to && $conversation->assigned_to !== $authId) {
            return response()->json([
                'success' => false,
                'message' => 'تم استلام هذه المحادثة بواسطة موظف آخر.',
                'conversation' => $this->formatConversation($conversation->fresh('assignedAgent:id,name,is_online', 'assignedSupplier:id,name,status')),
            ], 409);
        }

        if ($authId && ! $conversation->assigned_to && ! $conversation->assigned_supplier_id) {
            $claimed = Conversation::query()
                ->whereKey($conversation->id)
                ->whereNull('assigned_to')
                ->whereNull('assigned_supplier_id')
                ->update(['assigned_to' => $authId]);

            if ($claimed === 0) {
                $conversation->refresh();

                return response()->json([
                    'success' => false,
                    'message' => 'تم استلام هذه المحادثة بواسطة موظف آخر.',
                    'conversation' => $this->formatConversation($conversation->fresh('assignedAgent:id,name,is_online', 'assignedSupplier:id,name,status')),
                ], 409);
            }

            $conversation->assigned_to = $authId;
        }

        $messageType = $validated['message_type'];
        $template = null;
        $content = trim((string) ($validated['message'] ?? ''));
        $resolvedUserNs = null;
        $templateParams = collect($validated['template_params'] ?? [])
            ->map(fn ($value) => trim((string) $value))
            ->values()
            ->all();

        if ($messageType === 'template') {
            $template = MessageTemplate::findOrFail($validated['template_id']);
            $content = $content !== '' ? $content : $this->renderTemplatePreview($template->content, $templateParams);

            if ($template->faalwa_namespace && is_array($template->params_schema) && count($template->params_schema) > 0) {
                $missingParam = collect($templateParams)
                    ->take(count($template->params_schema))
                    ->contains(fn ($value) => $value === '');

                if ($missingParam || count($templateParams) < count($template->params_schema)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'يرجى تعبئة جميع متغيرات القالب قبل الإرسال.',
                    ], 422);
                }
            }
        }

        if ($content === '') {
            return response()->json(['success' => false, 'message' => 'Message content is required.'], 422);
        }

        $message = WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'agent',
            'message' => $content,
            'message_type' => $messageType,
            'status' => 'sent',
            'created_at' => now(),
        ]);

        try {
            $resolvedUserNs = $faalwaService->resolveUserNs($conversation->customer_phone);
            $this->syncClaimToFaalwa($conversation, $resolvedUserNs, $authId, $faalwaService);

            $result = ($messageType === 'template' && $template && $template->faalwa_namespace)
                ? $faalwaService->sendTemplateMessage($conversation->customer_phone, [
                    'namespace' => $template->faalwa_namespace,
                    'name' => $template->name,
                    'lang' => $template->language_code ?: 'ar',
                    'params' => $templateParams,
                ])
                : $faalwaService->sendTextMessage($conversation->customer_phone, $content);

            $message->update([
                'status' => $result['status'] ?? 'sent',
                'external_id' => $result['external_id'] ?? null,
                'message_type' => ($messageType === 'template' && $template && $template->faalwa_namespace) ? 'template' : 'text',
            ]);
        } catch (Throwable $throwable) {
            $message->update(['status' => 'failed']);

            return response()->json([
                'success' => false,
                'message' => $throwable->getMessage(),
                'data' => $this->formatMessage($message->fresh()),
            ], 500);
        }

        $conversation->update([
            'assigned_to' => $conversation->assigned_to,
            'last_message' => $content,
            'last_message_at' => now(),
            'status' => $conversation->status === 'closed' ? 'pending' : $conversation->status,
        ]);

        return response()->json([
            'success' => true,
            'conversation' => $this->formatConversation($conversation->fresh('assignedAgent:id,name,is_online', 'assignedSupplier:id,name,status')),
            'data' => $this->formatMessage($message->fresh()),
        ]);
    }

    public function assignConversation(Request $request, Conversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $assignedTo = $validated['assigned_to'] ?? null;
        $assignedSupplierId = $validated['assigned_supplier_id'] ?? null;

        if ($assignedSupplierId) {
            Supplier::where('id', $assignedSupplierId)
                ->where('status', 'approved')
                ->firstOrFail();
            $assignedTo = null;
        }

        if ($assignedTo) {
            $assignedSupplierId = null;
        }

        $conversation->update([
            'assigned_to' => $assignedTo,
            'assigned_supplier_id' => $assignedSupplierId,
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->formatConversation($conversation->fresh('assignedAgent:id,name,is_online', 'assignedSupplier:id,name,status')),
        ]);
    }

    public function updateStatus(Request $request, Conversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:open,pending,closed',
        ]);

        $conversation->update(['status' => $validated['status']]);

        try {
            $faalwaService = app(FaalwaService::class);
            $userNs = $faalwaService->resolveUserNs($conversation->customer_phone);

            if ($validated['status'] === 'closed') {
                $faalwaService->moveChatTo($userNs, 'closed');
                $faalwaService->resumeBot($userNs);
            } else {
                $faalwaService->moveChatTo($userNs, $validated['status']);
                $this->syncClaimToFaalwa($conversation->fresh(), $userNs, Auth::id(), $faalwaService);
            }
        } catch (Throwable $throwable) {
            report($throwable);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatConversation($conversation->fresh('assignedAgent:id,name,is_online', 'assignedSupplier:id,name,status')),
        ]);
    }

    public function poll(Request $request): JsonResponse
    {
        $payload = [
            'success' => true,
            'conversations' => $this->conversations($request)->getData(true)['data'] ?? [],
        ];

        if ($request->filled('conversation_id')) {
            $conversation = Conversation::find($request->integer('conversation_id'));
            if ($conversation) {
                $payload['messages'] = $this->messages($request, $conversation)->getData(true)['data'] ?? [];
            }
        }

        return response()->json($payload);
    }

    public function panel(Conversation $conversation, FaalwaService $faalwaService): JsonResponse
    {
        $messages = $conversation->messages()->latest('id')->limit(20)->get()->reverse()->values();
        $subscriber = [];
        $livechatUrl = null;

        try {
            $subscriber = $faalwaService->getSubscriberByPhone($conversation->customer_phone);
            $userNs = data_get($subscriber, 'user_ns') ?? data_get($subscriber, 'data.user_ns');
            if ($userNs) {
                $flowId = strstr((string) config('services.faalwa.base_url', ''), 'chat.faal-wa.sa') !== false ? 'f261493' : null;
                if ($flowId) {
                    $livechatUrl = 'https://chat.faal-wa.sa/inbox/'.$userNs;
                }
            }
        } catch (Throwable $throwable) {
            report($throwable);
        }

        return response()->json([
            'success' => true,
            'conversation' => $this->formatConversation($conversation->fresh('assignedAgent:id,name,is_online', 'assignedSupplier:id,name,status')),
            'subscriber' => [
                'user_ns' => data_get($subscriber, 'user_ns') ?? data_get($subscriber, 'data.user_ns'),
                'user_id' => data_get($subscriber, 'user_id') ?? data_get($subscriber, 'data.user_id'),
                'name' => data_get($subscriber, 'name') ?? data_get($subscriber, 'data.name'),
                'phone' => data_get($subscriber, 'phone') ?? data_get($subscriber, 'data.phone'),
                'status' => data_get($subscriber, 'status') ?? data_get($subscriber, 'data.status'),
                'allow_send_message' => (bool) (data_get($subscriber, 'allow_send_message') ?? data_get($subscriber, 'data.allow_send_message')),
                'paused_diff_seconds' => (int) (data_get($subscriber, 'paused_diff_seconds') ?? data_get($subscriber, 'data.paused_diff_seconds') ?? 0),
                'subscribed' => data_get($subscriber, 'subscribed') ?? data_get($subscriber, 'data.subscribed'),
                'last_interaction' => data_get($subscriber, 'last_interaction') ?? data_get($subscriber, 'data.last_interaction'),
                'last_message_at' => data_get($subscriber, 'last_message_at') ?? data_get($subscriber, 'data.last_message_at'),
                'last_message_type' => data_get($subscriber, 'last_message_type') ?? data_get($subscriber, 'data.last_message_type'),
                'labels' => data_get($subscriber, 'labels') ?? data_get($subscriber, 'data.labels') ?? [],
                'tags' => data_get($subscriber, 'tags') ?? data_get($subscriber, 'data.tags') ?? [],
                'livechat_url' => $livechatUrl,
            ],
            'stats' => [
                'messages_count' => $conversation->messages()->count(),
                'agent_messages_count' => $conversation->messages()->where('sender_type', 'agent')->count(),
                'customer_messages_count' => $conversation->messages()->where('sender_type', 'customer')->count(),
            ],
            'messages' => $messages->map(fn (WhatsAppMessage $message) => $this->formatMessage($message)),
        ]);
    }

    public function pauseBot(Request $request, Conversation $conversation, FaalwaService $faalwaService): JsonResponse
    {
        $validated = $request->validate([
            'minutes' => 'nullable|integer|min:0|max:525600',
            'resume' => 'nullable|boolean',
        ]);

        $userNs = $faalwaService->resolveUserNs($conversation->customer_phone);

        if ((bool) ($validated['resume'] ?? false)) {
            $faalwaService->resumeBot($userNs);

            return response()->json(['success' => true, 'message' => 'تم استئناف البوت.']);
        }

        $minutes = (int) ($validated['minutes'] ?? 30);
        $faalwaService->pauseBot($userNs, $minutes);

        return response()->json(['success' => true, 'message' => 'تم إيقاف البوت مؤقتًا.']);
    }

    protected function formatConversation(Conversation $conversation): array
    {
        $lastMessage = $conversation->messages()->latest('id')->first();
        return [
            'id' => $conversation->id,
            'customer_name' => $conversation->customer_name ?: $conversation->customer_phone,
            'customer_phone' => $conversation->customer_phone,
            'status' => $conversation->status,
            'assigned_to' => $conversation->assigned_to,
            'assigned_supplier_id' => $conversation->assigned_supplier_id,
            'assigned_agent' => $conversation->assignedAgent?->name ?: $conversation->assignedSupplier?->name,
            'assigned_agent_online' => (bool) $conversation->assignedAgent?->is_online,
            'assigned_supplier' => $conversation->assignedSupplier?->name,
            'assignment_type' => $conversation->assigned_supplier_id ? 'supplier' : ($conversation->assigned_to ? 'agent' : null),
            'assigned_target_value' => $conversation->assigned_supplier_id
                ? 'supplier:'.$conversation->assigned_supplier_id
                : ($conversation->assigned_to ? 'user:'.$conversation->assigned_to : ''),
            'last_message' => $conversation->last_message,
            'last_message_at' => optional($conversation->last_message_at)->format('H:i'),
            'last_message_at_iso' => optional($conversation->last_message_at)?->toIso8601String(),
            'unread_count' => $conversation->unread_count,
            'last_sender_type' => $lastMessage?->sender_type ?? 'customer',
        ];
    }

    protected function formatMessage(WhatsAppMessage $message): array
    {
        return [
            'id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'sender_type' => $message->sender_type,
            'message' => $message->message,
            'message_type' => $message->message_type,
            'status' => $message->status,
            'external_id' => $message->external_id,
            'created_at' => optional($message->created_at)->format('H:i'),
            'created_at_iso' => optional($message->created_at)?->toIso8601String(),
        ];
    }

    public function startConversation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone'   => ['required', 'string', 'regex:/^[0-9]{7,15}$/'],
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $phone   = $validated['phone'];
        $message = $validated['message'];

        // Find or create conversation
        $conversation = Conversation::firstOrCreate(
            ['customer_phone' => $phone],
            [
                'customer_name' => $phone,
                'status'        => 'open',
                'assigned_to'   => Auth::id(),
                'unread_count'  => 0,
            ]
        );

        try {
            /** @var FaalwaService $faalwa */
            $faalwa = app(FaalwaService::class);
            $result = $faalwa->sendTextMessage($phone, $message);

            $status = $result['status'] ?? 'sent';

            $msg = $conversation->messages()->create([
                'sender_type'  => 'agent',
                'sender_id'    => Auth::id(),
                'message'      => $message,
                'message_type' => 'text',
                'status'       => $status,
                'external_id'  => $result['external_id'] ?? null,
            ]);

            $conversation->update([
                'last_message'    => $message,
                'last_message_at' => now(),
            ]);

            return response()->json([
                'success'         => true,
                'conversation_id' => $conversation->id,
                'data'            => $this->formatMessage($msg),
            ]);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    protected function syncClaimToFaalwa(Conversation $conversation, string $userNs, ?int $authId, FaalwaService $faalwaService): void
    {
        $faalwaService->pauseBot($userNs);

        if (! $authId) {
            return;
        }

        $agent = User::query()->find($authId);
        if (! $agent?->faalwa_agent_id) {
            return;
        }

        $faalwaService->assignAgent($userNs, (int) $agent->faalwa_agent_id);
    }

    protected function renderTemplatePreview(string $content, array $params): string
    {
        $index = 0;

        return preg_replace_callback('/{{\s*[^}]+\s*}}/', function () use ($params, &$index) {
            $value = $params[$index] ?? '';
            $index++;

            return $value !== '' ? $value : '_____';
        }, $content) ?? $content;
    }
}