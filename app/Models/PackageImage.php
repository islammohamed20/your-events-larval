<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PackageImage extends Model
{
    protected $fillable = [
        'package_id',
        'image_path',
        'alt_text',
        'is_thumbnail',
        'sort_order',
    ];

    protected $casts = [
        'is_thumbnail' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * العلاقة مع الباقة
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * الحصول على URL كامل للصورة
     */
    public function getImageUrlAttribute()
    {
        return Storage::url($this->image_path);
    }

    /**
     * حذف الصورة من التخزين عند حذف السجل
     */
    protected static function booted()
    {
        static::deleting(function ($image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        });
    }
}
