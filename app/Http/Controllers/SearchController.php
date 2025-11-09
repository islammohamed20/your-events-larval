<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Package;
use App\Models\Category;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type', 'all'); // all, services, packages
        
        if (empty($query)) {
            return redirect()->route('home')->with('error', 'الرجاء إدخال كلمة بحث');
        }

        $results = collect();
        
        // Search in Services
        if ($type === 'all' || $type === 'services') {
            $services = Service::where('is_active', true)
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('features', 'LIKE', "%{$query}%");
                })
                ->with('category')
                ->get()
                ->map(function($service) {
                    $service->result_type = 'service';
                    return $service;
                });
            
            $results = $results->merge($services);
        }
        
        // Search in Packages
        if ($type === 'all' || $type === 'packages') {
            $packages = Package::where('is_active', true)
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%")
                      ->orWhere('features', 'LIKE', "%{$query}%");
                })
                ->get()
                ->map(function($package) {
                    $package->result_type = 'package';
                    return $package;
                });
            
            $results = $results->merge($packages);
        }

        return view('search.results', [
            'query' => $query,
            'results' => $results,
            'type' => $type,
            'total' => $results->count()
        ]);
    }

    public function autocomplete(Request $request)
    {
        $query = $request->input('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $services = Service::where('is_active', true)
            ->where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'image')
            ->limit(5)
            ->get()
            ->map(function($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'type' => 'service',
                    'url' => route('services.show', $service),
                    'image' => $service->image ? asset('storage/' . $service->image) : null
                ];
            });

        $packages = Package::where('is_active', true)
            ->where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'image')
            ->limit(3)
            ->get()
            ->map(function($package) {
                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'type' => 'package',
                    'url' => route('packages.show', $package),
                    'image' => $package->image ? asset('storage/' . $package->image) : null
                ];
            });

        return response()->json($services->merge($packages));
    }
}
