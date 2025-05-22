<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Sneaker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SneakersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Получаем ID брендов и категорий из базы данных
        $brandNike = Brand::where('slug', 'nike')->first()->id;
        $brandAdidas = Brand::where('slug', 'adidas')->first()->id;
        $brandPuma = Brand::where('slug', 'puma')->first()->id;
        $brandNewBalance = Brand::where('slug', 'new-balance')->first()->id;
        $brandJordan = Brand::where('slug', 'jordan')->first()->id;
        $brandUnderArmour = Brand::where('slug', 'under-armour')->first()->id;
        
        $categoryMen = Category::where('slug', 'muzskie-krossovki')->first()->id;
        
        // Создаем кроссовки из примера на изображении
        $sneakers = [
            [
                'name' => 'Nike Blazer Mid 77',
                'brand_id' => $brandNike,
                'category_id' => $categoryMen,
                'description' => 'Мужские кроссовки Nike Blazer Mid 77',
                'price' => 12998,
                'in_stock' => true,
                'images' => json_encode(['nike_blazer_mid_77_1.jpg', 'nike_blazer_mid_77_2.jpg']),
                'sizes' => json_encode(['40', '41', '42', '43', '44', '45']),
                'colors' => json_encode(['yellow']),
                'is_favorite' => false,
            ],
            [
                'name' => 'Nike Air Max 270',
                'brand_id' => $brandNike,
                'category_id' => $categoryMen,
                'description' => 'Мужские кроссовки Nike Air Max 270',
                'price' => 12999,
                'in_stock' => true,
                'images' => json_encode(['nike_air_max_270_1.jpg', 'nike_air_max_270_2.jpg']),
                'sizes' => json_encode(['40', '41', '42', '43', '44']),
                'colors' => json_encode(['black']),
                'is_favorite' => false,
            ],
            [
                'name' => 'Nike Blazer Mid',
                'brand_id' => $brandNike,
                'category_id' => $categoryMen,
                'description' => 'Мужские кроссовки Nike Blazer Mid',
                'price' => 8499,
                'in_stock' => true,
                'images' => json_encode(['nike_blazer_mid_1.jpg', 'nike_blazer_mid_2.jpg']),
                'sizes' => json_encode(['40', '41', '42', '43', '44', '45']),
                'colors' => json_encode(['lime']),
                'is_favorite' => false,
            ],
            [
                'name' => 'Puma X Aka Boku Future Rider',
                'brand_id' => $brandPuma,
                'category_id' => $categoryMen,
                'description' => 'Кроссовки Puma X Aka Boku Future Rider',
                'price' => 8999,
                'in_stock' => true,
                'images' => json_encode(['puma_future_rider_1.jpg', 'puma_future_rider_2.jpg']),
                'sizes' => json_encode(['39', '40', '41', '42', '43']),
                'colors' => json_encode(['black']),
                'is_favorite' => false,
            ],
            [
                'name' => 'Under Armour Curry 8',
                'brand_id' => $brandUnderArmour,
                'category_id' => $categoryMen,
                'description' => 'Мужские кроссовки Under Armour Curry 8',
                'price' => 15199,
                'in_stock' => true,
                'images' => json_encode(['under_armour_curry_8_1.jpg', 'under_armour_curry_8_2.jpg']),
                'sizes' => json_encode(['40', '41', '42', '43', '44', '45', '46']),
                'colors' => json_encode(['blue']),
                'is_favorite' => false,
            ],
            [
                'name' => 'Nike Kyrie 7',
                'brand_id' => $brandNike,
                'category_id' => $categoryMen,
                'description' => 'Мужские кроссовки Nike Kyrie 7',
                'price' => 11299,
                'in_stock' => true,
                'images' => json_encode(['nike_kyrie_7_1.jpg', 'nike_kyrie_7_2.jpg']),
                'sizes' => json_encode(['40', '41', '42', '43', '44']),
                'colors' => json_encode(['turquoise']),
                'is_favorite' => false,
            ],
            [
                'name' => 'Jordan Air Jordan 11',
                'brand_id' => $brandJordan,
                'category_id' => $categoryMen,
                'description' => 'Мужские кроссовки Jordan Air Jordan 11',
                'price' => 10799,
                'in_stock' => true,
                'images' => json_encode(['jordan_air_jordan_11_1.jpg', 'jordan_air_jordan_11_2.jpg']),
                'sizes' => json_encode(['40', '41', '42', '43', '44', '45']),
                'colors' => json_encode(['green']),
                'is_favorite' => false,
            ],
            [
                'name' => 'Nike LeBron XVIII',
                'brand_id' => $brandNike,
                'category_id' => $categoryMen,
                'description' => 'Мужские кроссовки Nike LeBron XVIII',
                'price' => 16499,
                'in_stock' => true,
                'images' => json_encode(['nike_lebron_xviii_1.jpg', 'nike_lebron_xviii_2.jpg']),
                'sizes' => json_encode(['41', '42', '43', '44', '45', '46']),
                'colors' => json_encode(['white']),
                'is_favorite' => false,
            ],
            [
                'name' => 'Nike Lebron XVIII Low',
                'brand_id' => $brandNike,
                'category_id' => $categoryMen,
                'description' => 'Мужские кроссовки Nike Lebron XVIII Low',
                'price' => 13999,
                'in_stock' => true,
                'images' => json_encode(['nike_lebron_xviii_low_1.jpg', 'nike_lebron_xviii_low_2.jpg']),
                'sizes' => json_encode(['40', '41', '42', '43', '44', '45']),
                'colors' => json_encode(['multicolor']),
                'is_favorite' => false,
            ],
            [
                'name' => 'Nike Blazer Mid Suede',
                'brand_id' => $brandNike,
                'category_id' => $categoryMen,
                'description' => 'Мужские кроссовки Nike Blazer Mid Suede',
                'price' => 8499,
                'in_stock' => true,
                'images' => json_encode(['nike_blazer_mid_suede_1.jpg', 'nike_blazer_mid_suede_2.jpg']),
                'sizes' => json_encode(['40', '41', '42', '43', '44', '45']),
                'colors' => json_encode(['lime']),
                'is_favorite' => false,
            ],
            [
                'name' => 'Puma X Boku Future Rider',
                'brand_id' => $brandPuma,
                'category_id' => $categoryMen,
                'description' => 'Кроссовки Puma X Aka Boku Future Rider',
                'price' => 8999,
                'in_stock' => true,
                'images' => json_encode(['puma_x_aka_boku_1.jpg', 'puma_x_aka_boku_2.jpg']),
                'sizes' => json_encode(['39', '40', '41', '42', '43']),
                'colors' => json_encode(['multicolor']),
                'is_favorite' => false,
            ],
            [
                'name' => 'Nike Kyrie Flytrap IV',
                'brand_id' => $brandNike,
                'category_id' => $categoryMen,
                'description' => 'Мужские кроссовки Nike Kyrie Flytrap IV',
                'price' => 11299,
                'in_stock' => true,
                'images' => json_encode(['nike_kyrie_flytrap_iv_1.jpg', 'nike_kyrie_flytrap_iv_2.jpg']),
                'sizes' => json_encode(['40', '41', '42', '43', '44']),
                'colors' => json_encode(['multicolor']),
                'is_favorite' => false,
            ],
        ];

        foreach ($sneakers as $sneakerData) {
            $name = $sneakerData['name'];
            $baseSlug = Str::slug($name);
            $slug = $baseSlug;
            $counter = 1;
            
            // Проверяем, существует ли такой slug, и если да, добавляем число
            while (Sneaker::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            $sneakerData['slug'] = $slug;
            Sneaker::create($sneakerData);
        }
    }
}
