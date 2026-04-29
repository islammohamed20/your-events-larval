<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\MessageTemplate;
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
            'unassigned_chats'    => Conversation::whereNull('assigned_to')->count(),
            'messages_today'      => WhatsAppMessage::whereDate('created_at', today())->count(),
            'active_agents'       => User::where('is_online', true)->where(function ($query) {
                $query->where('role', 'agent')->orWhere('is_admin', true);
            })->count(),
        ];

        $templates = MessageTemplate::orderBy('type')->orderBy('name')->get();
        $agents = User::where(function ($query) {
            $query->where('role', 'agent')->orWhere('is_admin', true);
        })->orderBy('name')->get(['id', 'name', 'role', 'is_online']);

        return view('admin.whatsapp.index', compact('stats', 'templates', 'agents'));
    }

    public function conversations(Request $request): JsonResponse
    {
        $userId = Auth::id();

        $conversations = Conversation::query()
            ->with('assignedAgent:id,name,is_online')
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
            ->forInboxFilter($request->string('filter')->toString(), $userId)
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
            'conversation' => $this->formatConversation($conversation->fresh('assignedAgent:id,name,is_online')),
            'data' => $messages->map(fn (WhatsAppMessage $message) => $this->formatMessage($message)),
        ]);
    }

    public function sendMessage(Request $request, Conversation $conversation, FaalwaService $faalwaService): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'nullable|string',
            'message_type' => 'required|in:text,template',
            'template_id' => 'nullable|exists:message_templates,id',
        ]);

        $messageType = $validated['message_type'];
        $template = null;
        $content = trim((string) ($validated['message'] ?? ''));

        if ($messageType === 'template') {
            $template = MessageTemplate::findOrFail($validated['template_id']);
            $content = $content !== '' ? $content : $template->content;
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
            $result = $messageType === 'template'
                ? $faalwaService->sendTemplateMessage($conversation->customer_phone, [
                    'name' => $template?->name,
                    'type' => $template?->type,
                    'body' => $content,
                ])
                : $faalwaService->sendTextMessage($conversation->customer_phone, $content);

            $message->update([
                'status' => $result['status'] ?? 'sent',
                'external_id' => $result['external_id'] ?? null,
            ]);
        } catch (Throwable $throwable) {
            $message->update(['status' => 'failed']);

            return response()->json([
                'success' => false,
                'message' => $throwable->getMessage(),
                'data' => $this->formatMessage($message->fresh()),
            ], 500);
        }

        if (! $conversation->assigned_to && Auth::id()) {
            $conversation->assigned_to = Auth::id();
        }

        $conversation->update([
            'assigned_to' => $conversation->assigned_to,
            'last_message' => $content,
            'last_message_at' => now(),
            'status' => $conversation->status === 'closed' ? 'pending' : $conversation->status,
        ]);

        return response()->json([
            'success' => true,
            'conversation' => $this->formatConversation($conversation->fresh('assignedAgent:id,name,is_online')),
            'data' => $this->formatMessage($message->fresh()),
        ]);
    }

    public function assignConversation(Request $request, Conversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $conversation->update([
            'assigned_to' => $validated['assigned_to'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->formatConversation($conversation->fresh('assignedAgent:id,name,is_online')),
        ]);
    }

    public function updateStatus(Request $request, Conversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:open,pending,closed',
        ]);

        $conversation->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'data' => $this->formatConversation($conversation->fresh('assignedAgent:id,name,is_online')),
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

    protected function formatConversation(Conversation $conversation): array
    {
        return [
            'id' => $conversation->id,
            'customer_name' => $conversation->customer_name ?: $conversation->customer_phone,
            'customer_phone' => $conversation->customer_phone,
            'status' => $conversation->status,
            'assigned_to' => $conversation->assigned_to,
            'assigned_agent' => $conversation->assignedAgent?->name,
            'assigned_agent_online' => (bool) $conversation->assignedAgent?->is_online,
            'last_message' => $conversation->last_message,
            'last_message_at' => optional($conversation->last_message_at)->format('H:i'),
            'last_message_at_iso' => optional($conversation->last_message_at)?->toIso8601String(),
            'unread_count' => $conversation->unread_count,
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
}