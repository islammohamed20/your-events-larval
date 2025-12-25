<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('images')->latest()->get();

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'persons_min' => 'nullable|integer|min:1',
            'persons_max' => 'nullable|integer|min:1|gte:persons_min',
            'description' => 'required|string',
            'features' => 'nullable|array',
            'attributes' => 'nullable|array',
            'attributes.*.name' => 'nullable|string|max:255',
            'attributes.*.description' => 'nullable|string',
            'attributes.*.details' => 'nullable|string',
            'attributes.*.visible' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('packages', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        // تنظيف الخواص الفارغة وحفظ حالة الظهور
        if (isset($validated['attributes'])) {
            $validated['attributes'] = array_filter($validated['attributes'], function ($attr) {
                return ! empty($attr['name']);
            });
            // تحويل visible إلى boolean وإعادة ترقيم المصفوفة
            $validated['attributes'] = array_values(array_map(function ($attr) {
                $attr['visible'] = isset($attr['visible']) && $attr['visible'] ? true : false;

                return $attr;
            }, $validated['attributes']));
        }

        $package = Package::create($validated);

        // معالجة الصور المتعددة
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('packages', 'public');
                $package->images()->create([
                    'image_path' => $path,
                    'alt_text' => $package->name,
                    'is_thumbnail' => ($index === 0), // أول صورة تكون thumbnail
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم إضافة الباقة بنجاح');
    }

    public function edit(Package $package)
    {
        $package->load('images');

        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'persons_min' => 'nullable|integer|min:1',
            'persons_max' => 'nullable|integer|min:1|gte:persons_min',
            'description' => 'required|string',
            'features' => 'nullable|array',
            'attributes' => 'nullable|array',
            'attributes.*.name' => 'nullable|string|max:255',
            'attributes.*.description' => 'nullable|string',
            'attributes.*.details' => 'nullable|string',
            'attributes.*.visible' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail_id' => 'nullable|integer|exists:package_images,id',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:package_images,id',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($package->image) {
                Storage::disk('public')->delete($package->image);
            }
            $validated['image'] = $request->file('image')->store('packages', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        // تنظيف الخواص الفارغة وحفظ حالة الظهور
        if (isset($validated['attributes'])) {
            $validated['attributes'] = array_filter($validated['attributes'], function ($attr) {
                return ! empty($attr['name']);
            });
            // تحويل visible إلى boolean وإعادة ترقيم المصفوفة
            $validated['attributes'] = array_values(array_map(function ($attr) {
                $attr['visible'] = isset($attr['visible']) && $attr['visible'] ? true : false;

                return $attr;
            }, $validated['attributes']));
        }

        $package->update($validated);

        // حذف الصور المحددة
        if ($request->has('delete_images')) {
            foreach ($request->input('delete_images') as $imageId) {
                $image = $package->images()->find($imageId);
                if ($image) {
                    $image->delete();
                }
            }
        }

        // إضافة صور جديدة
        if ($request->hasFile('images')) {
            $currentMaxOrder = $package->images()->max('sort_order') ?? -1;
            $hasThumbnail = $package->images()->where('is_thumbnail', true)->exists();

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('packages', 'public');
                $package->images()->create([
                    'image_path' => $path,
                    'alt_text' => $package->name,
                    'is_thumbnail' => (! $hasThumbnail && $index === 0),
                    'sort_order' => $currentMaxOrder + $index + 1,
                ]);
            }
        }

        // تحديد الصورة المصغرة
        if ($request->filled('thumbnail_id')) {
            $package->images()->update(['is_thumbnail' => false]);
            $package->images()->where('id', $request->input('thumbnail_id'))->update(['is_thumbnail' => true]);
        }

        return redirect()->route('admin.packages.edit', $package)
            ->with('success', 'تم تحديث الباقة بنجاح');
    }

    public function destroy(Package $package)
    {
        // منع حذف الباقة إذا كانت مستخدمة في حجوزات
        $bookingsCount = \App\Models\Booking::where('package_id', $package->id)->count();
        if ($bookingsCount > 0) {
            return redirect()->route('admin.packages.index')
                ->with('error', "لا يمكن حذف الباقة \"{$package->name}\" لأنها مستخدمة في {$bookingsCount} حجز. يمكنك إيقاف نشاطها بدلاً من حذفها.");
        }

        // حذف الصورة القديمة
        if ($package->image) {
            Storage::disk('public')->delete($package->image);
        }

        // حذف جميع الصور المتعددة (سيتم حذفها تلقائياً عبر cascade)
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم حذف الباقة بنجاح');
    }

    /**
     * حذف صورة واحدة من الباقة
     */
    public function deleteImage(Request $request, Package $package, PackageImage $image)
    {
        if ($image->package_id !== $package->id) {
            return response()->json(['success' => false, 'message' => 'صورة غير صحيحة'], 403);
        }

        $image->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الصورة بنجاح']);
    }

    /**
     * تعيين صورة كـ thumbnail
     */
    public function setThumbnail(Request $request, Package $package, PackageImage $image)
    {
        if ($image->package_id !== $package->id) {
            return response()->json(['success' => false, 'message' => 'صورة غير صحيحة'], 403);
        }

        $package->images()->update(['is_thumbnail' => false]);
        $image->update(['is_thumbnail' => true]);

        return response()->json(['success' => true, 'message' => 'تم تعيين الصورة المصغرة بنجاح']);
    }
}
