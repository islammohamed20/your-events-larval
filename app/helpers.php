<?php

use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    /**
     * Get a setting value from settings.json file
     *
     * @param  string  $key  Setting key
     * @param  mixed  $default  Default value if setting not found
     * @return mixed
     */
    function setting($key, $default = null)
    {
        static $settings = null;

        if ($settings === null) {
            $settings = Cache::remember('site_settings', 3600, function () {
                $settingsFile = storage_path('app/settings.json');

                if (file_exists($settingsFile)) {
                    $data = json_decode(file_get_contents($settingsFile), true);

                    return is_array($data) ? $data : [];
                }

                return [];
            });
        }

        return $settings[$key] ?? $default;
    }
}

if (! function_exists('settings')) {
    /**
     * Get all settings from settings.json file
     *
     * @return array
     */
    function settings()
    {
        return Cache::remember('site_settings', 3600, function () {
            $settingsFile = storage_path('app/settings.json');

            if (file_exists($settingsFile)) {
                $data = json_decode(file_get_contents($settingsFile), true);

                return is_array($data) ? $data : [];
            }

            return [];
        });
    }
}

if (! function_exists('get_setting')) {
    /**
     * Alias for setting() function
     *
     * @param  string  $key  Setting key
     * @param  mixed  $default  Default value if setting not found
     * @return mixed
     */
    function get_setting($key, $default = null)
    {
        return setting($key, $default);
    }
}
