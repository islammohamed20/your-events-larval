<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Service;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display user's wishlist
     */
    public function index()
    {
        $wishlists = auth()->user()->wishlists()->with('service')->latest()->get();
        return view('wishlist.index', compact('wishlists'));
    }

    /**
     * Add service to wishlist
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $service = Service::findOrFail($request->service_id);
        
        // Check if already in wishlist
        $exists = auth()->user()->wishlists()
            ->where('service_id', $request->service_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'الخدمة موجودة بالفعل في قائمة الأمنيات'
            ]);
        }

        auth()->user()->wishlists()->create([
            'service_id' => $request->service_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة الخدمة إلى قائمة الأمنيات',
            'count' => auth()->user()->wishlists()->count()
        ]);
    }

    /**
     * Remove service from wishlist
     */
    public function destroy($id)
    {
        $wishlist = auth()->user()->wishlists()->findOrFail($id);
        $wishlist->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إزالة الخدمة من قائمة الأمنيات',
                'count' => auth()->user()->wishlists()->count()
            ]);
        }

        return redirect()->route('wishlist.index')
            ->with('success', 'تم إزالة الخدمة من قائمة الأمنيات');
    }

    /**
     * Toggle wishlist (add/remove)
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $wishlist = auth()->user()->wishlists()
            ->where('service_id', $request->service_id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'تم إزالة الخدمة من قائمة الأمنيات',
                'count' => auth()->user()->wishlists()->count()
            ]);
        } else {
            auth()->user()->wishlists()->create([
                'service_id' => $request->service_id,
            ]);
            return response()->json([
                'success' => true,
                'action' => 'added',
                'message' => 'تمت إضافة الخدمة إلى قائمة الأمنيات',
                'count' => auth()->user()->wishlists()->count()
            ]);
        }
    }

    /**
     * Get wishlist count
     */
    public function count()
    {
        return response()->json([
            'count' => auth()->user()->wishlists()->count()
        ]);
    }
}
