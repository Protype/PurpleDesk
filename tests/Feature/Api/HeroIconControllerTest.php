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
    
    // ===== 變體功能 API 測試 =====
    
    /**
     * 測試取得變體映射資訊 API
     */
    public function test_can_get_heroicons_variants()
    {
        $response = $this->getJson('/api/config/icon/heroicons/variants');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'mapping' => [
                        'outline' => [
                            'path',
                            'suffix',
                            'description'
                        ],
                        'solid' => [
                            'path',
                            'suffix',
                            'description'
                        ]
                    ],
                    'supported' => []
                ]
            ]);
        
        $data = $response->json();
        
        // 驗證變體映射內容
        $this->assertEquals('@heroicons/vue/outline', $data['data']['mapping']['outline']['path']);
        $this->assertEquals('@heroicons/vue/solid', $data['data']['mapping']['solid']['path']);
        $this->assertContains('outline', $data['data']['supported']);
        $this->assertContains('solid', $data['data']['supported']);
    }
    
    /**
     * 測試取得 outline 樣式圖標 API
     */
    public function test_can_get_heroicons_by_outline_style()
    {
        $response = $this->getJson('/api/config/icon/heroicons/style/outline');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'component',
                        'category',
                        'keywords',
                        'currentStyle'
                    ]
                ],
                'meta' => [
                    'total',
                    'categories',
                    'currentStyle',
                    'description'
                ]
            ]);
        
        $data = $response->json();
        
        // 驗證樣式相關資訊
        $this->assertEquals('outline', $data['meta']['currentStyle']);
        $this->assertEquals('線條樣式', $data['meta']['description']);
        $this->assertEquals(230, $data['meta']['total']);
        
        // 驗證每個圖標都有 currentStyle 屬性
        foreach ($data['data'] as $icon) {
            $this->assertEquals('outline', $icon['currentStyle']);
        }
    }
    
    /**
     * 測試取得 solid 樣式圖標 API
     */
    public function test_can_get_heroicons_by_solid_style()
    {
        $response = $this->getJson('/api/config/icon/heroicons/style/solid');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'component',
                        'category',
                        'keywords',
                        'currentStyle'
                    ]
                ],
                'meta' => [
                    'total',
                    'categories',
                    'currentStyle',
                    'description'
                ]
            ]);
        
        $data = $response->json();
        
        // 驗證樣式相關資訊
        $this->assertEquals('solid', $data['meta']['currentStyle']);
        $this->assertEquals('實心樣式', $data['meta']['description']);
        $this->assertEquals(230, $data['meta']['total']);
        
        // 驗證每個圖標都有 currentStyle 屬性
        foreach ($data['data'] as $icon) {
            $this->assertEquals('solid', $icon['currentStyle']);
        }
    }
    
    /**
     * 測試無效樣式返回錯誤
     */
    public function test_invalid_style_returns_error()
    {
        $response = $this->getJson('/api/config/icon/heroicons/style/invalid');
        
        $response->assertStatus(400)
            ->assertJsonStructure([
                'error'
            ]);
        
        $data = $response->json();
        $this->assertStringContainsString('Unsupported style', $data['error']);
    }
    
    /**
     * 測試取得單一圖標變體資訊 API
     */
    public function test_can_get_single_icon_variants()
    {
        $response = $this->getJson('/api/config/icon/heroicons/icon/AcademicCapIcon/variants');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'component',
                    'variants' => [
                        'outline' => [
                            'component',
                            'path',
                            'description'
                        ],
                        'solid' => [
                            'component',
                            'path',
                            'description'
                        ]
                    ]
                ]
            ]);
        
        $data = $response->json();
        
        // 驗證圖標變體資訊
        $this->assertEquals('AcademicCapIcon', $data['data']['component']);
        $this->assertEquals('AcademicCapIcon', $data['data']['variants']['outline']['component']);
        $this->assertEquals('AcademicCapIcon', $data['data']['variants']['solid']['component']);
        $this->assertEquals('@heroicons/vue/outline', $data['data']['variants']['outline']['path']);
        $this->assertEquals('@heroicons/vue/solid', $data['data']['variants']['solid']['path']);
    }
    
    /**
     * 測試不存在的圖標變體返回 404
     */
    public function test_non_existent_icon_variants_returns_404()
    {
        $response = $this->getJson('/api/config/icon/heroicons/icon/NonExistentIcon/variants');
        
        $response->assertStatus(404)
            ->assertJsonStructure([
                'error'
            ]);
    }
    
    /**
     * 測試檢查圖標是否支援特定樣式 API
     */
    public function test_can_check_icon_has_variant()
    {
        // 測試支援的樣式
        $response = $this->getJson('/api/config/icon/heroicons/icon/AcademicCapIcon/variant/outline');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'component',
                    'style',
                    'hasVariant',
                    'variantInfo'
                ]
            ]);
        
        $data = $response->json();
        $this->assertEquals('AcademicCapIcon', $data['data']['component']);
        $this->assertEquals('outline', $data['data']['style']);
        $this->assertTrue($data['data']['hasVariant']);
        $this->assertIsArray($data['data']['variantInfo']);
    }
    
    /**
     * 測試檢查圖標不支援的樣式
     */
    public function test_icon_does_not_have_invalid_variant()
    {
        $response = $this->getJson('/api/config/icon/heroicons/icon/AcademicCapIcon/variant/invalid');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'component',
                    'style',
                    'hasVariant',
                    'variantInfo'
                ]
            ]);
        
        $data = $response->json();
        $this->assertEquals('AcademicCapIcon', $data['data']['component']);
        $this->assertEquals('invalid', $data['data']['style']);
        $this->assertFalse($data['data']['hasVariant']);
        $this->assertNull($data['data']['variantInfo']);
    }
    
    /**
     * 測試不存在的圖標檢查樣式支援
     */
    public function test_non_existent_icon_variant_check()
    {
        $response = $this->getJson('/api/config/icon/heroicons/icon/NonExistentIcon/variant/outline');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'component',
                    'style',
                    'hasVariant',
                    'variantInfo'
                ]
            ]);
        
        $data = $response->json();
        $this->assertEquals('NonExistentIcon', $data['data']['component']);
        $this->assertEquals('outline', $data['data']['style']);
        $this->assertFalse($data['data']['hasVariant']);
        $this->assertNull($data['data']['variantInfo']);
    }
    
    /**
     * 測試變體 API 回應時間
     */
    public function test_variant_api_response_time()
    {
        $endpoints = [
            '/api/config/icon/heroicons/variants',
            '/api/config/icon/heroicons/style/outline',
            '/api/config/icon/heroicons/icon/AcademicCapIcon/variants',
            '/api/config/icon/heroicons/icon/AcademicCapIcon/variant/outline'
        ];
        
        foreach ($endpoints as $endpoint) {
            $startTime = microtime(true);
            
            $response = $this->getJson($endpoint);
            
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000; // 轉換為毫秒
            
            $response->assertStatus(200);
            
            // 變體 API 回應時間應該小於 300ms
            $this->assertLessThan(300, $responseTime, "Variant API endpoint {$endpoint} response time is too slow");
        }
    }
}