<?php

if (!function_exists('setting')) {
    /**
     * Get a setting value from storage/app/settings.json
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        static $settings = null;

        if ($settings === null) {
            $path = storage_path('app/settings.json');
            if (file_exists($path)) {
                $settings = json_decode(file_get_contents($path), true);
            } else {
                $settings = [];
            }
        }

        return $settings[$key] ?? $default;
    }
}
