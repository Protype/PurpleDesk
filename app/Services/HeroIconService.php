<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class HeroIconService
{
    /**
     * 取得所有 HeroIcons 資料
     * 
     * @return array
     */
    public function getAllHeroIcons(): array
    {
        return Cache::remember('heroicons_data', 86400, function () {
            $config = config('icon.heroicons');
            $icons = $config['icons'] ?? [];
            $categories = $config['categories'] ?? [];
            $variantMapping = $config['variant_mapping'] ?? [];
            $supportedVariants = $config['supported_variants'] ?? [];
            
            // 為每個圖標添加變體資訊
            $iconsWithVariants = array_map(function ($icon) use ($variantMapping) {
                return array_merge($icon, [
                    'base' => $icon['component'],
                    'variants' => $this->buildIconVariants($icon['component'], $variantMapping),
                    'defaultVariant' => 'outline'
                ]);
            }, $icons);
            
            $result = [
                'data' => $iconsWithVariants,
                'meta' => [
                    'total' => count($iconsWithVariants),
                    'categories' => array_keys($categories),
                    'supportedVariants' => $supportedVariants,
                    'variantMapping' => $variantMapping
                ]
            ];
            
            return $result;
        });
    }
    
    /**
     * 清除快取
     * 
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('heroicons_data');
    }
    
    /**
     * 取得分類清單
     * 
     * @return array
     */
    public function getCategories(): array
    {
        $config = config('icon.heroicons');
        return $config['categories'] ?? [];
    }
    
    /**
     * 取得指定分類的圖標
     * 
     * @param string $category
     * @return array
     */
    public function getIconsByCategory(string $category): array
    {
        $allIcons = $this->getAllHeroIcons();
        
        $filteredIcons = array_filter($allIcons['data'], function ($icon) use ($category) {
            return $icon['category'] === $category;
        });
        
        return [
            'data' => array_values($filteredIcons),
            'meta' => [
                'total' => count($filteredIcons),
                'category' => $category,
                'categories' => $allIcons['meta']['categories']
            ]
        ];
    }
    
    /**
     * 搜尋圖標
     * 
     * @param string $query
     * @return array
     */
    public function searchIcons(string $query): array
    {
        $allIcons = $this->getAllHeroIcons();
        $query = strtolower($query);
        
        $filteredIcons = array_filter($allIcons['data'], function ($icon) use ($query) {
            // 搜尋名稱
            if (strpos(strtolower($icon['name']), $query) !== false) {
                return true;
            }
            
            // 搜尋關鍵字
            foreach ($icon['keywords'] as $keyword) {
                if (strpos(strtolower($keyword), $query) !== false) {
                    return true;
                }
            }
            
            return false;
        });
        
        return [
            'data' => array_values($filteredIcons),
            'meta' => [
                'total' => count($filteredIcons),
                'query' => $query,
                'categories' => $allIcons['meta']['categories']
            ]
        ];
    }
    
    /**
     * 建立圖標變體資訊
     * 
     * @param string $componentName
     * @param array $variantMapping
     * @return array
     */
    private function buildIconVariants(string $componentName, array $variantMapping): array
    {
        $variants = [];
        
        foreach ($variantMapping as $style => $mapping) {
            $variants[$style] = [
                'component' => $componentName,
                'path' => $mapping['path'],
                'description' => $mapping['description']
            ];
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
        $config = config('icon.heroicons');
        return $config['variant_mapping'] ?? [];
    }
    
    /**
     * 取得支援的變體類型
     * 
     * @return array
     */
    public function getSupportedVariants(): array
    {
        $config = config('icon.heroicons');
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
        $allIcons = $this->getAllHeroIcons();
        $variantMapping = $this->getVariantMapping();
        
        if (!isset($variantMapping[$style])) {
            throw new \InvalidArgumentException("Unsupported style: {$style}");
        }
        
        // 為每個圖標應用指定樣式
        $styledIcons = array_map(function ($icon) use ($style) {
            return array_merge($icon, [
                'currentStyle' => $style,
                'component' => $icon['variants'][$style]['component'] ?? $icon['component']
            ]);
        }, $allIcons['data']);
        
        return [
            'data' => $styledIcons,
            'meta' => array_merge($allIcons['meta'], [
                'currentStyle' => $style,
                'description' => $variantMapping[$style]['description'] ?? $style
            ])
        ];
    }
    
    /**
     * 取得單一圖標的變體資訊
     * 
     * @param string $componentName
     * @return array|null
     */
    public function getIconVariants(string $componentName): ?array
    {
        $allIcons = $this->getAllHeroIcons();
        
        foreach ($allIcons['data'] as $icon) {
            if ($icon['component'] === $componentName || $icon['base'] === $componentName) {
                return $icon['variants'] ?? null;
            }
        }
        
        return null;
    }
    
    /**
     * 檢查圖標是否支援特定樣式
     * 
     * @param string $componentName
     * @param string $style
     * @return bool
     */
    public function hasStyleVariant(string $componentName, string $style): bool
    {
        $variants = $this->getIconVariants($componentName);
        
        return $variants && isset($variants[$style]);
    }
    
    /**
     * 取得圖標的特定樣式組件名稱
     * 
     * @param string $componentName
     * @param string $style
     * @return string|null
     */
    public function getIconComponent(string $componentName, string $style): ?string
    {
        $variants = $this->getIconVariants($componentName);
        
        if ($variants && isset($variants[$style])) {
            return $variants[$style]['component'];
        }
        
        return null;
    }
}