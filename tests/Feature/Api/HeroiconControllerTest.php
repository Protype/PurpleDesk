<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class HeroiconControllerTest extends TestCase
{
    /**
     * 測試取得所有 heroicon 資料的 API
     */
    public function test_can_get_all_heroicons()
    {
        $response = $this->getJson('/api/config/icon/heroicon');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'categories' => [
                    '*' => [
                        'id',
                        'name',
                        'icon',
                        'priority',
                        'subgroups' => [
                            '*' => [
                                'name',
                                'icons' => [
                                    '*' => [
                                        'icon',
                                        'name'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'stats' => [
                    'total_categories',
                    'total_icons',
                    'styles'
                ]
            ]);
    }
    
    /**
     * 測試 heroicon 資料包含所有必要的分類
     */
    public function test_heroicon_data_contains_expected_categories()
    {
        $response = $this->getJson('/api/config/icon/heroicon');
        
        $data = $response->json();
        
        // 驗證包含所有主要分類
        $expectedCategories = [
            'navigation',
            'communication',
            'interface',
            'document',
            'device',
            'finance',
            'media',
            'misc'
        ];
        
        foreach ($expectedCategories as $category) {
            $this->assertArrayHasKey($category, $data['categories']);
        }
    }
    
    /**
     * 測試 heroicon 統計資料
     */
    public function test_heroicon_statistics()
    {
        $response = $this->getJson('/api/config/icon/heroicon');
        
        $data = $response->json();
        
        // 驗證統計資料
        $this->assertArrayHasKey('stats', $data);
        $this->assertArrayHasKey('total_categories', $data['stats']);
        $this->assertArrayHasKey('total_icons', $data['stats']);
        $this->assertArrayHasKey('styles', $data['stats']);
        
        // 驗證分類數量（應該是 8 個）
        $this->assertEquals(8, $data['stats']['total_categories']);
        
        // 驗證總圖標數量（Heroicons v1 有 230 個圖標）
        $this->assertEquals(230, $data['stats']['total_icons']);
        
        // 驗證樣式支援
        $this->assertContains('outline', $data['stats']['styles']);
        $this->assertContains('solid', $data['stats']['styles']);
    }
    
    /**
     * 測試 heroicon 資料格式正確
     */
    public function test_heroicon_data_format()
    {
        $response = $this->getJson('/api/config/icon/heroicon');
        
        $data = $response->json();
        
        // 取得第一個分類的第一個子群組的第一個圖標來驗證格式
        $firstCategory = array_values($data['categories'])[0];
        $this->assertIsArray($firstCategory['subgroups']);
        
        if (!empty($firstCategory['subgroups'])) {
            $firstSubgroup = array_values($firstCategory['subgroups'])[0];
            $this->assertArrayHasKey('name', $firstSubgroup);
            $this->assertArrayHasKey('icons', $firstSubgroup);
            
            if (!empty($firstSubgroup['icons'])) {
                $firstIcon = $firstSubgroup['icons'][0];
                $this->assertArrayHasKey('icon', $firstIcon);
                $this->assertArrayHasKey('name', $firstIcon);
                $this->assertIsString($firstIcon['icon']);
                $this->assertIsString($firstIcon['name']);
            }
        }
    }
    
    /**
     * 測試 API 回應時間合理
     */
    public function test_api_response_time()
    {
        $startTime = microtime(true);
        
        $response = $this->getJson('/api/config/icon/heroicon');
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // 轉換為毫秒
        
        $response->assertStatus(200);
        
        // API 回應時間應該小於 500ms
        $this->assertLessThan(500, $responseTime, 'API response time is too slow');
    }
    
    /**
     * 測試優先級設定正確
     */
    public function test_heroicon_priority_settings()
    {
        $response = $this->getJson('/api/config/icon/heroicon');
        
        $data = $response->json();
        
        // 驗證優先級值
        $validPriorities = ['immediate', 'high', 'medium', 'low'];
        
        foreach ($data['categories'] as $category) {
            $this->assertContains($category['priority'], $validPriorities);
        }
        
        // 驗證 navigation 應該是 immediate 優先級
        $this->assertEquals('immediate', $data['categories']['navigation']['priority']);
        
        // 驗證 communication 和 interface 應該是 high 優先級
        $this->assertEquals('high', $data['categories']['communication']['priority']);
        $this->assertEquals('high', $data['categories']['interface']['priority']);
    }
    
    /**
     * 測試特定分類的圖標數量
     */
    public function test_specific_category_icon_count()
    {
        $response = $this->getJson('/api/config/icon/heroicon');
        
        $data = $response->json();
        
        // 驗證 navigation 分類有足夠的圖標
        $navigationCategory = $data['categories']['navigation'];
        $iconCount = 0;
        foreach ($navigationCategory['subgroups'] as $subgroup) {
            $iconCount += count($subgroup['icons']);
        }
        $this->assertGreaterThan(30, $iconCount, 'Navigation category should have more than 30 icons');
        
        // 驗證 interface 分類有足夠的圖標
        $interfaceCategory = $data['categories']['interface'];
        $iconCount = 0;
        foreach ($interfaceCategory['subgroups'] as $subgroup) {
            $iconCount += count($subgroup['icons']);
        }
        $this->assertGreaterThan(40, $iconCount, 'Interface category should have more than 40 icons');
    }
}