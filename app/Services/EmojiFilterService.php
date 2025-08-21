<?php

namespace App\Services;

/**
 * Emoji 相容性過濾服務
 * 基於用戶確認結果自動生成
 * 
 * 統計資料:
 * - 總測試: 383 個 emoji
 * - 實際問題: 57 個 (14.9%)
 * - 預測準確度: 100.0%
 */
class EmojiFilterService
{
    /**
     * 確認有問題的 emoji 黑名單 (57 個)
     */
    private const PROBLEMATIC_EMOJIS = [
        "🇨🇶",
        "🫩",
        "🫆",
        "🪾",
        "🫜",
        "🪉",
        "🪏",
        "🫟",
        "🚶‍♀️‍➡️",
        "🚶‍♂️‍➡️",
        "🧎‍♀️‍➡️",
        "🧎‍♂️‍➡️",
        "🏃‍♀️‍➡️",
        "🏃‍♂️‍➡️",
        "🧑‍🦯‍➡️",
        "👨‍🦯‍➡️",
        "👩‍🦯‍➡️",
        "🧑‍🦼‍➡️",
        "👨‍🦼‍➡️",
        "👩‍🦼‍➡️",
        "🧑‍🦽‍➡️",
        "👨‍🦽‍➡️",
        "👩‍🦽‍➡️",
        "🧑‍🧑‍🧒‍🧒",
        "🙂‍↔️",
        "🙂‍↕️",
        "🚶‍➡️",
        "🧎‍➡️",
        "🏃‍➡️",
        "🧑‍🧑‍🧒",
        "🧑‍🧒‍🧒",
        "⛓️‍💥",
        "🧑‍🧒",
        "🐦‍🔥",
        "🍋‍🟩",
        "🍄‍🟫",
        "🐦‍⬛",
        "🫨",
        "🩷",
        "🩵",
        "🩶",
        "🫷",
        "🫸",
        "🫎",
        "🫏",
        "🪽",
        "🪿",
        "🪼",
        "🪻",
        "🫚",
        "🫛",
        "🪭",
        "🪮",
        "🪇",
        "🪈",
        "🪯",
        "🛜"
    ];

    /**
     * 版本過濾規則
     */
    private const VERSION_RULES = [
        "15" => "block",
        "16" => "block",
        "15.1" => "block"
    ];

    /**
     * 風險因子規則
     */
    private const FACTOR_RULES = [
        "FLAG_SEQUENCE" => "high_risk"
    ];

    /**
     * 檢查 emoji 是否應該過濾
     * 
     * @param array $emojiData emoji 資料陣列，包含 emoji、version、factors 等欄位
     * @return array ['shouldFilter' => bool, 'reason' => string, 'riskLevel' => string]
     */
    public function shouldFilterEmoji(array $emojiData): array
    {
        $emoji = $emojiData['emoji'] ?? '';
        
        // 檢查黑名單
        if (in_array($emoji, self::PROBLEMATIC_EMOJIS)) {
            return [
                'shouldFilter' => true,
                'reason' => '用戶確認有顯示問題',
                'riskLevel' => 'high'
            ];
        }
        
        // 檢查版本規則
        if (isset($emojiData['version']) && isset(self::VERSION_RULES[$emojiData['version']])) {
            $rule = self::VERSION_RULES[$emojiData['version']];
            if ($rule === 'block') {
                return [
                    'shouldFilter' => true,
                    'reason' => "Unicode {$emojiData['version']} 高問題率版本",
                    'riskLevel' => 'high'
                ];
            } else if ($rule === 'high_risk') {
                return [
                    'shouldFilter' => false,
                    'reason' => "Unicode {$emojiData['version']} 中風險版本",
                    'riskLevel' => 'medium'
                ];
            }
        }
        
        // 檢查因子規則
        if (isset($emojiData['factors']) && is_array($emojiData['factors'])) {
            foreach ($emojiData['factors'] as $factor) {
                if (isset(self::FACTOR_RULES[$factor]) && self::FACTOR_RULES[$factor] === 'high_risk') {
                    return [
                        'shouldFilter' => false,
                        'reason' => "包含高風險因子: {$factor}",
                        'riskLevel' => 'medium'
                    ];
                }
            }
        }
        
        return [
            'shouldFilter' => false,
            'reason' => '通過所有檢查',
            'riskLevel' => 'low'
        ];
    }

    /**
     * 過濾 emoji 陣列，移除有問題的 emoji
     * 
     * @param array $emojis emoji 陣列
     * @return array 過濾後的 emoji 陣列
     */
    public function filterEmojis(array $emojis): array
    {
        return array_filter($emojis, function ($emoji) {
            $emojiStr = is_string($emoji) ? $emoji : ($emoji['emoji'] ?? '');
            return !in_array($emojiStr, self::PROBLEMATIC_EMOJIS);
        });
    }

    /**
     * 過濾和清理 emoji，移除複合 emoji、膚色變體和黑名單 emoji
     * 
     * @param array $emojis 原始 emoji 陣列
     * @return array 過濾後的 emoji 陣列
     */
    public function filterAndCleanEmojis(array $emojis): array
    {
        $seen = [];
        $result = [];
        
        foreach ($emojis as $emojiData) {
            if (!isset($emojiData['emoji']) || empty($emojiData['emoji'])) {
                continue;
            }
            
            // 檢查黑名單過濾
            $filterResult = $this->shouldFilterEmoji($emojiData);
            if ($filterResult['shouldFilter']) {
                // 在開發環境可以記錄過濾資訊
                if (config('app.debug')) {
                    \Log::info("過濾黑名單 emoji: {$emojiData['emoji']} - {$filterResult['reason']}");
                }
                continue; // 跳過黑名單中的 emoji
            }
            
            // 移除膚色修飾符和變化選擇器
            $baseEmoji = $this->cleanEmoji($emojiData['emoji']);
            
            // 跳過空字符串或已經處理過的基礎 emoji
            if (empty($baseEmoji) || isset($seen[$baseEmoji])) {
                continue;
            }
            
            // 跳過複合 emoji（包含 ZWJ 的 emoji）
            if (strpos($emojiData['emoji'], "\u{200D}") !== false) {
                continue;
            }
            
            // 跳過膚色變體（保留基礎版本）
            if (preg_match('/[\x{1F3FB}-\x{1F3FF}]/u', $emojiData['emoji'])) {
                continue;
            }
            
            $seen[$baseEmoji] = true;
            $result[] = array_merge($emojiData, [
                'emoji' => $baseEmoji
            ]);
        }
        
        return $result;
    }

    /**
     * 清理 emoji 字符，移除修飾符
     */
    private function cleanEmoji(string $emoji): string
    {
        // 移除膚色修飾符
        $cleaned = preg_replace('/[\x{1F3FB}-\x{1F3FF}]/u', '', $emoji);
        
        // 移除變化選擇器
        $cleaned = str_replace("\u{FE0F}", '', $cleaned);
        
        // 移除 ZWJ 序列（複合 emoji）
        $cleaned = preg_replace('/\x{200D}.*$/u', '', $cleaned);
        
        return trim($cleaned);
    }

    /**
     * 統計資訊
     */
    public function getFilterStats(): array
    {
        return [
            "totalTested" => 383,
            "actualProblems" => count(self::PROBLEMATIC_EMOJIS),
            "problemRate" => 14.9,
            "predictionAccuracy" => 100,
            "generatedAt" => "2025-08-21T13:43:00.000Z"
        ];
    }
}