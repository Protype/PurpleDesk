<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class HeroIconService
{
    /**
     * 取得所有 HeroIcons 資料（新格式：data/meta 結構）
     */
    public function getAllHeroIcons(): array
    {
        return Cache::remember('heroicons_data_v2', 86400, function () {
            $config = config('icon.heroicons');
            $icons = $config['icons'] ?? [];
            $categories = $config['categories'] ?? [];
            
            $result = [
                'data' => [],
                'meta' => [
                    'total' => 0,
                    'type' => 'heroicons',
                    'categories' => []
                ]
            ];
            
            // 展開每個 icon 的變體為獨立項目
            $expandedIcons = [];
            $variants = ['outline', 'solid'];
            
            foreach ($icons as $icon) {
                $baseName = $icon['name'];
                $baseComponent = $icon['component'];
                $category = $icon['category'];
                $keywords = $icon['keywords'] ?? [];
                
                // 為每個變體創建獨立的 icon 項目
                foreach ($variants as $variant) {
                    $iconId = $this->generateIconId($baseComponent, $variant);
                    
                    $expandedIcon = [
                        'id' => $iconId,
                        'name' => $baseName,
                        'value' => $baseComponent, // HeroIcon 使用 component 名稱作為 value
                        'type' => 'heroicons',
                        'keywords' => $this->generateKeywords($baseName, $keywords),
                        'category' => $category,
                        'has_variants' => true,
                        'variant_type' => $variant
                    ];
                    
                    $expandedIcons[] = $expandedIcon;
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
            
            // 計算總數和分類資訊
            $result['meta']['total'] = count($expandedIcons);
            
            foreach ($categories as $categoryId => $categoryName) {
                if (isset($result['data'][$categoryId])) {
                    $result['meta']['categories'][$categoryId] = [
                        'name' => $categoryName,
                        'description' => $this->generateCategoryDescription($categoryName)
                    ];
                }
            }
            
            return $result;
        });
    }
    
    /**
     * 生成 icon ID
     */
    private function generateIconId(string $component, string $variant): string
    {
        // 從組件名稱移除 "Icon" 後綴，然後加上變體後綴
        $baseName = preg_replace('/Icon$/', '', $component);
        $baseName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $baseName));
        
        return $baseName . '-' . $variant;
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
     * 生成分類描述
     */
    private function generateCategoryDescription(string $categoryName): string
    {
        $descriptions = [
            '一般' => '一般用途圖標',
            '導航' => '導航與方向相關圖標',
            '使用者介面' => 'UI 控制項與介面元素',
            '溝通' => '溝通與聯繫相關圖標',
            '媒體' => '媒體與多媒體圖標',
            '商業' => '商業與金融相關圖標',
            '科技' => '科技與技術相關圖標',
            '安全' => '安全與保護相關圖標',
            '狀態' => '狀態與通知圖標',
            '資料' => '資料與圖表相關圖標',
            '設計' => '設計與創意工具',
            '科學' => '科學與研究相關圖標'
        ];
        
        return $descriptions[$categoryName] ?? $categoryName;
    }
    
    /**
     * 清除快取
     */
    public function clearCache(): void
    {
        Cache::forget('heroicons_data');
        Cache::forget('heroicons_data_v2');
    }
    
    /**
     * 取得分類清單
     */
    public function getCategories(): array
    {
        $allData = $this->getAllHeroIcons();
        return $allData['meta']['categories'] ?? [];
    }
    
    /**
     * 根據分類取得 HeroIcons
     */
    public function getHeroIconsByCategory(string $categoryId): array
    {
        $allData = $this->getAllHeroIcons();
        $categories = $allData['meta']['categories'];
        
        // 驗證分類是否存在
        if (!isset($categories[$categoryId])) {
            throw new \InvalidArgumentException("Invalid category: {$categoryId}");
        }
        
        // 取得該分類的 HeroIcons
        $categoryIcons = $allData['data'][$categoryId] ?? [];
        
        return [
            'data' => [
                $categoryId => $categoryIcons
            ],
            'meta' => [
                'total' => count($categoryIcons),
                'type' => 'heroicons',
                'categories' => [
                    $categoryId => $categories[$categoryId]
                ]
            ]
        ];
    }
}