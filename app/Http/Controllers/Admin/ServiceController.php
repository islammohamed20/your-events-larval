<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServicesExport;
use App\Exports\ServicesTemplateExport;
use App\Imports\ServicesImport;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        // قاعدة البحث
        $search = trim((string) $request->get('q', ''));

        // Eager-load category لإظهارها بدون N+1، مع فلترة حسب البحث
        $query = Service::with('category')->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('marketing_description', 'like', "%{$search}%")
                  ->orWhere('what_we_offer', 'like', "%{$search}%")
                  ->orWhere('why_choose_us', 'like', "%{$search}%")
                  ->orWhere('meta_description', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('service_type', 'like', "%{$search}%")
                  ->orWhere('duration', 'like', "%{$search}%")
                  // الحقول المصفوفة/JSON: استخدام LIKE كبحث نصي عام
                  ->orWhere('features', 'like', "%{$search}%")
                  ->orWhere('custom_fields', 'like', "%{$search}%");

                // البحث الرقمي: المطابقة على id أو السعر
                if (is_numeric($search)) {
                    $q->orWhere('id', (int) $search)
                      ->orWhere('price', (float) $search);
                }
            })
            ->orWhereHas('category', function ($cq) use ($search) {
                $cq->where('name', 'like', "%{$search}%")
                   ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        // ترقيم الصفحات مع الحفاظ على معاملات الاستعلام
        $services = $query->paginate(15)->withQueryString();

        return view('admin.services.index', compact('services'))
            ->with('search', $search);
    }

    public function show(Service $service)
    {
        // Redirect to edit page for a more useful default behavior
        return redirect()->route('admin.services.edit', $service);
    }

    public function create()
    {
        return view('admin.services.create', [
            'attributes' => Attribute::active()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|integer|exists:categories,id',
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            // Make description optional
            'description' => 'nullable|string',
            'marketing_description' => 'nullable|string',
            'what_we_offer' => 'nullable|string',
            'why_choose_us' => 'nullable|string',
            'meta_description' => 'nullable|string|max:160',
            'service_type' => 'nullable|in:simple,variable',
            'price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:0',
            'type' => 'nullable|string|max:255',
            'features' => 'nullable|array',
            'custom_fields' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array', // صور متعددة
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'attributes' => 'nullable|array',
            'attributes.*' => 'integer|exists:attributes,id',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('services', 'public');
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        // Default service_type to 'simple' if not provided
        $validated['service_type'] = $validated['service_type'] ?? 'simple';
        $validated['has_variations'] = ($validated['service_type'] === 'variable') ? 1 : 0;
        $validated['custom_fields'] = $this->normalizeCustomFields($request->input('custom_fields', []));

        // Ensure description is not null (DB column may be NOT NULL)
        if (array_key_exists('description', $validated)) {
            $validated['description'] = $validated['description'] ?? '';
        }

        // Normalize features: remove null/empty entries and keep as array
        $validated['features'] = collect($request->input('features', []))
            ->map(function ($v) { return is_string($v) ? trim($v) : ''; })
            ->filter(function ($v) { return $v !== ''; })
            ->values()
            ->toArray();
        
        // Create service
        $service = Service::create($validated);

        // معالجة الصور المتعددة
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('services', 'public');
                $service->images()->create([
                    'image_path' => $path,
                    'alt_text' => $service->name,
                    'is_thumbnail' => ($index === 0), // أول صورة تكون thumbnail
                    'sort_order' => $index,
                ]);
            }
        }

        // Sync attributes for variable services
        if ($validated['service_type'] === 'variable' && $request->has('attributes')) {
            $service->attributes()->sync($request->input('attributes', []));
        }

        return redirect()->route('admin.services.index')
                         ->with('success', 'تم إضافة الخدمة بنجاح');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', [
            'service' => $service,
            'attributes' => Attribute::active()->get(),
        ]);
    }

    public function update(Request $request, Service $service)
    {
        try {
            $validated = $request->validate([
            'category_id' => 'nullable|integer|exists:categories,id',
            'name' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            // Make description optional
            'description' => 'nullable|string',
            'marketing_description' => 'nullable|string',
            'what_we_offer' => 'nullable|string',
            'why_choose_us' => 'nullable|string',
            'meta_description' => 'nullable|string|max:160',
            'service_type' => 'required|in:simple,variable',
            'price' => 'required_if:service_type,simple|nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:0',
            'type' => 'nullable|string|max:255',
            'features' => 'nullable|array',
            'custom_fields' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array', // صور متعددة
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail_id' => 'nullable|integer|exists:service_images,id', // تحديد الصورة المصغرة
            'delete_images' => 'nullable|array', // حذف صور
            'delete_images.*' => 'integer|exists:service_images,id',
            'attributes' => 'nullable|array',
            'attributes.*' => 'integer|exists:attributes,id',
            ]);
        } catch (ValidationException $e) {
            Log::warning('Service update validation failed', [
                'service_id' => $service->id,
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        if ($request->hasFile('image')) {
            if ($service->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($service->image);
            }
            $validated['image'] = $request->file('image')->store('services', 'public');
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['has_variations'] = ($request->input('service_type') === 'variable') ? 1 : 0;
        $validated['custom_fields'] = $this->normalizeCustomFields($request->input('custom_fields', []));
        
        // Ensure description is not null (DB column may be NOT NULL)
        if (array_key_exists('description', $validated)) {
            $validated['description'] = $validated['description'] ?? '';
        }
        
        // Normalize features: remove null/empty entries and keep as array
        $validated['features'] = collect($request->input('features', []))
            ->map(function ($v) { return is_string($v) ? trim($v) : ''; })
            ->filter(function ($v) { return $v !== ''; })
            ->values()
            ->toArray();
        
        // Update service
        $service->update($validated);

        // حذف الصور المحددة
        if ($request->has('delete_images')) {
            foreach ($request->input('delete_images') as $imageId) {
                $image = $service->images()->find($imageId);
                if ($image) {
                    $image->delete(); // سيتم حذف الملف تلقائياً عبر booted() في Model
                }
            }
        }

        // إضافة صور جديدة
        if ($request->hasFile('images')) {
            $currentMaxOrder = $service->images()->max('sort_order') ?? -1;
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('services', 'public');
                $service->images()->create([
                    'image_path' => $path,
                    'alt_text' => $service->name,
                    'is_thumbnail' => false,
                    'sort_order' => $currentMaxOrder + $index + 1,
                ]);
            }
        }

        // تحديد الصورة المصغرة
        if ($request->filled('thumbnail_id')) {
            // إزالة thumbnail من جميع الصور
            $service->images()->update(['is_thumbnail' => false]);
            // تعيين الصورة الجديدة كـ thumbnail
            $service->images()->where('id', $request->input('thumbnail_id'))->update(['is_thumbnail' => true]);
        }

        // Sync attributes for variable services
        if ($request->input('service_type') === 'variable' && $request->has('attributes')) {
            $service->attributes()->sync($request->input('attributes', []));
        } else {
            $service->attributes()->sync([]);
        }

        return redirect()->route('admin.services.edit', $service)
                         ->with('success', 'تم تحديث الخدمة بنجاح');
    }

    /**
     * Bulk update service_type for selected services
     */
    public function bulkUpdateType(Request $request)
    {
        $idsCsv = (string) $request->input('ids', '');
        $targetType = (string) $request->input('service_type', '');

        // Validate target type
        if (!in_array($targetType, ['simple', 'variable'], true)) {
            return redirect()->route('admin.services.index')
                             ->with('error', 'يرجى اختيار نوع خدمة صحيح (ثابت أو متغير)');
        }

        // Parse IDs CSV -> unique integer IDs
        $ids = collect(explode(',', $idsCsv))
            ->map(fn($v) => (int) trim($v))
            ->filter(fn($v) => $v > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return redirect()->route('admin.services.index')
                             ->with('error', 'لم يتم تحديد أي خدمات للتحديث');
        }

        $services = Service::whereIn('id', $ids)->get();
        $updatedCount = 0;
        foreach ($services as $service) {
            $service->service_type = $targetType;
            $service->has_variations = ($targetType === 'variable') ? 1 : 0;
            // If switching to simple, clear attributes relations
            if ($targetType === 'simple') {
                try {
                    $service->attributes()->sync([]);
                } catch (\Exception $e) {
                    // Continue even if relation sync fails
                    Log::warning('Failed to clear attributes for service ID '.$service->id.' error: '.$e->getMessage());
                }
            }
            $service->save();
            $updatedCount++;
        }

        return redirect()->route('admin.services.index')
                         ->with('success', "تم تحديث نوع الخدمة لـ {$updatedCount} خدمة");
    }

    public function destroy(Service $service)
    {
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
        
        $service->delete();

        return redirect()->route('admin.services.index')
                         ->with('success', 'تم حذف الخدمة بنجاح');
    }

    /**
     * Bulk delete services
     */
    public function bulkDelete(Request $request)
    {
        $idsCsv = (string) $request->input('ids', '');
        $ids = collect(explode(',', $idsCsv))
            ->map(fn($v) => (int) trim($v))
            ->filter(fn($v) => $v > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return redirect()->route('admin.services.index')
                             ->with('error', 'لم يتم تحديد أي خدمات للحذف');
        }

        // Fetch services with images to delete files
        $services = Service::whereIn('id', $ids)->get();
        $deletedCount = 0;
        foreach ($services as $service) {
            if ($service->image) {
                try {
                    Storage::disk('public')->delete($service->image);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete service image: '.$service->image.' error: '.$e->getMessage());
                }
            }
            $service->delete();
            $deletedCount++;
        }

        if ($deletedCount === 0) {
            return redirect()->route('admin.services.index')
                             ->with('warning', 'لم يتم العثور على خدمات مطابقة للحذف');
        }

        return redirect()->route('admin.services.index')
                         ->with('success', "تم حذف {$deletedCount} خدمة بنجاح");
    }

    private function normalizeCustomFields(array $fields): array
    {
        $normalized = [];
        foreach ($fields as $field) {
            $label = isset($field['label']) ? trim((string)$field['label']) : '';
            if ($label === '') { continue; }
            $type = isset($field['type']) && in_array($field['type'], ['single','multiple']) ? $field['type'] : 'single';
            $optionsRaw = $field['options'] ?? [];
            if (is_string($optionsRaw)) {
                $options = array_map('trim', explode(',', $optionsRaw));
            } elseif (is_array($optionsRaw)) {
                $options = array_map('trim', $optionsRaw);
            } else {
                $options = [];
            }
            $options = array_values(array_filter(array_unique($options), fn($v) => $v !== ''));
            if (count($options) === 0) { continue; }
            $normalized[] = [
                'label' => $label,
                'type' => $type,
                'options' => $options,
            ];
        }
        return $normalized;
    }

    /**
     * Export services to Excel
     */
    public function export()
    {
        return Excel::download(new ServicesExport, 'services_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Import services from Excel
     */
    public function import(Request $request)
    {
        // Set longer execution time for large imports
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '256M');
        
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120', // 5MB max
        ]);

        try {
            // Count before import
            $beforeCount = Service::count();
            
            $import = new ServicesImport;
            Excel::import($import, $request->file('file'));

            // Count after import
            $afterCount = Service::count();
            $newServices = $afterCount - $beforeCount;

            $failures = $import->failures();
            
            if (count($failures) > 0) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $errorMessages[] = "الصف {$failure->row()}: " . implode(', ', $failure->errors());
                }
                
                $message = "تم استيراد الخدمات (جديد: {$newServices}) مع بعض الأخطاء:<br>" 
                    . implode('<br>', array_slice($errorMessages, 0, 10));
                
                if (count($errorMessages) > 10) {
                    $message .= '<br>... و ' . (count($errorMessages) - 10) . ' أخطاء أخرى';
                }
                
                return redirect()->route('admin.services.index')
                    ->with('warning', $message);
            }

            $message = "تم استيراد الخدمات بنجاح!";
            if ($newServices > 0) {
                $message .= " (تم إنشاء {$newServices} خدمة جديدة)";
            } else {
                $message .= " (تم تحديث الخدمات الموجودة)";
            }

            return redirect()->route('admin.services.index')
                ->with('success', $message);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "الصف {$failure->row()}: " . implode(', ', $failure->errors());
            }
            
            return redirect()->route('admin.services.index')
                ->with('error', 'أخطاء في التحقق من البيانات:<br>' . implode('<br>', array_slice($errorMessages, 0, 10)));
                
        } catch (\Exception $e) {
            Log::error('Import Error: ' . $e->getMessage());
            
            return redirect()->route('admin.services.index')
                ->with('error', 'حدث خطأ أثناء الاستيراد: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template
     */
    public function downloadTemplate()
    {
        return Excel::download(new ServicesTemplateExport, 'services_template.xlsx');
    }

    /**
     * حذف صورة واحدة من الخدمة
     */
    public function deleteImage(Request $request, Service $service, ServiceImage $image)
    {
        // التأكد من أن الصورة تنتمي للخدمة
        if ($image->service_id !== $service->id) {
            return response()->json(['success' => false, 'message' => 'صورة غير صحيحة'], 403);
        }

        $image->delete();
        return response()->json(['success' => true, 'message' => 'تم حذف الصورة بنجاح']);
    }

    /**
     * تعيين صورة كـ thumbnail
     */
    public function setThumbnail(Request $request, Service $service, ServiceImage $image)
    {
        // التأكد من أن الصورة تنتمي للخدمة
        if ($image->service_id !== $service->id) {
            return response()->json(['success' => false, 'message' => 'صورة غير صحيحة'], 403);
        }

        // إزالة thumbnail من جميع الصور
        $service->images()->update(['is_thumbnail' => false]);
        
        // تعيين الصورة الحالية كـ thumbnail
        $image->update(['is_thumbnail' => true]);

        return response()->json(['success' => true, 'message' => 'تم تعيين الصورة المصغرة بنجاح']);
    }
}
