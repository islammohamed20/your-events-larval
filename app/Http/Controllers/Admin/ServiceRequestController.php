<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = ServiceRequest::with(['service', 'category', 'booking']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $serviceRequests = $query->paginate(20);
        $categories = Category::all();

        return view('admin.service-requests.index', compact('serviceRequests', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $services = Service::all();
        $bookings = Booking::all();

        return view('admin.service-requests.create', compact('categories', 'services', 'bookings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'category_id' => 'required|exists:categories,id',
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'customer_notes' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        ServiceRequest::create($validated);

        return redirect()->route('service-requests.index')->with('success', 'تم إنشاء الطلب بنجاح');
    }

    public function show(ServiceRequest $serviceRequest)
    {
        return view('admin.service-requests.show', compact('serviceRequest'));
    }

    public function edit(ServiceRequest $serviceRequest)
    {
        $categories = Category::all();
        $services = Service::all();
        $bookings = Booking::all();

        return view('admin.service-requests.edit', compact('serviceRequest', 'categories', 'services', 'bookings'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'customer_notes' => 'nullable|string',
            'admin_notes' => 'nullable|string',
            'status' => 'required|in:pending,accepted,rejected,completed',
        ]);

        $serviceRequest->update($validated);

        return redirect()->route('service-requests.show', $serviceRequest)->with('success', 'تم تحديث الطلب بنجاح');
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        $serviceRequest->delete();

        return redirect()->route('service-requests.index')->with('success', 'تم حذف الطلب بنجاح');
    }

    public function accept(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update(['status' => 'accepted']);

        return back()->with('success', 'تم قبول الطلب بنجاح');
    }

    public function reject(ServiceRequest $serviceRequest)
    {
        $serviceRequest->update(['status' => 'rejected']);

        return back()->with('success', 'تم رفض الطلب');
    }
}
