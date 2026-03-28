<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Supplier;
use App\Models\SupplierOrderStatus;
use App\Models\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * إنشاء طلب جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'customer_notes' => 'nullable|string',
            'general_notes' => 'nullable|string',
        ]);

        // إنشاء الطلب
        $order = Order::create($validated);

        // البحث عن الموردين المطابقين
        $suppliers = SupplierService::where('service_id', $order->service_id)
            ->where('category_id', $order->category_id)
            ->where('is_available', true)
            ->pluck('supplier_id')
            ->unique();

        // إنشاء records في supplier_order_status لكل مورد
        foreach ($suppliers as $supplierId) {
            SupplierOrderStatus::create([
                'order_id' => $order->id,
                'supplier_id' => $supplierId,
                'status' => 'pending',
            ]);

            // إرسال الإيميل للمورد
            $supplier = Supplier::find($supplierId);
            if ($supplier) {
                Mail::mailer('hello')->send('emails.order-request', [
                    'order' => $order,
                    'supplier' => $supplier,
                ], function ($message) use ($supplier, $order) {
                    $message->to($supplier->email)
                        ->subject('طلب جديد - '.$order->service->name);
                });
            }
        }

        return response()->json([
            'message' => 'تم إنشاء الطلب بنجاح',
            'order' => $order,
            'suppliers_count' => count($suppliers),
        ], 201);
    }

    /**
     * قبول الطلب من قبل مورد
     * GET /api/orders/{order_id}/accept?supplier_id=XXX
     */
    public function accept(Request $request, $orderId)
    {
        $supplierId = $request->query('supplier_id');

        if (! $supplierId) {
            return response()->json([
                'message' => 'supplier_id مطلوب',
            ], 400);
        }

        $order = Order::find($orderId);

        if (! $order) {
            return response()->json([
                'message' => 'الطلب غير موجود',
            ], 404);
        }

        // التحقق من أن الطلب ما زال متاح
        if (! $order->isAvailable()) {
            return response()->json([
                'message' => 'تم إسناد الطلب لمورد آخر بالفعل',
                'supplier_id' => $order->supplier_id,
                'assigned_at' => $order->assigned_at,
            ], 409);
        }

        // قبول الطلب
        $success = $order->acceptBySupplier($supplierId);

        if (! $success) {
            return response()->json([
                'message' => 'فشل قبول الطلب',
            ], 400);
        }

        return response()->json([
            'message' => 'تم قبول الطلب بنجاح',
            'order' => $order->fresh(),
            'status' => 'assigned',
        ], 200);
    }

    /**
     * عرض تفاصيل الطلب
     */
    public function show($id)
    {
        $order = Order::with(['customer', 'supplier', 'service', 'category', 'supplierStatuses.supplier'])
            ->find($id);

        if (! $order) {
            return response()->json(['message' => 'الطلب غير موجود'], 404);
        }

        return response()->json($order);
    }

    /**
     * عرض جميع الطلبات
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'supplier', 'service', 'category']);

        // تصفية حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // تصفية حسب العميل
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // تصفية حسب المورد
        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        return response()->json($query->paginate(20));
    }
}
