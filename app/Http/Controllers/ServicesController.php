<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Booking;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::active()
            ->withCount([
                'suppliers as suppliers_count' => function ($q) {
                    $q->where('suppliers.status', 'approved')
                        ->where('supplier_services.is_available', true);
                },
            ])
            ->whereHas('suppliers', function ($q) {
                $q->where('suppliers.status', 'approved')
                    ->where('supplier_services.is_available', true);
            });

        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Paginate results (12 per page) and preserve query string (e.g., category)
        $services = $query->paginate(12)->withQueryString();

        // Get categories with visible service count
        $categories = \App\Models\Category::active()
            ->ordered()
            ->withCount([
                'services as services_count' => function ($q) {
                    $q->active()->whereHas('suppliers', function ($sq) {
                        $sq->where('suppliers.status', 'approved')
                            ->where('supplier_services.is_available', true);
                    });
                },
            ])
            ->get();

        $selectedCategory = $request->category ? \App\Models\Category::find($request->category) : null;

        return view('services.index', compact('services', 'categories', 'selectedCategory'));
    }

    public function show($id)
    {
        $service = Service::with([
            'category',
            'attributes.values',
            'variations' => function ($q) {
                $q->where('is_active', true);
            },
            'suppliers' => function ($q) {
                $q->where('suppliers.status', 'approved')
                    ->where('supplier_services.is_available', true);
            },
        ])->findOrFail($id);

        if (! $service->suppliers || $service->suppliers->count() === 0) {
            abort(404);
        }

        // Similar services: same category if available, exclude current, active only
        $similar = Service::active()
            ->where('id', '<>', $service->id)
            ->when($service->category_id, function ($q) use ($service) {
                $q->where('category_id', $service->category_id);
            })
            ->whereHas('suppliers', function ($q) {
                $q->where('suppliers.status', 'approved')
                    ->where('supplier_services.is_available', true);
            })
            ->with(['thumbnailImage', 'category'])
            ->inRandomOrder()
            ->take(12)
            ->get();

        return view('services.show', compact('service', 'similar'));
    }

    public function unavailableDates($id)
    {
        $service = Service::with('category')->active()->whereHas('suppliers')->findOrFail($id);

        if (! optional($service->category)->book_from_service) {
            return response()->json(['dates' => []]);
        }

        $dates = Booking::query()
            ->where('service_id', $service->id)
            ->blockingServiceDate()
            ->whereDate('event_date', '>=', now()->toDateString())
            ->selectRaw('DATE(event_date) as event_date')
            ->groupBy('event_date')
            ->pluck('event_date')
            ->map(function ($date) {
                return (string) $date;
            })
            ->values();

        return response()->json(['dates' => $dates]);
    }
}
