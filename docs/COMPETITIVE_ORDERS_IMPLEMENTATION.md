# نظام الطلبات التنافسية - دليل التنفيذ الكامل

## ✅ ما تم إنشاؤه بالفعل:

### 1. قاعدة البيانات (Migrations)
- ✅ `competitive_orders` - جدول الطلبات
- ✅ `order_services` - ربط الطلبات بالخدمات
- ✅ `order_notifications` - تتبع الإشعارات

### 2. Models
- ✅ `CompetitiveOrder` - مع جميع العلاقات والوظائف
- ✅ `OrderNotification` - تتبع الإشعارات
- ✅ `Supplier` - تم إضافة العلاقات

### 3. Mail
- ✅ `OrderNotificationMail` - بريد إشعار الموردين
- ✅ `competitive-order-notification.blade.php` - قالب البريد

---

## 📝 الأكواد المتبقية للنسخ:

### 1. CompetitiveOrderController.php
```php
<?php

namespace App\Http\Controllers;

use App\Models\CompetitiveOrder;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompetitiveOrderController extends Controller
{
    public function create()
    {
        $services = Service::where('is_active', true)->with('category')->get()->groupBy('category.name');
        return view('competitive-orders.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'event_date' => 'required|date|after:today',
            'event_time' => 'nullable',
            'event_location' => 'nullable|string|max:500',
            'guests_count' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:1000',
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|integer|min:1',
            'services.*.notes' => 'nullable|string|max:500',
        ]);

        $order = CompetitiveOrder::create([
            'user_id' => Auth::id(),
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'],
            'event_date' => $validated['event_date'],
            'event_time' => $validated['event_time'],
            'event_location' => $validated['event_location'],
            'guests_count' => $validated['guests_count'],
            'notes' => $validated['notes'],
        ]);

        foreach ($validated['services'] as $serviceData) {
            $order->services()->attach($serviceData['service_id'], [
                'quantity' => $serviceData['quantity'],
                'notes' => $serviceData['notes'] ?? null,
            ]);
        }

        $notifiedCount = $order->notifyEligibleSuppliers();

        return redirect()->route('competitive-orders.show', $order)
                         ->with('success', "تم إنشاء طلبك بنجاح وإرسال إشعار لـ {$notifiedCount} مورد!");
    }

    public function show(CompetitiveOrder $competitiveOrder)
    {
        $this->authorize('view', $competitiveOrder);
        $competitiveOrder->load(['services', 'acceptedBySupplier', 'notifiedSuppliers']);
        return view('competitive-orders.show', ['order' => $competitiveOrder]);
    }

    public function index()
    {
        $orders = CompetitiveOrder::where('user_id', Auth::id())
                                   ->with(['services', 'acceptedBySupplier'])
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(10);
        return view('competitive-orders.index', compact('orders'));
    }

    public function cancel(CompetitiveOrder $competitiveOrder)
    {
        $this->authorize('update', $competitiveOrder);
        if ($competitiveOrder->status !== 'pending') {
            return back()->with('error', 'لا يمكن إلغاء طلب تم قبوله بالفعل');
        }
        $competitiveOrder->status = 'cancelled';
        $competitiveOrder->save();
        return back()->with('success', 'تم إلغاء الطلب بنجاح');
    }
}
```

### 2. SupplierOrderController.php
```php
<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\CompetitiveOrder;
use App\Models\OrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierOrderController extends Controller
{
    public function index()
    {
        $supplier = Auth::guard('supplier')->user();
        
        $pendingOrders = $supplier->receivedOrders()
                                  ->where('status', 'pending')
                                  ->where('expires_at', '>', now())
                                  ->withPivot('viewed_at', 'response')
                                  ->orderBy('expires_at', 'asc')
                                  ->get();
        
        $acceptedOrders = $supplier->acceptedOrders()
                                   ->with('services')
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        
        return view('supplier.orders.index', compact('pendingOrders', 'acceptedOrders'));
    }

    public function show($id)
    {
        $supplier = Auth::guard('supplier')->user();
        $order = CompetitiveOrder::findOrFail($id);
        
        // التحقق من أن المورد مُشعر بهذا الطلب
        $notification = $order->notifications()
                              ->where('supplier_id', $supplier->id)
                              ->first();
        
        if (!$notification) {
            abort(403, 'غير مصرح لك بالوصول لهذا الطلب');
        }
        
        // تحديث حالة المشاهدة
        $notification->markAsViewed();
        
        $order->load(['services', 'user', 'acceptedBySupplier']);
        
        return view('supplier.orders.show', compact('order', 'notification'));
    }

    public function accept(Request $request, $id)
    {
        $supplier = Auth::guard('supplier')->user();
        $order = CompetitiveOrder::findOrFail($id);
        
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);
        
        // محاولة قبول الطلب (مع الحماية من race condition)
        $accepted = $order->acceptBySupplier($supplier, $validated['notes'] ?? null);
        
        if ($accepted) {
            // إرسال إشعار للعميل
            try {
                \Mail::to($order->user->email)->send(new \App\Mail\OrderAcceptedBySupplierMail($order, $supplier));
            } catch (\Exception $e) {
                \Log::error('Failed to send order accepted email: ' . $e->getMessage());
            }
            
            return redirect()->route('supplier.orders.index')
                           ->with('success', 'مبروك! تم قبول الطلب بنجاح وستتواصل معك الإدارة قريباً');
        }
        
        return back()->with('error', 'عذراً، تم قبول الطلب من مورد آخر أو انتهى وقت القبول');
    }

    public function reject($id)
    {
        $supplier = Auth::guard('supplier')->user();
        $order = CompetitiveOrder::findOrFail($id);
        
        $notification = $order->notifications()
                              ->where('supplier_id', $supplier->id)
                              ->first();
        
        if ($notification && $notification->response === 'pending') {
            $notification->response = 'rejected';
            $notification->responded_at = now();
            $notification->save();
            
            return back()->with('success', 'تم رفض الطلب');
        }
        
        return back()->with('error', 'لا يمكن رفض هذا الطلب');
    }
}
```

