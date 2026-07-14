<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\ACL\Database\Seeders\PermissionSeeder;
use Modules\Core\Database\Seeders\SettingSeeder;
use Modules\User\Database\Seeders\ProvinceSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProvinceSeeder::class,
            PermissionSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
