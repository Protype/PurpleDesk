<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class EmojiSkinToneApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // 清除快取確保測試獨立性
        Cache::forget('all_emojis');
    }

    /** @test */
    public function api_returns_has_skin_tone_flag()
    {
        $response = $this->get('/api/config/icon/emoji');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertArrayHasKey('categories', $data);
        
        // 檢查至少一個分類中包含有膚色標記的 emoji
        $foundSkinToneEmoji = false;
        foreach ($data['categories'] as $category) {
            foreach ($category['subgroups'] as $subgroup) {
                foreach ($subgroup['emojis'] as $emoji) {
                    if (isset($emoji['has_skin_tone'])) {
                        $this->assertIsBool($emoji['has_skin_tone']);
                        $foundSkinToneEmoji = true;
                        break 3;
                    }
                }
            }
        }
        
        $this->assertTrue($foundSkinToneEmoji, 'API 應該返回包含 has_skin_tone 標記的 emoji');
    }

    /** @test */
    public function api_returns_skin_variations_object()
    {
        $response = $this->get('/api/config/icon/emoji');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // 尋找支援膚色的 emoji
        $foundSkinVariations = false;
        foreach ($data['categories'] as $category) {
            foreach ($category['subgroups'] as $subgroup) {
                foreach ($subgroup['emojis'] as $emoji) {
                    if (isset($emoji['has_skin_tone']) && $emoji['has_skin_tone'] === true) {
                        // 應該包含 skin_variations
                        $this->assertArrayHasKey('skin_variations', $emoji);
                        $this->assertIsArray($emoji['skin_variations']);
                        
                        // 檢查變體結構
                        foreach ($emoji['skin_variations'] as $tone => $variation) {
                            $this->assertIsInt($tone);
                            $this->assertGreaterThanOrEqual(1, $tone);
                            $this->assertLessThanOrEqual(5, $tone);
                            $this->assertIsString($variation);
                            $this->assertNotEmpty($variation);
                        }
                        
                        $foundSkinVariations = true;
                        break 3;
                    }
                }
            }
        }
        
        $this->assertTrue($foundSkinVariations, 'API 應該返回包含 skin_variations 的 emoji');
    }

    /** @test */
    public function api_reduces_total_emoji_count()
    {
        $response = $this->get('/api/config/icon/emoji');

        $response->assertStatus(200);
        
        $data = $response->json();
        $totalEmojis = $data['stats']['total_emojis'];
        
        // 在新的結構化資料中，總數應該顯著減少
        // 原本約 3724，現在應該約 1800-1900（基礎 emoji）
        $this->assertLessThan(2000, $totalEmojis, '結構化後的 emoji 總數應該大幅減少');
        $this->assertGreaterThan(1500, $totalEmojis, '但仍應該有足夠的 emoji 可用');
    }

    /** @test */
    public function api_maintains_data_integrity()
    {
        $response = $this->get('/api/config/icon/emoji');

        $response->assertStatus(200);
        
        $data = $response->json();
        
        // 檢查資料結構完整性
        $this->assertArrayHasKey('categories', $data);
        $this->assertArrayHasKey('stats', $data);
        $this->assertArrayHasKey('total_emojis', $data['stats']);
        $this->assertArrayHasKey('total_categories', $data['stats']);
        
        // 檢查每個 emoji 的必要欄位
        foreach ($data['categories'] as $categoryId => $category) {
            $this->assertArrayHasKey('subgroups', $category);
            
            foreach ($category['subgroups'] as $subgroupKey => $subgroup) {
                $this->assertArrayHasKey('emojis', $subgroup);
                
                foreach ($subgroup['emojis'] as $emoji) {
                    // 必要欄位
                    $this->assertArrayHasKey('emoji', $emoji);
                    $this->assertArrayHasKey('name', $emoji);
                    $this->assertArrayHasKey('has_skin_tone', $emoji);
                    
                    // 如果支援膚色，必須有 skin_variations
                    if ($emoji['has_skin_tone']) {
                        $this->assertArrayHasKey('skin_variations', $emoji);
                        $this->assertNotEmpty($emoji['skin_variations']);
                    } else {
                        // 如果不支援膚色，不應該有 skin_variations
                        $this->assertArrayNotHasKey('skin_variations', $emoji);
                    }
                }
            }
        }
    }

    /** @test */
    public function api_excludes_duplicate_skin_tone_variations()
    {
        $response = $this->get('/api/config/icon/emoji');

        $response->assertStatus(200);
        
        $data = $response->json();
        $allEmojis = [];
        
        // 收集所有 emoji 字符
        foreach ($data['categories'] as $category) {
            foreach ($category['subgroups'] as $subgroup) {
                foreach ($subgroup['emojis'] as $emoji) {
                    $allEmojis[] = $emoji['emoji'];
                    
                    // 如果有膚色變體，收集變體
                    if (isset($emoji['skin_variations'])) {
                        foreach ($emoji['skin_variations'] as $variation) {
                            $allEmojis[] = $variation;
                        }
                    }
                }
            }
        }
        
        // 檢查膚色變體不會出現在主 emoji 列表中
        $skinTonePattern = '/[\x{1F3FB}-\x{1F3FF}]/u';
        $mainEmojiList = [];
        $variationsList = [];
        
        foreach ($data['categories'] as $category) {
            foreach ($category['subgroups'] as $subgroup) {
                foreach ($subgroup['emojis'] as $emoji) {
                    $mainEmojiList[] = $emoji['emoji'];
                }
            }
        }
        
        // 主列表中不應該包含膚色變體
        foreach ($mainEmojiList as $emoji) {
            $this->assertEquals(0, preg_match($skinTonePattern, $emoji), 
                "主 emoji 列表不應包含膚色變體：{$emoji}");
        }
    }

    /** @test */
    public function api_performance_meets_requirements()
    {
        $startTime = microtime(true);
        
        $response = $this->get('/api/config/icon/emoji');
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // 轉換為毫秒
        
        $response->assertStatus(200);
        
        // 回應時間應該少於 200ms
        $this->assertLessThan(200, $responseTime, 'API 回應時間應該少於 200ms');
        
        // 檢查回應大小（粗略估算）
        $responseSize = strlen(json_encode($response->json()));
        $this->assertLessThan(250000, $responseSize, '回應大小應該顯著減少'); // ~250KB
    }
}