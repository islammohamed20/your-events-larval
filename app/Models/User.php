<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'role',
        'is_admin',
        'status',
        'registration_source',
        'card_type',
        'card_holder_name',
        'card_last_four',
        'card_expiry_month',
        'card_expiry_year',
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
