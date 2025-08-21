<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmojiApiTest extends TestCase
{
    /**
     * 測試獲取所有 emoji API 結構
     */
    public function test_get_all_emoji_returns_correct_structure()
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

        // 檢查 meta 內容
        $meta = $response->json('meta');
        $this->assertEquals('emoji', $meta['type']);
        $this->assertIsInt($meta['total']);
        $this->assertGreaterThan(0, $meta['total']);
        $this->assertIsArray($meta['categories']);
    }

    /**
     * 測試 emoji 項目具有必要欄位
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
     * 測試獲取分類清單
     */
    public function test_get_categories_returns_correct_structure()
    {
        $response = $this->getJson('/api/config/icon/emoji/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'description'
                    ]
                ]
            ]);

        $data = $response->json('data');
        
        // 應該至少有一個分類
        $this->assertGreaterThan(0, count($data));
        
        // 檢查分類格式
        foreach ($data as $categoryId => $categoryInfo) {
            $this->assertIsString($categoryId);
            $this->assertArrayHasKey('name', $categoryInfo);
            $this->assertArrayHasKey('description', $categoryInfo);
            $this->assertIsString($categoryInfo['name']);
            $this->assertIsString($categoryInfo['description']);
        }
    }

    /**
     * 測試按分類獲取 emoji
     */
    public function test_get_emoji_by_category()
    {
        // 先取得所有分類
        $categoriesResponse = $this->getJson('/api/config/icon/emoji/categories');
        $categories = $categoriesResponse->json('data');
        
        // 取得第一個分類 ID
        $firstCategoryId = array_keys($categories)[0];
        
        // 測試該分類的端點
        $response = $this->getJson("/api/config/icon/emoji/category/{$firstCategoryId}");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    $firstCategoryId => []
                ],
                'meta' => [
                    'total',
                    'type',
                    'categories'
                ]
            ]);
        
        $data = $response->json('data');
        
        // 應該只有一個分類
        $this->assertCount(1, $data);
        $this->assertArrayHasKey($firstCategoryId, $data);
        
        // 檢查該分類下的 emoji
        $categoryEmojis = $data[$firstCategoryId];
        foreach ($categoryEmojis as $emoji) {
            $this->assertEquals($firstCategoryId, $emoji['category']);
            $this->assertEquals('emoji', $emoji['type']);
        }
        
        // 檢查 meta
        $meta = $response->json('meta');
        $this->assertEquals('emoji', $meta['type']);
        $this->assertEquals(count($categoryEmojis), $meta['total']);
    }

    /**
     * 測試無效分類返回錯誤
     */
    public function test_invalid_category_returns_error()
    {
        $response = $this->getJson('/api/config/icon/emoji/category/invalid-category');
        
        $response->assertStatus(400)
            ->assertJsonStructure([
                'error'
            ]);
    }

    /**
     * 測試 emoji 膚色變體格式
     */
    public function test_emoji_skin_variations_format()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json('data');
        
        // 尋找有膚色變體的 emoji
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
     * 測試分類一致性
     */
    public function test_category_consistency()
    {
        // 取得所有 emoji
        $allResponse = $this->getJson('/api/config/icon/emoji');
        $allData = $allResponse->json('data');
        $allMeta = $allResponse->json('meta');
        
        // 取得分類清單
        $categoriesResponse = $this->getJson('/api/config/icon/emoji/categories');
        $categoriesData = $categoriesResponse->json('data');
        
        // data 中的分類 ID 應該與 categories 中的一致
        $dataCategories = array_keys($allData);
        $metaCategories = array_keys($allMeta['categories']);
        $listCategories = array_keys($categoriesData);
        
        sort($dataCategories);
        sort($metaCategories);
        sort($listCategories);
        
        $this->assertEquals($dataCategories, $metaCategories);
        $this->assertEquals($dataCategories, $listCategories);
    }
}