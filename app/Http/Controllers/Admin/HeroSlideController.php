<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::orderBy('order')->get();
        return view('admin.hero-slides.index', compact('slides'));
    }

    public function create()
    {
        return view('admin.hero-slides.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'required|image|max:5120',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'button_style' => 'required|in:primary,secondary,accent',
            'transition_effect' => 'required|in:fade,slide,zoom,flip',
            'duration' => 'required|integer|min:2000|max:15000',
            'order' => 'nullable|integer'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('hero-slides', 'public');
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['order'] = $validated['order'] ?? HeroSlide::max('order') + 1;

        HeroSlide::create($validated);

        return redirect()->route('admin.hero-slides.index')->with('success', 'تم إضافة السلايد بنجاح');
    }

    public function edit(HeroSlide $heroSlide)
    {
        return view('admin.hero-slides.edit', compact('heroSlide'));
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'button_style' => 'required|in:primary,secondary,accent',
            'transition_effect' => 'required|in:fade,slide,zoom,flip',
            'duration' => 'required|integer|min:2000|max:15000',
            'order' => 'nullable|integer'
        ]);

        if ($request->hasFile('image')) {
            if ($heroSlide->image) {
                Storage::disk('public')->delete($heroSlide->image);
            }
            $validated['image'] = $request->file('image')->store('hero-slides', 'public');
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $heroSlide->update($validated);

        return redirect()->route('admin.hero-slides.index')->with('success', 'تم تحديث السلايد بنجاح');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        if ($heroSlide->image) {
            Storage::disk('public')->delete($heroSlide->image);
        }

        $heroSlide->delete();

        return redirect()->route('admin.hero-slides.index')->with('success', 'تم حذف السلايد بنجاح');
    }

    public function updateOrder(Request $request)
    {
        $orders = $request->input('orders', []);

        foreach ($orders as $order => $id) {
            HeroSlide::where('id', $id)->update(['order' => $order]);
        }

        return response()->json(['success' => true, 'message' => 'تم تحديث الترتيب بنجاح']);
    }

    public function toggleActive(HeroSlide $heroSlide)
    {
        $heroSlide->update(['is_active' => !$heroSlide->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $heroSlide->is_active,
            'message' => $heroSlide->is_active ? 'تم تفعيل السلايد' : 'تم إخفاء السلايد'
        ]);
    }
}
