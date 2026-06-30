<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\WhatsAppConversation;
use App\Models\SupplierWhatsAppMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierWhatsAppController extends Controller
{
    protected WhatsAppService $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Get current supplier
     */
    protected function supplier(): ?Supplier
    {
        $supplier = Auth::guard('supplier')->user();

        return $supplier instanceof Supplier ? $supplier : null;
    }

    /**
     * Display WhatsApp conversations page
     */
    public function index()
    {
        $supplier = $this->supplier();

        // الحصول على المحادثات المعينة للمورد الحالي فقط
        $conversations = WhatsAppConversation::where('assigned_supplier_id', $supplier->id)
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

        return view('supplier.whatsapp.index', compact('conversations'));
    }

    /**
     * Display specific conversation
     */
    public function show($id)
    {
        $supplier = $this->supplier();

        $conversation = WhatsAppConversation::where('id', $id)
            ->where('assigned_supplier_id', $supplier->id)
            ->firstOrFail();

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
        ];

        return view('supplier.whatsapp.show', [
            'conversation' => $conversationData,
            'messages' => $messages,
        ]);
    }

    /**
     * Send message
     */
    public function sendMessage(Request $request, $id)
    {
        $supplier = $this->supplier();

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $conversation = WhatsAppConversation::where('id', $id)
            ->where('assigned_supplier_id', $supplier->id)
            ->firstOrFail();

        // إرسال الرسالة عبر WhatsApp API
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
}
