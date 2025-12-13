<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [

        'category_id',
        'name',
        'subtitle',
        'description',
        'marketing_description',
        'what_we_offer',
        'why_choose_us',
        'meta_description',
        'price',
        'service_type',
        'duration',
        'type',
        'features',
        'custom_fields',
        'image',
        'is_active',
        'has_variations',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
        'custom_fields' => 'array',
        'price' => 'decimal:2',
        'has_variations' => 'boolean',
    ];

    /**
     * Get the category that owns the service
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get suppliers offering this service
     */
    public function suppliers()
    {
        return $this->belongsToMany(
            \App\Models\Supplier::class,
            'supplier_services',
            'service_id',
            'supplier_id'
        )->withPivot('category_id', 'is_available')
         ->withTimestamps();
    }

    /**
     * Get bookings for this service
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all images for this service
     */
    public function images()
    {
        return $this->hasMany(ServiceImage::class)->orderBy('sort_order');
    }

    /**
     * Get the thumbnail image for this service
     */
    public function thumbnailImage()
    {
        return $this->hasOne(ServiceImage::class)->where('is_thumbnail', true);
    }

    /**
     * Get thumbnail URL (fallback to old 'image' column if no thumbnails exist)
     */
    public function getThumbnailUrlAttribute()
    {
        // أولاً: جرب الحصول على صورة محددة كـ thumbnail
        $thumbnail = $this->thumbnailImage;
        if ($thumbnail) {
            return $thumbnail->image_url;
        }

        // ثانياً: جرب أول صورة في المعرض
        $firstImage = $this->images()->first();
        if ($firstImage) {
            return $firstImage->image_url;
        }

        // ثالثاً: استخدم العمود القديم 'image'
        if ($this->image) {
            return \Storage::url($this->image);
        }

        // أخيراً: صورة افتراضية
        return 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80';
    }

    /**
     * Attributes related to service (for variations)
     */
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_service');
    }

    /**
     * Variations for this service
     */
    public function variations()
    {
        return $this->hasMany(ServiceVariation::class);
    }

    /**
     * Scope for active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if service is variable (has variations)
     */
    public function isVariable()
    {
        return $this->service_type === 'variable';
    }

    /**
     * Check if service is simple (fixed price)
     */
    public function isSimple()
    {
        return $this->service_type === 'simple';
    }

    /**
     * Get min price (for variable services)
     */
    public function getMinPriceAttribute()
    {
        if ($this->isVariable()) {
            $minVariation = $this->variations()->active()->orderBy('price')->first();
            return $minVariation ? $minVariation->active_price : 0;
        }
        return $this->price ?? 0;
    }

    /**
     * Get max price (for variable services)
     */
    public function getMaxPriceAttribute()
    {
        if ($this->isVariable()) {
            $maxVariation = $this->variations()->active()->orderByDesc('price')->first();
            return $maxVariation ? $maxVariation->active_price : 0;
        }
        return $this->price ?? 0;
    }

    /**
     * Get price range formatted
     */
    public function getPriceRangeAttribute()
    {
        if ($this->isVariable()) {
            $min = $this->min_price;
            $max = $this->max_price;
            if ($min == $max) {
                return number_format($min, 2) . ' ر.س';
            }
            return number_format($min, 2) . ' - ' . number_format($max, 2) . ' ر.س';
        }
        return number_format($this->price ?? 0, 2) . ' ر.س';
    }

    /**
     * Get wishlist entries for this service
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get users who wishlisted this service
     */
    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }
}
