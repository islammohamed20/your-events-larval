<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index()
    {
        $categories = Category::withCount('services')
            ->ordered()
            ->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'icon_png' => 'nullable|image|mimes:png|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'color' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        // Handle PNG icon upload
        if ($request->hasFile('icon_png')) {
            $validated['icon_png'] = $request->file('icon_png')->store('category-icons', 'public');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('category-banners', 'public');
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم إنشاء الفئة بنجاح');
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'icon_png' => 'nullable|image|mimes:png|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'color' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'delete_image' => 'nullable|boolean',
            'delete_icon_png' => 'nullable|boolean',
            'delete_banner' => 'nullable|boolean',
        ]);

        // Handle image deletion
        if ($request->input('delete_image') == '1' && $category->image) {
            Storage::disk('public')->delete($category->image);
            $validated['image'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        // Handle PNG icon deletion
        if ($request->input('delete_icon_png') == '1' && $category->icon_png) {
            Storage::disk('public')->delete($category->icon_png);
            $validated['icon_png'] = null;
        }

        // Handle PNG icon upload
        if ($request->hasFile('icon_png')) {
            // Delete old icon
            if ($category->icon_png) {
                Storage::disk('public')->delete($category->icon_png);
            }
            $validated['icon_png'] = $request->file('icon_png')->store('category-icons', 'public');
        }

        // Handle banner deletion
        if ($request->input('delete_banner') == '1' && $category->banner) {
            Storage::disk('public')->delete($category->banner);
            $validated['banner'] = null;
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            // Delete old banner
            if ($category->banner) {
                Storage::disk('public')->delete($category->banner);
            }
            $validated['banner'] = $request->file('banner')->store('category-banners', 'public');
        }

        // Remove delete fields from validated data before update
        unset($validated['delete_image']);
        unset($validated['delete_icon_png']);
        unset($validated['delete_banner']);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم تحديث الفئة بنجاح');
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Check if category has services
        if ($category->services()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الفئة لأنها تحتوي على خدمات');
        }

        // Delete image
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        // Delete banner
        if ($category->banner) {
            Storage::disk('public')->delete($category->banner);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم حذف الفئة بنجاح');
    }

    /**
     * Toggle category active status
     */
    public function toggleActive(Category $category)
    {
        $category->update(['is_active' => ! $category->is_active]);

        return back()->with('success', 'تم تحديث حالة الفئة بنجاح');
    }
}
