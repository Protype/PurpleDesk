<?php

/**
 * 圖標處理器
 * 負責圖標名稱轉換、關鍵字生成、變體檢測等邏輯
 */
class IconProcessor
{
    /**
     * 將圖標名稱轉換為顯示名稱
     * 例如: 'house-door' => 'House Door'
     * @param string $iconName 圖標名稱
     * @return string 顯示名稱
     */
    public function iconNameToDisplayName($iconName)
    {
        // 將連字號替換為空格，每個單字首字母大寫
        return ucwords(str_replace('-', ' ', $iconName));
    }
    
    /**
     * 從圖標名稱生成關鍵字
     * @param string $iconName 圖標名稱
     * @return array 關鍵字陣列
     */
    public function generateKeywords($iconName)
    {
        // 按連字號分割
        return explode('-', $iconName);
    }
    
    /**
     * 檢測圖標變體
     * @param string $iconName 圖標名稱
     * @return array 變體配置
     */
    public function detectVariants($iconName)
    {
        $variants = [];
        
        if (substr($iconName, -5) === '-fill') {
            // 這是 fill 圖標，有 solid 和 outline 變體
            $baseIconName = substr($iconName, 0, -5); // 移除 '-fill'
            $variants['solid'] = ['class' => "bi-$iconName"];
            $variants['outline'] = ['class' => "bi-$baseIconName"];
        } else {
            // 檢查是否有對應的 fill 變體
            $fillIconName = $iconName . '-fill';
            $variants['outline'] = ['class' => "bi-$iconName"];
            
            // 這裡簡化處理，實際上應該檢查 fill 變體是否真的存在
            // 為了簡化，我們先假設部分常見圖標有 fill 變體
            $commonFillIcons = [
                'alarm', 'house', 'cart', 'heart', 'star', 'bell', 
                'bookmark', 'calendar', 'envelope', 'folder',
                'person', 'shield', 'clock', 'badge'
            ];
            
            if (in_array($iconName, $commonFillIcons)) {
                $variants['solid'] = ['class' => "bi-$fillIconName"];
            }
        }
        
        return $variants;
    }
    
    /**
     * 處理單個圖標，生成完整的配置資料
     * @param string $iconName 圖標名稱
     * @param string $category 分類，預設為 'general'
     * @return array 完整的圖標配置
     */
    public function processIcon($iconName, $category = 'general')
    {
        return [
            'name' => $iconName,
            'displayName' => $this->iconNameToDisplayName($iconName),
            'class' => "bi-$iconName",
            'keywords' => $this->generateKeywords($iconName),
            'type' => 'bootstrap',
            'category' => $category,
            'variants' => $this->detectVariants($iconName)
        ];
    }
    
    /**
     * 根據分類篩選圖標
     * @param array $iconNames 所有圖標名稱
     * @param string $category 目標分類
     * @return array 篩選後的圖標名稱
     */
    public function filterIconsByCategory($iconNames, $category)
    {
        // 簡化的分類邏輯
        $categoryKeywords = [
            'general' => [
                'house', 'home', 'search', 'settings', 'gear', 'cog',
                'alarm', 'clock', 'calendar', 'bookmark', 'star',
                'heart', 'thumbs', 'hand', 'eye', 'visible'
            ],
            'ui' => [
                'menu', 'bars', 'grid', 'list', 'table', 'layout',
                'sidebar', 'navbar', 'toggle', 'switch', 'button'
            ],
            'files' => [
                'file', 'folder', 'document', 'archive', 'download',
                'upload', 'save', 'open', 'disk', 'storage'
            ],
            'communications' => [
                'envelope', 'mail', 'message', 'chat', 'phone',
                'telephone', 'mobile', 'wifi', 'signal', 'broadcast'
            ]
        ];
        
        if (!isset($categoryKeywords[$category])) {
            return $iconNames; // 如果分類不存在，返回所有圖標
        }
        
        $keywords = $categoryKeywords[$category];
        $filteredIcons = [];
        
        foreach ($iconNames as $iconName) {
            foreach ($keywords as $keyword) {
                if (strpos($iconName, $keyword) !== false) {
                    $filteredIcons[] = $iconName;
                    break; // 找到一個匹配就足夠了
                }
            }
        }
        
        return $filteredIcons;
    }
    
    /**
     * 批量處理圖標
     * @param array $iconNames 圖標名稱陣列
     * @param string $category 分類
     * @return array 處理後的圖標配置陣列
     */
    public function processIcons($iconNames, $category = 'general')
    {
        $processedIcons = [];
        
        foreach ($iconNames as $iconName) {
            $processedIcons[] = $this->processIcon($iconName, $category);
        }
        
        return $processedIcons;
    }
}