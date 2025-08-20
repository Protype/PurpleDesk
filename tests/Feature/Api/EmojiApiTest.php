<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmojiApiTest extends TestCase
{
    /**
     * 測試 Emoji API 回傳正確的基本結構
     */
    public function test_emoji_api_returns_correct_structure()
    {
        $response = $this->getJson('/api/config/icon/emoji');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [] // 應該是分類陣列
                ],
                'meta' => [
                    'total',
                    'type',
                    'categories'
                ]
            ]);
    }

    /**
     * 測試 API 具有 data 和 meta 兩個主要鍵
     */
    public function test_emoji_api_has_data_and_meta_keys()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json();
        
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('meta', $data);
        $this->assertIsArray($data['data']);
        $this->assertIsArray($data['meta']);
    }

    /**
     * 測試 data 按分類分組
     */
    public function test_emoji_data_is_grouped_by_category()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json('data');
        
        // 應該至少有一個分類
        $this->assertGreaterThan(0, count($data));
        
        // 每個分類都應該是陣列
        foreach ($data as $categoryId => $categoryEmojis) {
            $this->assertIsString($categoryId);
            $this->assertIsArray($categoryEmojis);
        }
    }

    /**
     * 測試 Emoji 項目具有必要欄位
     */
    public function test_emoji_item_has_required_fields()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json('data');
        
        // 取得第一個分類的第一個 emoji
        $firstCategory = array_values($data)[0];
        $this->assertGreaterThan(0, count($firstCategory));
        
        $emoji = $firstCategory[0];
        
        // 檢查必要欄位
        $this->assertArrayHasKey('id', $emoji);
        $this->assertArrayHasKey('name', $emoji);
        $this->assertArrayHasKey('emoji', $emoji);
        $this->assertArrayHasKey('type', $emoji);
        $this->assertArrayHasKey('keywords', $emoji);
        $this->assertArrayHasKey('category', $emoji);
        $this->assertArrayHasKey('has_skin_tone', $emoji);
        
        // 檢查欄位類型
        $this->assertIsString($emoji['id']);
        $this->assertIsString($emoji['name']);
        $this->assertIsString($emoji['emoji']);
        $this->assertEquals('emoji', $emoji['type']);
        $this->assertIsArray($emoji['keywords']);
        $this->assertIsString($emoji['category']);
        $this->assertIsBool($emoji['has_skin_tone']);
    }

    /**
     * 測試有膚色變體的 emoji 包含 skin_variations
     */
    public function test_emoji_with_skin_tone_has_variations()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json('data');
        
        // 尋找有膚色變體的 emoji（通常在 people_body 分類）
        $emojiWithSkinTone = null;
        
        foreach ($data as $categoryEmojis) {
            foreach ($categoryEmojis as $emoji) {
                if ($emoji['has_skin_tone'] === true) {
                    $emojiWithSkinTone = $emoji;
                    break 2;
                }
            }
        }
        
        $this->assertNotNull($emojiWithSkinTone, 'Should find at least one emoji with skin tone');
        
        // 檢查 skin_variations 欄位
        $this->assertArrayHasKey('skin_variations', $emojiWithSkinTone);
        $this->assertIsArray($emojiWithSkinTone['skin_variations']);
        
        // 檢查至少有索引 0 的變體（基礎 emoji）
        $this->assertArrayHasKey(0, $emojiWithSkinTone['skin_variations']);
        $this->assertIsString($emojiWithSkinTone['skin_variations'][0]);
    }

    /**
     * 測試沒有膚色變體的 emoji 不包含 skin_variations
     */
    public function test_emoji_without_skin_tone_has_no_variations()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json('data');
        
        // 尋找沒有膚色變體的 emoji（通常在 smileys_emotion 分類）
        $emojiWithoutSkinTone = null;
        
        foreach ($data as $categoryEmojis) {
            foreach ($categoryEmojis as $emoji) {
                if ($emoji['has_skin_tone'] === false) {
                    $emojiWithoutSkinTone = $emoji;
                    break 2;
                }
            }
        }
        
        $this->assertNotNull($emojiWithoutSkinTone, 'Should find at least one emoji without skin tone');
        
        // 檢查不應該有 skin_variations 欄位
        $this->assertArrayNotHasKey('skin_variations', $emojiWithoutSkinTone);
    }

    /**
     * 測試 meta 包含總數
     */
    public function test_meta_contains_total_count()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $meta = $response->json('meta');
        
        $this->assertArrayHasKey('total', $meta);
        $this->assertIsInt($meta['total']);
        $this->assertGreaterThan(0, $meta['total']);
    }

    /**
     * 測試 meta 包含正確的 type
     */
    public function test_meta_contains_type_emoji()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $meta = $response->json('meta');
        
        $this->assertArrayHasKey('type', $meta);
        $this->assertEquals('emoji', $meta['type']);
    }

    /**
     * 測試 meta 包含分類資訊
     */
    public function test_meta_contains_categories_info()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $meta = $response->json('meta');
        
        $this->assertArrayHasKey('categories', $meta);
        $this->assertIsArray($meta['categories']);
        
        // 檢查至少有一個分類
        $this->assertGreaterThan(0, count($meta['categories']));
        
        // 檢查分類格式
        foreach ($meta['categories'] as $categoryId => $categoryInfo) {
            $this->assertIsString($categoryId);
            $this->assertIsArray($categoryInfo);
            $this->assertArrayHasKey('name', $categoryInfo);
            $this->assertArrayHasKey('description', $categoryInfo);
            $this->assertIsString($categoryInfo['name']);
            $this->assertIsString($categoryInfo['description']);
        }
    }

    /**
     * 測試 data 中的總數與 meta.total 一致
     */
    public function test_data_count_matches_meta_total()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json('data');
        $meta = $response->json('meta');
        
        // 計算 data 中的總數
        $actualTotal = 0;
        foreach ($data as $categoryEmojis) {
            $actualTotal += count($categoryEmojis);
        }
        
        $this->assertEquals($meta['total'], $actualTotal);
    }

    /**
     * 測試分類 ID 的一致性
     */
    public function test_category_ids_consistency()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json('data');
        $meta = $response->json('meta');
        
        // data 中的分類 ID 應該與 meta.categories 中的一致
        $dataCategories = array_keys($data);
        $metaCategories = array_keys($meta['categories']);
        
        sort($dataCategories);
        sort($metaCategories);
        
        $this->assertEquals($dataCategories, $metaCategories);
        
        // 每個 emoji 的 category 欄位應該對應實際的分類 ID
        foreach ($data as $categoryId => $categoryEmojis) {
            foreach ($categoryEmojis as $emoji) {
                $this->assertEquals($categoryId, $emoji['category']);
            }
        }
    }
}