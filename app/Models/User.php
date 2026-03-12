<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ADMIN_PERMISSIONS = [
        'manage_users',
        'manage_emails',
        'manage_services',
        'manage_categories',
        'manage_packages',
        'manage_customers',
        'manage_bookings',
        'customers.view',
        'customers.edit',
        'customers.delete',
        'customers.export',
        'customers.reset_password',
        'bookings.view',
        'bookings.edit',
        'bookings.delete',
        'quotes.view',
        'quotes.edit',
        'quotes.delete',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'company_name',
        'tax_number',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'is_admin',
        'status',
        'registration_source',
        'must_change_password',
        'logout_other_devices',
        'session_version',
        'card_type',
        'card_holder_name',
        'card_last_four',
        'card_expiry_month',
        'card_expiry_year',
        'permissions',
    ];

    /**
     * The attributes that have default values.
     *
     * @var array
     */
    protected $attributes = [
        'role' => 'user',
        'is_admin' => false,
        'status' => 'active',
        'must_change_password' => false,
        'logout_other_devices' => false,
        'session_version' => 1,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'must_change_password' => 'boolean',
            'logout_other_devices' => 'boolean',
            'session_version' => 'integer',
            'permissions' => 'array',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->is_admin || $this->role === 'admin';
    }

    /**
     * Check granular admin permission.
     */
    public function hasAdminPermission(?string $permission): bool
    {
        if (! $this->isAdmin()) {
            return false;
        }

        if (! $permission) {
            return true;
        }

        $permissions = $this->permissions;

        // Backward compatibility: old admin users without stored permissions keep access.
        if (empty($permissions) || ! is_array($permissions)) {
            return true;
        }

        if (in_array($permission, $permissions, true)) {
            return true;
        }

        // manage_users is the top-level admin permission for this panel.
        if (in_array('manage_users', $permissions, true)) {
            return true;
        }

        // Legacy broad permissions still grant granular access.
        if (str_starts_with($permission, 'customers.') && in_array('manage_customers', $permissions, true)) {
            return true;
        }

        if ((str_starts_with($permission, 'bookings.') || str_starts_with($permission, 'quotes.')) && in_array('manage_bookings', $permissions, true)) {
            return true;
        }

        if (str_starts_with($permission, 'users.') && in_array('manage_users', $permissions, true)) {
            return true;
        }

        if (str_starts_with($permission, 'emails.') && in_array('manage_emails', $permissions, true)) {
            return true;
        }

        if (str_starts_with($permission, 'services.') && in_array('manage_services', $permissions, true)) {
            return true;
        }

        if (str_starts_with($permission, 'categories.') && in_array('manage_categories', $permissions, true)) {
            return true;
        }

        if (str_starts_with($permission, 'packages.') && in_array('manage_packages', $permissions, true)) {
            return true;
        }

        return false;
    }

    /**
     * Get the is_admin attribute
     */
    public function getIsAdminAttribute($value)
    {
        return $value || $this->role === 'admin';
    }

    /**
     * Get bookings for this user
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get quotes for this user
     */
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * Get wishlist items for this user
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get wishlist services for this user
     */
    public function wishlistServices()
    {
        return $this->belongsToMany(Service::class, 'wishlists')->withTimestamps();
    }

    /**
     * Check if service is in user's wishlist
     */
    public function hasInWishlist($serviceId)
    {
        return $this->wishlists()->where('service_id', $serviceId)->exists();
    }

    /**
     * Get supplier services (services provided by this supplier)
     */
    public function services()
    {
        return $this->hasMany(\App\Models\SupplierService::class);
    }

    /**
     * Orders created by this customer
     */
    public function customersOrders()
    {
        return $this->hasMany(\App\Models\Order::class, 'customer_id');
    }

    /**
     * Orders assigned to this supplier
     */
    public function supplierOrders()
    {
        return $this->hasMany(\App\Models\Order::class, 'supplier_id');
    }

    /**
     * Get passkeys for this user
     */
    public function passkeys()
    {
        return $this->hasMany(Passkey::class, 'user_id')->where('user_type', 'user');
    }

    /**
     * Supplier order statuses (orders sent to this supplier)
     */
    public function orderStatuses()
    {
        return $this->hasMany(\App\Models\SupplierOrderStatus::class, 'supplier_id');
    }

    /**
     * Customer profile (if this user is a customer)
     */
    public function customerProfile()
    {
        return $this->hasOne(Customer::class);
    }
}
