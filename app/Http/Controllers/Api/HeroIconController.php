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
    
    /**
     * 取得變體映射資訊
     * 
     * @return JsonResponse
     */
    public function variants(): JsonResponse
    {
        return response()->json([
            'data' => [
                'mapping' => $this->heroIconService->getVariantMapping(),
                'supported' => $this->heroIconService->getSupportedVariants()
            ]
        ]);
    }
    
    /**
     * 取得特定樣式的圖標資料
     * 
     * @param Request $request
     * @param string $style
     * @return JsonResponse
     */
    public function byStyle(Request $request, string $style): JsonResponse
    {
        try {
            return response()->json($this->heroIconService->getIconsByStyle($style));
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * 取得單一圖標的變體資訊
     * 
     * @param Request $request
     * @param string $component
     * @return JsonResponse
     */
    public function iconVariants(Request $request, string $component): JsonResponse
    {
        $variants = $this->heroIconService->getIconVariants($component);
        
        if (!$variants) {
            return response()->json([
                'error' => 'Icon not found'
            ], 404);
        }
        
        return response()->json([
            'data' => [
                'component' => $component,
                'variants' => $variants
            ]
        ]);
    }
    
    /**
     * 檢查圖標是否支援特定樣式
     * 
     * @param Request $request
     * @param string $component
     * @param string $style
     * @return JsonResponse
     */
    public function hasVariant(Request $request, string $component, string $style): JsonResponse
    {
        $hasVariant = $this->heroIconService->hasStyleVariant($component, $style);
        $componentName = null;
        
        if ($hasVariant) {
            $componentName = $this->heroIconService->getIconComponent($component, $style);
        }
        
        return response()->json([
            'data' => [
                'component' => $component,
                'style' => $style,
                'hasVariant' => $hasVariant,
                'variantComponent' => $componentName
            ]
        ]);
    }
}