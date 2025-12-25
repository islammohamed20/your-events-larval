<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::active();

        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Paginate results (12 per page) and preserve query string (e.g., category)
        $services = $query->paginate(12)->withQueryString();

        // Get categories with service count
        $categories = \App\Models\Category::active()->ordered()->withCount('services')->get();

        $selectedCategory = $request->category ? \App\Models\Category::find($request->category) : null;

        return view('services.index', compact('services', 'categories', 'selectedCategory'));
    }

    public function show($id)
    {
        $service = Service::with([
            'attributes.values' => function ($q) {
                $q->active();
            },
            'variations' => function ($q) {
                $q->where('is_active', true);
            },
        ])->findOrFail($id);

        // Similar services: same category if available, exclude current, active only
        $similar = Service::active()
            ->where('id', '<>', $service->id)
            ->when($service->category_id, function ($q) use ($service) {
                $q->where('category_id', $service->category_id);
            })
            ->with(['thumbnailImage', 'category'])
            ->inRandomOrder()
            ->take(12)
            ->get();

        return view('services.show', compact('service', 'similar'));
    }
}
