<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Получить список всех брендов
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $brands = Brand::all(['id', 'name', 'slug']);
        return response()->json([
            'data' => $brands,
        ]);
    }

    /**
     * Получить информацию о конкретном бренде
     *
     * @param Brand $brand
     * @return JsonResponse
     */
    public function show(Brand $brand): JsonResponse
    {
        return response()->json([
            'data' => $brand,
        ]);
    }
} 