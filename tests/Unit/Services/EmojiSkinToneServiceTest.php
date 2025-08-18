<?php

namespace Tests\Unit\Services;

use App\Services\EmojiSkinToneService;
use Tests\TestCase;

class EmojiSkinToneServiceTest extends TestCase
{
    private EmojiSkinToneService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EmojiSkinToneService();
    }

    /** @test */
    public function it_identifies_skin_tone_capable_emojis()
    {
        $emoji = ['emoji' => '👋', 'name' => 'waving hand'];
        
        $result = $this->service->processEmoji($emoji);
        
        $this->assertTrue($result['has_skin_tone']);
        $this->assertArrayHasKey('skin_variations', $result);
        $this->assertIsArray($result['skin_variations']);
        $this->assertCount(6, $result['skin_variations']); // 0-5 = 6 個項目
    }

    /** @test */
    public function it_marks_non_skin_tone_emojis_correctly()
    {
        $emoji = ['emoji' => '😀', 'name' => 'grinning face'];
        
        $result = $this->service->processEmoji($emoji);
        
        $this->assertFalse($result['has_skin_tone']);
        $this->assertArrayNotHasKey('skin_variations', $result);
    }

    /** @test */
    public function it_groups_skin_tone_variations_correctly()
    {
        $emojis = [
            ['emoji' => '👋', 'name' => 'waving hand'],
            ['emoji' => '👋🏻', 'name' => 'waving hand: light skin tone'],
            ['emoji' => '👋🏼', 'name' => 'waving hand: medium-light skin tone'],
            ['emoji' => '👋🏽', 'name' => 'waving hand: medium skin tone'],
            ['emoji' => '👋🏾', 'name' => 'waving hand: medium-dark skin tone'],
            ['emoji' => '👋🏿', 'name' => 'waving hand: dark skin tone'],
        ];

        $result = $this->service->groupVariations($emojis);

        $this->assertCount(1, $result); // 只有一個基礎 emoji
        $this->assertEquals('👋', $result[0]['emoji']);
        $this->assertTrue($result[0]['has_skin_tone']);
        $this->assertCount(6, $result[0]['skin_variations']); // 0-5 = 6 個項目
        
        // 檢查膚色變體映射
        $variations = $result[0]['skin_variations'];
        $this->assertEquals('👋', $variations[0]); // 基礎 emoji
        $this->assertEquals('👋🏻', $variations[1]);
        $this->assertEquals('👋🏼', $variations[2]);
        $this->assertEquals('👋🏽', $variations[3]);
        $this->assertEquals('👋🏾', $variations[4]);
        $this->assertEquals('👋🏿', $variations[5]);
    }

    /** @test */
    public function it_preserves_base_emoji_when_grouping()
    {
        $emojis = [
            ['emoji' => '👋', 'name' => 'waving hand'],
            ['emoji' => '👋🏻', 'name' => 'waving hand: light skin tone'],
            ['emoji' => '😀', 'name' => 'grinning face'],
        ];

        $result = $this->service->groupVariations($emojis);

        $this->assertCount(2, $result); // 基礎 emoji: 👋 和 😀
        
        // 檢查 👋 有膚色支援
        $wavingHand = collect($result)->firstWhere('emoji', '👋');
        $this->assertTrue($wavingHand['has_skin_tone']);
        $this->assertArrayHasKey('skin_variations', $wavingHand);
        
        // 檢查 😀 沒有膚色支援
        $grinningFace = collect($result)->firstWhere('emoji', '😀');
        $this->assertFalse($grinningFace['has_skin_tone']);
        $this->assertArrayNotHasKey('skin_variations', $grinningFace);
    }

    /** @test */
    public function it_handles_emojis_without_variations()
    {
        $emojis = [
            ['emoji' => '😀', 'name' => 'grinning face'],
            ['emoji' => '🎉', 'name' => 'party popper'],
        ];

        $result = $this->service->groupVariations($emojis);

        $this->assertCount(2, $result);
        foreach ($result as $emoji) {
            $this->assertFalse($emoji['has_skin_tone']);
            $this->assertArrayNotHasKey('skin_variations', $emoji);
        }
    }

    /** @test */
    public function it_converts_flat_structure_to_grouped_structure()
    {
        $flatEmojis = [
            ['emoji' => '👋', 'name' => 'waving hand'],
            ['emoji' => '👋🏻', 'name' => 'waving hand: light skin tone'],
            ['emoji' => '👋🏼', 'name' => 'waving hand: medium-light skin tone'],
            ['emoji' => '🤚', 'name' => 'raised back of hand'],
            ['emoji' => '🤚🏻', 'name' => 'raised back of hand: light skin tone'],
            ['emoji' => '😀', 'name' => 'grinning face'],
        ];

        $result = $this->service->groupVariations($flatEmojis);

        // 應該從 6 個項目縮減為 3 個基礎 emoji
        $this->assertCount(3, $result);
        
        // 檢查每個基礎 emoji
        $emojis = collect($result);
        
        $wavingHand = $emojis->firstWhere('emoji', '👋');
        $this->assertTrue($wavingHand['has_skin_tone']);
        $this->assertCount(3, $wavingHand['skin_variations']); // 0+2個變體 = 3個項目
        
        $raisedHand = $emojis->firstWhere('emoji', '🤚');
        $this->assertTrue($raisedHand['has_skin_tone']);
        $this->assertCount(2, $raisedHand['skin_variations']); // 0+1個變體 = 2個項目
        
        $grinningFace = $emojis->firstWhere('emoji', '😀');
        $this->assertFalse($grinningFace['has_skin_tone']);
    }

    /** @test */
    public function it_reduces_data_redundancy()
    {
        // 模擬包含大量膚色變體的資料
        $flatEmojis = [];
        
        // 添加 10 個支援膚色的 emoji，每個都有 5 個變體
        $baseEmojis = ['👋', '🤚', '✋', '👌', '👍', '👎', '👊', '👏', '🙌', '👐'];
        $skinTones = ['🏻', '🏼', '🏽', '🏾', '🏿'];
        
        foreach ($baseEmojis as $base) {
            $flatEmojis[] = ['emoji' => $base, 'name' => "base emoji $base"];
            foreach ($skinTones as $skin) {
                $flatEmojis[] = ['emoji' => $base . $skin, 'name' => "emoji $base with skin tone"];
            }
        }
        
        // 原始資料：60 個項目 (10 * 6)
        $this->assertCount(60, $flatEmojis);
        
        $result = $this->service->groupVariations($flatEmojis);
        
        // 處理後：10 個基礎 emoji
        $this->assertCount(10, $result);
        
        // 每個基礎 emoji 都應該有 6 個項目（0-5）
        foreach ($result as $emoji) {
            $this->assertTrue($emoji['has_skin_tone']);
            $this->assertCount(6, $emoji['skin_variations']); // 0-5 = 6 個項目
        }
    }

    /** @test */
    public function it_detects_skin_tone_variation_correctly()
    {
        // 使用 reflection 測試 private 方法
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('isSkinToneVariation');
        $method->setAccessible(true);

        // 測試膚色變體
        $this->assertTrue($method->invoke($this->service, '👋🏻'));
        $this->assertTrue($method->invoke($this->service, '👋🏼'));
        $this->assertTrue($method->invoke($this->service, '👋🏽'));
        $this->assertTrue($method->invoke($this->service, '👋🏾'));
        $this->assertTrue($method->invoke($this->service, '👋🏿'));

        // 測試基礎 emoji
        $this->assertFalse($method->invoke($this->service, '👋'));
        $this->assertFalse($method->invoke($this->service, '😀'));
        $this->assertFalse($method->invoke($this->service, '🎉'));
    }

    /** @test */
    public function it_extracts_base_emoji_correctly()
    {
        // 使用 reflection 測試 private 方法
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('extractBaseEmoji');
        $method->setAccessible(true);

        // 測試膚色變體
        $this->assertEquals('👋', $method->invoke($this->service, '👋🏻'));
        $this->assertEquals('👋', $method->invoke($this->service, '👋🏼'));
        $this->assertEquals('🤚', $method->invoke($this->service, '🤚🏽'));

        // 測試基礎 emoji（應該返回原本的）
        $this->assertEquals('👋', $method->invoke($this->service, '👋'));
        $this->assertEquals('😀', $method->invoke($this->service, '😀'));
    }
}