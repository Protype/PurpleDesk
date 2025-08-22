<?php

namespace App\Services;

/**
 * Emoji 相容性過濾服務
 * 使用 config/icon/emoji-filter.php 進行設定管理
 */
class EmojiFilterService
{

    /**
     * 檢查過濾功能是否啟用
     */
    public function isFilteringEnabled(): bool
    {
        return config('icon.emoji-filter.enabled', true);
    }

    /**
     * 檢查 emoji 是否應該過濾
     * 
     * @param array $emojiData emoji 資料陣列，包含 emoji、version、factors 等欄位
     * @return array ['shouldFilter' => bool, 'reason' => string, 'riskLevel' => string]
     */
    public function shouldFilterEmoji(array $emojiData): array
    {
        // 如果過濾功能未啟用，直接通過
        if (!$this->isFilteringEnabled()) {
            return [
                'shouldFilter' => false,
                'reason' => '過濾功能已停用',
                'riskLevel' => 'disabled'
            ];
        }

        $emoji = $emojiData['emoji'] ?? '';
        
        // 檢查黑名單
        $blacklist = config('icon.emoji-filter.blacklist', []);
        if (in_array($emoji, $blacklist)) {
            return [
                'shouldFilter' => true,
                'reason' => '用戶確認有顯示問題',
                'riskLevel' => 'high'
            ];
        }
        
        // 檢查版本規則
        $versionRules = config('icon.emoji-filter.version_rules', []);
        if (isset($emojiData['version']) && isset($versionRules[$emojiData['version']])) {
            $rule = $versionRules[$emojiData['version']];
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
        $factorRules = config('icon.emoji-filter.factor_rules', []);
        if (isset($emojiData['factors']) && is_array($emojiData['factors'])) {
            foreach ($emojiData['factors'] as $factor) {
                if (isset($factorRules[$factor]) && $factorRules[$factor] === 'high_risk') {
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
        if (!$this->isFilteringEnabled()) {
            return $emojis;
        }

        $blacklist = config('icon.emoji-filter.blacklist', []);
        
        return array_filter($emojis, function ($emoji) use ($blacklist) {
            $emojiStr = is_string($emoji) ? $emoji : ($emoji['emoji'] ?? '');
            return !in_array($emojiStr, $blacklist);
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
        if (!$this->isFilteringEnabled()) {
            return $emojis;
        }

        $seen = [];
        $result = [];
        
        // 取得設定
        $filterCompound = config('icon.emoji-filter.filter_compound_emojis', true);
        $filterSkinTones = config('icon.emoji-filter.filter_skin_tone_variants', true);
        $filterDuplicates = config('icon.emoji-filter.filter_duplicates', true);
        $logFiltering = config('icon.emoji-filter.log_filtering', true) && config('app.debug');
        
        foreach ($emojis as $emojiData) {
            if (!isset($emojiData['emoji']) || empty($emojiData['emoji'])) {
                continue;
            }
            
            // 檢查黑名單過濾
            $filterResult = $this->shouldFilterEmoji($emojiData);
            if ($filterResult['shouldFilter']) {
                // 在開發環境可以記錄過濾資訊
                if ($logFiltering) {
                    \Log::info("過濾黑名單 emoji: {$emojiData['emoji']} - {$filterResult['reason']}");
                }
                continue; // 跳過黑名單中的 emoji
            }
            
            // 移除膚色修飾符和變化選擇器
            $baseEmoji = $this->cleanEmoji($emojiData['emoji']);
            
            // 跳過空字符串
            if (empty($baseEmoji)) {
                continue;
            }

            // 檢查是否過濾重複項
            if ($filterDuplicates && isset($seen[$baseEmoji])) {
                continue;
            }
            
            // 檢查是否過濾複合 emoji（包含 ZWJ 的 emoji）
            if ($filterCompound && strpos($emojiData['emoji'], "\u{200D}") !== false) {
                continue;
            }
            
            // 檢查是否過濾膚色變體（保留基礎版本）
            if ($filterSkinTones && preg_match('/[\x{1F3FB}-\x{1F3FF}]/u', $emojiData['emoji'])) {
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
        $stats = config('icon.emoji-filter.stats', []);
        $blacklist = config('icon.emoji-filter.blacklist', []);
        
        return array_merge($stats, [
            'current_blacklist_count' => count($blacklist),
            'filtering_enabled' => $this->isFilteringEnabled()
        ]);
    }

    /**
     * 取得目前設定摘要
     */
    public function getConfigSummary(): array
    {
        return [
            'enabled' => $this->isFilteringEnabled(),
            'blacklist_count' => count(config('icon.emoji-filter.blacklist', [])),
            'version_rules' => config('icon.emoji-filter.version_rules', []),
            'factor_rules' => config('icon.emoji-filter.factor_rules', []),
            'filter_compound_emojis' => config('icon.emoji-filter.filter_compound_emojis', true),
            'filter_skin_tone_variants' => config('icon.emoji-filter.filter_skin_tone_variants', true),
            'filter_duplicates' => config('icon.emoji-filter.filter_duplicates', true),
            'log_filtering' => config('icon.emoji-filter.log_filtering', true),
        ];
    }
}