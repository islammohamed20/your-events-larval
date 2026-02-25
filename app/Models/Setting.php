<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group'];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value, $type = 'text', $group = 'general')
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );

        Cache::forget("setting_{$key}");

        return $setting;
    }

    /**
     * Get all settings as key-value pairs
     */
    public static function all_cached()
    {
        return Cache::remember('all_settings', 3600, function () {
            return self::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get multiple settings by keys
     */
    public static function getSettings($keys = [])
    {
        return Cache::remember('settings_' . md5(implode(',', $keys)), 3600, function () use ($keys) {
            if (empty($keys)) {
                return self::all()->pluck('value', 'key')->toArray();
            }
            
            $settings = self::whereIn('key', $keys)->pluck('value', 'key')->toArray();
            
            // Add defaults for missing keys
            foreach ($keys as $key) {
                if (!isset($settings[$key])) {
                    $settings[$key] = null;
                }
            }
            
            return $settings;
        });
    }

    /**
     * Clear settings cache
     */
    public static function clearCache()
    {
        Cache::forget('all_settings');
        self::all()->each(function ($setting) {
            Cache::forget("setting_{$setting->key}");
        });
    }
}
