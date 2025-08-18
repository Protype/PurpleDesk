<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class HeroIconControllerTest extends TestCase
{
    /**
     * 測試取得所有 HeroIcons 資料的 API
     */
    public function test_can_get_all_heroicons()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'component',
                        'category',
                        'keywords'
                    ]
                ],
                'meta' => [
                    'total',
                    'categories'
                ]
            ]);
    }
    
    /**
     * 測試 HeroIcons 資料包含正確數量的圖標
     */
    public function test_heroicons_data_count()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json();
        
        // 驗證總數量為 230 個圖標
        $this->assertEquals(230, $data['meta']['total']);
        $this->assertCount(230, $data['data']);
    }
    
    /**
     * 測試 HeroIcons 資料格式正確
     */
    public function test_heroicons_data_format()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json();
        
        // 檢查第一個圖標的格式
        $firstIcon = $data['data'][0];
        $this->assertArrayHasKey('name', $firstIcon);
        $this->assertArrayHasKey('component', $firstIcon);
        $this->assertArrayHasKey('category', $firstIcon);
        $this->assertArrayHasKey('keywords', $firstIcon);
        
        // 驗證資料類型
        $this->assertIsString($firstIcon['name']);
        $this->assertIsString($firstIcon['component']);
        $this->assertIsString($firstIcon['category']);
        $this->assertIsArray($firstIcon['keywords']);
    }
    
    /**
     * 測試特定圖標存在
     */
    public function test_specific_heroicons_exist()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json();
        
        // 檢查一些重要的圖標是否存在
        $iconNames = array_column($data['data'], 'name');
        $this->assertContains('Academic Cap', $iconNames);
        $this->assertContains('Home', $iconNames);
        $this->assertContains('User', $iconNames);
    }
    
    /**
     * 測試分類資料
     */
    public function test_heroicons_categories()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json();
        
        // 驗證分類元資料存在
        $this->assertArrayHasKey('categories', $data['meta']);
        $this->assertIsArray($data['meta']['categories']);
        
        // 驗證至少有 general 分類
        $this->assertContains('general', $data['meta']['categories']);
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
        
        // API 回應時間應該小於 300ms（根據需求）
        $this->assertLessThan(300, $responseTime, 'API response time is too slow');
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
}