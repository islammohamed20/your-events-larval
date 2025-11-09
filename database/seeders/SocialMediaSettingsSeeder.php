<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SocialMediaSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Social Media
            [
                'key' => 'facebook_url',
                'value' => '',
                'type' => 'text',
                'group' => 'social_media',
            ],
            [
                'key' => 'twitter_url',
                'value' => '',
                'type' => 'text',
                'group' => 'social_media',
            ],
            [
                'key' => 'instagram_url',
                'value' => '',
                'type' => 'text',
                'group' => 'social_media',
            ],
            [
                'key' => 'linkedin_url',
                'value' => '',
                'type' => 'text',
                'group' => 'social_media',
            ],
            [
                'key' => 'youtube_url',
                'value' => '',
                'type' => 'text',
                'group' => 'social_media',
            ],
            [
                'key' => 'tiktok_url',
                'value' => '',
                'type' => 'text',
                'group' => 'social_media',
            ],
            [
                'key' => 'snapchat_url',
                'value' => '',
                'type' => 'text',
                'group' => 'social_media',
            ],
            [
                'key' => 'whatsapp_number',
                'value' => '',
                'type' => 'text',
                'group' => 'social_media',
            ],
            
            // Contact Info
            [
                'key' => 'contact_address',
                'value' => 'الرياض، المملكة العربية السعودية',
                'type' => 'textarea',
                'group' => 'general',
            ],
            [
                'key' => 'working_hours',
                'value' => 'السبت - الخميس: 9:00 ص - 6:00 م',
                'type' => 'text',
                'group' => 'general',
            ],
            
            // SEO Settings
            [
                'key' => 'meta_keywords',
                'value' => 'تنظيم فعاليات، حفلات، مناسبات، السعودية',
                'type' => 'textarea',
                'group' => 'seo',
            ],
            [
                'key' => 'og_image',
                'value' => '',
                'type' => 'image',
                'group' => 'seo',
            ],
            
            // Email Settings
            [
                'key' => 'smtp_host',
                'value' => '',
                'type' => 'text',
                'group' => 'email',
            ],
            [
                'key' => 'smtp_port',
                'value' => '587',
                'type' => 'number',
                'group' => 'email',
            ],
            [
                'key' => 'smtp_username',
                'value' => '',
                'type' => 'text',
                'group' => 'email',
            ],
            [
                'key' => 'smtp_password',
                'value' => '',
                'type' => 'password',
                'group' => 'email',
            ],
            [
                'key' => 'smtp_encryption',
                'value' => 'tls',
                'type' => 'select',
                'group' => 'email',
            ],
            
            // Maintenance Mode
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'general',
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'الموقع تحت الصيانة، سنعود قريباً!',
                'type' => 'textarea',
                'group' => 'general',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
