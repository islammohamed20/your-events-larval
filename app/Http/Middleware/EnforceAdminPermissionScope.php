<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceAdminPermissionScope
{
    /**
     * Apply a default-deny scope for admins with custom granular permissions.
     *
     * Backward compatibility: admins without stored permissions keep full access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->isAdmin()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا القسم');
        }

        $permissions = $user->permissions;

        if (empty($permissions) || ! is_array($permissions)) {
            return $next($request);
        }

        if (in_array('manage_users', $permissions, true)) {
            return $next($request);
        }

        $routeName = (string) optional($request->route())->getName();

        if ($routeName === '' || ! str_starts_with($routeName, 'admin.')) {
            return $next($request);
        }

        $adminRoute = substr($routeName, 6);

        if (
            $adminRoute === 'dashboard'
            || str_starts_with($adminRoute, 'notifications.')
            || str_starts_with($adminRoute, 'force-password.')
        ) {
            return $next($request);
        }

        if (
            str_starts_with($adminRoute, 'email-management.')
            || str_starts_with($adminRoute, 'email-templates.')
            || str_starts_with($adminRoute, 'email-test.')
        ) {
            if (in_array('manage_emails', $permissions, true)) {
                return $next($request);
            }

            abort(403, 'غير مصرح لك بالوصول إلى هذا القسم');
        }

        if (str_starts_with($adminRoute, 'whatsapp.')) {
            if (in_array('manage_whatsapp', $permissions, true)) {
                return $next($request);
            }

            abort(403, 'غير مصرح لك بالوصول إلى هذا القسم');
        }

        if (str_starts_with($adminRoute, 'categories.')) {
            if (in_array('manage_categories', $permissions, true)) {
                return $next($request);
            }

            abort(403, 'غير مصرح لك بالوصول إلى هذا القسم');
        }

        if (str_starts_with($adminRoute, 'packages.')) {
            if (in_array('manage_packages', $permissions, true)) {
                return $next($request);
            }

            abort(403, 'غير مصرح لك بالوصول إلى هذا القسم');
        }

        if (str_starts_with($adminRoute, 'services.') || str_starts_with($adminRoute, 'attributes.')) {
            if (in_array('manage_services', $permissions, true)) {
                return $next($request);
            }

            abort(403, 'غير مصرح لك بالوصول إلى هذا القسم');
        }

        if (str_starts_with($adminRoute, 'customers.')) {
            return $this->authorizeCustomerRoute($adminRoute, $permissions)
                ? $next($request)
                : abort(403, 'غير مصرح لك بالوصول إلى هذا القسم');
        }

        if (str_starts_with($adminRoute, 'bookings.')) {
            return $this->authorizeBookingRoute($adminRoute, $permissions)
                ? $next($request)
                : abort(403, 'غير مصرح لك بالوصول إلى هذا القسم');
        }

        if (str_starts_with($adminRoute, 'quotes.')) {
            return $this->authorizeQuoteRoute($adminRoute, $permissions)
                ? $next($request)
                : abort(403, 'غير مصرح لك بالوصول إلى هذا القسم');
        }

        abort(403, 'غير مصرح لك بالوصول إلى هذا القسم');
    }

    private function authorizeCustomerRoute(string $route, array $permissions): bool
    {
        if (str_starts_with($route, 'customers.edit') || str_starts_with($route, 'customers.update')) {
            return in_array('customers.edit', $permissions, true) || in_array('manage_customers', $permissions, true);
        }

        if (str_starts_with($route, 'customers.destroy')) {
            return in_array('customers.delete', $permissions, true) || in_array('manage_customers', $permissions, true);
        }

        if (str_starts_with($route, 'customers.reset-password')) {
            return in_array('customers.reset_password', $permissions, true) || in_array('manage_customers', $permissions, true);
        }

        if (str_starts_with($route, 'customers.export')) {
            return in_array('customers.export', $permissions, true) || in_array('manage_customers', $permissions, true);
        }

        return in_array('customers.view', $permissions, true) || in_array('manage_customers', $permissions, true);
    }

    private function authorizeBookingRoute(string $route, array $permissions): bool
    {
        if (str_starts_with($route, 'bookings.update-status')) {
            return in_array('bookings.edit', $permissions, true) || in_array('manage_bookings', $permissions, true);
        }

        if (str_starts_with($route, 'bookings.destroy')) {
            return in_array('bookings.delete', $permissions, true) || in_array('manage_bookings', $permissions, true);
        }

        return in_array('bookings.view', $permissions, true) || in_array('manage_bookings', $permissions, true);
    }

    private function authorizeQuoteRoute(string $route, array $permissions): bool
    {
        if (
            str_starts_with($route, 'quotes.update-status')
            || str_starts_with($route, 'quotes.send-email')
            || str_starts_with($route, 'quotes.convert-paid')
        ) {
            return in_array('quotes.edit', $permissions, true) || in_array('manage_bookings', $permissions, true);
        }

        if (str_starts_with($route, 'quotes.destroy')) {
            return in_array('quotes.delete', $permissions, true) || in_array('manage_bookings', $permissions, true);
        }

        return in_array('quotes.view', $permissions, true) || in_array('manage_bookings', $permissions, true);
    }
}
