<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'Мужские кроссовки',
            'Женские кроссовки',
            'Спортивные кроссовки',
            'Повседневные кроссовки',
            'Беговые кроссовки',
        ];

        foreach ($categories as $categoryName) {
            $slug = Str::slug($categoryName);
            echo "Creating category: {$categoryName}, slug: {$slug}\n";
            
            Category::create([
                'name' => $categoryName,
                'slug' => $slug,
            ]);
        }
    }
}
