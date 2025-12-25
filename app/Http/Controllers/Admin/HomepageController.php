<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageController extends Controller
{
    public function index()
    {
        $sections = HomepageSection::orderBy('order')->get();

        return view('admin.homepage.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.homepage.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_key' => 'required|string|unique:homepage_sections,section_key',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|array',
            'image' => 'nullable|image|max:5120',
            'video_url' => 'nullable|url',
            'background_type' => 'required|in:color,gradient,image',
            'background_value' => 'nullable|string',
            'background_image' => 'nullable|image|max:5120',
            'order' => 'nullable|integer',
            'settings' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('homepage/sections', 'public');
        }

        if ($request->hasFile('background_image')) {
            $validated['background_value'] = $request->file('background_image')->store('homepage/backgrounds', 'public');
            $validated['background_type'] = 'image';
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['order'] = $validated['order'] ?? HomepageSection::max('order') + 1;

        HomepageSection::create($validated);

        return redirect()->route('admin.homepage.index')->with('success', 'تم إضافة القسم بنجاح');
    }

    public function edit(HomepageSection $section)
    {
        return view('admin.homepage.edit', compact('section'));
    }

    public function update(Request $request, HomepageSection $section)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:5120',
            'video_url' => 'nullable|url',
            'background_type' => 'required|in:color,gradient,image',
            'background_value' => 'nullable|string',
            'background_image' => 'nullable|image|max:5120',
            'order' => 'nullable|integer',
            'settings' => 'nullable|array',
            'settings.display_count' => 'nullable|integer|min:1|max:12',
        ]);

        // Build content array
        $content = $section->content ?? [];
        if ($request->filled('description')) {
            $content['description'] = $request->description;
        }
        if ($request->filled('button_text')) {
            $content['button_text'] = $request->button_text;
        }
        if ($request->filled('button_link')) {
            $content['button_link'] = $request->button_link;
        }
        $validated['content'] = $content;

        // Handle settings array
        if ($request->has('settings')) {
            $validated['settings'] = $request->settings;
        }

        if ($request->hasFile('image')) {
            if ($section->image) {
                Storage::disk('public')->delete($section->image);
            }
            $validated['image'] = $request->file('image')->store('homepage/sections', 'public');
        }

        if ($request->hasFile('background_image')) {
            if ($section->background_type === 'image' && $section->background_value) {
                Storage::disk('public')->delete($section->background_value);
            }
            $validated['background_value'] = $request->file('background_image')->store('homepage/backgrounds', 'public');
            $validated['background_type'] = 'image';
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $section->update($validated);

        return redirect()->route('admin.homepage.index')->with('success', 'تم تحديث القسم بنجاح');
    }

    public function destroy(HomepageSection $section)
    {
        if ($section->image) {
            Storage::disk('public')->delete($section->image);
        }
        if ($section->background_type === 'image' && $section->background_value) {
            Storage::disk('public')->delete($section->background_value);
        }

        $section->delete();

        return redirect()->route('admin.homepage.index')->with('success', 'تم حذف القسم بنجاح');
    }

    public function updateOrder(Request $request)
    {
        $orders = $request->input('orders', []);

        foreach ($orders as $order => $id) {
            HomepageSection::where('id', $id)->update(['order' => $order]);
        }

        return response()->json(['success' => true, 'message' => 'تم تحديث الترتيب بنجاح']);
    }

    public function toggleActive(HomepageSection $section)
    {
        $section->update(['is_active' => ! $section->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $section->is_active,
            'message' => $section->is_active ? 'تم تفعيل القسم' : 'تم إخفاء القسم',
        ]);
    }

    public function initializeDefaults()
    {
        $defaults = [
            [
                'section_key' => 'hero',
                'title' => 'مرحباً بك في Your Events',
                'subtitle' => 'نحن نجعل كل حدث لا يُنسى',
                'is_active' => true,
                'order' => 1,
                'background_type' => 'gradient',
                'background_value' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            ],
            [
                'section_key' => 'stats',
                'title' => 'إحصائياتنا',
                'subtitle' => 'نفخر بإنجازاتنا',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'section_key' => 'categories',
                'title' => 'الأقسام',
                'subtitle' => 'استكشف الأقسام بحسب اهتمامك',
                'is_active' => true,
                'order' => 3,
                'background_type' => 'gradient',
                'background_value' => 'linear-gradient(135deg, #ffffff 0%, #f8f9fa 50%, #e9ecef 100%)',
            ],
            [
                'section_key' => 'services',
                'title' => 'خدماتنا',
                'subtitle' => 'نقدم أفضل الخدمات لك',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'section_key' => 'packages',
                'title' => 'الباقات المميزة',
                'subtitle' => 'اختر الباقة المناسبة لك',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'section_key' => 'gallery',
                'title' => 'معرض الصور',
                'subtitle' => 'استعرض أعمالنا السابقة',
                'is_active' => true,
                'order' => 6,
            ],
            [
                'section_key' => 'reviews',
                'title' => 'آراء العملاء',
                'subtitle' => 'ماذا يقول عملاؤنا',
                'is_active' => true,
                'order' => 7,
            ],
            [
                'section_key' => 'contact',
                'title' => 'تواصل معنا',
                'subtitle' => 'نحن هنا للإجابة على استفساراتك',
                'is_active' => true,
                'order' => 8,
            ],
        ];

        foreach ($defaults as $default) {
            HomepageSection::firstOrCreate(
                ['section_key' => $default['section_key']],
                $default
            );
        }

        return redirect()->route('admin.homepage.index')->with('success', 'تم تهيئة الأقسام الافتراضية بنجاح');
    }
}
