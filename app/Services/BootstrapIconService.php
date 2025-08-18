<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class BootstrapIconService
{
    /**
     * 取得所有 Bootstrap Icons 資料
     * 
     * @return array
     */
    public function getAllBootstrapIcons(): array
    {
        return Cache::remember('bootstrap_icons_data', 86400, function () {
            $config = config('icon.bootstrap-icons');
            $icons = $config['icons'] ?? [];
            $categories = $config['categories'] ?? [];
            
            // 計算總圖標數量
            $totalCount = 0;
            foreach ($icons as $categoryIcons) {
                $totalCount += count($categoryIcons);
            }
            
            $result = [
                'data' => $icons,
                'meta' => [
                    'total' => $totalCount,
                    'categories' => $categories
                ]
            ];
            
            return $result;
        });
    }
    
    /**
     * 根據分類篩選取得 Bootstrap Icons
     * 
     * @param array $categoryNames 分類名稱陣列
     * @return array
     */
    public function getIconsByCategories(array $categoryNames = []): array
    {
        if (empty($categoryNames)) {
            return $this->getAllBootstrapIcons();
        }
        
        $cacheKey = 'bootstrap_icons_filtered_' . md5(implode(',', $categoryNames));
        
        return Cache::remember($cacheKey, 3600, function () use ($categoryNames) {
            $config = config('icon.bootstrap-icons');
            $allIcons = $config['icons'] ?? [];
            $categories = $config['categories'] ?? [];
            
            $filteredIcons = [];
            $filteredCategories = [];
            $totalCount = 0;
            
            foreach ($categoryNames as $categoryName) {
                if (isset($allIcons[$categoryName])) {
                    $filteredIcons[$categoryName] = $allIcons[$categoryName];
                    $filteredCategories[$categoryName] = $categories[$categoryName] ?? [];
                    $totalCount += count($allIcons[$categoryName]);
                }
            }
            
            return [
                'data' => $filteredIcons,
                'meta' => [
                    'total' => $totalCount,
                    'categories' => $filteredCategories,
                    'requested_categories' => $categoryNames,
                    'found_categories' => array_keys($filteredIcons)
                ]
            ];
        });
    }
    
    /**
     * 搜尋 Bootstrap Icons
     * 
     * @param string $query 搜尋關鍵字
     * @param array $categories 限制搜尋的分類
     * @return array
     */
    public function searchIcons(string $query, array $categories = []): array
    {
        $query = strtolower(trim($query));
        if (empty($query)) {
            return [
                'data' => [],
                'meta' => [
                    'total' => 0,
                    'query' => $query,
                    'categories' => []
                ]
            ];
        }
        
        $cacheKey = 'bootstrap_icons_search_' . md5($query . implode(',', $categories));
        
        return Cache::remember($cacheKey, 1800, function () use ($query, $categories) {
            $config = config('icon.bootstrap-icons');
            $allIcons = $config['icons'] ?? [];
            
            $results = [];
            $totalCount = 0;
            
            // 決定要搜尋的分類
            $searchCategories = empty($categories) ? array_keys($allIcons) : $categories;
            
            foreach ($searchCategories as $categoryName) {
                if (!isset($allIcons[$categoryName])) {
                    continue;
                }
                
                $categoryResults = [];
                
                foreach ($allIcons[$categoryName] as $icon) {
                    $matches = false;
                    
                    // 搜尋名稱
                    if (strpos(strtolower($icon['name']), $query) !== false) {
                        $matches = true;
                    }
                    
                    // 搜尋 CSS class
                    if (!$matches && strpos(strtolower($icon['class']), $query) !== false) {
                        $matches = true;
                    }
                    
                    // 搜尋關鍵字
                    if (!$matches && isset($icon['keywords'])) {
                        foreach ($icon['keywords'] as $keyword) {
                            if (strpos(strtolower($keyword), $query) !== false) {
                                $matches = true;
                                break;
                            }
                        }
                    }
                    
                    if ($matches) {
                        $categoryResults[] = $icon;
                    }
                }
                
                if (!empty($categoryResults)) {
                    $results[$categoryName] = $categoryResults;
                    $totalCount += count($categoryResults);
                }
            }
            
            return [
                'data' => $results,
                'meta' => [
                    'total' => $totalCount,
                    'query' => $query,
                    'categories' => array_keys($results),
                    'searched_categories' => $searchCategories
                ]
            ];
        });
    }
    
    /**
     * 取得分類清單
     * 
     * @return array
     */
    public function getCategories(): array
    {
        $config = config('icon.bootstrap-icons');
        return $config['categories'] ?? [];
    }
    
    /**
     * 取得載入優先級設定
     * 
     * @return array
     */
    public function getLoadingPriority(): array
    {
        $config = config('icon.bootstrap-icons');
        return $config['loading_priority'] ?? [];
    }
    
    /**
     * 根據優先級取得分類
     * 
     * @param string $priority 優先級 (immediate, high, medium, low)
     * @return array
     */
    public function getIconsByPriority(string $priority): array
    {
        $loadingPriority = $this->getLoadingPriority();
        $categoryNames = $loadingPriority[$priority] ?? [];
        
        return $this->getIconsByCategories($categoryNames);
    }
    
    /**
     * 取得圖標統計資訊
     * 
     * @return array
     */
    public function getIconStats(): array
    {
        $config = config('icon.bootstrap-icons');
        $icons = $config['icons'] ?? [];
        $categories = $config['categories'] ?? [];
        
        $stats = [
            'total_categories' => count($categories),
            'total_icons' => 0,
            'categories' => []
        ];
        
        foreach ($icons as $categoryName => $categoryIcons) {
            $count = count($categoryIcons);
            $stats['total_icons'] += $count;
            $stats['categories'][$categoryName] = [
                'name' => $categories[$categoryName]['name'] ?? $categoryName,
                'count' => $count,
                'priority' => $categories[$categoryName]['priority'] ?? 'low'
            ];
        }
        
        return $stats;
    }
    
    /**
     * 清除快取
     * 
     * @return void
     */
    public function clearCache(): void
    {
        // 清除主快取
        Cache::forget('bootstrap_icons_data');
        
        // 清除搜尋相關快取（使用標籤清除）
        $cacheKeys = [
            'bootstrap_icons_filtered_*',
            'bootstrap_icons_search_*'
        ];
        
        // 由於 Laravel 的檔案快取不支援標籤，我們需要手動清除
        // 在生產環境中建議使用 Redis 等支援標籤的快取驅動
        $this->clearCacheByPattern('bootstrap_icons_filtered_');
        $this->clearCacheByPattern('bootstrap_icons_search_');
    }
    
    /**
     * 根據模式清除快取
     * 
     * @param string $pattern
     * @return void
     */
    private function clearCacheByPattern(string $pattern): void
    {
        // 這裡是簡化的實作，在實際生產環境中應該使用支援標籤的快取系統
        // 或者維護一個快取鍵的清單來進行批量清除
        
        // 暫時使用簡單的方法：清除常見的分類組合
        $commonCombinations = [
            ['general'],
            ['ui'],
            ['general', 'ui'],
            ['communications'],
            ['files'],
            ['media'],
            ['people'],
            ['alphanumeric'],
            ['others'],
            ['general', 'ui', 'communications'],
        ];
        
        foreach ($commonCombinations as $combination) {
            $key = $pattern . md5(implode(',', $combination));
            Cache::forget($key);
        }
    }
    
    /**
     * 驗證分類名稱是否有效
     * 
     * @param array $categoryNames
     * @return array 有效的分類名稱
     */
    public function validateCategories(array $categoryNames): array
    {
        $categories = $this->getCategories();
        $validCategories = [];
        
        foreach ($categoryNames as $categoryName) {
            if (isset($categories[$categoryName])) {
                $validCategories[] = $categoryName;
            }
        }
        
        return $validCategories;
    }
}