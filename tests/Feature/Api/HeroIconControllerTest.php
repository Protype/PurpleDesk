<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class HeroIconControllerTest extends TestCase
{
    /**
     * 測試取得所有 HeroIcons 資料的 API - 新的 data/meta 格式
     */
    public function test_can_get_all_heroicons()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [ // 分類 ID 作為 key
                        '*' => [ // heroicons 陣列
                            'id',
                            'name',
                            'value', // HeroIcon 使用 component 名稱
                            'type',
                            'keywords',
                            'category',
                            'has_variants',
                            'variant_type'
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
     * 測試 HeroIcons meta 資料格式正確
     */
    public function test_heroicons_meta_data_format()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json();
        
        // 驗證 meta 資料結構
        $this->assertArrayHasKey('meta', $data);
        $this->assertArrayHasKey('total', $data['meta']);
        $this->assertArrayHasKey('type', $data['meta']);
        $this->assertArrayHasKey('categories', $data['meta']);
        
        // 驗證 type 為 heroicons
        $this->assertEquals('heroicons', $data['meta']['type']);
        
        // 驗證 total 是正整數
        $this->assertIsInt($data['meta']['total']);
        $this->assertGreaterThan(0, $data['meta']['total']);
        
        // HeroIcons 包含 outline 和 solid 變體，所以總數應該是基礎圖標數的兩倍
        $this->assertGreaterThan(400, $data['meta']['total']); // 230 * 2 = 460
    }
    
    /**
     * 測試 HeroIcons 資料格式正確
     */
    public function test_heroicons_data_format()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json();
        
        // 取得第一個分類的第一個 HeroIcon 來驗證格式
        $this->assertArrayHasKey('data', $data);
        $this->assertNotEmpty($data['data']);
        
        $firstCategory = array_values($data['data'])[0];
        $this->assertIsArray($firstCategory);
        $this->assertNotEmpty($firstCategory);
        
        $firstIcon = $firstCategory[0];
        
        // 驗證必要欄位
        $this->assertArrayHasKey('id', $firstIcon);
        $this->assertArrayHasKey('name', $firstIcon);
        $this->assertArrayHasKey('value', $firstIcon);
        $this->assertArrayHasKey('type', $firstIcon);
        $this->assertArrayHasKey('keywords', $firstIcon);
        $this->assertArrayHasKey('category', $firstIcon);
        $this->assertArrayHasKey('has_variants', $firstIcon);
        $this->assertArrayHasKey('variant_type', $firstIcon);
        
        // 驗證資料類型
        $this->assertIsString($firstIcon['id']);
        $this->assertIsString($firstIcon['name']);
        $this->assertIsString($firstIcon['value']); // component 名稱
        $this->assertEquals('heroicons', $firstIcon['type']);
        $this->assertIsArray($firstIcon['keywords']);
        $this->assertIsString($firstIcon['category']);
        $this->assertTrue($firstIcon['has_variants']); // HeroIcons 都有變體
        $this->assertContains($firstIcon['variant_type'], ['outline', 'solid']);
    }
    
    /**
     * 測試分類 API
     */
    public function test_can_get_heroicons_categories()
    {
        $response = $this->getJson('/api/config/icon/heroicons/categories');
        
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
     * 測試取得特定分類的 HeroIcons
     */
    public function test_can_get_heroicons_by_category()
    {
        // 先取得分類列表
        $categoriesResponse = $this->getJson('/api/config/icon/heroicons/categories');
        $categoriesData = $categoriesResponse->json();
        
        if (empty($categoriesData['data'])) {
            $this->markTestSkipped('No categories available');
        }
        
        $firstCategoryId = array_keys($categoriesData['data'])[0];
        
        $response = $this->getJson("/api/config/icon/heroicons/category/{$firstCategoryId}");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    $firstCategoryId => [
                        '*' => [
                            'id',
                            'name',
                            'value',
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
        $this->assertArrayHasKey($firstCategoryId, $data['data']);
        
        // 驗證每個 HeroIcon 都屬於正確分類
        foreach ($data['data'][$firstCategoryId] as $icon) {
            $this->assertEquals($firstCategoryId, $icon['category']);
        }
    }
    
    /**
     * 測試無效分類回應 400 錯誤
     */
    public function test_invalid_heroicons_category_returns_error()
    {
        $response = $this->getJson('/api/config/icon/heroicons/category/invalid_category');
        
        $response->assertStatus(400)
            ->assertJsonStructure([
                'error'
            ]);
            
        $data = $response->json();
        $this->assertStringContainsString('Invalid category', $data['error']);
    }
    
    /**
     * 測試特定 HeroIcons 存在
     */
    public function test_specific_heroicons_exist()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json();
        
        // 收集所有圖標名稱 (去重)
        $iconNames = [];
        foreach ($data['data'] as $category) {
            foreach ($category as $icon) {
                $iconNames[] = $icon['name'];
            }
        }
        $iconNames = array_unique($iconNames);
        
        // 檢查一些重要的圖標是否存在
        $this->assertContains('Academic Cap', $iconNames);
        $this->assertContains('Home', $iconNames);
        $this->assertContains('User', $iconNames);
    }
    
    /**
     * 測試 API 回應時間
     */
    public function test_api_response_time()
    {
        $startTime = microtime(true);
        
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // 轉換為毫秒
        
        $response->assertStatus(200);
        
        // API 回應時間應該小於 500ms
        $this->assertLessThan(500, $responseTime, 'API response time is too slow');
    }
    
    /**
     * 測試緩存功能
     */
    public function test_heroicons_caching()
    {
        // 第一次請求
        $firstResponse = $this->getJson('/api/config/icon/heroicons');
        $firstResponse->assertStatus(200);
        
        // 第二次請求應該使用緩存
        $secondResponse = $this->getJson('/api/config/icon/heroicons');
        $secondResponse->assertStatus(200);
        
        // 資料應該完全相同
        $this->assertEquals($firstResponse->json(), $secondResponse->json());
    }
    
    /**
     * 測試 HeroIcons 變體功能
     */
    public function test_heroicons_variants()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        $data = $response->json();
        
        // 收集所有圖標，檢查變體
        $outlineIcons = [];
        $solidIcons = [];
        
        foreach ($data['data'] as $category) {
            foreach ($category as $icon) {
                if ($icon['variant_type'] === 'outline') {
                    $outlineIcons[] = $icon['name'];
                } elseif ($icon['variant_type'] === 'solid') {
                    $solidIcons[] = $icon['name'];
                }
            }
        }
        
        // 每個圖標都應該有 outline 和 solid 兩個變體
        $this->assertNotEmpty($outlineIcons);
        $this->assertNotEmpty($solidIcons);
        
        // 變體數量應該相等（每個基礎圖標都有兩個變體）
        $this->assertEquals(count($outlineIcons), count($solidIcons));
    }
    
    /**
     * 測試總圖標數量合理
     */
    public function test_heroicons_total_count()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json();
        
        // 總數應該合理（230 個基礎圖標 * 2 變體 = 460）
        $this->assertGreaterThan(400, $data['meta']['total']);
        $this->assertLessThan(500, $data['meta']['total']);
    }
}