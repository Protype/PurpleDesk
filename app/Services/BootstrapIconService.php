<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class BootstrapIconService
{
    /**
     * 取得所有 Bootstrap Icons 資料（新格式：data/meta 結構）
     */
    public function getAllBootstrapIcons(): array
    {
        return Cache::remember('bootstrap_icons_data_v2', 86400, function () {
            // 從新的分類檔案結構載入資料
            $categoriesData = $this->loadCategoriesFromFiles();
            
            $result = [
                'data' => [],
                'meta' => [
                    'total' => 0,
                    'type' => 'bootstrap-icons',
                    'categories' => []
                ]
            ];
            
            // 展開每個 icon 的變體為獨立項目
            $expandedIcons = [];
            
            foreach ($categoriesData as $categoryData) {
                $categoryId = $categoryData['category'];
                
                // 添加分類資訊到 meta
                $result['meta']['categories'][$categoryId] = [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description']
                ];
                
                // 處理該分類下的每個 icon
                foreach ($categoryData['icons'] as $icon) {
                    $baseName = $icon['displayName'] ?? $icon['name'];
                    $baseClass = $icon['class'];
                    $keywords = $icon['keywords'] ?? [];
                    $variants = $icon['variants'] ?? [];
                    
                    // 為每個變體創建獨立的項目
                    foreach ($variants as $variantType => $variantData) {
                        $variantClass = $variantData['class'];
                        $iconId = str_replace('bi-', '', $icon['name']) . '-' . $variantType;
                        
                        $expandedIcon = [
                            'id' => $iconId,
                            'name' => $baseName,
                            'value' => $variantClass, // Bootstrap Icon 使用 CSS class 作為 value
                            'type' => 'bootstrap-icons',
                            'keywords' => $this->generateKeywords($baseName, $keywords),
                            'category' => $categoryId,
                            'has_variants' => count($variants) > 1,
                            'variant_type' => $variantType
                        ];
                        
                        $expandedIcons[] = $expandedIcon;
                    }
                }
            }
            
            // 按分類分組
            foreach ($expandedIcons as $icon) {
                $categoryId = $icon['category'];
                
                if (!isset($result['data'][$categoryId])) {
                    $result['data'][$categoryId] = [];
                }
                
                $result['data'][$categoryId][] = $icon;
            }
            
            // 計算總數
            $result['meta']['total'] = count($expandedIcons);
            
            return $result;
        });
    }

    /**
     * 從分類檔案載入資料
     */
    private function loadCategoriesFromFiles(): array
    {
        $categoriesData = [];
        $categoryFiles = [
            'general', 'ui', 'communications', 'files', 
            'media', 'people', 'alphanumeric', 'others'
        ];
        
        foreach ($categoryFiles as $categoryFile) {
            $configKey = "icon.bootstrap-icons.{$categoryFile}";
            $categoryData = config($configKey);
            
            if ($categoryData && isset($categoryData['icons'])) {
                $categoriesData[] = $categoryData;
            }
        }
        
        return $categoriesData;
    }
    
    /**
     * 生成關鍵字
     */
    private function generateKeywords(string $name, array $originalKeywords): array
    {
        // 合併原有關鍵字和從名稱產生的關鍵字
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
    
    /**
     * 清除快取
     */
    public function clearCache(): void
    {
        // 清除主快取
        Cache::forget('bootstrap_icons_data');
        Cache::forget('bootstrap_icons_data_v2');
    }
    
    /**
     * 取得分類清單
     */
    public function getCategories(): array
    {
        $allData = $this->getAllBootstrapIcons();
        return $allData['meta']['categories'] ?? [];
    }
    
    /**
     * 根據分類取得 Bootstrap Icons
     */
    public function getBootstrapIconsByCategory(string $categoryId): array
    {
        $allData = $this->getAllBootstrapIcons();
        $categories = $allData['meta']['categories'];
        
        // 驗證分類是否存在
        if (!isset($categories[$categoryId])) {
            throw new \InvalidArgumentException("Invalid category: {$categoryId}");
        }
        
        // 取得該分類的 Bootstrap Icons
        $categoryIcons = $allData['data'][$categoryId] ?? [];
        
        return [
            'data' => [
                $categoryId => $categoryIcons
            ],
            'meta' => [
                'total' => count($categoryIcons),
                'type' => 'bootstrap-icons',
                'categories' => [
                    $categoryId => $categories[$categoryId]
                ]
            ]
        ];
    }
}