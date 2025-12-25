<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Supplier extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'email',
        'password',
        'supplier_type',
        'status',
        'primary_phone',
        'secondary_phone',
        'description',
        'address',
        'headquarters_city',
        'commercial_register',
        'tax_number',
        'commercial_register_file',
        'tax_certificate_file',
        'company_profile_file',
        'portfolio_files',
        'services_offered',
        'social_media',
        'terms_accepted',
        'privacy_accepted',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'social_media' => 'array',
        'portfolio_files' => 'array',
        'services_offered' => 'array',
    ];

    /**
     * Services offered by this supplier (Many-to-Many via supplier_services)
     */
    public function services()
    {
        return $this->belongsToMany(
            Service::class,
            'supplier_services',
            'supplier_id',
            'service_id'
        )->withPivot('category_id', 'is_available')
            ->withTimestamps();
    }

    /**
     * Categories selected by this supplier (Many-to-Many via supplier_services)
     */
    public function serviceCategories()
    {
        return $this->belongsToMany(
            Category::class,
            'supplier_services',
            'supplier_id',
            'category_id'
        )->distinct();
    }

    /**
     * Pivot details for supplier services
     */
    public function supplierServices()
    {
        return $this->hasMany(SupplierService::class, 'supplier_id');
    }

    /**
     * Activity logs related to this supplier (polymorphic)
     */
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    /**
     * Competitive orders received by this supplier
     */
    public function receivedOrders()
    {
        return $this->belongsToMany(CompetitiveOrder::class, 'order_notifications', 'supplier_id', 'competitive_order_id')
            ->withPivot('notified_at', 'viewed_at', 'responded_at', 'response')
            ->withTimestamps();
    }

    /**
     * Orders accepted by this supplier
     */
    public function acceptedOrders()
    {
        return $this->hasMany(CompetitiveOrder::class, 'accepted_by_supplier_id');
    }

    /**
     * Computed HTML badge for supplier status
     */
    public function getStatusBadgeAttribute()
    {
        $status = $this->status;
        switch ($status) {
            case 'pending':
                return '<span class="badge bg-warning">قيد المراجعة</span>';
            case 'approved':
                return '<span class="badge bg-success">مقبول</span>';
            case 'rejected':
                return '<span class="badge bg-danger">مرفوض</span>';
            case 'suspended':
                return '<span class="badge bg-warning">معلق</span>';
            default:
                return '<span class="badge bg-secondary">'.e($status).'</span>';
        }
    }
}
