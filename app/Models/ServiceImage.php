<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ServiceImage extends Model
{
    protected $fillable = [
        'service_id',
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
     * العلاقة مع الخدمة
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
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
            if (Storage::exists($image->image_path)) {
                Storage::delete($image->image_path);
            }
        });
    }
}
