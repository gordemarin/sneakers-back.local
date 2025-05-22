<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SneakerCollection;
use App\Http\Resources\SneakerResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Sneaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SneakerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sneaker::query()
            ->with(['brand', 'category']);
        
        // Фильтрация по категории
        if ($request->has('category')) {
            $category = $request->input('category');
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        }
        
        // Фильтрация по наличию
        if ($request->has('in_stock')) {
            $query->where('in_stock', $request->boolean('in_stock'));
        }
        
        // Поиск по названию
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Сортировка
        $sortField = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');
        $allowedSortFields = ['id', 'name', 'price', 'created_at'];
        
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }
        
        $perPage = $request->input('per_page', 12);
        $sneakers = $query->paginate($perPage);
        
        return new SneakerCollection($sneakers);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sneaker $sneaker)
    {
        $sneaker->load(['brand', 'category']);
        return new SneakerResource($sneaker);
    }

    /**
     * Получить список всех брендов
     *
     * @return JsonResponse
     */
    public function getBrands(): JsonResponse
    {
        $brands = Brand::all(['id', 'name', 'slug']);
        return response()->json([
            'data' => $brands,
        ]);
    }

    /**
     * Переключить статус "избранное" для кроссовок
     *
     * @param Sneaker $sneaker
     * @return JsonResponse
     */
    public function toggleFavorite(Sneaker $sneaker): JsonResponse
    {
        $sneaker->is_favorite = !$sneaker->is_favorite;
        $sneaker->save();
        
        return response()->json([
            'data' => [
                'id' => $sneaker->id,
                'is_favorite' => $sneaker->is_favorite,
            ],
            'message' => $sneaker->is_favorite ? 'Добавлено в избранное' : 'Удалено из избранного',
        ]);
    }

    /**
     * Загрузка изображения для кроссовок
     * 
     * @param Request $request
     * @param Sneaker $sneaker
     * @return JsonResponse
     */
    public function uploadImage(Request $request, Sneaker $sneaker): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // Генерируем уникальное имя файла
            $filename = Str::slug($sneaker->name) . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Сохраняем файл в публичную директорию
            $path = $image->storeAs('images/sneakers', $filename, 'public');
            
            // Получаем текущие изображения кроссовок
            // Проверяем, является ли $sneaker->images массивом или преобразуем его в массив, если это строка
            $images = is_array($sneaker->images) ? $sneaker->images : (is_string($sneaker->images) && json_decode($sneaker->images) ? json_decode($sneaker->images, true) : []);
            
            if (!is_array($images)) {
                $images = [];
            }
            
            // Добавляем новое изображение в массив
            $images[] = $filename;
            
            // Обновляем кроссовки с новым списком изображений
            $sneaker->images = $images;
            $sneaker->save();
            
            return response()->json([
                'success' => true, 
                'path' => asset('storage/' . $path),
                'message' => 'Изображение успешно загружено',
                'data' => [
                    'id' => $sneaker->id,
                    'images' => $sneaker->images,
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Ошибка при загрузке изображения'
        ], 400);
    }

    /**
     * Получить все изображения для кроссовок
     * 
     * @param Sneaker $sneaker
     * @return JsonResponse
     */
    public function getImages(Sneaker $sneaker): JsonResponse
    {
        $images = [];
        
        if (!empty($sneaker->images)) {
            // Проверяем, является ли $sneaker->images массивом или преобразуем его в массив, если это строка
            $imagesData = is_array($sneaker->images) ? $sneaker->images : (is_string($sneaker->images) && json_decode($sneaker->images) ? json_decode($sneaker->images, true) : []);
            
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
        
        return response()->json([
            'data' => $images
        ]);
    }

    /**
     * Удалить изображение кроссовки
     * 
     * @param Request $request
     * @param Sneaker $sneaker
     * @return JsonResponse
     */
    public function deleteImage(Request $request, Sneaker $sneaker): JsonResponse
    {
        $request->validate([
            'filename' => 'required|string',
        ]);
        
        $filename = $request->input('filename');
        
        // Проверяем, является ли $sneaker->images массивом или преобразуем его в массив, если это строка
        $images = is_array($sneaker->images) ? $sneaker->images : (is_string($sneaker->images) && json_decode($sneaker->images) ? json_decode($sneaker->images, true) : []);
        
        if (!is_array($images)) {
            $images = [];
        }
        
        // Проверяем, существует ли такое изображение у этой кроссовки
        if (!in_array($filename, $images)) {
            return response()->json([
                'success' => false,
                'message' => 'Изображение не найдено'
            ], 404);
        }
        
        // Удаляем файл с диска
        $path = 'public/images/sneakers/' . $filename;
        if (Storage::exists($path)) {
            Storage::delete($path);
        }
        
        // Удаляем имя файла из массива изображений
        $images = array_values(array_filter($images, function ($image) use ($filename) {
            return $image !== $filename;
        }));
        
        // Обновляем кроссовку
        $sneaker->images = $images;
        $sneaker->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Изображение успешно удалено',
            'data' => [
                'id' => $sneaker->id,
                'images' => $sneaker->images,
            ]
        ]);
    }

    /**
     * Получить список всех категорий
     *
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        $categories = Category::all(['id', 'name', 'slug']);
        return response()->json([
            'data' => $categories,
        ]);
    }

    public function byBrand(Brand $brand)
    {
        $sneakers = Sneaker::where('brand_id', $brand->id)
            ->with(['brand', 'category'])
            ->get();
        
        // Используем SneakerResource для корректной обработки изображений
        return response()->json([
            'data' => SneakerResource::collection($sneakers)
        ]);
    }

    public function byCategory(Category $category)
    {
        $sneakers = Sneaker::where('category_id', $category->id)
            ->with(['brand', 'category'])
            ->get();
        
        // Используем SneakerResource для корректной обработки изображений
        return response()->json([
            'data' => SneakerResource::collection($sneakers)
        ]);
    }
}
