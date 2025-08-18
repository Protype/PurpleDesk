<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HeroIconService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HeroIconController extends Controller
{
    private HeroIconService $heroIconService;
    
    public function __construct(HeroIconService $heroIconService)
    {
        $this->heroIconService = $heroIconService;
    }
    
    /**
     * 取得所有 HeroIcons 資料
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->heroIconService->getAllHeroIcons());
    }
    
    /**
     * 取得指定分類的圖標
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function byCategory(Request $request): JsonResponse
    {
        $category = $request->get('category');
        
        if (!$category) {
            return response()->json([
                'error' => 'Category parameter is required'
            ], 400);
        }
        
        return response()->json($this->heroIconService->getIconsByCategory($category));
    }
    
    /**
     * 搜尋圖標
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');
        
        if (!$query) {
            return response()->json([
                'error' => 'Query parameter (q) is required'
            ], 400);
        }
        
        return response()->json($this->heroIconService->searchIcons($query));
    }
    
    /**
     * 取得分類清單
     * 
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => $this->heroIconService->getCategories()
        ]);
    }
}