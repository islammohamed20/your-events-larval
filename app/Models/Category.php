<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_en',
        'supplier_form_name',
        'book_from_service',
        'description',
        'icon',
        'color',
        'icon_png',
        'image',
        'banner',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'book_from_service' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get services in this category
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get suppliers offering services in this category
     */
    public function suppliers()
    {
        return $this->belongsToMany(
            \App\Models\Supplier::class,
            'supplier_services',
            'category_id',
            'supplier_id'
        )->distinct();
    }

    /**
     * Get active services count
     */
    public function getActiveServicesCountAttribute()
    {
        return $this->services()->where('is_active', true)->count();
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('name', 'asc');
    }
}
