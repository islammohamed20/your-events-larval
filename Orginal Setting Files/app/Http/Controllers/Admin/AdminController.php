<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Package;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Gallery;
use App\Models\Review;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'users' => User::count(),
            'packages' => Package::count(),
            'services' => Service::count(),
            'bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'gallery_items' => Gallery::count(),
            'reviews' => Review::count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
        ];

        $recent_bookings = Booking::with(['package', 'service'])
                                 ->latest()
                                 ->take(5)
                                 ->get();

        return view('admin.dashboard', compact('stats', 'recent_bookings'));
    }
}
