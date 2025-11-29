<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $guard = 'supplier';

    protected $fillable = [
        'supplier_type',
        'name',
        'commercial_register',
        'tax_number',
        'headquarters_city',
        'description',
        'services_offered',
        'commercial_register_file',
        'tax_certificate_file',
        'company_profile_file',
        'portfolio_files',
        'primary_phone',
        'secondary_phone',
        'email',
        'password',
        'social_media',
        'address',
        'status',
        'rejection_reason',
        'email_verified_at',
        'terms_accepted',
        'privacy_accepted',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'services_offered' => 'array',
        'portfolio_files' => 'array',
        'social_media' => 'array',
        'email_verified_at' => 'datetime',
        'terms_accepted' => 'boolean',
        'privacy_accepted' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">قيد المراجعة</span>',
            'approved' => '<span class="badge bg-success">موافق عليه</span>',
            'rejected' => '<span class="badge bg-danger">مرفوض</span>',
            'suspended' => '<span class="badge bg-secondary">موقوف</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'قيد المراجعة',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'suspended' => 'موقوف',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get supplier type text
     */
    public function getSupplierTypeTextAttribute()
    {
        return $this->supplier_type === 'individual' ? 'فرد' : 'منشأة';
    }

    /**
     * Get services offered as text
     */
    public function getServicesTextAttribute()
    {
        if (!$this->services_offered) {
            return 'لا توجد خدمات محددة';
        }

        $services = [
            'photography' => 'التصوير والفيديو',
            'catering' => 'الضيافة والتموين',
            'entertainment' => 'الألعاب الترفيهية والحركية',
            'gifts' => 'تجهيز وتوفير الهدايا',
            'logistics' => 'الدعم اللوجستي وسيارات VIP',
            'handicrafts' => 'الحِرَف اليدوية',
        ];

        $selected = [];
        foreach ($this->services_offered as $service) {
            if (isset($services[$service])) {
                $selected[] = $services[$service];
            }
        }

        return implode('، ', $selected);
    }

    /**
     * Scope for approved suppliers
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending suppliers
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if supplier is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if supplier is individual
     */
    public function isIndividual()
    {
        return $this->supplier_type === 'individual';
    }

    /**
     * Check if supplier is company
     */
    public function isCompany()
    {
        return $this->supplier_type === 'company';
    }

    /**
     * Activity logs relation
     */
    public function activityLogs()
    {
        return $this->morphMany(\App\Models\ActivityLog::class, 'subject')->latest();
    }

    /**
     * Model events for logging
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($supplier) {
            \App\Models\ActivityLog::record($supplier, 'created', 'تم تسجيل مورد جديد', [
                'status' => $supplier->status,
                'email' => $supplier->email,
                'supplier_type' => $supplier->supplier_type,
            ]);
        });

        static::updated(function ($supplier) {
            $changes = $supplier->getChanges();
            unset($changes['updated_at']);
            if (!empty($changes)) {
                \App\Models\ActivityLog::record($supplier, 'updated', 'تم تعديل بيانات المورد', [
                    'changes' => $changes,
                ]);
            }
        });
    }
}
