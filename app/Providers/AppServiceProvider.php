<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\ContactMessage;
// Models
use App\Models\Order;
use App\Models\Payment;
use App\Models\Quote;
use App\Models\Supplier;
use App\Models\User;
use App\Observers\BookingObserver;
use App\Observers\ContactMessageObserver;
// Observers
use App\Observers\OrderObserver;
use App\Observers\PaymentObserver;
use App\Observers\QuoteObserver;
use App\Observers\SupplierObserver;
use App\Observers\UserObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap 5 pagination views across the app (admin uses Bootstrap)
        Paginator::useBootstrapFive();
        Paginator::defaultView('vendor.pagination.bootstrap-5');
        Paginator::defaultSimpleView('vendor.pagination.simple-bootstrap-5');

        // Note: Locale is now handled by SetLocale middleware for better session handling

        // Register Observers for Admin Notifications
        Quote::observe(QuoteObserver::class);
        Booking::observe(BookingObserver::class);
        Order::observe(OrderObserver::class);
        Payment::observe(PaymentObserver::class);
        ContactMessage::observe(ContactMessageObserver::class);
        Supplier::observe(SupplierObserver::class);
        User::observe(UserObserver::class);
    }
}
