<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Gallery;
use App\Models\HeroSlide;
use App\Models\HomepageSection;
use App\Models\Package;
use App\Models\Review;
use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        // Get hero slides
        $heroSlides = HeroSlide::where('is_active', true)->orderBy('order')->get();

        // Get homepage sections
        $sections = HomepageSection::where('is_active', true)->orderBy('order')->get();

        $visibleSupplierServices = function ($query) {
            $query->where('is_active', true)
                ->whereHas('suppliers', function ($supplierQuery) {
                    $supplierQuery->where('suppliers.status', 'approved')
                        ->where('supplier_services.is_available', true);
                });
        };

        // Get data for each section
        $packages = Package::active()->take(3)->get();
        $services = Service::query()
            ->where($visibleSupplierServices)
            ->take(6)
            ->get();
        $gallery = Gallery::featured()->take(8)->get();
        $reviews = Review::approved()->take(3)->get();
        $categories = Category::where('is_active', true)
            ->whereHas('services', $visibleSupplierServices)
            ->withCount(['services as services_count' => $visibleSupplierServices])
            ->orderBy('order')
            ->get();

        return view('welcome', compact('heroSlides', 'sections', 'packages', 'services', 'gallery', 'reviews', 'categories'));
    }
}
