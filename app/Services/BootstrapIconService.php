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
                // 相容新舊格式：新格式使用 'id'，舊格式使用 'category'
                $categoryId = $categoryData['id'] ?? $categoryData['category'];
                
                // 添加分類資訊到 meta
                $result['meta']['categories'][$categoryId] = [
                    'name' => $categoryData['name'],
                    'description' => $categoryData['description']
                ];
                
                // 處理該分類下的每個 icon (新的扁平結構)
                foreach ($categoryData['icons'] as $icon) {
                    // 新結構中每個 icon 已經是完整的項目，直接使用
                    $expandedIcons[] = $icon;
                }
            }
            
            // 按分類分組 - 根據實際圖標的分類來分組
            $allCategories = ['all', 'general', 'ui', 'communications', 'files', 'media', 'people', 'alphanumeric', 'others'];
            
            // 初始化所有分類
            foreach ($allCategories as $categoryId) {
                $result['data'][$categoryId] = [];
            }
            
            // 將圖標分配到對應分類
            foreach ($expandedIcons as $icon) {
                $iconCategory = $icon['category'];
                if (!isset($result['data'][$iconCategory])) {
                    $result['data'][$iconCategory] = [];
                }
                $result['data'][$iconCategory][] = $icon;
            }
            
            // 為實際存在的分類添加 meta 資訊
            foreach ($categoriesData as $categoryData) {
                $categoryId = $categoryData['id'] ?? $categoryData['category'];
                if (!isset($result['meta']['categories'][$categoryId])) {
                    $result['meta']['categories'][$categoryId] = [
                        'name' => $categoryData['name'],
                        'description' => $categoryData['description']
                    ];
                }
            }
            
            // 計算總數 - 計算唯一基礎圖標數量而非變體數量
            $uniqueBaseIcons = [];
            foreach ($expandedIcons as $icon) {
                $baseName = str_replace(['-fill', '-solid'], '', $icon['name']);
                $uniqueBaseIcons[$baseName] = true;
            }
            $result['meta']['total'] = count($uniqueBaseIcons);
            
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
            'all', 'general', 'ui', 'communications', 'files', 
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
    
    /**
     * 取得過濾後的 'all' 分類圖標
     * 只回傳 category 欄位仍然是 'all' 的圖標
     */
    public function getAllFilteredIcons(): array
    {
        $allData = $this->getAllBootstrapIcons();
        
        // 取得 'all' 分類的圖標
        $allCategoryIcons = $allData['data']['all'] ?? [];
        
        // 篩選出 category 欄位仍然是 'all' 的圖標
        $filteredIcons = [];
        foreach ($allCategoryIcons as $icon) {
            // 檢查展開後圖標的 category 欄位
            if (isset($icon['category']) && $icon['category'] === 'all') {
                $filteredIcons[] = $icon;
            }
        }
        
        return [
            'data' => [
                'all-filtered' => $filteredIcons
            ],
            'meta' => [
                'total' => count($filteredIcons),
                'type' => 'bootstrap-icons',
                'description' => '只顯示未被重新分類的 all 類別圖標',
                'original_total' => count($allCategoryIcons),
                'filtered_count' => count($allCategoryIcons) - count($filteredIcons),
                'categories' => [
                    'all-filtered' => [
                        'name' => '全部圖標 (未分類)',
                        'description' => '只包含 category 仍為 all 的圖標'
                    ]
                ]
            ]
        ];
    }
}