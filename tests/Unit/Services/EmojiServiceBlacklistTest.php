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

    /** @test */
    public function it_ensures_emojis_arrays_are_json_serializable()
    {
        $allEmojis = $this->emojiService->getAllEmojis();
        
        // 測試 JSON 序列化
        $json = json_encode($allEmojis);
        $this->assertNotFalse($json, 'Failed to JSON encode emoji data');
        
        // 測試反序列化
        $decoded = json_decode($json, true);
        $this->assertNotNull($decoded, 'Failed to JSON decode emoji data');
        
        // 驗證所有 emojis 在 JSON 中都是陣列（不是物件）
        foreach ($decoded['categories'] as $categoryId => $category) {
            foreach ($category['subgroups'] as $subgroupKey => $subgroup) {
                $this->assertIsArray($subgroup['emojis'], 
                    "Emojis in {$categoryId}/{$subgroupKey} should be array after JSON serialization"
                );
                // 檢查 JSON 中的索引是否從 0 開始的連續數字
                $emojis = $subgroup['emojis'];
                if (!empty($emojis)) {
                    $this->assertEquals(
                        array_keys($emojis), 
                        range(0, count($emojis) - 1),
                        "Emojis array in {$categoryId}/{$subgroupKey} should have sequential numeric keys"
                    );
                }
            }
        }
    }
}