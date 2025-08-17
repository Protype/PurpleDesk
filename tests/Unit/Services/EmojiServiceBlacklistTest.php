<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\EmojiService;
use Illuminate\Support\Facades\Cache;

class EmojiServiceBlacklistTest extends TestCase
{
    private EmojiService $emojiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->emojiService = new EmojiService();
        // 清除快取確保測試乾淨
        $this->emojiService->clearCache();
    }

    /** @test */
    public function it_has_57_blacklisted_emojis()
    {
        $stats = $this->emojiService->getBlacklistStats();
        
        $this->assertEquals(57, $stats['total_blacklisted']);
        $this->assertCount(57, $stats['blacklisted_emojis']);
        $this->assertEquals('1.0.0', $stats['filter_version']);
        $this->assertEquals('100%', $stats['accuracy']);
    }

    /** @test */
    public function it_filters_problematic_emojis_from_api_response()
    {
        $allEmojis = $this->emojiService->getAllEmojis();
        $blacklistedEmojis = $this->emojiService->getBlacklistStats()['blacklisted_emojis'];
        
        // 檢查 API 回應中不包含任何黑名單 emoji
        $foundProblematic = [];
        
        foreach ($allEmojis['categories'] as $category) {
            foreach ($category['subgroups'] as $subgroup) {
                foreach ($subgroup['emojis'] as $emoji) {
                    $emojiChar = $emoji['emoji'] ?? '';
                    if (in_array($emojiChar, $blacklistedEmojis)) {
                        $foundProblematic[] = $emojiChar;
                    }
                }
            }
        }
        
        $this->assertEmpty($foundProblematic, 
            'Found blacklisted emojis in API response: ' . implode(', ', $foundProblematic)
        );
    }

    /** @test */
    public function it_includes_specific_blacklisted_emojis()
    {
        $blacklist = $this->emojiService->getBlacklistStats()['blacklisted_emojis'];
        
        // 測試一些已知的問題 emoji
        $knownProblematic = [
            "🇨🇶", // 薩克島旗幟
            "🫩", // Unicode 15 emoji
            "🚶‍♀️‍➡️", // 複合 ZWJ 序列
            "🙂‍↔️", // 新的表情變體
            "🛜"  // Unicode 15.1 emoji
        ];
        
        foreach ($knownProblematic as $emoji) {
            $this->assertContains($emoji, $blacklist, 
                "Emoji {$emoji} should be in blacklist"
            );
        }
    }

    /** @test */
    public function it_maintains_api_structure_after_filtering()
    {
        $allEmojis = $this->emojiService->getAllEmojis();
        
        // 驗證 API 結構完整性
        $this->assertArrayHasKey('categories', $allEmojis);
        $this->assertArrayHasKey('stats', $allEmojis);
        $this->assertArrayHasKey('total_emojis', $allEmojis['stats']);
        $this->assertArrayHasKey('total_categories', $allEmojis['stats']);
        
        // 驗證分類結構
        foreach ($allEmojis['categories'] as $categoryId => $category) {
            $this->assertArrayHasKey('id', $category);
            $this->assertArrayHasKey('name', $category);
            $this->assertArrayHasKey('subgroups', $category);
            
            foreach ($category['subgroups'] as $subgroup) {
                $this->assertArrayHasKey('name', $subgroup);
                $this->assertArrayHasKey('emojis', $subgroup);
                $this->assertIsArray($subgroup['emojis']);
            }
        }
    }

    /** @test */
    public function it_caches_filtered_results()
    {
        // 第一次呼叫
        $first = $this->emojiService->getAllEmojis();
        
        // 第二次呼叫應該從快取載入
        $second = $this->emojiService->getAllEmojis();
        
        $this->assertEquals($first, $second);
        
        // 驗證快取存在
        $this->assertTrue(Cache::has('all_emojis'));
    }
}