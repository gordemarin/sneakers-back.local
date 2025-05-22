<?php


namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = [
            'Nike',
            'Adidas',
            'Puma',
            'Reebok',
            'New Balance',
            'Asics',
            'Jordan',
            'Vans',
            'Converse',
            'Under Armour',
        ];

        foreach ($brands as $brandName) {
            Brand::create([
                'name' => $brandName,
                'slug' => Str::slug($brandName),
            ]);
        }
    }
}
