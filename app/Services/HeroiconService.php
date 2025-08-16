<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class HeroiconService
{
    /**
     * 取得所有 heroicon 資料（一次性載入）
     */
    public function getAllHeroicons()
    {
        return Cache::remember('all_heroicons', 86400, function () {
            $result = [
                'categories' => [],
                'stats' => [
                    'total_icons' => 0,
                    'total_categories' => 0,
                    'styles' => ['outline', 'solid'],
                ],
            ];
            
            // 載入索引檔案
            $indexPath = config_path('icon/heroicon/index.php');
            if (!File::exists($indexPath)) {
                return $result;
            }
            
            $index = require $indexPath;
            $categories = $index['categories'] ?? [];
            
            // 載入每個分類的 heroicon
            foreach ($categories as $categoryId => $categoryInfo) {
                $filePath = config_path('icon/heroicon/' . $categoryInfo['file']);
                
                if (!File::exists($filePath)) {
                    continue;
                }
                
                $data = require $filePath;
                
                // 轉換為前端格式
                $categoryIcons = [];
                foreach ($data as $subgroupKey => $subgroupData) {
                    $categoryIcons[$subgroupKey] = [
                        'name' => $subgroupData['name'],
                        'icons' => $subgroupData['icons']
                    ];
                    
                    $result['stats']['total_icons'] += count($subgroupData['icons']);
                }
                
                $result['categories'][$categoryId] = [
                    'id' => $categoryId,
                    'name' => $categoryInfo['name'],
                    'icon' => $categoryInfo['icon'],
                    'priority' => $categoryInfo['priority'],
                    'subgroups' => $categoryIcons
                ];
            }
            
            $result['stats']['total_categories'] = count($result['categories']);
            $result['stats'] = array_merge($result['stats'], $index['stats'] ?? []);
            
            return $result;
        });
    }
    
    /**
     * 清除快取
     */
    public function clearCache()
    {
        Cache::forget('all_heroicons');
    }
}