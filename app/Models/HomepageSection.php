<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    protected $fillable = [
        'section_key',
        'title',
        'subtitle',
        'content',
        'image',
        'video_url',
        'background_type',
        'background_value',
        'is_active',
        'order',
        'settings'
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean'
    ];

    // Get active sections ordered
    public static function active()
    {
        return self::where('is_active', true)->orderBy('order')->get();
    }

    // Get section by key
    public static function getByKey($key)
    {
        return self::where('section_key', $key)->first();
    }

    // Get background style
    public function getBackgroundStyle()
    {
        if (!$this->background_value) {
            return '';
        }

        switch ($this->background_type) {
            case 'color':
                return "background: {$this->background_value};";
            case 'gradient':
                return "background: {$this->background_value};";
            case 'image':
                return "background-image: url('" . asset('storage/' . $this->background_value) . "'); background-size: cover; background-position: center;";
            default:
                return '';
        }
    }
}
