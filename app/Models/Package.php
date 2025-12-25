<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'persons_min',
        'persons_max',
        'description',
        'features',
        'attributes',
        'image',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'attributes' => 'array',
        'price' => 'decimal:2',
        'persons_min' => 'integer',
        'persons_max' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get persons range text
     */
    public function getPersonsRangeAttribute()
    {
        if ($this->persons_min && $this->persons_max) {
            return $this->persons_min.' إلى '.$this->persons_max.' شخص';
        } elseif ($this->persons_min) {
            return $this->persons_min.' شخص';
        } elseif ($this->persons_max) {
            return 'حتى '.$this->persons_max.' شخص';
        }

        return null;
    }

    /**
     * Get all images for this package
     */
    public function images()
    {
        return $this->hasMany(PackageImage::class)->orderBy('sort_order');
    }

    /**
     * Get the thumbnail image for this package
     */
    public function thumbnailImage()
    {
        return $this->hasOne(PackageImage::class)->where('is_thumbnail', true);
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
            return Storage::url($this->image);
        }

        // أخيراً: صورة افتراضية
        return 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80';
    }

    /**
     * Get bookings for this package
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope for active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
