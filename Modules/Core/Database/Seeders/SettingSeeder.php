<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\Setting;
use Modules\Core\Enums\SettingType;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'title' => 'عنوان سایت',
                'key' => 'site_title',
                'value' => 'فروشگاه گلاب',
                'type' => SettingType::TEXT->value,
                'group' => 'general',
            ],
            [
                'title' => 'لوگو',
                'key' => 'logo',
                'value' => null,
                'type' => SettingType::IMAGE->value,
                'group' => 'general',
                'description' => 'سایز 130*35 px'
            ],
            [
                'title' => 'favicon',
                'key' => 'favicon',
                'value' => null,
                'type' => SettingType::IMAGE->value,
                'group' => 'general',
                'description' => 'سایز 20*20 px'
            ],
            [
                'title' => 'امکان ثبت نام توسط کاربر',
                'key' => 'user_can_register',
                'type' => SettingType::BOOL->value,
                'group' => 'login',
                'value' => null
            ],
            [
                'title' => 'ورود با پسورد',
                'key' => 'login_with_password',
                'type' => SettingType::BOOL->value,
                'group' => 'login',
                'value' => true
            ],
        ];

        foreach ($settings as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                [
                    'value' => $item['value'],
                    'title' => $item['title'],
                    'type' => $item['type'],
                    'group' => $item['group'],
                    'description' => $item['description'] ?? null
                ]
            );
        }
    }
}
