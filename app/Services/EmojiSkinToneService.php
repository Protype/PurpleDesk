<?php

namespace App\Services;

class EmojiSkinToneService
{
    /**
     * 膚色 Unicode 修飾符範圍
     */
    private const SKIN_TONE_MODIFIERS = [
        1 => '\u{1F3FB}', // Light skin tone
        2 => '\u{1F3FC}', // Medium-light skin tone
        3 => '\u{1F3FD}', // Medium skin tone
        4 => '\u{1F3FE}', // Medium-dark skin tone
        5 => '\u{1F3FF}', // Dark skin tone
    ];

    /**
     * 膚色修飾符的 Unicode 範圍正規表達式
     */
    private const SKIN_TONE_REGEX = '/[\x{1F3FB}-\x{1F3FF}]/u';

    /**
     * 處理單個 emoji，添加膚色支援資訊
     */
    public function processEmoji(array $emoji): array
    {
        $emojiChar = $emoji['emoji'] ?? '';
        
        // 檢查這個 emoji 是否支援膚色
        $hasSkinTone = $this->canHaveSkinTone($emojiChar);
        
        $result = $emoji;
        $result['has_skin_tone'] = $hasSkinTone;
        
        if ($hasSkinTone) {
            // 生成所有膚色變體
            $result['skin_variations'] = $this->generateSkinToneVariations($emojiChar);
        }
        
        return $result;
    }

    /**
     * 將扁平的 emoji 列表分組，合併膚色變體
     */
    public function groupVariations(array $emojis): array
    {
        $grouped = [];
        $processed = [];
        
        foreach ($emojis as $emoji) {
            $emojiChar = $emoji['emoji'] ?? '';
            
            // 如果是膚色變體，提取基礎 emoji
            if ($this->isSkinToneVariation($emojiChar)) {
                $baseEmoji = $this->extractBaseEmoji($emojiChar);
                $skinTone = $this->extractSkinTone($emojiChar);
                
                // 找到對應的基礎 emoji 或建立新的
                $baseIndex = $this->findBaseEmojiIndex($grouped, $baseEmoji);
                
                if ($baseIndex === null) {
                    // 如果基礎 emoji 不存在，跳過這個變體
                    continue;
                }
                
                // 添加膚色變體到基礎 emoji
                if (!isset($grouped[$baseIndex]['skin_variations'])) {
                    $grouped[$baseIndex]['skin_variations'] = [];
                }
                
                $grouped[$baseIndex]['skin_variations'][$skinTone] = $emojiChar;
                $grouped[$baseIndex]['has_skin_tone'] = true;
                
            } else {
                // 基礎 emoji
                $hasVariations = $this->hasExistingVariations($emojis, $emojiChar);
                
                $processedEmoji = $emoji;
                $processedEmoji['has_skin_tone'] = $hasVariations;
                
                if ($hasVariations) {
                    $processedEmoji['skin_variations'] = [];
                }
                
                $grouped[] = $processedEmoji;
            }
        }
        
        return $grouped;
    }

    /**
     * 檢查 emoji 是否為膚色變體
     */
    private function isSkinToneVariation(string $emoji): bool
    {
        return preg_match(self::SKIN_TONE_REGEX, $emoji) === 1;
    }

    /**
     * 從膚色變體中提取基礎 emoji
     */
    private function extractBaseEmoji(string $emoji): string
    {
        return preg_replace(self::SKIN_TONE_REGEX, '', $emoji);
    }

    /**
     * 從膚色變體中提取膚色代號
     */
    private function extractSkinTone(string $emoji): ?int
    {
        if (preg_match(self::SKIN_TONE_REGEX, $emoji, $matches)) {
            $skinToneChar = $matches[0];
            
            foreach (self::SKIN_TONE_MODIFIERS as $tone => $unicode) {
                if ($skinToneChar === mb_convert_encoding($unicode, 'UTF-8', 'UTF-8')) {
                    return $tone;
                }
            }
        }
        
        return null;
    }

    /**
     * 檢查基礎 emoji 是否可以有膚色變體
     */
    private function canHaveSkinTone(string $emoji): bool
    {
        // 使用 Unicode 屬性檢查是否為 Emoji_Modifier_Base
        // 這是 Unicode 標準中定義支援膚色修飾符的官方方式
        
        // 常見支援膚色的 emoji 類別
        $humanRelatedEmojis = [
            // 手勢
            '👋', '🤚', '🖐', '✋', '🖖', '👌', '🤌', '🤏', '✌', '🤞',
            '🤟', '🤘', '🤙', '👈', '👉', '👆', '🖕', '👇', '☝', '👍',
            '👎', '👊', '✊', '🤛', '🤜', '👏', '🙌', '👐', '🤲', '🙏',
            '✍', '💅', '🤳', '💪',
            // 人物
            '🧑', '👨', '👩', '🧒', '👶', '👧', '🧓', '👴', '👵', '🙍',
            '🙎', '🙅', '🙆', '💁', '🙋', '🧏', '🙇', '🤦', '🤷',
            // 職業和角色
            '👮', '🕵', '💂', '🥷', '👷', '🤴', '👸', '👳', '👲', '🧕',
            '🤵', '👰', '🤰', '🤱', '👼', '🎅', '🤶', '🧙', '🧚', '🧛',
            '🧜', '🧝', '🧞', '🧟',
            // 活動
            '💆', '💇', '🚶', '🧍', '🧎', '🏃', '💃', '🕺', '🕴', '👯',
            '🧗', '🤺', '🏇', '⛷', '🏂', '🏌', '🏄', '🚣', '🏊', '⛹',
            '🏋', '🚴', '🚵', '🤸', '🤼', '🤽', '🤾', '🤹', '🧘', '🛀', '🛌'
        ];
        
        return in_array($emoji, $humanRelatedEmojis);
    }

    /**
     * 生成指定 emoji 的所有膚色變體
     */
    private function generateSkinToneVariations(string $baseEmoji): array
    {
        $variations = [];
        
        foreach (self::SKIN_TONE_MODIFIERS as $tone => $unicode) {
            $skinToneChar = mb_convert_encoding($unicode, 'UTF-8', 'UTF-8');
            $variations[$tone] = $baseEmoji . $skinToneChar;
        }
        
        return $variations;
    }

    /**
     * 在分組列表中尋找基礎 emoji 的索引
     */
    private function findBaseEmojiIndex(array $grouped, string $baseEmoji): ?int
    {
        foreach ($grouped as $index => $item) {
            if (($item['emoji'] ?? '') === $baseEmoji) {
                return $index;
            }
        }
        
        return null;
    }

    /**
     * 檢查基礎 emoji 在原始列表中是否有變體
     */
    private function hasExistingVariations(array $emojis, string $baseEmoji): bool
    {
        foreach ($emojis as $emoji) {
            $emojiChar = $emoji['emoji'] ?? '';
            
            if ($this->isSkinToneVariation($emojiChar)) {
                $extractedBase = $this->extractBaseEmoji($emojiChar);
                if ($extractedBase === $baseEmoji) {
                    return true;
                }
            }
        }
        
        return false;
    }
}