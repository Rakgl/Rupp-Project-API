<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_title',
                'name' => 'Site Title',
                'value' => 'Car Service Admin',
                'type' => 'string',
                'group' => 'Site Information',
                'description' => 'The title of the car service website, appearing in the browser tab and search engine results.',
            ],
            [
                'key' => 'admin_email',
                'name' => 'Administrator Email',
                'value' => 'admin@carservice.com',
                'type' => 'string',
                'group' => 'Site Information',
                'description' => 'The email address for the primary car service system administrator.',
            ],
            [
                'key' => 'default_timezone',
                'name' => 'Default Timezone',
                'value' => 'Asia/Phnom_Penh',
                'type' => 'string',
                'group' => 'Localization',
                'description' => 'The default timezone used for displaying appointment times and service schedules throughout the system.',
            ],
            [
                'key' => 'default_currency',
                'name' => 'Default Currency',
                'value' => 'USD',
                'type' => 'string',
                'group' => 'Localization',
                'description' => 'The default currency code for car service pricing (e.g., USD, KHR).',
            ],
            [
                'key' => 'email_notifications_enabled',
                'name' => 'Enable Email Notifications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'Notifications',
                'description' => 'Globally enable or disable sending email notifications for car service appointments and updates.',
            ],
             [
                'key' => 'email_from_address',
                'name' => 'Email "From" Address',
                'value' => 'no-reply@carservice.com',
                'type' => 'string',
                'group' => 'Notifications',
                'description' => 'The email address that car service system emails will be sent from.',
            ],
            [
                'key' => 'business_hours',
                'name' => 'Business Hours',
                'value' => 'Mon-Fri: 8:00 AM - 6:00 PM, Sat: 9:00 AM - 4:00 PM',
                'type' => 'text',
                'group' => 'Business Settings',
                'description' => 'Operating hours for the car service center.',
            ],
            [
                'key' => 'contact_phone',
                'name' => 'Contact Phone',
                'value' => '+855-12-345-678',
                'type' => 'string',
                'group' => 'Business Settings',
                'description' => 'Primary phone number for customer inquiries and appointments.',
            ],
        ];

        foreach ($settings as $setting) {
            GeneralSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}