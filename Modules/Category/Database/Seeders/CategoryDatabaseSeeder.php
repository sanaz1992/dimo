<?php

namespace Modules\Category\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Category\Entities\Category;
use Modules\Category\Enums\CategoryType;

class CategoryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product_categories = [
            [
                'slug' => 'میز-غذا-خوری-و-صندلی',
                'title' => 'میز غذا خوری و صندلی',
            ],
            [
                'slug' => 'مبلمان',
                'title' => 'مبلمان',
            ],
            [
                'slug' => 'میز-tv',
                'title' => 'میز tv',
            ],
            [
                'slug' => 'میز-جلو-مبلی-و-میز-عسلی',
                'title' => 'میز جلو مبلی و میز عسلی',
            ],
            [
                'slug' => 'میز-کنسول',
                'title' => 'میز کنسول',
            ],
            [
                'slug' => 'کوسن',
                'title' => 'کوسن',
            ],
        ];
        $product_categories = array_map(fn($cat) => [...$cat, 'type' => CategoryType::PRODUCT], $product_categories);

        $categories = array_merge($product_categories);

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
