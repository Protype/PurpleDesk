<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BootstrapIconService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BootstrapIconController extends Controller
{
    private BootstrapIconService $bootstrapIconService;
    
    public function __construct(BootstrapIconService $bootstrapIconService)
    {
        $this->bootstrapIconService = $bootstrapIconService;
    }
    
    /**
     * 取得所有 Bootstrap Icons 資料或根據分類篩選
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // 檢查是否有分類篩選參數
        $categoriesParam = $request->get('categories', '');
        
        if (empty($categoriesParam)) {
            // 返回所有圖標
            return response()->json($this->bootstrapIconService->getAllBootstrapIcons());
        }
        
        // 解析分類參數
        $requestedCategories = array_filter(
            array_map('trim', explode(',', $categoriesParam)),
            function($category) {
                return !empty($category);
            }
        );
        
        if (empty($requestedCategories)) {
            // 空的分類參數，返回所有圖標
            return response()->json($this->bootstrapIconService->getAllBootstrapIcons());
        }
        
        // 驗證分類名稱
        $validCategories = $this->bootstrapIconService->validateCategories($requestedCategories);
        
        // 根據分類篩選
        return response()->json($this->bootstrapIconService->getIconsByCategories($validCategories));
    }
    
    /**
     * 搜尋 Bootstrap Icons
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([
                'error' => 'Query parameter (q) is required'
            ], 400);
        }
        
        // 可選的分類限制
        $categoriesParam = $request->get('categories', '');
        $categories = [];
        
        if (!empty($categoriesParam)) {
            $categories = array_filter(
                array_map('trim', explode(',', $categoriesParam)),
                function($category) {
                    return !empty($category);
                }
            );
            
            // 驗證分類名稱
            $categories = $this->bootstrapIconService->validateCategories($categories);
        }
        
        return response()->json(
            $this->bootstrapIconService->searchIcons($query, $categories)
        );
    }
    
    /**
     * 取得分類清單
     * 
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => $this->bootstrapIconService->getCategories()
        ]);
    }
    
    /**
     * 取得載入優先級設定
     * 
     * @return JsonResponse
     */
    public function priority(): JsonResponse
    {
        return response()->json([
            'data' => $this->bootstrapIconService->getLoadingPriority()
        ]);
    }
    
    /**
     * 根據優先級取得圖標
     * 
     * @param Request $request
     * @param string $priority
     * @return JsonResponse
     */
    public function byPriority(Request $request, string $priority): JsonResponse
    {
        $validPriorities = ['immediate', 'high', 'medium', 'low'];
        
        if (!in_array($priority, $validPriorities)) {
            return response()->json([
                'error' => "Invalid priority. Must be one of: " . implode(', ', $validPriorities)
            ], 400);
        }
        
        return response()->json(
            $this->bootstrapIconService->getIconsByPriority($priority)
        );
    }
    
    /**
     * 取得圖標統計資訊
     * 
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'data' => $this->bootstrapIconService->getIconStats()
        ]);
    }
    
    /**
     * 清除快取（用於開發和測試）
     * 
     * @return JsonResponse
     */
    public function clearCache(): JsonResponse
    {
        $this->bootstrapIconService->clearCache();
        
        return response()->json([
            'message' => 'Bootstrap Icons cache cleared successfully'
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
                'mapping' => $this->bootstrapIconService->getVariantMapping(),
                'supported' => $this->bootstrapIconService->getSupportedVariants()
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
            return response()->json($this->bootstrapIconService->getIconsByStyle($style));
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
     * @param string $className
     * @return JsonResponse
     */
    public function iconVariants(Request $request, string $className): JsonResponse
    {
        $variants = $this->bootstrapIconService->getIconVariants($className);
        
        if (!$variants) {
            return response()->json([
                'error' => 'Icon not found'
            ], 404);
        }
        
        return response()->json([
            'data' => [
                'class' => $className,
                'variants' => $variants
            ]
        ]);
    }
    
    /**
     * 檢查圖標是否支援特定樣式
     * 
     * @param Request $request
     * @param string $className
     * @param string $style
     * @return JsonResponse
     */
    public function hasVariant(Request $request, string $className, string $style): JsonResponse
    {
        $hasVariant = $this->bootstrapIconService->hasStyleVariant($className, $style);
        $variantClassName = null;
        
        if ($hasVariant) {
            $variantClassName = $this->bootstrapIconService->getIconClass($className, $style);
        }
        
        return response()->json([
            'data' => [
                'class' => $className,
                'style' => $style,
                'hasVariant' => $hasVariant,
                'variantClass' => $variantClassName
            ]
        ]);
    }
}