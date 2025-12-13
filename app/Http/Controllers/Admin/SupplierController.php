<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SupplierController extends Controller
{
    /**
     * عرض قائمة الموردين
     */
    public function index(Request $request)
    {
        $query = Supplier::query();

        // إظهار الموردين الحقيقيين فقط: نوع محدد (company/individual)
        $query->whereIn('supplier_type', ['company', 'individual']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('supplier_type', $request->type);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * صفحة إنشاء مورد
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.suppliers.create', compact('categories'));
    }

    /**
     * حفظ مورد جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_type' => 'required|in:company,individual',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email',
            'password' => 'required|string|min:6',
            'primary_phone' => 'required|string|max:255',
            'secondary_phone' => 'nullable|string|max:255',
            'headquarters_city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'commercial_register' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'services_offered' => 'nullable|array',
            'social_media' => 'nullable|array',
            'commercial_register_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp',
            'tax_certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp',
            'company_profile_file' => 'nullable|file|mimes:pdf,doc,docx',
            'portfolio_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp',
        ]);

        // رفع الملفات إن وجدت
        $commercialRegisterPath = $request->file('commercial_register_file')
            ? $request->file('commercial_register_file')->store('suppliers/docs')
            : null;
        $taxCertificatePath = $request->file('tax_certificate_file')
            ? $request->file('tax_certificate_file')->store('suppliers/docs')
            : null;
        $companyProfilePath = $request->file('company_profile_file')
            ? $request->file('company_profile_file')->store('suppliers/docs')
            : null;

        $portfolioPaths = [];
        if ($request->hasFile('portfolio_files')) {
            foreach ($request->file('portfolio_files') as $file) {
                $portfolioPaths[] = $file->store('suppliers/portfolio');
            }
        }

        $supplier = Supplier::create([
            'supplier_type' => $validated['supplier_type'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'primary_phone' => $validated['primary_phone'],
            'secondary_phone' => $validated['secondary_phone'] ?? null,
            'headquarters_city' => $validated['headquarters_city'] ?? null,
            'address' => $validated['address'] ?? null,
            'description' => $validated['description'] ?? null,
            'commercial_register' => $validated['commercial_register'] ?? null,
            'tax_number' => $validated['tax_number'] ?? null,
            'services_offered' => $validated['services_offered'] ?? null,
            'social_media' => $validated['social_media'] ?? null,
            'commercial_register_file' => $commercialRegisterPath,
            'tax_certificate_file' => $taxCertificatePath,
            'company_profile_file' => $companyProfilePath,
            'portfolio_files' => count($portfolioPaths) ? $portfolioPaths : null,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.suppliers.show', $supplier)->with('success', 'تم إضافة المورد بنجاح');
    }

    /**
     * عرض تفاصيل مورد
     */
    public function show(Supplier $supplier)
    {
        // التأكد من أن المورد حقيقي وليس من جدول users
        if (!in_array($supplier->supplier_type, ['company', 'individual'])) {
            abort(404, 'هذا ليس مورداً صحيحاً');
        }
        
        $categories = Category::active()->ordered()->get();
        return view('admin.suppliers.show', compact('supplier', 'categories'));
    }

    /**
     * صفحة تعديل مورد
     */
    public function edit(Supplier $supplier)
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.suppliers.edit', compact('supplier', 'categories'));
    }

    /**
     * تحديث مورد
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'supplier_type' => 'required|in:company,individual',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $supplier->id,
            'password' => 'nullable|string|min:6',
            'primary_phone' => 'required|string|max:255',
            'secondary_phone' => 'nullable|string|max:255',
            'headquarters_city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'commercial_register' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'services_offered' => 'nullable|array',
            'social_media' => 'nullable|array',
            'commercial_register_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp',
            'tax_certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp',
            'company_profile_file' => 'nullable|file|mimes:pdf,doc,docx',
            'portfolio_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp',
        ]);

        // تحديث الملفات إن وجدت
        if ($request->hasFile('commercial_register_file')) {
            $supplier->commercial_register_file = $request->file('commercial_register_file')->store('suppliers/docs');
        }
        if ($request->hasFile('tax_certificate_file')) {
            $supplier->tax_certificate_file = $request->file('tax_certificate_file')->store('suppliers/docs');
        }
        if ($request->hasFile('company_profile_file')) {
            $supplier->company_profile_file = $request->file('company_profile_file')->store('suppliers/docs');
        }
        if ($request->hasFile('portfolio_files')) {
            $newPortfolio = [];
            foreach ($request->file('portfolio_files') as $file) {
                $newPortfolio[] = $file->store('suppliers/portfolio');
            }
            $current = is_array($supplier->portfolio_files) ? $supplier->portfolio_files : [];
            $supplier->portfolio_files = array_values(array_unique(array_merge($current, $newPortfolio)));
        }

        $supplier->supplier_type = $validated['supplier_type'];
        $supplier->name = $validated['name'];
        $supplier->email = $validated['email'];
        if (!empty($validated['password'])) {
            $supplier->password = Hash::make($validated['password']);
        }
        $supplier->primary_phone = $validated['primary_phone'];
        $supplier->secondary_phone = $validated['secondary_phone'] ?? null;
        $supplier->headquarters_city = $validated['headquarters_city'] ?? null;
        $supplier->address = $validated['address'] ?? null;
        $supplier->description = $validated['description'] ?? null;
        $supplier->commercial_register = $validated['commercial_register'] ?? null;
        $supplier->tax_number = $validated['tax_number'] ?? null;
        $supplier->services_offered = $validated['services_offered'] ?? null;
        $supplier->social_media = $validated['social_media'] ?? null;

        $supplier->save();

        return redirect()->route('admin.suppliers.show', $supplier)->with('success', 'تم حفظ التعديلات بنجاح');
    }

    /**
     * حذف مورد
     */
    public function destroy(Supplier $supplier)
    {
        // حذف ملفات مرتبطة إن أردت (اختياري)
        foreach (['commercial_register_file', 'tax_certificate_file', 'company_profile_file'] as $fileField) {
            if ($supplier->{$fileField}) {
                Storage::delete($supplier->{$fileField});
            }
        }
        if (is_array($supplier->portfolio_files)) {
            foreach ($supplier->portfolio_files as $file) {
                Storage::delete($file);
            }
        }

        $supplier->delete();
        return redirect()->route('admin.suppliers.index')->with('success', 'تم حذف المورد');
    }


    /**
     * تنزيل مستندات المورد
     */
    public function downloadDocument(Supplier $supplier, string $type)
    {
        $map = [
            'commercial_register' => 'commercial_register_file',
            'tax_certificate' => 'tax_certificate_file',
            'company_profile' => 'company_profile_file',
        ];
        if (!array_key_exists($type, $map)) {
            abort(404, 'نوع الملف غير معروف');
        }
        $field = $map[$type];
        $path = $supplier->{$field};
        
        if (!$path) {
            abort(404, 'الملف غير موجود');
        }
        
        $fullPath = 'public/' . $path;
        if (!Storage::exists($fullPath)) {
            abort(404, 'الملف غير موجود في النظام');
        }
        
        return Storage::download($fullPath);
    }

    /**
     * الموافقة على مورد
     */
    public function approve(Request $request, Supplier $supplier)
    {
        $prev = $supplier->status;
        if ($supplier->status !== 'approved') {
            $supplier->status = 'approved';
            
            // تعيين تأكيد البريد الإلكتروني تلقائياً عند الموافقة
            if (!$supplier->email_verified_at) {
                $supplier->email_verified_at = now();
            }
            
            $supplier->save();

            ActivityLog::record(
                $supplier,
                'supplier.approved',
                'تمت الموافقة على المورد',
                [
                    'from' => $prev,
                    'to' => 'approved',
                ]
            );

            // إرسال بريد قبول المورد
            try {
                $variables = [
                    'supplierName' => $supplier->name,
                    'supplierEmail' => $supplier->email,
                    'approvalDate' => now()->format('Y/m/d H:i'),
                    'dashboardUrl' => url('/supplier/dashboard'),
                    'companyName' => config('app.name', 'Your Events'),
                    'supportEmail' => config('mail.from.address', 'hello@yourevents.sa'),
                ];

                \Illuminate\Support\Facades\Mail::send('emails.supplier-approval', $variables, function ($message) use ($supplier) {
                    $message->to($supplier->email)
                        ->subject('🎉 تم قبولك كمورد لدى ' . config('app.name', 'Your Events') . ' – ابدأ الآن');
                });
                
                \Illuminate\Support\Facades\Log::info('Supplier approval email sent successfully', [
                    'supplier_id' => $supplier->id,
                    'supplier_email' => $supplier->email,
                ]);
            } catch (\Throwable $e) {
                // لا نمنع الموافقة في حال فشل البريد، فقط نعرض إشعاراً
                \Illuminate\Support\Facades\Log::error('Failed to send supplier approval email: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.suppliers.show', $supplier)->with('success', 'تمت الموافقة على المورد بنجاح');
    }

    /**
     * رفض مورد
     */
    public function reject(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $prev = $supplier->status;
        if ($supplier->status !== 'rejected') {
            $supplier->status = 'rejected';
            // حفظ سبب الرفض إن كان الحقل موجوداً
            if (isset($supplier->rejection_reason) || $supplier->isFillable('rejection_reason')) {
                $supplier->rejection_reason = $data['reason'] ?? null;
            }
            $supplier->save();

            ActivityLog::record(
                $supplier,
                'supplier.rejected',
                'تم رفض المورد' . ($data['reason'] ? (' - السبب: ' . $data['reason']) : ''),
                [
                    'from' => $prev,
                    'to' => 'rejected',
                    'reason' => $data['reason'] ?? null,
                ]
            );
        }

        return redirect()->route('admin.suppliers.show', $supplier)->with('success', 'تم رفض المورد');
    }

    /**
     * إيقاف مورد
     */
    public function suspend(Request $request, Supplier $supplier)
    {
        $prev = $supplier->status;
        if ($supplier->status !== 'suspended') {
            $supplier->status = 'suspended';
            $supplier->save();

            ActivityLog::record(
                $supplier,
                'supplier.suspended',
                'تم إيقاف المورد مؤقتاً',
                [
                    'from' => $prev,
                    'to' => 'suspended',
                ]
            );
        }

        return redirect()->route('admin.suppliers.show', $supplier)->with('success', 'تم إيقاف المورد');
    }

    /**
     * تفعيل مورد
     */
    public function activate(Request $request, Supplier $supplier)
    {
        // إعادة التفعيل تعيد الحالة إلى approved
        $prev = $supplier->status;
        if ($supplier->status !== 'approved') {
            $supplier->status = 'approved';
            $supplier->save();

            ActivityLog::record(
                $supplier,
                'supplier.activated',
                'تم تفعيل المورد',
                [
                    'from' => $prev,
                    'to' => 'approved',
                ]
            );
        }

        return redirect()->route('admin.suppliers.show', $supplier)->with('success', 'تم تفعيل المورد');
    }

    /**
     * إرجاع حالة مورد إلى قيد المراجعة
     */
    public function pending(Request $request, Supplier $supplier)
    {
        $prev = $supplier->status;
        if ($supplier->status !== 'pending') {
            $supplier->status = 'pending';
            $supplier->save();

            ActivityLog::record(
                $supplier,
                'supplier.pending',
                'تم إرجاع المورد إلى قيد المراجعة',
                [
                    'from' => $prev,
                    'to' => 'pending',
                ]
            );
        }

        return redirect()->route('admin.suppliers.show', $supplier)->with('success', 'تم إرجاع المورد إلى حالة قيد المراجعة');
    }
    /**
     * ربط خدمة بالمورد
     */
    public function addService(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        SupplierService::updateOrCreate(
            [
                'supplier_id' => $supplier->id,
                'service_id' => $validated['service_id'],
            ],
            [
                'category_id' => $validated['category_id'],
                'is_available' => true,
            ]
        );

        return back()->with('success', 'تم إضافة الخدمة للمورد');
    }

    /**
     * إزالة خدمة من مورد
     */
    public function removeService(Supplier $supplier, int $serviceId)
    {
        SupplierService::where('supplier_id', $supplier->id)
            ->where('service_id', $serviceId)
            ->delete();

        return back()->with('success', 'تم إزالة الخدمة من المورد');
    }
}
