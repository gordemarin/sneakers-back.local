<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class TestApiController extends Controller
{
    /**
     * Простой тестовый метод для возврата статических данных
     */
    public function testSneakers(): JsonResponse
    {
        $sneakers = [
            [
                'id' => 1,
                'name' => 'Мужские Кроссовки Nike Blazer Mid Suede',
                'price' => 12999,
                'image' => '/storage/images/sneakers/image5-1.jpg'
            ],
            [
                'id' => 2,
                'name' => 'Мужские Кроссовки Nike Air Max 270',
                'price' => 15600,
                'image' => '/storage/images/sneakers/image5-2.jpg'
            ],
            [
                'id' => 3,
                'name' => 'Мужские Кроссовки Nike Blazer Mid Suede',
                'price' => 8499,
                'image' => '/storage/images/sneakers/image5-3.jpg'
            ],
            [
                'id' => 4,
                'name' => 'Кроссовки Puma X Aka Boku Future Rider',
                'price' => 8999,
                'image' => '/storage/images/sneakers/image 4.png'
            ]
        ];

        return response()->json([
            'data' => $sneakers
        ]);
    }
} 