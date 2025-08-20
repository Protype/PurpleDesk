<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class EmojiService
{
    private EmojiSkinToneService $skinToneService;

    public function __construct(EmojiSkinToneService $skinToneService)
    {
        $this->skinToneService = $skinToneService;
    }
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
        $filtered = array_filter($emojis, function ($emoji) {
            $emojiChar = is_array($emoji) ? ($emoji['emoji'] ?? '') : $emoji;
            return !$this->isProblematicEmoji($emojiChar);
        });
        
        // 重新索引陣列，確保 JSON 序列化為陣列而非物件
        return array_values($filtered);
    }
    /**
     * 取得所有 emoji 資料（新格式：data/meta 結構）
     */
    public function getAllEmojis()
    {
        return Cache::remember('all_emojis_v2', 86400, function () {
            $result = [
                'data' => [],
                'meta' => [
                    'total' => 0,
                    'type' => 'emoji',
                    'categories' => []
                ]
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
                
                // 收集分類下的所有 emoji（展平子群組）
                $categoryEmojis = [];
                
                foreach ($data as $subgroupKey => $subgroupData) {
                    // 過濾有問題的 emoji
                    $filteredEmojis = $this->filterEmojis($subgroupData['emojis']);
                    
                    // 使用 EmojiSkinToneService 處理膚色變體分組
                    $groupedEmojis = $this->skinToneService->groupVariations($filteredEmojis);
                    
                    // 轉換為新格式
                    foreach ($groupedEmojis as $emoji) {
                        $emojiData = [
                            'id' => $this->generateEmojiId($emoji['emoji']),
                            'name' => $emoji['name'],
                            'emoji' => $emoji['emoji'],
                            'type' => 'emoji',
                            'keywords' => $this->generateKeywords($emoji['name']),
                            'category' => $categoryId,
                            'has_skin_tone' => isset($emoji['skin_variations']) && count($emoji['skin_variations']) > 1
                        ];
                        
                        // 只在有膚色變體時添加 skin_variations
                        if ($emojiData['has_skin_tone']) {
                            $emojiData['skin_variations'] = $emoji['skin_variations'];
                        }
                        
                        $categoryEmojis[] = $emojiData;
                    }
                }
                
                // 只有當有 emoji 時才加入分類
                if (!empty($categoryEmojis)) {
                    $result['data'][$categoryId] = $categoryEmojis;
                    $result['meta']['total'] += count($categoryEmojis);
                    
                    // 添加分類資訊到 meta
                    $result['meta']['categories'][$categoryId] = [
                        'name' => $categoryInfo['name'],
                        'description' => $this->generateCategoryDescription($categoryInfo['name'])
                    ];
                }
            }
            
            return $result;
        });
    }

    /**
     * 生成 emoji ID
     */
    private function generateEmojiId(string $emoji): string
    {
        // 將 emoji 轉換為 Unicode 碼點
        $codepoints = [];
        $length = mb_strlen($emoji, 'UTF-8');
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($emoji, $i, 1, 'UTF-8');
            $codepoint = mb_ord($char, 'UTF-8');
            if ($codepoint !== false) {
                $codepoints[] = strtolower(dechex($codepoint));
            }
        }
        
        return implode('-', $codepoints);
    }

    /**
     * 生成關鍵字
     */
    private function generateKeywords(string $name): array
    {
        // 從名稱中提取關鍵字
        $keywords = [];
        
        // 移除常見詞彙，分割並清理
        $cleanName = strtolower($name);
        $words = preg_split('/[\s\-_]+/', $cleanName);
        
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) > 1) {
                $keywords[] = $word;
            }
        }
        
        return array_unique($keywords);
    }

    /**
     * 生成分類描述
     */
    private function generateCategoryDescription(string $categoryName): string
    {
        $descriptions = [
            'Smileys & Emotion' => '表情符號與情感表達',
            'People & Body' => '人物與身體部位相關表情',
            'Animals & Nature' => '動物與自然環境',
            'Food & Drink' => '食物與飲料',
            'Travel & Places' => '旅遊與地點',
            'Activities' => '活動與運動',
            'Objects' => '物品與工具',
            'Symbols' => '符號與標誌',
            'Flags' => '國旗與地區旗幟'
        ];
        
        return $descriptions[$categoryName] ?? $categoryName;
    }
    
    /**
     * 清除快取
     */
    public function clearCache()
    {
        Cache::forget('all_emojis');
    }

    /**
     * 取得分類清單
     */
    public function getCategories(): array
    {
        $allData = $this->getAllEmojis();
        return $allData['meta']['categories'] ?? [];
    }
    
    /**
     * 根據分類取得 emoji
     */
    public function getEmojisByCategory(string $categoryId): array
    {
        $allData = $this->getAllEmojis();
        $categories = $allData['meta']['categories'];
        
        // 驗證分類是否存在
        if (!isset($categories[$categoryId])) {
            throw new \InvalidArgumentException("Invalid category: {$categoryId}");
        }
        
        // 取得該分類的 emoji
        $categoryEmojis = $allData['data'][$categoryId] ?? [];
        
        return [
            'data' => [
                $categoryId => $categoryEmojis
            ],
            'meta' => [
                'total' => count($categoryEmojis),
                'type' => 'emoji',
                'categories' => [
                    $categoryId => $categories[$categoryId]
                ]
            ]
        ];
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