<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $packages = Package::active()->get();
        $services = Service::active()->whereHas('suppliers')->get();

        $selectedPackage = null;
        $selectedService = null;

        if ($request->has('package_id')) {
            $selectedPackage = Package::find($request->package_id);
        }

        if ($request->has('service_id')) {
            $selectedService = Service::whereHas('suppliers')->find($request->service_id);
        }

        return view('booking.create', compact('packages', 'services', 'selectedPackage', 'selectedService'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:20',
            'event_date' => 'required|date|after:today',
            'event_location' => 'required|string|max:255',
            'event_lat' => 'required|numeric|between:-90,90',
            'event_lng' => 'required|numeric|between:-180,180',
            'guests_count' => 'required|integer|min:1',
            'booking_type' => 'required|in:service,package',
            'package_id' => [
                'nullable',
                'required_if:booking_type,package',
                Rule::exists('packages', 'id')->where('is_active', true),
            ],
            'service_id' => [
                'nullable',
                'required_if:booking_type,service',
                Rule::exists('services', 'id')->where('is_active', true),
            ],
            'special_requests' => 'nullable|string',
        ]);

        if ($validated['booking_type'] === 'service') {
            $validated['package_id'] = null;
        } else {
            $validated['service_id'] = null;
        }

        // Calculate total amount
        $totalAmount = 0;
        if (isset($validated['package_id']) && $validated['package_id']) {
            $package = Package::active()->find($validated['package_id']);
            if (! $package) {
                return redirect()->back()->withInput()->withErrors([
                    'package_id' => 'هذه الباقة غير متوفرة حالياً ولا يمكن حجزها.',
                ]);
            }
            $totalAmount += $package->price;
        }
        if (isset($validated['service_id']) && $validated['service_id']) {
            $service = Service::whereHas('suppliers')->find($validated['service_id']);
            if (! $service) {
                return redirect()->back()->withInput()->withErrors([
                    'service_id' => 'هذه الخدمة غير متوفرة حالياً ولا يمكن حجزها.',
                ]);
            }
            $totalAmount += $service->price;
        }

        $validated['total_amount'] = $totalAmount;
        $validated['user_id'] = Auth::check() ? Auth::id() : null;

        $booking = Booking::create($validated);

        // Send confirmation email
        try {
            Mail::to($booking->client_email)->send(new BookingConfirmation($booking));
        } catch (\Exception $e) {
            // Log error but don't fail the booking
        }

        return redirect()->route('booking.success', $booking->booking_reference)
            ->with('success', 'تم إرسال طلب الحجز بنجاح! سنتواصل معك قريباً.');
    }

    public function success($reference)
    {
        $booking = Booking::where('booking_reference', $reference)->firstOrFail();

        return view('booking.success', compact('booking'));
    }

    public function myBookings()
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $bookings = Booking::where('user_id', Auth::id())->latest()->get();

        return view('booking.my-bookings', compact('bookings'));
    }
}
