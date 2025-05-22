<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SneakerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $images = [];
        
        if (!empty($this->images)) {
            // Проверяем, является ли $this->images массивом или преобразуем его в массив, если это строка
            $imagesData = is_array($this->images) ? $this->images : (is_string($this->images) && json_decode($this->images) ? json_decode($this->images, true) : []);
            
            foreach ($imagesData as $image) {
                // Нормализуем имя файла: проверяем, содержит ли оно пробел в формате "image 5-1.jpg"
                // Если нет, преобразуем "image5-1.jpg" в правильный формат
                $normalizedFilename = $image;
                if (strpos($image, 'image') === 0 && strpos($image, ' ') === false && strpos($image, '-') !== false) {
                    // Обрабатываем случай, когда имя файла имеет формат "image5-1.jpg"
                    $normalizedFilename = preg_replace('/^(image)(\d+)/', 'image$2', $image);
                }
                
                $images[] = [
                    'filename' => $normalizedFilename,
                    'url' => asset('storage/images/sneakers/' . $normalizedFilename)
                ];
            }
        }
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'brand' => $this->whenLoaded('brand', function () {
                return [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name,
                    'slug' => $this->brand->slug,
                ];
            }),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'description' => $this->description,
            'price' => $this->price,
            'price_formatted' => number_format($this->price, 0, '.', ' ') . ' руб.',
            'in_stock' => $this->in_stock,
            'images' => $images,
            'sizes' => $this->sizes,
            'colors' => $this->colors,
            'is_favorite' => $this->is_favorite,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
