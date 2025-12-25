<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $packages = Package::active()->get();
        $services = Service::active()->get();

        $selectedPackage = null;
        $selectedService = null;

        if ($request->has('package_id')) {
            $selectedPackage = Package::find($request->package_id);
        }

        if ($request->has('service_id')) {
            $selectedService = Service::find($request->service_id);
        }

        return view('booking.create', compact('packages', 'services', 'selectedPackage', 'selectedService'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:20',
            'event_date' => 'required|date|after:today',
            'event_location' => 'required|string|max:255',
            'guests_count' => 'required|integer|min:1',
            'package_id' => 'nullable|exists:packages,id',
            'service_id' => 'nullable|exists:services,id',
            'special_requests' => 'nullable|string',
        ]);

        // Calculate total amount
        $totalAmount = 0;
        if (isset($validated['package_id']) && $validated['package_id']) {
            $package = Package::find($validated['package_id']);
            $totalAmount += $package->price;
        }
        if (isset($validated['service_id']) && $validated['service_id']) {
            $service = Service::find($validated['service_id']);
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
