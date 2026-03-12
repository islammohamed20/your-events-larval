<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            /** @var User|null $user */
            if (! $user instanceof User || ! $user->isAdmin()) {
                abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $attributes = Attribute::withCount('values')
            ->orderBy('order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.attributes.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:attributes,slug',
                'type' => 'required|in:select,radio,checkbox',
                'order' => 'nullable|integer|min:0',
            ]);

            // Handle checkbox value (convert to integer 0 or 1)
            $validated['is_active'] = $request->has('is_active') ? 1 : 0;

            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            // Log for debugging
            Log::info('Creating attribute:', $validated);

            $attribute = Attribute::create($validated);

            Log::info('Attribute created successfully', ['id' => $attribute->id]);

            return redirect()->route('admin.attributes.edit', $attribute)
                ->with('success', 'تم إنشاء الخاصية بنجاح. الآن يمكنك إضافة قيم لها.');
        } catch (\Exception $e) {
            Log::error('Error creating attribute: '.$e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الخاصية: '.$e->getMessage());
        }
    }

    public function edit(Attribute $attribute)
    {
        $attribute->load(['values' => function ($query) {
            $query->orderBy('order');
        }]);

        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:attributes,slug,'.$attribute->id,
            'type' => 'required|in:select,radio,checkbox',
            'order' => 'nullable|integer|min:0',
        ]);

        // Handle checkbox value (convert to integer 0 or 1)
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $attribute->update($validated);

        return redirect()->route('admin.attributes.index')->with('success', 'تم تحديث الخاصية بنجاح');
    }

    public function destroy(Attribute $attribute)
    {
        // منع الحذف إذا كانت الخاصية مرتبطة بخدمات
        $servicesCount = $attribute->services()->count();
        if ($servicesCount > 0) {
            return redirect()->route('admin.attributes.index')
                ->with('error', "لا يمكن حذف الخاصية \"{$attribute->name}\" لأنها مستخدمة في {$servicesCount} خدمة. قم بإزالتها من الخدمات أولاً.");
        }

        $attribute->delete();

        return redirect()->route('admin.attributes.index')->with('success', 'تم حذف الخاصية بنجاح');
    }

    // Attribute Values Management
    public function storeValue(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        // Handle checkbox value (convert to integer 0 or 1)
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['value']);
        }

        $attribute->values()->create($validated);

        return redirect()->route('admin.attributes.edit', $attribute)->with('success', 'تم إضافة قيمة بنجاح');
    }

    public function updateValue(Request $request, Attribute $attribute, AttributeValue $value)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        // Handle checkbox value (convert to integer 0 or 1)
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['value']);
        }

        $value->update($validated);

        return redirect()->route('admin.attributes.edit', $attribute)->with('success', 'تم تحديث القيمة بنجاح');
    }

    public function destroyValue(Attribute $attribute, AttributeValue $value)
    {
        // منع الحذف إذا كانت القيمة مستخدمة في تباينات الخدمات
        $variationsCount = \App\Models\ServiceVariation::whereJsonContains('attribute_value_ids', $value->id)->count();
        if ($variationsCount > 0) {
            return redirect()->route('admin.attributes.edit', $attribute)
                ->with('error', "لا يمكن حذف القيمة \"{$value->value}\" لأنها مستخدمة في {$variationsCount} تباين خدمة.");
        }

        $value->delete();

        return redirect()->route('admin.attributes.edit', $attribute)->with('success', 'تم حذف القيمة بنجاح');
    }
}
