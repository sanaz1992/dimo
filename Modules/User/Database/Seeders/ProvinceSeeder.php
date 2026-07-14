<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Entities\City;
use Modules\User\Entities\Province;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(base_path('Modules/User/Database/data/provinces.json')); //get provinces from json file
        $provinces = json_decode($json, true);

        foreach ($provinces as $province) {
            Province::firstOrCreate(
                ['slug' => $province['slug']],
                [
                    'name' => $province['name'],
                    'tel_prefix' => $province['tel_prefix']
                ]
            );
        }

        $citiesJson = file_get_contents(base_path('Modules/User/Database/data/cities.json')); //get provinces from json file
        $cities = json_decode($citiesJson, true);

        foreach ($cities as $city) {
            City::firstOrCreate(
                ['slug' => $city['slug']],
                [
                    'name' => $city['name'],
                    'province_id' => $city['province_id'] - 99
                ]
            );
        }
    }
}
