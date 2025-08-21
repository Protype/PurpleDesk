<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BootstrapIconService;
use Illuminate\Http\JsonResponse;

class BootstrapIconController extends Controller
{
    private BootstrapIconService $bootstrapIconService;
    
    public function __construct(BootstrapIconService $bootstrapIconService)
    {
        $this->bootstrapIconService = $bootstrapIconService;
    }
    
    /**
     * 取得所有 Bootstrap Icons 資料
     */
    public function index(): JsonResponse
    {
        return response()->json($this->bootstrapIconService->getAllBootstrapIcons());
    }
    
    /**
     * 取得分類清單
     */
    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => $this->bootstrapIconService->getCategories()
        ]);
    }
    
    /**
     * 取得指定分類的 Bootstrap Icons
     */
    public function category(string $category): JsonResponse
    {
        try {
            // 檢查是否為特殊的 all-filtered 分類
            if ($category === 'all-filtered') {
                $result = $this->bootstrapIconService->getAllFilteredIcons();
                return response()->json($result);
            }
            
            $result = $this->bootstrapIconService->getBootstrapIconsByCategory($category);
            return response()->json($result);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * 取得過濾後的全部圖標 (只顯示 category='all' 的圖標)
     */
    public function allFiltered(): JsonResponse
    {
        return response()->json($this->bootstrapIconService->getAllFilteredIcons());
    }
    
    /**
     * 完全獨立的 all-filtered API 端點
     * 直接讀取 all.php 配置檔案並篩選 category='all' 的圖標
     * 不依賴任何現有的處理邏輯
     */
    public function allFilteredIndependent(): JsonResponse
    {
        try {
            // 直接載入 all.php 配置檔案
            $configPath = config_path('icon/bootstrap-icons/all.php');
            
            if (!file_exists($configPath)) {
                return response()->json([
                    'error' => 'All icons configuration file not found'
                ], 404);
            }
            
            $allConfig = include $configPath;
            
            if (!isset($allConfig['icons']) || !is_array($allConfig['icons'])) {
                return response()->json([
                    'error' => 'Invalid configuration format'
                ], 500);
            }
            
            $filteredIcons = [];
            $originalIcons = $allConfig['icons'];
            
            // 展開變體並篩選出仍為 'all' 分類的圖標
            foreach ($originalIcons as $icon) {
                $baseName = $icon['displayName'] ?? $icon['name'];
                $keywords = $icon['keywords'] ?? [];
                $variants = $icon['variants'] ?? [];
                $iconCategory = $icon['category'] ?? 'all';
                
                // 只顯示 category 仍為 'all' 的圖標
                if ($iconCategory !== 'all') {
                    continue; // 跳過已被重新分類的圖標
                }
                
                // 為每個變體創建獨立項目
                foreach ($variants as $variantType => $variantData) {
                    $variantClass = $variantData['class'];
                    $iconId = str_replace('bi-', '', $icon['name']) . '-' . $variantType;
                    
                    $expandedIcon = [
                        'id' => $iconId,
                        'name' => $baseName,
                        'value' => $variantClass,
                        'type' => 'bootstrap-icons',
                        'keywords' => $this->generateIconKeywords($baseName, $keywords),
                        'category' => 'all',
                        'has_variants' => count($variants) > 1,
                        'variant_type' => $variantType
                    ];
                    
                    $filteredIcons[] = $expandedIcon;
                }
            }
            
            return response()->json([
                'data' => [
                    'all-filtered' => $filteredIcons
                ],
                'meta' => [
                    'total' => count($filteredIcons),
                    'type' => 'bootstrap-icons',
                    'description' => '只顯示未被重新分類的 all 類別圖標',
                    'original_total' => count($originalIcons),
                    'filtered_count' => count($originalIcons) - count($filteredIcons),
                    'categories' => [
                        'all-filtered' => [
                            'name' => '全部圖標 (未分類)',
                            'description' => '只包含 category 仍為 all 的圖標'
                        ]
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load filtered icons: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 檢查圖標是否在其他分類中存在
     */
    private function isIconInOtherCategories(string $iconName): bool
    {
        $categoryFiles = ['general', 'ui', 'communications', 'files', 'media', 'people', 'alphanumeric', 'others'];
        
        foreach ($categoryFiles as $categoryFile) {
            $configPath = config_path("icon/bootstrap-icons/{$categoryFile}.php");
            
            if (file_exists($configPath)) {
                $categoryConfig = include $configPath;
                
                if (isset($categoryConfig['icons']) && is_array($categoryConfig['icons'])) {
                    foreach ($categoryConfig['icons'] as $icon) {
                        if ($icon['name'] === $iconName) {
                            return true; // 在其他分類中找到
                        }
                    }
                }
            }
        }
        
        return false; // 未在其他分類中找到
    }
    
    /**
     * 生成圖標關鍵字
     */
    private function generateIconKeywords(string $name, array $originalKeywords): array
    {
        $keywords = $originalKeywords;
        
        // 從名稱中提取關鍵字
        $nameKeywords = [];
        $cleanName = strtolower($name);
        $words = preg_split('/[\s\-_]+/', $cleanName);
        
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) > 1) {
                $nameKeywords[] = $word;
            }
        }
        
        return array_unique(array_merge($keywords, $nameKeywords));
    }
}