<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SneakerController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\TestApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API маршруты для кроссовок
Route::group(['prefix' => 'v1'], function () {
    // Получение списка кроссовок с пагинацией и фильтрацией
    Route::get('/sneakers', [SneakerController::class, 'index']);
    
    // Получение информации о конкретной паре кроссовок
    Route::get('/sneakers/{sneaker:slug}', [SneakerController::class, 'show']);
    
    // Получение списка всех категорий
    Route::get('/categories', [CategoryController::class, 'index']);
    
    // Переключение статуса "избранное" для кроссовок
    Route::post('/sneakers/{sneaker}/favorite', [SneakerController::class, 'toggleFavorite']);
    
    // Загрузка изображений для кроссовок
    Route::post('/sneakers/{sneaker}/images', [SneakerController::class, 'uploadImage']);
    
    // Получение всех изображений для кроссовок
    Route::get('/sneakers/{sneaker}/images', [SneakerController::class, 'getImages']);
    
    // Удаление изображения кроссовки
    Route::delete('/sneakers/{sneaker}/images', [SneakerController::class, 'deleteImage']);
});

// Маршруты без префикса v1 (базовые маршруты API)
Route::group(['middleware' => 'api'], function () {
    // Маршруты для кроссовок - более специфичные маршруты должны быть ПЕРЕД общими
    Route::get('/sneakers/brand/{brand:slug}', [SneakerController::class, 'byBrand']);
    Route::get('/sneakers/category/{category:slug}', [SneakerController::class, 'byCategory']);
    
    // Общие маршруты для кроссовок - должны быть ПОСЛЕ специфичных
    Route::get('/sneakers', [SneakerController::class, 'index']);
    Route::get('/sneakers/{sneaker:slug}', [SneakerController::class, 'show']);

    // Маршруты для брендов
    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/brands/{brand:slug}', [BrandController::class, 'show']);

    // Маршруты для категорий
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show']);
});

// Тестовый маршрут для отладки
Route::get('/test-sneakers', [TestApiController::class, 'testSneakers']);