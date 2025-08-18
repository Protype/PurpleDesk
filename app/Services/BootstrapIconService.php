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
            $variantMapping = $config['variant_mapping'] ?? [];
            $supportedVariants = $config['supported_variants'] ?? [];
            
            // 為每個分類的圖標添加變體資訊
            $iconsWithVariants = [];
            $totalCount = 0;
            
            foreach ($icons as $categoryName => $categoryIcons) {
                $iconsWithVariants[$categoryName] = array_map(function ($icon) use ($variantMapping) {
                    return array_merge($icon, [
                        'base' => $this->extractBaseIconName($icon['class']),
                        'variants' => $this->buildIconVariants($icon['class'], $variantMapping),
                        'defaultVariant' => 'outline'
                    ]);
                }, $categoryIcons);
                
                $totalCount += count($categoryIcons);
            }
            
            $result = [
                'data' => $iconsWithVariants,
                'meta' => [
                    'total' => $totalCount,
                    'categories' => $categories,
                    'supportedVariants' => $supportedVariants,
                    'variantMapping' => $variantMapping
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
    
    /**
     * 提取基礎圖標名稱（移除 -fill 後綴）
     * 
     * @param string $className
     * @return string
     */
    private function extractBaseIconName(string $className): string
    {
        return preg_replace('/-fill$/', '', $className);
    }
    
    /**
     * 建立圖標變體資訊
     * 
     * @param string $className
     * @param array $variantMapping
     * @return array
     */
    private function buildIconVariants(string $className, array $variantMapping): array
    {
        $baseClassName = $this->extractBaseIconName($className);
        $variants = [];
        
        foreach ($variantMapping as $style => $mapping) {
            if ($style === 'outline') {
                $variants[$style] = [
                    'class' => $baseClassName,
                    'description' => $mapping['description']
                ];
            } elseif ($style === 'solid') {
                $variants[$style] = [
                    'class' => $baseClassName . $mapping['suffix'],
                    'description' => $mapping['description']
                ];
            }
        }
        
        return $variants;
    }
    
    /**
     * 取得變體映射資訊
     * 
     * @return array
     */
    public function getVariantMapping(): array
    {
        $config = config('icon.bootstrap-icons');
        return $config['variant_mapping'] ?? [];
    }
    
    /**
     * 取得支援的變體類型
     * 
     * @return array
     */
    public function getSupportedVariants(): array
    {
        $config = config('icon.bootstrap-icons');
        return $config['supported_variants'] ?? [];
    }
    
    /**
     * 取得特定樣式的圖標資料
     * 
     * @param string $style
     * @return array
     */
    public function getIconsByStyle(string $style): array
    {
        $allIcons = $this->getAllBootstrapIcons();
        $variantMapping = $this->getVariantMapping();
        
        if (!isset($variantMapping[$style])) {
            throw new \InvalidArgumentException("Unsupported style: {$style}");
        }
        
        // 根據樣式過濾和轉換圖標
        $styledIcons = [];
        $totalCount = 0;
        
        foreach ($allIcons['data'] as $categoryName => $categoryIcons) {
            $filteredIcons = [];
            
            foreach ($categoryIcons as $icon) {
                if ($this->shouldIncludeIconForStyle($icon, $style)) {
                    $styledIcon = array_merge($icon, [
                        'currentStyle' => $style,
                        'class' => $this->getIconClassForStyle($icon, $style)
                    ]);
                    $filteredIcons[] = $styledIcon;
                }
            }
            
            if (!empty($filteredIcons)) {
                $styledIcons[$categoryName] = $filteredIcons;
                $totalCount += count($filteredIcons);
            }
        }
        
        return [
            'data' => $styledIcons,
            'meta' => array_merge($allIcons['meta'], [
                'total' => $totalCount,
                'currentStyle' => $style,
                'description' => $variantMapping[$style]['description'] ?? $style
            ])
        ];
    }
    
    /**
     * 判斷圖標是否應該包含在特定樣式中
     * 
     * @param array $icon
     * @param string $style
     * @return bool
     */
    private function shouldIncludeIconForStyle(array $icon, string $style): bool
    {
        $className = $icon['class'];
        $isFillIcon = str_contains($className, '-fill');
        
        if ($style === 'outline') {
            // outline 樣式：排除 -fill 圖標
            return !$isFillIcon;
        } elseif ($style === 'solid') {
            if ($isFillIcon) {
                // 如果是 -fill 圖標，直接包含
                return true;
            } else {
                // 基礎圖標：檢查是否有對應的 -fill 版本
                // 如果沒有 -fill 版本，也包含基礎版本
                return !$this->hasFilledVariant($className);
            }
        }
        
        return true;
    }
    
    /**
     * 取得圖標在特定樣式下的 class 名稱
     * 
     * @param array $icon
     * @param string $style
     * @return string
     */
    private function getIconClassForStyle(array $icon, string $style): string
    {
        $baseClassName = $this->extractBaseIconName($icon['class']);
        
        if ($style === 'outline') {
            return $baseClassName;
        } elseif ($style === 'solid') {
            // 如果原本就是 -fill 圖標，保持原樣
            if (str_contains($icon['class'], '-fill')) {
                return $icon['class'];
            }
            // 否則添加 -fill 後綴
            return $baseClassName . '-fill';
        }
        
        return $icon['class'];
    }
    
    /**
     * 檢查圖標是否有填充變體
     * 
     * @param string $baseClassName
     * @return bool
     */
    private function hasFilledVariant(string $baseClassName): bool
    {
        $allIcons = $this->getAllBootstrapIcons();
        $filledClassName = $baseClassName . '-fill';
        
        foreach ($allIcons['data'] as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                if ($icon['class'] === $filledClassName) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * 取得單一圖標的變體資訊
     * 
     * @param string $className
     * @return array|null
     */
    public function getIconVariants(string $className): ?array
    {
        $allIcons = $this->getAllBootstrapIcons();
        
        foreach ($allIcons['data'] as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                if ($icon['class'] === $className || $icon['base'] === $this->extractBaseIconName($className)) {
                    return $icon['variants'] ?? null;
                }
            }
        }
        
        return null;
    }
    
    /**
     * 檢查圖標是否支援特定樣式
     * 
     * @param string $className
     * @param string $style
     * @return bool
     */
    public function hasStyleVariant(string $className, string $style): bool
    {
        $variants = $this->getIconVariants($className);
        
        return $variants && isset($variants[$style]);
    }
    
    /**
     * 取得圖標的特定樣式 class 名稱
     * 
     * @param string $className
     * @param string $style
     * @return string|null
     */
    public function getIconClass(string $className, string $style): ?string
    {
        $variants = $this->getIconVariants($className);
        
        if ($variants && isset($variants[$style])) {
            return $variants[$style]['class'];
        }
        
        return null;
    }
}