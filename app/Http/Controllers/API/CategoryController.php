<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Получить список всех категорий
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = Category::all(['id', 'name', 'slug']);
        return response()->json([
            'data' => $categories,
        ]);
    }

    /**
     * Получить информацию о конкретной категории
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'data' => $category,
        ]);
    }
} 