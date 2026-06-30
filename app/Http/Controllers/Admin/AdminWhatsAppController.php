<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\WhatsAppConversation;
use App\Models\SupplierWhatsAppMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminWhatsAppController extends Controller
{
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Display all WhatsApp conversations for admin
     */
    public function index()
    {
        // الأدمن يرى جميع المحادثات
        $conversations = WhatsAppConversation::where('status', 'active')
            ->orderBy('last_message_at', 'desc')
            ->with(['assignedSupplier', 'messages' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(1);
            }])
            ->get()
            ->map(function ($conversation) {
                $lastMessage = $conversation->messages->first();
                return [
                    'id' => $conversation->id,
                    'name' => $conversation->customer_name ?? 'عميل',
                    'phone' => $conversation->customer_phone,
                    'last_message' => $lastMessage ? $lastMessage->message : 'لا توجد رسائل',
                    'time' => $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '-',
                    'unread' => $conversation->unread_count,
                    'avatar' => mb_substr($conversation->customer_name ?? 'ع', 0, 1),
                    'assigned_supplier' => $conversation->assignedSupplier ? [
                        'id' => $conversation->assignedSupplier->id,
                        'name' => $conversation->assignedSupplier->name,
                    ] : null,
                ];
            });

        // الحصول على الموردين الموافق عليهم للتعيين
        $approvedSuppliers = Supplier::where('status', 'approved')->get(['id', 'name']);

        return view('admin.whatsapp.supplier-conversations', compact('conversations', 'approvedSuppliers'));
    }

    /**
     * Display specific conversation for admin
     */
    public function show($id)
    {
        $conversation = WhatsAppConversation::with('assignedSupplier')->findOrFail($id);

        // تحديث المحادثة كمقروءة
        $conversation->markAsRead();

        // الحصول على الرسائل
        $messages = SupplierWhatsAppMessage::where('conversation_id', $id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'text' => $message->message,
                    'sender' => $message->direction === 'incoming' ? 'customer' : 'supplier',
                    'time' => $message->sent_at ? $message->sent_at->format('h:i A') : $message->created_at->format('h:i A'),
                ];
            });

        $conversationData = [
            'id' => $conversation->id,
            'name' => $conversation->customer_name ?? 'عميل',
            'phone' => $conversation->customer_phone,
            'avatar' => mb_substr($conversation->customer_name ?? 'ع', 0, 1),
            'assigned_supplier' => $conversation->assignedSupplier ? [
                'id' => $conversation->assignedSupplier->id,
                'name' => $conversation->assignedSupplier->name,
            ] : null,
        ];

        // الحصول على الموردين الموافق عليهم للتعيين
        $approvedSuppliers = Supplier::where('status', 'approved')->get(['id', 'name']);

        return view('admin.whatsapp.supplier-show', [
            'conversation' => $conversationData,
            'messages' => $messages,
            'approvedSuppliers' => $approvedSuppliers,
        ]);
    }

    /**
     * Assign conversation to a supplier
     */
    public function assignSupplier(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $conversation = WhatsAppConversation::findOrFail($id);

        if ($request->supplier_id) {
            $supplier = Supplier::where('id', $request->supplier_id)
                ->where('status', 'approved')
                ->firstOrFail();

            $conversation->assignToSupplier($supplier);

            return response()->json([
                'success' => true,
                'message' => 'تم تعيين المحادثة للمورد بنجاح',
                'supplier' => [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                ],
            ]);
        } else {
            // إلغاء التعيين
            $conversation->assignToSupplier(null);

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء تعيين المحادثة',
            ]);
        }
    }

    /**
     * Send message as admin
     */
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $conversation = WhatsAppConversation::findOrFail($id);

        // إرسال الرسالة عبر Faalwa API
        $result = $this->whatsappService->sendMessage(
            $conversation->customer_phone,
            $request->message,
            $conversation
        );

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'تم إرسال الرسالة بنجاح',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'فشل إرسال الرسالة',
        ], 500);
    }

    /**
     * Get unassigned conversations
     */
    public function getUnassigned()
    {
        $conversations = WhatsAppConversation::whereNull('assigned_supplier_id')
            ->where('status', 'active')
            ->orderBy('last_message_at', 'desc')
            ->with(['messages' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(1);
            }])
            ->get()
            ->map(function ($conversation) {
                $lastMessage = $conversation->messages->first();
                return [
                    'id' => $conversation->id,
                    'name' => $conversation->customer_name ?? 'عميل',
                    'phone' => $conversation->customer_phone,
                    'last_message' => $lastMessage ? $lastMessage->message : 'لا توجد رسائل',
                    'time' => $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '-',
                    'unread' => $conversation->unread_count,
                    'avatar' => mb_substr($conversation->customer_name ?? 'ع', 0, 1),
                ];
            });

        return response()->json([
            'conversations' => $conversations,
        ]);
    }
}
