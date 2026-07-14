<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\ACL\Database\Seeders\PermissionSeeder;
use Modules\Core\Database\Seeders\SettingSeeder;
use Modules\Factory\Database\Seeders\HallSeeder;
use Modules\Organization\Database\Seeders\ChartSeeder;
use Modules\Category\Database\Seeders\CategoryDatabaseSeeder;
use Modules\User\Database\Seeders\ProvinceSeeder;
use Modules\Warehouse\Database\Seeders\ColorSeeder;

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
            HallSeeder::class,
            CategoryDatabaseSeeder::class,
            SettingSeeder::class,
            // ChartSeeder::class,
            ColorSeeder::class,
        ]);
    }
}
