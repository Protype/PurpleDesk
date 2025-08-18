<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class BootstrapIconControllerTest extends TestCase
{
    /**
     * 測試取得所有 Bootstrap Icons 資料的 API
     */
    public function test_can_get_all_bootstrap_icons()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        '*' => [
                            'name',
                            'class',
                            'category',
                            'keywords'
                        ]
                    ]
                ],
                'meta' => [
                    'total',
                    'categories'
                ]
            ]);
    }
    
    /**
     * 測試 Bootstrap Icons 包含正確分類數量
     */
    public function test_bootstrap_icons_categories_count()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json();
        
        // 應該有 8 個分類
        $this->assertGreaterThanOrEqual(8, count($data['meta']['categories']));
        $this->assertContains('general', array_keys($data['meta']['categories']));
        $this->assertContains('ui', array_keys($data['meta']['categories']));
        $this->assertContains('communications', array_keys($data['meta']['categories']));
    }
    
    /**
     * 測試 Bootstrap Icons 資料格式正確
     */
    public function test_bootstrap_icons_data_format()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json();
        
        // 檢查第一個分類的第一個圖標格式
        $firstCategory = array_values($data['data'])[0];
        $firstIcon = $firstCategory[0];
        
        $this->assertArrayHasKey('name', $firstIcon);
        $this->assertArrayHasKey('class', $firstIcon);
        $this->assertArrayHasKey('category', $firstIcon);
        $this->assertArrayHasKey('keywords', $firstIcon);
        
        // 驗證資料類型
        $this->assertIsString($firstIcon['name']);
        $this->assertIsString($firstIcon['class']);
        $this->assertIsString($firstIcon['category']);
        $this->assertIsArray($firstIcon['keywords']);
    }
    
    /**
     * 測試分類篩選功能
     */
    public function test_category_filtering()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons?categories=general,ui');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        // 應該只包含指定的分類
        $this->assertArrayHasKey('general', $data['data']);
        $this->assertArrayHasKey('ui', $data['data']);
        
        // 檢查每個圖標是否屬於正確分類
        foreach ($data['data']['general'] as $icon) {
            $this->assertEquals('general', $icon['category']);
        }
        
        foreach ($data['data']['ui'] as $icon) {
            $this->assertEquals('ui', $icon['category']);
        }
    }
    
    /**
     * 測試總圖標數量合理
     */
    public function test_bootstrap_icons_total_count()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json();
        
        // 總數應該合理（目前配置約 250+）
        $this->assertGreaterThan(200, $data['meta']['total']);
        $this->assertLessThan(500, $data['meta']['total']);
    }
    
    /**
     * 測試 API 回應時間
     */
    public function test_api_response_time()
    {
        $startTime = microtime(true);
        
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // 轉換為毫秒
        
        $response->assertStatus(200);
        
        // API 回應時間應該小於 500ms（根據需求）
        $this->assertLessThan(500, $responseTime, 'API response time is too slow');
    }
    
    /**
     * 測試空分類篩選參數
     */
    public function test_empty_categories_parameter()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons?categories=');
        
        // 空參數應該回應所有分類
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertGreaterThanOrEqual(8, count($data['meta']['categories']));
    }
    
    /**
     * 測試無效分類篩選參數
     */
    public function test_invalid_categories_parameter()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons?categories=invalid,nonexistent');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        // 無效分類應該被忽略，返回所有結果（因為沒有有效分類）
        $this->assertGreaterThan(200, $data['meta']['total']);
        $this->assertGreaterThanOrEqual(8, count($data['meta']['categories']));
    }
    
    /**
     * 測試緩存功能
     */
    public function test_bootstrap_icons_caching()
    {
        // 第一次請求
        $firstResponse = $this->getJson('/api/config/icon/bootstrap-icons');
        $firstResponse->assertStatus(200);
        
        // 第二次請求應該使用緩存
        $secondResponse = $this->getJson('/api/config/icon/bootstrap-icons');
        $secondResponse->assertStatus(200);
        
        // 資料應該完全相同
        $this->assertEquals($firstResponse->json(), $secondResponse->json());
    }
}