<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        // Accept both legacy and new field names; file_path preferred
        'file_path',
        'path', // legacy column (will be ignored on insert if not present)
        'type',
        'category',
        'is_featured',
        'file_size',
        'mime_type',
        'alt_text',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'file_size' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Scope for images only
     */
    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    /**
     * Scope for videos only
     */
    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    /**
     * Scope for featured items
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get the file URL
     */
    public function getFileUrlAttribute()
    {
        $fp = $this->file_path ?? $this->path ?? null;
        if (! $fp) {
            return null;
        }

        return Storage::url($fp);
    }

    /**
     * Accessor unify file_path (fallback to legacy path)
     */
    public function getFilePathAttribute($value)
    {
        return $value ?? ($this->attributes['path'] ?? null);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (! $this->file_size) {
            return 'غير محدد';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    /**
     * Get category display name
     */
    public function getCategoryDisplayAttribute()
    {
        $categories = [
            'events' => 'الفعاليات',
            'vr_experiences' => 'تجارب الواقع الافتراضي',
            'behind_scenes' => 'خلف الكواليس',
            'client_moments' => 'لحظات العملاء',
            'equipment' => 'المعدات',
            'team' => 'الفريق',
            'other' => 'أخرى',
        ];

        return $categories[$this->category] ?? 'غير محدد';
    }

    /**
     * Scope for specific category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for ordered items
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured()
    {
        $this->update(['is_featured' => ! $this->is_featured]);

        return $this;
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Delete file when model is deleted
        static::deleting(function ($gallery) {
            if ($gallery->file_path && Storage::disk('public')->exists($gallery->file_path)) {
                Storage::disk('public')->delete($gallery->file_path);
            }
        });

        // Set sort order when creating
        static::creating(function ($gallery) {
            if (! $gallery->sort_order) {
                $gallery->sort_order = (int) static::max('sort_order') + 1;
            }
            // Ensure legacy 'path' column is populated if present and empty
            if (empty($gallery->path) && ! empty($gallery->file_path)) {
                $gallery->path = $gallery->file_path; // for backward compatibility with old schema
            }
            if (empty($gallery->file_path) && ! empty($gallery->path)) {
                $gallery->file_path = $gallery->path; // normalize forward
            }
        });
    }
}
