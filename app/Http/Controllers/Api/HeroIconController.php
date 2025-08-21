<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HeroIconService;
use Illuminate\Http\JsonResponse;

class HeroIconController extends Controller
{
    private HeroIconService $heroIconService;
    
    public function __construct(HeroIconService $heroIconService)
    {
        $this->heroIconService = $heroIconService;
    }
    
    /**
     * 取得所有 HeroIcons 資料
     */
    public function index(): JsonResponse
    {
        return response()->json($this->heroIconService->getAllHeroIcons());
    }
    
    /**
     * 取得分類清單
     */
    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => $this->heroIconService->getCategories()
        ]);
    }
    
    /**
     * 取得指定分類的 HeroIcons
     */
    public function category(string $category): JsonResponse
    {
        try {
            $result = $this->heroIconService->getHeroIconsByCategory($category);
            return response()->json($result);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}