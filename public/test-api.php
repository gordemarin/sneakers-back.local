<?php
// Простой скрипт для проверки API

// Разрешаем кросс-доменные запросы
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With');
header('Content-Type: application/json');

// Имитируем API для кроссовок
$sneakers = [
    [
        'id' => 1,
        'name' => 'Мужские Кроссовки Nike Blazer Mid Suede',
        'price' => 12999,
        'image' => '/image 5-1.jpg'
    ],
    [
        'id' => 2,
        'name' => 'Мужские Кроссовки Nike Air Max 270',
        'price' => 15600,
        'image' => '/image 5-2.jpg'
    ],
    [
        'id' => 3,
        'name' => 'Мужские Кроссовки Nike Blazer Mid Suede',
        'price' => 8499,
        'image' => '/image 5-3.jpg'
    ],
    [
        'id' => 4,
        'name' => 'Кроссовки Puma X Aka Boku Future Rider',
        'price' => 8999,
        'image' => '/image 4.png'
    ]
];

// Возвращаем данные
echo json_encode(['data' => $sneakers]); 