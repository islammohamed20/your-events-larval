<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image',
        'button_text',
        'button_link',
        'button_style',
        'transition_effect',
        'duration',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Get active slides ordered
    public static function active()
    {
        return self::where('is_active', true)->orderBy('order')->get();
    }

    // Scope for active slides
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for ordered
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
