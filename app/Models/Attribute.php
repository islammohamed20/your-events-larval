<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot function to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attribute) {
            if (empty($attribute->slug)) {
                $attribute->slug = Str::slug($attribute->name);
            }
        });

        static::updating(function ($attribute) {
            if ($attribute->isDirty('name') && empty($attribute->slug)) {
                $attribute->slug = Str::slug($attribute->name);
            }
        });
    }

    /**
     * Get the values for this attribute
     */
    public function values()
    {
        return $this->hasMany(AttributeValue::class)->orderBy('order');
    }

    /**
     * Get services using this attribute
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'attribute_service')
            ->withTimestamps()
            ->withPivot('order')
            ->orderBy('attribute_service.order');
    }

    /**
     * Scope for active attributes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered attributes
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
