<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Entities\User;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['mobile' => '09358364707'],
            [
                'name'     => 'sanaz',
                'password' => Hash::make('12345678'),
                'level'    => 'admin',
            ]
        );
        $this->call([
            ProvinceSeeder::class,
        ]);
    }
}
