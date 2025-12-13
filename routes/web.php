<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\CustomerManagementController;
use App\Http\Controllers\Admin\AttributeController as AdminAttributeController;
use App\Http\Controllers\Admin\ServiceVariationController as AdminServiceVariationController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// SEO: Sitemap & Robots
Route::get('/robots.txt', function () {
    $lines = [];
    $lines[] = 'User-agent: *';
    $lines[] = 'Allow: /';
    $lines[] = 'Disallow: /admin/';
    $lines[] = 'Disallow: /login';
    $lines[] = 'Disallow: /register';
    $lines[] = 'Disallow: /cart';
    $lines[] = 'Disallow: /suppliers/verify-otp';
    $lines[] = 'Disallow: /verify-otp';
    $lines[] = 'Disallow: /password/verify-otp';
    $lines[] = 'Disallow: /wishlist';
    $lines[] = 'Sitemap: ' . URL::to('/sitemap.xml');
    return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/sitemap.xml', function () {
    $now = now()->toAtomString();

    $urls = [];
    // Static pages
    $urls[] = ['loc' => URL::to('/'), 'changefreq' => 'daily', 'priority' => '1.0'];
    $urls[] = ['loc' => URL::to('/services'), 'changefreq' => 'daily', 'priority' => '0.9'];
    $urls[] = ['loc' => URL::to('/packages'), 'changefreq' => 'weekly', 'priority' => '0.8'];
    $urls[] = ['loc' => URL::to('/gallery'), 'changefreq' => 'weekly', 'priority' => '0.6'];
    $urls[] = ['loc' => URL::to('/contact'), 'changefreq' => 'yearly', 'priority' => '0.3'];
    $urls[] = ['loc' => URL::to('/terms-and-conditions'), 'changefreq' => 'yearly', 'priority' => '0.2'];
    $urls[] = ['loc' => URL::to('/privacy'), 'changefreq' => 'yearly', 'priority' => '0.2'];

    // Categories
    try {
        $categories = \App\Models\Category::active()->ordered()->get(['id', 'updated_at']);
        foreach ($categories as $category) {
            // If there is a public categories listing, include it; otherwise include filtered services by category
            $urls[] = [
                'loc' => URL::route('services.index', ['category' => $category->id]),
                'lastmod' => optional($category->updated_at)->toAtomString() ?? $now,
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }
    } catch (\Throwable $e) {
        // Skip if model/table missing
    }

    // Services
    try {
        $services = \App\Models\Service::active()->get(['id', 'updated_at']);
        foreach ($services as $service) {
            $urls[] = [
                'loc' => URL::route('services.show', ['id' => $service->id]),
                'lastmod' => optional($service->updated_at)->toAtomString() ?? $now,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }
    } catch (\Throwable $e) {
        // Skip if model/table missing
    }

    // Packages
    try {
        $packages = \App\Models\Package::active()->get(['id', 'updated_at']);
        foreach ($packages as $package) {
            $urls[] = [
                'loc' => URL::route('packages.show', ['id' => $package->id]),
                'lastmod' => optional($package->updated_at)->toAtomString() ?? $now,
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }
    } catch (\Throwable $e) {
        // Skip if model/table missing
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($urls as $u) {
        $xml .= '<url>';
        $xml .= '<loc>' . e($u['loc']) . '</loc>';
        if (!empty($u['lastmod'])) $xml .= '<lastmod>' . e($u['lastmod']) . '</lastmod>';
        if (!empty($u['changefreq'])) $xml .= '<changefreq>' . e($u['changefreq']) . '</changefreq>';
        if (!empty($u['priority'])) $xml .= '<priority>' . e($u['priority']) . '</priority>';
        $xml .= '</url>';
    }
    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'application/xml');
})->name('sitemap');

// Language Switch Route  
Route::get('/lang/{locale}', function ($locale) {
    $supported = ['ar', 'en'];
    if (!in_array($locale, $supported)) {
        return redirect()->back();
    }
    
    // إعادة التوجيه مع حفظ اللغة في cookie
    $response = redirect()->back();
    return $response->withCookie(Cookie::make(
        'app_locale',
        $locale,
        60 * 24 * 365, // سنة واحدة
        '/',
        null,
        true,
        false // بدون تشفير
    ));
})->name('lang.switch');

// Search Routes
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/autocomplete', [SearchController::class, 'autocomplete'])->name('search.autocomplete');

// Supplier Registration Routes
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\Supplier\SupplierAuthController;
use App\Http\Controllers\Supplier\SupplierDashboardController;
Route::get('/suppliers/register', [SupplierController::class, 'create'])->name('suppliers.register');
Route::post('/suppliers/register', [SupplierController::class, 'store'])->name('suppliers.store');
Route::get('/suppliers/verify-otp', [SupplierController::class, 'showVerifyOtp'])->name('suppliers.verify-otp');
Route::post('/suppliers/verify-otp', [SupplierController::class, 'verifyOtp'])->name('suppliers.verify-otp.post');
Route::get('/suppliers/success', [SupplierController::class, 'success'])->name('suppliers.success');
Route::post('/suppliers/resend-otp', [SupplierController::class, 'resendOtp'])
    ->name('suppliers.resend-otp')
    ->middleware('throttle:3,5');

// Services Routes
Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
Route::get('/services/{id}', [ServicesController::class, 'show'])->name('services.show');

// AJAX endpoint to get variation price by selected value ids
Route::post('/services/{service}/get-variation', function(\Illuminate\Http\Request $request, \App\Models\Service $service){
    $validated = $request->validate([
        'value_ids' => 'required|array',
        'value_ids.*' => 'integer',
    ]);
    $ids = array_map('intval', array_values($validated['value_ids']));
    sort($ids);
    $variation = $service->variations()->where('is_active', true)->get()->first(function($var) use ($ids){
        $existing = array_map('intval', (array) $var->attribute_value_ids);
        sort($existing);
        return $existing === $ids;
    });
    if ($variation) {
        return response()->json(['success' => true, 'price' => (float)$variation->price, 'variation_id' => $variation->id]);
    }
    return response()->json(['success' => false, 'message' => 'لا يوجد سعر لهذه التركيبة'], 404);
})->name('services.get-variation');

// Packages Routes
Route::get('/packages', [PackagesController::class, 'index'])->name('packages.index');
Route::get('/packages/{id}', [PackagesController::class, 'show'])->name('packages.show');

// Gallery Routes
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');

// Contact Route
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Terms and Conditions Route
Route::get('/terms-and-conditions', function () {
    return view('terms');
})->name('terms');

// Privacy Policy Route
Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes with OTP
use App\Http\Controllers\Auth\PasswordResetController;
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetOtp'])->name('password.email');
Route::get('/password/verify-otp', [PasswordResetController::class, 'showOtpVerifyForm'])->name('password.otp.verify');
Route::post('/password/verify-otp', [PasswordResetController::class, 'verifyResetOtp'])->name('password.otp.verify.post');
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// OTP Routes
use App\Http\Controllers\OtpController;
Route::get('/verify-otp', [OtpController::class, 'showVerifyForm'])->name('otp.verify.form');
Route::post('/otp/send', [OtpController::class, 'sendOtp'])->name('otp.send')->middleware('throttle:3,5');
Route::post('/otp/verify', [OtpController::class, 'verifyOtp'])->name('otp.verify')->middleware('throttle:5,1');
Route::post('/otp/resend', [OtpController::class, 'resendOtp'])->name('otp.resend')->middleware('throttle:3,5');
Route::get('/register/complete', function() {
    return view('auth.register-complete');
})->name('register.complete');
Route::post('/register/complete', [OtpController::class, 'completeRegistration'])->name('register.complete.post');

// OTP Test Page (للاختبار فقط - احذفها في الإنتاج)
Route::get('/otp-test', function() {
    return view('otp-test');
})->name('otp.test');

// Email Preview (للمعاينة أثناء التطوير فقط - احذفها في الإنتاج)
Route::get('/preview/email/supplier-otp', function() {
    return view('emails.supplier-otp', [
        'otp' => '845680',
        'supplierName' => 'مورد تجريبي',
        'typeLabel' => 'التحقق من البريد الإلكتروني',
        'expiryMinutes' => 10,
        'email' => 'supplier@example.com',
    ]);
});

// Booking Routes
Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/success/{reference}', [BookingController::class, 'success'])->name('booking.success');
Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('booking.my-bookings')->middleware('auth');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{service}', [CartController::class, 'add'])->name('cart.add');
// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
Route::get('/cart/dropdown', [CartController::class, 'getDropdownHtml'])->name('cart.dropdown');

// Quote Routes (Require Auth)
Route::middleware('auth')->group(function () {
    Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/{quote}', [QuoteController::class, 'show'])->name('quotes.show');
    Route::post('/quotes/checkout', [QuoteController::class, 'checkout'])->name('quotes.checkout');
    Route::get('/quotes/{quote}/download', [QuoteController::class, 'downloadPdf'])->name('quotes.download');
    Route::patch('/quotes/{quote}/notes', [QuoteController::class, 'updateNotes'])->name('quotes.update-notes');
    Route::get('/quotes/{quote}/payment', [QuoteController::class, 'showPayment'])->name('quotes.payment');
    Route::post('/quotes/{quote}/payment', [QuoteController::class, 'processPayment'])->name('quotes.process-payment');
    
    // Wishlist Routes
    Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [\App\Http\Controllers\WishlistController::class, 'store'])->name('wishlist.store');
    Route::post('/wishlist/toggle', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{wishlist}', [\App\Http\Controllers\WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::get('/wishlist/count', [\App\Http\Controllers\WishlistController::class, 'count'])->name('wishlist.count');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Ensure {service} route-model binding only matches numeric IDs
    Route::pattern('service', '[0-9]+');
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Categories Management
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::patch('categories/{category}/toggle-active', [\App\Http\Controllers\Admin\CategoryController::class, 'toggleActive'])->name('categories.toggle-active');
    
    // Packages Management
    Route::resource('packages', AdminPackageController::class);
    Route::delete('packages/{package}/images/{image}', [AdminPackageController::class, 'deleteImage'])->name('packages.images.delete');
    Route::post('packages/{package}/images/{image}/set-thumbnail', [AdminPackageController::class, 'setThumbnail'])->name('packages.images.set-thumbnail');
    
    // Services Management
    // Redirect accidental GET on bulk-delete to services list with a helpful message
    Route::get('services/bulk-delete', function () {
        return redirect()->route('admin.services.index')
            ->with('error', 'يرجى استخدام زر "حذف جماعي" الذي يرسل طلب DELETE.');
    })->name('services.bulk-delete.get');

    Route::resource('services', AdminServiceController::class);
    Route::delete('services/bulk-delete', [AdminServiceController::class, 'bulkDelete'])->name('services.bulk-delete');
    Route::post('services/bulk-update-type', [AdminServiceController::class, 'bulkUpdateType'])->name('services.bulk-update-type');
    Route::post('services/bulk-toggle-status', [AdminServiceController::class, 'bulkToggleStatus'])->name('services.bulk-toggle-status');
    Route::post('services/{service}/toggle-status', [AdminServiceController::class, 'toggleStatus'])->name('services.toggle-status');
    Route::get('services/export/excel', [AdminServiceController::class, 'export'])->name('services.export');
    Route::post('services/import/excel', [AdminServiceController::class, 'import'])->name('services.import');
    Route::get('services/download/template', [AdminServiceController::class, 'downloadTemplate'])->name('services.template');
    
    // Service Images Management
    Route::delete('services/{service}/images/{image}', [AdminServiceController::class, 'deleteImage'])->name('services.images.delete');
    Route::post('services/{service}/images/{image}/set-thumbnail', [AdminServiceController::class, 'setThumbnail'])->name('services.images.set-thumbnail');

    // Attributes Management
    Route::resource('attributes', AdminAttributeController::class);
    Route::post('attributes/{attribute}/values', [AdminAttributeController::class, 'storeValue'])->name('attributes.values.store');
    Route::put('attributes/{attribute}/values/{value}', [AdminAttributeController::class, 'updateValue'])->name('attributes.values.update');
    Route::delete('attributes/{attribute}/values/{value}', [AdminAttributeController::class, 'destroyValue'])->name('attributes.values.destroy');

    // Settings Management
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/toggle-maintenance', [SettingsController::class, 'toggleMaintenance'])->name('settings.toggle-maintenance');
    Route::post('settings/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear-cache');
    Route::get('settings/export', [SettingsController::class, 'exportSettings'])->name('settings.export');
    Route::post('settings/import', [SettingsController::class, 'importSettings'])->name('settings.import');
    Route::post('settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
    Route::post('settings/test-cloud', [SettingsController::class, 'testCloud'])->name('settings.test-cloud');

    // Homepage Management
    Route::get('homepage', [\App\Http\Controllers\Admin\HomepageController::class, 'index'])->name('homepage.index');
    Route::get('homepage/create', [\App\Http\Controllers\Admin\HomepageController::class, 'create'])->name('homepage.create');
    Route::post('homepage', [\App\Http\Controllers\Admin\HomepageController::class, 'store'])->name('homepage.store');
    Route::get('homepage/{section}/edit', [\App\Http\Controllers\Admin\HomepageController::class, 'edit'])->name('homepage.edit');
    Route::put('homepage/{section}', [\App\Http\Controllers\Admin\HomepageController::class, 'update'])->name('homepage.update');
    Route::delete('homepage/{section}', [\App\Http\Controllers\Admin\HomepageController::class, 'destroy'])->name('homepage.destroy');
    Route::post('homepage/update-order', [\App\Http\Controllers\Admin\HomepageController::class, 'updateOrder'])->name('homepage.update-order');
    Route::post('homepage/{section}/toggle', [\App\Http\Controllers\Admin\HomepageController::class, 'toggleActive'])->name('homepage.toggle');
    Route::get('homepage/initialize', [\App\Http\Controllers\Admin\HomepageController::class, 'initializeDefaults'])->name('homepage.initialize');

    // Hero Slides Management
    Route::resource('hero-slides', \App\Http\Controllers\Admin\HeroSlideController::class);
    Route::post('hero-slides/update-order', [\App\Http\Controllers\Admin\HeroSlideController::class, 'updateOrder'])->name('hero-slides.update-order');
    Route::post('hero-slides/{heroSlide}/toggle', [\App\Http\Controllers\Admin\HeroSlideController::class, 'toggleActive'])->name('hero-slides.toggle');

    // Email Test
    Route::get('email-test', [\App\Http\Controllers\Admin\EmailTestController::class, 'index'])->name('email-test.index');
    Route::post('email-test/send', [\App\Http\Controllers\Admin\EmailTestController::class, 'send'])->name('email-test.send');
    Route::get('email-test/config', [\App\Http\Controllers\Admin\EmailTestController::class, 'config'])->name('email-test.config');

    // Service Requests Management
    Route::resource('service-requests', \App\Http\Controllers\Admin\ServiceRequestController::class);
    Route::post('service-requests/{serviceRequest}/accept', [\App\Http\Controllers\Admin\ServiceRequestController::class, 'accept'])->name('service-requests.accept');
    Route::post('service-requests/{serviceRequest}/reject', [\App\Http\Controllers\Admin\ServiceRequestController::class, 'reject'])->name('service-requests.reject');

    // Email Templates Management
    Route::resource('email-templates', \App\Http\Controllers\Admin\EmailTemplateController::class);
    Route::post('email-templates/{emailTemplate}/toggle', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'toggleActive'])->name('email-templates.toggle');
    Route::post('email-templates/{emailTemplate}/send-test', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'sendTest'])->name('email-templates.send-test');
    Route::post('email-templates/{emailTemplate}/duplicate', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'duplicate'])->name('email-templates.duplicate');

    // Email Management (Unified Dashboard)
    Route::get('email-management', [\App\Http\Controllers\Admin\EmailManagementController::class, 'index'])->name('email-management.index');
    Route::post('email-management/send-test', [\App\Http\Controllers\Admin\EmailManagementController::class, 'sendTest'])->name('email-management.send-test');
    Route::get('email-management/statistics', [\App\Http\Controllers\Admin\EmailManagementController::class, 'statistics'])->name('email-management.statistics');

    // OTP Management Routes
    Route::get('otp', [\App\Http\Controllers\Admin\OtpManagementController::class, 'index'])->name('otp.index');
    Route::get('otp/{id}', [\App\Http\Controllers\Admin\OtpManagementController::class, 'show'])->name('otp.show');

    // Reports Management
    Route::get('reports', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [\App\Http\Controllers\Admin\ReportsController::class, 'export'])->name('reports.export');
    Route::get('reports/security', [\App\Http\Controllers\Admin\ReportsController::class, 'security'])->name('reports.security');
    Route::get('reports/security/export', [\App\Http\Controllers\Admin\ReportsController::class, 'exportSecurity'])->name('reports.security.export');
    Route::post('otp/clean-expired', [\App\Http\Controllers\Admin\OtpManagementController::class, 'cleanExpired'])->name('otp.clean-expired');
    Route::post('otp/delete-old', [\App\Http\Controllers\Admin\OtpManagementController::class, 'deleteOld'])->name('otp.delete-old');
    Route::delete('otp/{id}', [\App\Http\Controllers\Admin\OtpManagementController::class, 'destroy'])->name('otp.destroy');
    Route::get('otp/export/csv', [\App\Http\Controllers\Admin\OtpManagementController::class, 'export'])->name('otp.export');
    Route::get('otp/api/statistics', [\App\Http\Controllers\Admin\OtpManagementController::class, 'statistics'])->name('otp.statistics');

    // Service Variations Management
    Route::get('services/{service}/variations', [AdminServiceVariationController::class, 'index'])->name('services.variations.index');
    Route::post('services/{service}/variations', [AdminServiceVariationController::class, 'store'])->name('services.variations.store');
    Route::post('services/{service}/variations/generate', [AdminServiceVariationController::class, 'generate'])->name('services.variations.generate');
    Route::get('services/{service}/variations/{variation}', [AdminServiceVariationController::class, 'edit'])->name('services.variations.edit');
    Route::put('services/{service}/variations/{variation}', [AdminServiceVariationController::class, 'update'])->name('services.variations.update');
    Route::delete('services/{service}/variations/{variation}', [AdminServiceVariationController::class, 'destroy'])->name('services.variations.destroy');
    
    // Bookings Management
    Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::patch('bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::delete('bookings/{booking}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');
    
    // Quotes Management
    Route::get('quotes', [\App\Http\Controllers\Admin\QuoteController::class, 'index'])->name('quotes.index');
    Route::get('quotes/{quote}', [\App\Http\Controllers\Admin\QuoteController::class, 'show'])->name('quotes.show');
    Route::patch('quotes/{quote}/status', [\App\Http\Controllers\Admin\QuoteController::class, 'updateStatus'])->name('quotes.update-status');
    Route::post('quotes/{quote}/send-email', [\App\Http\Controllers\Admin\QuoteController::class, 'sendEmail'])->name('quotes.send-email');
    Route::delete('quotes/{quote}', [\App\Http\Controllers\Admin\QuoteController::class, 'destroy'])->name('quotes.destroy');

    // Payments Management
    Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
    Route::patch('payments/{payment}/status', [\App\Http\Controllers\Admin\PaymentController::class, 'updateStatus'])->name('payments.update-status');

    // Login Activities
    Route::get('login-activities', [\App\Http\Controllers\Admin\LoginActivityController::class, 'index'])->name('login-activities.index');
    
    // Gallery Management
    Route::get('gallery', [AdminGalleryController::class, 'index'])->name('gallery.index');
    Route::get('gallery/create', [AdminGalleryController::class, 'create'])->name('gallery.create');
    Route::post('gallery', [AdminGalleryController::class, 'store'])->name('gallery.store');
    Route::delete('gallery/{gallery}', [AdminGalleryController::class, 'destroy'])->name('gallery.destroy');
    Route::patch('gallery/{gallery}/featured', [AdminGalleryController::class, 'toggleFeatured'])->name('gallery.toggle-featured');
    
    // User Management (Authorized Admin Users)
    Route::prefix('user-management')->name('user-management.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\UserManagementController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-admin', [\App\Http\Controllers\Admin\UserManagementController::class, 'toggleAdmin'])->name('toggle-admin');
        Route::get('/permissions/manage', [\App\Http\Controllers\Admin\UserManagementController::class, 'permissions'])->name('permissions');
    });
    
    // Customer Management
    Route::get('customers', [CustomerManagementController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}', [CustomerManagementController::class, 'show'])->name('customers.show');
    Route::get('customers/{customer}/edit', [CustomerManagementController::class, 'edit'])->name('customers.edit');
    Route::put('customers/{customer}', [CustomerManagementController::class, 'update'])->name('customers.update');
    Route::delete('customers/{customer}', [CustomerManagementController::class, 'destroy'])->name('customers.destroy');
    Route::get('customers/{customer}/quotes', [CustomerManagementController::class, 'quotes'])->name('customers.quotes');
    Route::get('customers/{customer}/payments', [CustomerManagementController::class, 'payments'])->name('customers.payments');
    Route::get('customers/export/all', [CustomerManagementController::class, 'exportCustomers'])->name('customers.export');
    Route::get('customers/{customer}/export', [CustomerManagementController::class, 'exportCustomerDetail'])->name('customers.export-detail');
    Route::get('customers/search', [CustomerManagementController::class, 'search'])->name('customers.search');
    Route::get('customers/analytics', [CustomerManagementController::class, 'analytics'])->name('customers.analytics');
    
    // Suppliers Management
    Route::get('suppliers/create', [\App\Http\Controllers\Admin\SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('suppliers', [\App\Http\Controllers\Admin\SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('suppliers', [\App\Http\Controllers\Admin\SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('suppliers/{supplier}', [\App\Http\Controllers\Admin\SupplierController::class, 'show'])->name('suppliers.show');
    Route::get('suppliers/{supplier}/edit', [\App\Http\Controllers\Admin\SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('suppliers/{supplier}', [\App\Http\Controllers\Admin\SupplierController::class, 'update'])->name('suppliers.update');
    Route::post('suppliers/{supplier}/approve', [\App\Http\Controllers\Admin\SupplierController::class, 'approve'])->name('suppliers.approve');
    Route::post('suppliers/{supplier}/reject', [\App\Http\Controllers\Admin\SupplierController::class, 'reject'])->name('suppliers.reject');
    Route::post('suppliers/{supplier}/suspend', [\App\Http\Controllers\Admin\SupplierController::class, 'suspend'])->name('suppliers.suspend');
    Route::post('suppliers/{supplier}/activate', [\App\Http\Controllers\Admin\SupplierController::class, 'activate'])->name('suppliers.activate');
    Route::post('suppliers/{supplier}/pending', [\App\Http\Controllers\Admin\SupplierController::class, 'pending'])->name('suppliers.pending');
    Route::delete('suppliers/{supplier}', [\App\Http\Controllers\Admin\SupplierController::class, 'destroy'])->name('suppliers.destroy');
    Route::get('suppliers/{supplier}/download/{type}', [\App\Http\Controllers\Admin\SupplierController::class, 'downloadDocument'])->name('suppliers.download');
    Route::post('suppliers/{supplier}/add-service', [\App\Http\Controllers\Admin\SupplierController::class, 'addService'])->name('suppliers.add-service');
    Route::delete('suppliers/{supplier}/services/{serviceId}', [\App\Http\Controllers\Admin\SupplierController::class, 'removeService'])->name('suppliers.remove-service');
    
    // Orders Management
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'update'])->name('orders.update');
    Route::delete('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('orders.destroy');
});

// ===============================
// Lightweight API: Services by Category
// ===============================
// تُستخدم في واجهة مسؤول الموردين لتحميل الخدمات بعد اختيار الفئة
Route::get('/api/services', function (\Illuminate\Http\Request $request) {
    $categoryId = $request->query('category_id');
    $query = \App\Models\Service::query()->where('is_active', true);
    if ($categoryId) {
        $query->where('category_id', (int) $categoryId);
    }
    $services = $query->select('id', 'name', 'subtitle', 'category_id')
        ->orderBy('name')
        ->limit(500)
        ->get();
    return response()->json($services);
});

// ====================================
// Supplier Dashboard Routes
// ====================================
Route::prefix('supplier')->name('supplier.')->group(function () {
    // Guest routes (login) - redirect to dashboard if already logged in
    Route::middleware('supplier.guest')->group(function () {
        Route::get('/login', [SupplierAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [SupplierAuthController::class, 'login'])->name('login.post');
        Route::get('/forgot-password', [SupplierAuthController::class, 'showForgotPasswordForm'])->name('forgot-password');
        Route::post('/forgot-password', [SupplierAuthController::class, 'sendResetOtp'])->name('forgot-password.post');
        Route::get('/verify-reset-otp', [SupplierAuthController::class, 'showVerifyOtpForm'])->name('verify-reset-otp');
        Route::post('/verify-reset-otp', [SupplierAuthController::class, 'verifyResetOtp'])->name('verify-reset-otp.post');
        Route::get('/reset-password', [SupplierAuthController::class, 'showResetPasswordForm'])->name('reset-password');
        Route::post('/reset-password', [SupplierAuthController::class, 'resetPassword'])->name('reset-password.post');
    });
    
    // Authenticated supplier routes
    Route::middleware('supplier')->group(function () {
        // Dashboard
        Route::get('/', [SupplierDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard.index');
        
        // Services
        Route::get('/services', [SupplierDashboardController::class, 'services'])->name('services.index');
        Route::get('/services/{id}', [SupplierDashboardController::class, 'showService'])->name('services.show');
        Route::patch('/services/{id}/toggle', [SupplierDashboardController::class, 'toggleServiceAvailability'])->name('services.toggle');
        
        // Bookings
        Route::get('/bookings', [SupplierDashboardController::class, 'bookings'])->name('bookings.index');
        Route::get('/bookings/{booking}', [SupplierDashboardController::class, 'showBooking'])->name('bookings.show');
        Route::patch('/bookings/{booking}/status', [SupplierDashboardController::class, 'updateBookingStatus'])->name('bookings.update-status');
        
        // Customers
        Route::get('/customers', [SupplierDashboardController::class, 'customers'])->name('customers.index');
        Route::get('/customers/{id}', [SupplierDashboardController::class, 'showCustomer'])->name('customers.show');
        
        // Profile
        Route::get('/profile', [SupplierDashboardController::class, 'profile'])->name('profile.index');
        Route::put('/profile', [SupplierDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::get('/profile/password', [SupplierDashboardController::class, 'editPassword'])->name('profile.password');
        Route::put('/profile/password', [SupplierDashboardController::class, 'updatePassword'])->name('profile.password.update');
        
        // Reports
        Route::get('/reports', [SupplierDashboardController::class, 'reports'])->name('reports.index');

        // Quotes (Supplier-visible quotes that include supplier services)
        Route::get('/quotes', [SupplierDashboardController::class, 'quotes'])->name('quotes.index');
        Route::get('/quotes/{quote}', [SupplierDashboardController::class, 'showQuote'])->name('quotes.show');
        Route::post('/quotes/{quote}/accept', [SupplierDashboardController::class, 'acceptQuote'])->name('quotes.accept');
        Route::post('/quotes/{quote}/reject', [SupplierDashboardController::class, 'rejectQuote'])->name('quotes.reject');

        // Logout
        Route::post('/logout', [SupplierAuthController::class, 'logout'])->name('logout');
    });
});
