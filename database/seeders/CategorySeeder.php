<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Arts',
            'Leisure',
            'Business',
            'Climate',
            'Culture',
            'Food',
            'Fashion',
            'Film',
            'Foreign',
            'Games',
            'Graphics',
            'Learning',
            'Podcasts',
            'Politics',
            'RealEstate',
            'Science',
            'Styles',
            'Travel',
            'Weather',
            'Weekend',
            'Wellness',
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category],
                ['name' => $category]
            );
        }
    }
}
