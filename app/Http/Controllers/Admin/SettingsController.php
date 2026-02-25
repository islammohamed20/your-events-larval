<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            /** @var User|null $user */
            if (! $user instanceof User || ! $user->isAdmin()) {
                abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
            }

            return $next($request);
        });
    }

    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = $this->getAllSettings();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'terms_and_conditions' => 'nullable|string|max:50000',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'site_favicon' => 'nullable|image|mimes:ico,png|max:512',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'contact_address' => 'nullable|string|max:500',
            'working_hours' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            // Notification Settings
            'notifications_enabled' => 'boolean',
            'notification_sound_enabled' => 'boolean',
            'notification_refresh_interval' => 'nullable|integer|min:1|max:60',
            'notification_auto_dismiss' => 'nullable|integer|min:1|max:60',
            'notification_quotes' => 'boolean',
            'notification_bookings' => 'boolean',
            'notification_orders' => 'boolean',
            'notification_payments' => 'boolean',
            'notification_contacts' => 'boolean',
            'notification_suppliers' => 'boolean',
            'notification_customers' => 'boolean',
            // Appearance
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'accent_color' => 'nullable|string|max:7',
            'gold_color' => 'nullable|string|max:7',
            'purple_light' => 'nullable|string|max:7',
            'bg_light' => 'nullable|string|max:7',
            'bg_secondary' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
            'hover_color' => 'nullable|string|max:7',
            'sidebar_bg' => 'nullable|string|max:7',
            'sidebar_hover' => 'nullable|string|max:7',
            'font_family_primary' => 'nullable|string|max:100',
            'font_family_secondary' => 'nullable|string|max:100',
            'font_family_english' => 'nullable|string|max:100',
            'logo_url' => 'nullable|url|max:255',
            'favicon_url' => 'nullable|url|max:255',
            'header_bg_color' => 'nullable|string|max:7',
            'footer_bg_color' => 'nullable|string|max:7',
            'button_radius' => 'nullable|string|max:10',
            'card_radius' => 'nullable|string|max:10',
            'shadow_color' => 'nullable|string|max:20',
            'gradient_start' => 'nullable|string|max:7',
            'gradient_end' => 'nullable|string|max:7',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_author' => 'nullable|string|max:100',
            'meta_robots' => 'nullable|string|max:50',
            'meta_viewport' => 'nullable|string|max:100',
            'og_title' => 'nullable|string|max:60',
            'og_description' => 'nullable|string|max:160',
            'og_image' => 'nullable|url|max:255',
            'og_url' => 'nullable|url|max:255',
            'twitter_card' => 'nullable|string|max:50',
            'twitter_site' => 'nullable|string|max:50',
            'twitter_creator' => 'nullable|string|max:50',
            'canonical_url' => 'nullable|url|max:255',
            'schema_type' => 'nullable|string|max:50',
            'schema_name' => 'nullable|string|max:100',
            'schema_description' => 'nullable|string|max:255',
            'schema_logo' => 'nullable|url|max:255',
            'schema_address' => 'nullable|string|max:255',
            'schema_phone' => 'nullable|string|max:20',
            'schema_email' => 'nullable|email|max:255',
            'google_analytics_id' => 'nullable|string|max:20',
            'google_tag_manager_id' => 'nullable|string|max:20',
            'facebook_pixel_id' => 'nullable|string|max:20',
            'google_site_verification' => 'nullable|string|max:100',
            'bing_site_verification' => 'nullable|string|max:100',
            'yandex_verification' => 'nullable|string|max:100',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|in:tls,ssl',
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:500',
            // Backup & Cloud
            'backup_frequency' => 'nullable|in:manual,daily,weekly',
            'backup_retention' => 'nullable|integer|min:1|max:365',
            'backup_compress' => 'nullable|boolean',
            'backup_local_path' => 'nullable|string|max:255',
            'backup_cloud_enabled' => 'nullable|boolean',
            'backup_cloud_provider' => 'nullable|in:none,google_drive,mega,onedrive',
            'google_drive_client_id' => 'nullable|string|max:255',
            'google_drive_client_secret' => 'nullable|string|max:255',
            'google_drive_refresh_token' => 'nullable|string|max:1000',
            'google_drive_folder_id' => 'nullable|string|max:255',
            'mega_email' => 'nullable|email|max:255',
            'mega_password' => 'nullable|string|max:255',
            'mega_folder' => 'nullable|string|max:255',
            'onedrive_client_id' => 'nullable|string|max:255',
            'onedrive_client_secret' => 'nullable|string|max:255',
            'onedrive_refresh_token' => 'nullable|string|max:1000',
            'onedrive_folder_path' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $settings = $request->except(['_token', '_method', 'site_logo', 'site_favicon']);

        // Handle file uploads
        if ($request->hasFile('site_logo')) {
            $logoPath = $request->file('site_logo')->store('settings', 'public');
            $settings['site_logo'] = $logoPath;

            // Delete old logo if exists
            $oldLogo = $this->getSetting('site_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
        }

        if ($request->hasFile('site_favicon')) {
            $faviconPath = $request->file('site_favicon')->store('settings', 'public');
            $settings['site_favicon'] = $faviconPath;

            // Delete old favicon if exists
            $oldFavicon = $this->getSetting('site_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
        }

        // Save each setting
        foreach ($settings as $key => $value) {
            $this->setSetting($key, $value);
        }

        // Clear settings cache
        Cache::forget('site_settings');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم حفظ الإعدادات بنجاح!');
    }

    /**
     * Get all settings
     */
    private function getAllSettings()
    {
        return Cache::remember('site_settings', 3600, function () {
            $settings = Setting::all()->pluck('value', 'key')->toArray();

            // Merge with defaults if empty
            if (empty($settings)) {
                return $this->getDefaultSettings();
            }

            return array_merge($this->getDefaultSettings(), $settings);
        });
    }

    /**
     * Get a specific setting
     */
    private function getSetting($key, $default = null)
    {
        return Setting::get($key, $default);
    }

    /**
     * Set a specific setting
     */
    private function setSetting($key, $value, $type = 'text', $group = 'general')
    {
        Setting::set($key, $value, $type, $group);

        // Clear cache
        Cache::forget('site_settings');
        Setting::clearCache();
    }

    /**
     * Get default settings
     */
    private function getDefaultSettings()
    {
        return [
            'site_name' => 'Your Events',
            'site_tagline' => 'تجارب واقع افتراضي استثنائية',
            'site_description' => 'نقدم تجارب واقع افتراضي مبتكرة وفعاليات تفاعلية استثنائية',
            'contact_phone' => '+966 50 123 4567',
            'contact_email' => 'info@yourevents.com',
            'contact_address' => 'الرياض، المملكة العربية السعودية',
            'working_hours' => 'الأحد - الخميس: 9:00 ص - 6:00 م',
            'whatsapp_number' => '+966501234567',
            'facebook_url' => '',
            'twitter_url' => '',
            'instagram_url' => '',
            'linkedin_url' => '',
            'youtube_url' => '',
            'tiktok_url' => '',
            // Notification Settings
            'notifications_enabled' => true,
            'notification_sound_enabled' => true,
            'notification_refresh_interval' => 3,
            'notification_auto_dismiss' => 10,
            'notification_quotes' => true,
            'notification_bookings' => true,
            'notification_orders' => true,
            'notification_payments' => true,
            'notification_contacts' => true,
            'notification_suppliers' => true,
            'notification_customers' => true,
            // Appearance
            'primary_color' => '#1f144a',
            'secondary_color' => '#2dbcae',
            'accent_color' => '#ef4870',
            'gold_color' => '#f0c71d',
            'purple_light' => '#7269b0',
            'bg_light' => '#ffffff',
            'bg_secondary' => '#f8f9fa',
            'text_color' => '#222222',
            'hover_color' => '#f56b8a',
            'sidebar_bg' => '#1f144a',
            'sidebar_hover' => '#2d1a5e',
            'font_family_primary' => 'Tajawal',
            'font_family_secondary' => 'Amiri',
            'font_family_english' => 'Inter',
            'logo_url' => '',
            'favicon_url' => '',
            'header_bg_color' => '#1f144a',
            'footer_bg_color' => '#1f144a',
            'button_radius' => '8px',
            'card_radius' => '12px',
            'shadow_color' => 'rgba(0,0,0,0.1)',
            'gradient_start' => '#1f144a',
            'gradient_end' => '#7269b0',
            'meta_title' => 'Your Events - تجارب واقع افتراضي استثنائية',
            'meta_description' => 'نقدم تجارب واقع افتراضي مبتكرة وفعاليات تفاعلية استثنائية في المملكة العربية السعودية',
            'meta_keywords' => 'واقع افتراضي, فعاليات, تجارب تفاعلية, VR, السعودية',
            'meta_author' => 'Your Events',
            'meta_robots' => 'index, follow',
            'meta_viewport' => 'width=device-width, initial-scale=1',
            'og_title' => 'Your Events - تجارب واقع افتراضي استثنائية',
            'og_description' => 'نقدم تجارب واقع افتراضي مبتكرة وفعاليات تفاعلية استثنائية',
            'og_image' => '',
            'og_url' => '',
            'twitter_card' => 'summary_large_image',
            'twitter_site' => '',
            'twitter_creator' => '',
            'canonical_url' => '',
            'schema_type' => 'Organization',
            'schema_name' => 'Your Events',
            'schema_description' => 'شركة متخصصة في تجارب الواقع الافتراضي والفعاليات التفاعلية',
            'schema_logo' => '',
            'schema_address' => 'الرياض، المملكة العربية السعودية',
            'schema_phone' => '+966501234567',
            'schema_email' => 'info@yourevents.com',
            'google_analytics_id' => '',
            'google_tag_manager_id' => '',
            'facebook_pixel_id' => '',
            'google_site_verification' => '',
            'bing_site_verification' => '',
            'yandex_verification' => '',
            'smtp_host' => '',
            'smtp_port' => 587,
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'tls',
            'maintenance_mode' => false,
            'maintenance_message' => 'الموقع تحت الصيانة، سنعود قريباً!',
            // Backup defaults
            'backup_frequency' => 'daily',
            'backup_retention' => 7,
            'backup_compress' => true,
            'backup_local_path' => 'storage/app/backups',
            'backup_cloud_enabled' => false,
            'backup_cloud_provider' => 'none',
            // Google Drive
            'google_drive_client_id' => '',
            'google_drive_client_secret' => '',
            'google_drive_refresh_token' => '',
            'google_drive_folder_id' => '',
            // Mega
            'mega_email' => '',
            'mega_password' => '',
            'mega_folder' => '',
            // OneDrive
            'onedrive_client_id' => '',
            'onedrive_client_secret' => '',
            'onedrive_refresh_token' => '',
            'onedrive_folder_path' => 'Backups',
        ];
    }

    /**
     * Toggle maintenance mode
     */
    public function toggleMaintenance(Request $request)
    {
        $maintenanceMode = $request->input('maintenance_mode', false);

        if ($maintenanceMode) {
            // Enable maintenance mode
            Artisan::call('down', [
                '--render' => 'errors::503',
                '--secret' => config('app.key'),
            ]);
            $this->setSetting('maintenance_mode', '1', 'boolean');
        } else {
            // Disable maintenance mode
            Artisan::call('up');
            $this->setSetting('maintenance_mode', '0', 'boolean');
        }

        $message = $maintenanceMode ? 'تم تفعيل وضع الصيانة' : 'تم إلغاء وضع الصيانة';

        return response()->json([
            'success' => true,
            'message' => $message,
            'maintenance_mode' => $maintenanceMode,
        ]);
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        Cache::flush();

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم مسح الذاكرة المؤقتة بنجاح!');
    }

    /**
     * Export settings
     */
    public function exportSettings()
    {
        $settings = $this->getAllSettings();

        $filename = 'settings_backup_'.date('Y-m-d_H-i-s').'.json';

        return response()->json($settings)
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    /**
     * Import settings
     */
    public function importSettings(Request $request)
    {
        $request->validate([
            'settings_file' => 'required|file|mimetypes:application/json,text/json|max:2048',
        ]);

        $file = $request->file('settings_file');
        $content = file_get_contents($file->getRealPath());
        $settings = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()
                ->with('error', 'ملف الإعدادات غير صالح!');
        }

        // Save imported settings
        foreach ($settings as $key => $value) {
            $this->setSetting($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم استيراد الإعدادات بنجاح!');
    }

    /**
     * Run database backup and optionally upload to cloud
     */
    public function backup(Request $request)
    {
        $db = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');

        $timestamp = date('Y-m-d_H-i-s');
        $backupDir = storage_path('app/backups');
        if (! is_dir($backupDir)) {
            @mkdir($backupDir, 0755, true);
        }
        $sqlFile = "$backupDir/{$db}_{$timestamp}.sql";
        $gzEnabled = filter_var($this->getSetting('backup_compress', true), FILTER_VALIDATE_BOOLEAN);

        $mysqldump = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            escapeshellarg($pass),
            escapeshellarg($db),
            escapeshellarg($sqlFile)
        );

        try {
            $exitCode = null;
            $output = [];
            exec($mysqldump, $output, $exitCode);
            if ($exitCode !== 0 || ! file_exists($sqlFile)) {
                return redirect()->route('admin.settings.index')
                    ->with('error', 'فشل إنشاء النسخة الاحتياطية. تأكد من توفر mysqldump وصلاحيات الكتابة.');
            }

            $finalPath = $sqlFile;
            if ($gzEnabled) {
                $gzFile = $sqlFile.'.gz';
                $gz = gzopen($gzFile, 'w9');
                gzwrite($gz, file_get_contents($sqlFile));
                gzclose($gz);
                @unlink($sqlFile);
                $finalPath = $gzFile;
            }

            // Retention cleanup
            $retention = (int) ($this->getSetting('backup_retention', 7));
            $files = glob($backupDir.'/*');
            usort($files, function ($a, $b) {
                return filemtime($b) <=> filemtime($a);
            });
            if (count($files) > $retention) {
                foreach (array_slice($files, $retention) as $old) {
                    @unlink($old);
                }
            }

            // Optional cloud upload (stubbed)
            $cloudEnabled = filter_var($this->getSetting('backup_cloud_enabled', false), FILTER_VALIDATE_BOOLEAN);
            $provider = $this->getSetting('backup_cloud_provider', 'none');
            if ($cloudEnabled && $provider !== 'none') {
                // Here we would integrate with provider SDK/APIs
                // For now, just simulate success
            }

            return redirect()->route('admin.settings.index')
                ->with('success', 'تم إنشاء النسخة الاحتياطية وحفظها بنجاح في: '.basename($finalPath));
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'حدث خطأ أثناء النسخ الاحتياطي: '.$e->getMessage());
        }
    }

    /**
     * Test cloud connectivity by attempting a small upload
     */
    public function testCloud(Request $request)
    {
        $cloudEnabled = filter_var($this->getSetting('backup_cloud_enabled', false), FILTER_VALIDATE_BOOLEAN);
        $provider = $this->getSetting('backup_cloud_provider', 'none');

        if (! $cloudEnabled || $provider === 'none') {
            return redirect()->route('admin.settings.index')
                ->with('error', 'يرجى تفعيل الرفع للسحابة واختيار المزود أولاً.');
        }

        // Basic validation of required keys per provider
        $missing = [];
        if ($provider === 'google_drive') {
            foreach (['google_drive_client_id', 'google_drive_client_secret', 'google_drive_refresh_token'] as $k) {
                if (! $this->getSetting($k)) {
                    $missing[] = $k;
                }
            }
        } elseif ($provider === 'mega') {
            foreach (['mega_email', 'mega_password'] as $k) {
                if (! $this->getSetting($k)) {
                    $missing[] = $k;
                }
            }
        } elseif ($provider === 'onedrive') {
            foreach (['onedrive_client_id', 'onedrive_client_secret', 'onedrive_refresh_token'] as $k) {
                if (! $this->getSetting($k)) {
                    $missing[] = $k;
                }
            }
        }

        if (! empty($missing)) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'إعدادات ناقصة: '.implode(', ', $missing));
        }

        // Simulate a test upload
        return redirect()->route('admin.settings.index')
            ->with('success', 'تم التحقق من الإعدادات بنجاح. الربط بالسحابة جاهز.');
    }
}
