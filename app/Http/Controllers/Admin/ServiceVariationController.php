<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceVariation;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceVariationController extends Controller
{
    public function index(Service $service)
    {
        $service->load(['attributes.values', 'variations']);
        $variations = $service->variations;
        return view('admin.services.variations', compact('service', 'variations'));
    }

    public function store(Request $request, Service $service)
    {
        $validated = $request->validate([
            'attribute_values' => 'required|array',
            'attribute_values.*' => 'required|integer|exists:attribute_values,id',
            'sku' => 'nullable|string|max:255|unique:service_variations,sku',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);

        // Get attribute value IDs
        $valueIds = array_values($validated['attribute_values']);
        
        // Build attributes array (attribute_name => value_name)
        $attributes = [];
        foreach ($validated['attribute_values'] as $attrId => $valueId) {
            $value = AttributeValue::find($valueId);
            if ($value) {
                $attribute = Attribute::find($attrId);
                if ($attribute) {
                    $attributes[$attribute->name] = $value->value;
                }
            }
        }

        // Generate SKU if not provided
        $sku = $validated['sku'] ?? $this->generateSKU($service, $attributes);

        // Check for duplicates
        $existingVariation = $service->variations()
            ->where('attribute_value_ids', json_encode($valueIds))
            ->first();
            
        if ($existingVariation) {
            return redirect()->back()
                ->with('error', 'هذه التركيبة موجودة بالفعل')
                ->withInput();
        }

        $variation = new ServiceVariation();
        $variation->service_id = $service->id;
        $variation->sku = $sku;
        $variation->attributes = $attributes;
        $variation->attribute_value_ids = $valueIds;
        $variation->price = $validated['price'];
        $variation->sale_price = $validated['sale_price'] ?? null;
        $variation->stock = $validated['stock'] ?? null;
        $variation->is_active = $request->has('is_active') ? 1 : 0;
        $variation->save();

        return redirect()->route('admin.services.variations.index', $service)
            ->with('success', 'تم إضافة التنويعة بنجاح');
    }
    
    private function generateSKU(Service $service, array $attributes)
    {
        $sku = strtoupper(Str::slug($service->name, '-'));
        foreach ($attributes as $key => $value) {
            $sku .= '-' . strtoupper(Str::slug($value, ''));
        }
        
        // Ensure uniqueness
        $counter = 1;
        $baseSku = $sku;
        while (ServiceVariation::where('sku', $sku)->exists()) {
            $sku = $baseSku . '-' . $counter;
            $counter++;
        }
        
        return $sku;
    }

    public function edit(Service $service, ServiceVariation $variation)
    {
        if ($variation->service_id !== $service->id) {
            abort(404);
        }

        return response()->json([
            'success' => true,
            'variation' => $variation
        ]);
    }

    public function update(Request $request, Service $service, ServiceVariation $variation)
    {
        if ($variation->service_id !== $service->id) {
            abort(404);
        }

        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
        ]);

        $variation->price = $validated['price'];
        $variation->sale_price = $validated['sale_price'] ?? null;
        $variation->stock = $validated['stock'] ?? null;
        $variation->is_active = $request->has('is_active') ? 1 : 0;
        $variation->save();

        return redirect()->route('admin.services.variations.index', $service)
            ->with('success', 'تم تحديث التنويعة بنجاح');
    }
    
    public function generate(Request $request, Service $service)
    {
        try {
            $defaultPrice = $request->input('default_price', $service->price ?? 0);
            
            // Get all attributes and their values for this service
            $attributes = $service->attributes()->with('values')->get();
            
            if ($attributes->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد خصائص مرتبطة بهذه الخدمة'
                ]);
            }
            
            // Generate all possible combinations
            $combinations = $this->generateCombinations($attributes);
            
            $created = 0;
            $skipped = 0;
            
            foreach ($combinations as $combination) {
                // Check if variation already exists
                $valueIds = array_column($combination, 'value_id');
                $exists = $service->variations()
                    ->where('attribute_value_ids', json_encode($valueIds))
                    ->exists();
                    
                if ($exists) {
                    $skipped++;
                    continue;
                }
                
                // Build attributes array
                $attrs = [];
                foreach ($combination as $item) {
                    $attrs[$item['attribute_name']] = $item['value_name'];
                }
                
                // Generate SKU
                $sku = $this->generateSKU($service, $attrs);
                
                // Create variation
                $variation = new ServiceVariation();
                $variation->service_id = $service->id;
                $variation->sku = $sku;
                $variation->attributes = $attrs;
                $variation->attribute_value_ids = $valueIds;
                $variation->price = $defaultPrice;
                $variation->is_active = 1;
                $variation->save();
                
                $created++;
            }
            
            return response()->json([
                'success' => true,
                'message' => "تم إنشاء {$created} تنويعة جديدة" . ($skipped > 0 ? " (تم تجاهل {$skipped} تنويعة موجودة مسبقاً)" : "")
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Generate variations error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function generateCombinations($attributes, $currentIndex = 0, $currentCombination = [])
    {
        if ($currentIndex === $attributes->count()) {
            return [$currentCombination];
        }
        
        $results = [];
        $currentAttribute = $attributes[$currentIndex];
        
        foreach ($currentAttribute->values as $value) {
            $newCombination = $currentCombination;
            $newCombination[] = [
                'attribute_id' => $currentAttribute->id,
                'attribute_name' => $currentAttribute->name,
                'value_id' => $value->id,
                'value_name' => $value->value,
            ];
            
            $results = array_merge(
                $results,
                $this->generateCombinations($attributes, $currentIndex + 1, $newCombination)
            );
        }
        
        return $results;
    }

    public function destroy(Service $service, ServiceVariation $variation)
    {
        if ($variation->service_id !== $service->id) {
            abort(404);
        }

        $variation->delete();
        return redirect()->route('admin.services.variations.index', $service)->with('success', 'تم حذف التنويع');
    }
}