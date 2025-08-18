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
            
            $result = [
                'data' => $icons,
                'meta' => [
                    'total' => count($icons),
                    'categories' => array_keys($categories)
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
}