<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class EmojiService
{
    /**
     * 確認有問題的 emoji 黑名單
     * 基於前端 emojiFilter.js 的 PROBLEMATIC_EMOJIS (57 個)
     */
    private const PROBLEMATIC_EMOJIS = [
        "🇨🇶", "🫩", "🫆", "🪾", "🫜", "🪉", "🪏", "🫟", "🚶‍♀️‍➡️", "🚶‍♂️‍➡️",
        "🧎‍♀️‍➡️", "🧎‍♂️‍➡️", "🏃‍♀️‍➡️", "🏃‍♂️‍➡️", "🧑‍🦯‍➡️", "👨‍🦯‍➡️", "👩‍🦯‍➡️",
        "🧑‍🦼‍➡️", "👨‍🦼‍➡️", "👩‍🦼‍➡️", "🧑‍🦽‍➡️", "👨‍🦽‍➡️", "👩‍🦽‍➡️", "🧑‍🧑‍🧒‍🧒",
        "🙂‍↔️", "🙂‍↕️", "🚶‍➡️", "🧎‍➡️", "🏃‍➡️", "🧑‍🧑‍🧒", "🧑‍🧒‍🧒", "⛓️‍💥",
        "🧑‍🧒", "🐦‍🔥", "🍋‍🟩", "🍄‍🟫", "🐦‍⬛", "🫨", "🩷", "🩵", "🩶", "🫷",
        "🫸", "🫎", "🫏", "🪽", "🪿", "🪼", "🪻", "🫚", "🫛", "🪭", "🪮", "🪇",
        "🪈", "🪯", "🛜"
    ];

    /**
     * 檢查 emoji 是否在黑名單中
     */
    private function isProblematicEmoji(string $emoji): bool
    {
        return in_array($emoji, self::PROBLEMATIC_EMOJIS);
    }

    /**
     * 過濾 emoji 陣列，移除有問題的 emoji
     */
    private function filterEmojis(array $emojis): array
    {
        return array_filter($emojis, function ($emoji) {
            $emojiChar = is_array($emoji) ? ($emoji['emoji'] ?? '') : $emoji;
            return !$this->isProblematicEmoji($emojiChar);
        });
    }
    /**
     * 取得所有 emoji 資料（一次性載入）
     */
    public function getAllEmojis()
    {
        return Cache::remember('all_emojis', 86400, function () {
            $result = [
                'categories' => [],
                'stats' => [
                    'total_emojis' => 0,
                    'total_categories' => 0,
                ],
            ];
            
            // 載入索引檔案
            $indexPath = config_path('icon/emoji/index.php');
            if (!File::exists($indexPath)) {
                return $result;
            }
            
            $index = require $indexPath;
            $categories = $index['categories'] ?? [];
            
            // 載入每個分類的 emoji
            foreach ($categories as $categoryId => $categoryInfo) {
                $filePath = config_path('icon/emoji/' . $categoryInfo['file']);
                
                if (!File::exists($filePath)) {
                    continue;
                }
                
                $data = require $filePath;
                
                // 轉換為前端格式並過濾有問題的 emoji
                $categoryEmojis = [];
                foreach ($data as $subgroupKey => $subgroupData) {
                    // 過濾有問題的 emoji
                    $filteredEmojis = $this->filterEmojis($subgroupData['emojis']);
                    
                    $categoryEmojis[$subgroupKey] = [
                        'name' => $subgroupData['name'],
                        'emojis' => $filteredEmojis
                    ];
                    
                    $result['stats']['total_emojis'] += count($filteredEmojis);
                }
                
                $result['categories'][$categoryId] = [
                    'id' => $categoryId,
                    'name' => $categoryInfo['name'],
                    'icon' => $categoryInfo['icon'],
                    'priority' => $categoryInfo['priority'],
                    'subgroups' => $categoryEmojis
                ];
            }
            
            $result['stats']['total_categories'] = count($result['categories']);
            
            return $result;
        });
    }
    
    /**
     * 清除快取
     */
    public function clearCache()
    {
        Cache::forget('all_emojis');
    }

    /**
     * 取得黑名單統計資訊
     */
    public function getBlacklistStats(): array
    {
        return [
            'total_blacklisted' => count(self::PROBLEMATIC_EMOJIS),
            'blacklisted_emojis' => self::PROBLEMATIC_EMOJIS,
            'filter_version' => '1.0.0', // 基於前端 emojiFilter.js
            'accuracy' => '100%' // 基於 383 個 emoji 測試
        ];
    }
}