<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class EmojiControllerTest extends TestCase
{
    /**
     * 測試取得所有 emoji 資料的 API - 新的 data/meta 格式
     */
    public function test_can_get_all_emojis()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [ // 分類 ID 作為 key
                        '*' => [ // emoji 陣列
                            'id',
                            'name', 
                            'emoji',
                            'type',
                            'keywords',
                            'category',
                            'has_skin_tone'
                        ]
                    ]
                ],
                'meta' => [
                    'total',
                    'type',
                    'categories' => [
                        '*' => [
                            'name',
                            'description'
                        ]
                    ]
                ]
            ]);
    }
    
    /**
     * 測試 emoji 資料包含所有必要的分類
     */
    public function test_emoji_data_contains_expected_categories()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json();
        
        // 驗證包含所有主要分類
        $expectedCategories = [
            'smileys_emotion',
            'people_body', 
            'animals_nature',
            'food_drink',
            'travel_places',
            'activities',
            'objects',
            'symbols',
            'flags'
        ];
        
        foreach ($expectedCategories as $category) {
            $this->assertArrayHasKey($category, $data['meta']['categories'], "Missing category: {$category}");
        }
    }
    
    /**
     * 測試 emoji meta 資料格式正確
     */
    public function test_emoji_meta_data_format()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json();
        
        // 驗證 meta 資料結構
        $this->assertArrayHasKey('meta', $data);
        $this->assertArrayHasKey('total', $data['meta']);
        $this->assertArrayHasKey('type', $data['meta']);
        $this->assertArrayHasKey('categories', $data['meta']);
        
        // 驗證 type 為 emoji
        $this->assertEquals('emoji', $data['meta']['type']);
        
        // 驗證 total 是正整數
        $this->assertIsInt($data['meta']['total']);
        $this->assertGreaterThan(0, $data['meta']['total']);
        
        // 驗證分類數量合理（應該是 9 個）
        $this->assertEquals(9, count($data['meta']['categories']));
    }
    
    /**
     * 測試 emoji 資料格式正確
     */
    public function test_emoji_data_format()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json();
        
        // 取得第一個分類的第一個 emoji 來驗證格式
        $this->assertArrayHasKey('data', $data);
        $this->assertNotEmpty($data['data']);
        
        $firstCategory = array_values($data['data'])[0];
        $this->assertIsArray($firstCategory);
        $this->assertNotEmpty($firstCategory);
        
        $firstEmoji = $firstCategory[0];
        
        // 驗證必要欄位
        $this->assertArrayHasKey('id', $firstEmoji);
        $this->assertArrayHasKey('name', $firstEmoji);
        $this->assertArrayHasKey('emoji', $firstEmoji); 
        $this->assertArrayHasKey('type', $firstEmoji);
        $this->assertArrayHasKey('keywords', $firstEmoji);
        $this->assertArrayHasKey('category', $firstEmoji);
        $this->assertArrayHasKey('has_skin_tone', $firstEmoji);
        
        // 驗證資料類型
        $this->assertIsString($firstEmoji['id']);
        $this->assertIsString($firstEmoji['name']);
        $this->assertIsString($firstEmoji['emoji']);
        $this->assertEquals('emoji', $firstEmoji['type']);
        $this->assertIsArray($firstEmoji['keywords']);
        $this->assertIsString($firstEmoji['category']);
        $this->assertIsBool($firstEmoji['has_skin_tone']);
        
        // 如果有膚色變體，驗證 skin_variations 欄位
        if ($firstEmoji['has_skin_tone']) {
            $this->assertArrayHasKey('skin_variations', $firstEmoji);
            $this->assertIsArray($firstEmoji['skin_variations']);
        }
    }
    
    /**
     * 測試分類 API
     */
    public function test_can_get_emoji_categories()
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
    }
    
    /**
     * 測試取得特定分類的 emoji
     */
    public function test_can_get_emoji_by_category()
    {
        $response = $this->getJson('/api/config/icon/emoji/category/smileys_emotion');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'smileys_emotion' => [
                        '*' => [
                            'id',
                            'name',
                            'emoji', 
                            'type',
                            'keywords',
                            'category'
                        ]
                    ]
                ],
                'meta' => [
                    'total',
                    'type',
                    'categories'
                ]
            ]);
            
        $data = $response->json();
        
        // 驗證只包含指定分類
        $this->assertCount(1, $data['data']);
        $this->assertArrayHasKey('smileys_emotion', $data['data']);
        
        // 驗證每個 emoji 都屬於正確分類
        foreach ($data['data']['smileys_emotion'] as $emoji) {
            $this->assertEquals('smileys_emotion', $emoji['category']);
        }
    }
    
    /**
     * 測試無效分類回應 400 錯誤
     */
    public function test_invalid_category_returns_error()
    {
        $response = $this->getJson('/api/config/icon/emoji/category/invalid_category');
        
        $response->assertStatus(400)
            ->assertJsonStructure([
                'error'
            ]);
            
        $data = $response->json();
        $this->assertStringContainsString('Invalid category', $data['error']);
    }
    
    /**
     * 測試 API 回應時間合理
     */
    public function test_api_response_time()
    {
        $startTime = microtime(true);
        
        $response = $this->getJson('/api/config/icon/emoji');
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // 轉換為毫秒
        
        $response->assertStatus(200);
        
        // API 回應時間應該小於 500ms
        $this->assertLessThan(500, $responseTime, 'API response time is too slow');
    }
    
    /**
     * 測試快取功能
     */
    public function test_emoji_caching()
    {
        // 第一次請求
        $firstResponse = $this->getJson('/api/config/icon/emoji');
        $firstResponse->assertStatus(200);
        
        // 第二次請求應該使用快取
        $secondResponse = $this->getJson('/api/config/icon/emoji');
        $secondResponse->assertStatus(200);
        
        // 資料應該完全相同
        $this->assertEquals($firstResponse->json(), $secondResponse->json());
    }
    
    /**
     * 測試總 emoji 數量合理
     */
    public function test_emoji_total_count()
    {
        $response = $this->getJson('/api/config/icon/emoji');
        
        $data = $response->json();
        
        // 驗證總 emoji 數量合理（約 1630 個，黑名單過濾後）
        $this->assertGreaterThan(1600, $data['meta']['total']);
        $this->assertLessThan(1700, $data['meta']['total']);
    }
}