### 3. Routes (web.php)
أضف هذه الأسطر:

```php
// Customer Competitive Orders
Route::middleware(['auth'])->group(function () {
    Route::get('/orders/create', [CompetitiveOrderController::class, 'create'])->name('competitive-orders.create');
    Route::post('/orders', [CompetitiveOrderController::class, 'store'])->name('competitive-orders.store');
    Route::get('/orders', [CompetitiveOrderController::class, 'index'])->name('competitive-orders.index');
    Route::get('/orders/{competitiveOrder}', [CompetitiveOrderController::class, 'show'])->name('competitive-orders.show');
    Route::post('/orders/{competitiveOrder}/cancel', [CompetitiveOrderController::class, 'cancel'])->name('competitive-orders.cancel');
});

// Supplier Orders
Route::prefix('supplier')->name('supplier.')->middleware(['auth:supplier', 'supplier'])->group(function () {
    Route::get('/orders', [\App\Http\Controllers\Supplier\SupplierOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [\App\Http\Controllers\Supplier\SupplierOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/accept', [\App\Http\Controllers\Supplier\SupplierOrderController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{id}/reject', [\App\Http\Controllers\Supplier\SupplierOrderController::class, 'reject'])->name('orders.reject');
});
```

### 4. Policy (CompetitiveOrderPolicy.php)
قم بإنشائه:
```bash
php artisan make:policy CompetitiveOrderPolicy --model=CompetitiveOrder
```

```php
<?php

namespace App\Policies;

use App\Models\CompetitiveOrder;
use App\Models\User;

class CompetitiveOrderPolicy
{
    public function view(User $user, CompetitiveOrder $order)
    {
        return $user->id === $order->user_id;
    }

    public function update(User $user, CompetitiveOrder $order)
    {
        return $user->id === $order->user_id;
    }

    public function delete(User $user, CompetitiveOrder $order)
    {
        return $user->id === $order->user_id;
    }
}
```

سجلها في `AuthServiceProvider.php`:
```php
protected $policies = [
    CompetitiveOrder::class => CompetitiveOrderPolicy::class,
];
```

---

## 🚀 خطوات التشغيل:

1. **تشغيل Migrations:**
```bash
php artisan migrate
```

2. **تأكد من وجود علاقة Supplier-Services:**
تحقق من أن جدول `supplier_services` يربط الموردين بالخدمات.

3. **إنشاء البريد الإضافي (اختياري):**
```bash
php artisan make:mail OrderAcceptedBySupplierMail
```

4. **إنشاء Views:**
سأوفر لك الـ Views في الرسالة التالية

---

## 🎯 كيف يعمل النظام:

1. **العميل يختار خدمات** → يملأ نموذج الطلب
2. **النظام يبحث تلقائياً** عن جميع الموردين الذين يقدمون أي من هذه الخدمات
3. **إرسال إشعار فوري** لكل مورد مؤهل (بريد إلكتروني)
4. **المورد يفتح الطلب** → يرى التفاصيل والخدمات المطلوبة
5. **أول مورد يضغط "قبول"** → يحصل على الطلب كاملاً
6. **باقي الموردين** → يتم إخطارهم بأن الطلب تم قبوله

---

## 🔒 الحماية من Race Condition:

استخدمنا `lockForUpdate()` في method `acceptBySupplier()` لضمان أن مورد واحد فقط يقبل الطلب حتى لو ضغط عدة موردين في نفس اللحظة.

---

## ⏱️ المؤقت الزمني:

- كل طلب لديه `expires_at` (افتراضياً 24 ساعة)
- يمكنك إنشاء Cron Job لتحديث الطلبات المنتهية:

```php
// في app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        CompetitiveOrder::where('status', 'pending')
                        ->where('expires_at', '<=', now())
                        ->update(['status' => 'expired']);
    })->everyMinute();
}
```

---

هل تريد أن أكمل Views أو أي جزء آخر؟
