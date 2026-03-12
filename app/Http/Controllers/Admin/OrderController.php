<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * عرض قائمة الطلبات
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'supplier', 'service', 'category']);

        // تصفية حسب الحالة
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // تصفية حسب البحث (خدمة أو عميل)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('service', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })->orWhereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        $orders = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * عرض تفاصيل الطلب
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'supplier', 'service', 'category', 'supplierStatuses.supplier']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * تحديث الطلب
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'general_notes' => 'nullable|string',
            'status' => 'nullable|in:pending,assigned,completed,cancelled',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)->with('success', 'تم تحديث الطلب بنجاح');
    }

    /**
     * حذف الطلب
     */
    public function destroy(Order $order)
    {
        // منع حذف الطلبات المقبولة أو الجارية
        if (in_array($order->status, ['accepted', 'in_progress', 'completed'])) {
            return redirect()->route('admin.orders.index')
                ->with('error', "لا يمكن حذف الطلب رقم #{$order->id} لأن حالته \"{$order->status}\". لا يمكن حذف طلب تم قبوله أو إنجازه.");
        }

        // منع الحذف إذا كان الطلب مقبولاً من مورد
        if (! is_null($order->supplier_id)) {
            return redirect()->route('admin.orders.index')
                ->with('error', "لا يمكن حذف الطلب رقم #{$order->id} لأنه مرتبط بمورد بالفعل.");
        }

        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'تم حذف الطلب بنجاح');
    }
}
