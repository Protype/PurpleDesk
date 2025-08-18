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
    
    // ===== 變體功能 API 測試 =====
    
    /**
     * 測試取得變體映射資訊 API
     */
    public function test_can_get_bootstrap_icons_variants()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons/variants');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'mapping' => [
                        'outline' => [
                            'suffix',
                            'description',
                            'rule'
                        ],
                        'solid' => [
                            'suffix',
                            'description',
                            'rule'
                        ]
                    ],
                    'supported' => []
                ]
            ]);
        
        $data = $response->json();
        
        // 驗證變體映射內容
        $this->assertEquals('', $data['data']['mapping']['outline']['suffix']);
        $this->assertEquals('-fill', $data['data']['mapping']['solid']['suffix']);
        $this->assertEquals('remove_fill_suffix', $data['data']['mapping']['outline']['rule']);
        $this->assertEquals('add_fill_suffix', $data['data']['mapping']['solid']['rule']);
        $this->assertContains('outline', $data['data']['supported']);
        $this->assertContains('solid', $data['data']['supported']);
    }
    
    /**
     * 測試取得 outline 樣式圖標 API
     */
    public function test_can_get_bootstrap_icons_by_outline_style()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons/style/outline');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        '*' => [
                            'name',
                            'class',
                            'category',
                            'keywords',
                            'currentStyle'
                        ]
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
        
        // 驗證沒有包含 -fill 圖標
        foreach ($data['data'] as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                $this->assertEquals('outline', $icon['currentStyle']);
                $this->assertStringNotContainsString('-fill', $icon['class']);
            }
        }
    }
    
    /**
     * 測試取得 solid 樣式圖標 API
     */
    public function test_can_get_bootstrap_icons_by_solid_style()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons/style/solid');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        '*' => [
                            'name',
                            'class',
                            'category',
                            'keywords',
                            'currentStyle'
                        ]
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
        
        // 驗證每個圖標都有 currentStyle 屬性
        foreach ($data['data'] as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                $this->assertEquals('solid', $icon['currentStyle']);
            }
        }
    }
    
    /**
     * 測試無效樣式返回錯誤
     */
    public function test_invalid_bootstrap_style_returns_error()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons/style/invalid');
        
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
    public function test_can_get_single_bootstrap_icon_variants()
    {
        // 先取得一個測試用的圖標
        $allResponse = $this->getJson('/api/config/icon/bootstrap-icons');
        $allData = $allResponse->json();
        
        // 取得第一個分類的第一個圖標
        $firstCategory = array_values($allData['data'])[0];
        $testIcon = $firstCategory[0];
        
        $response = $this->getJson("/api/config/icon/bootstrap-icons/icon/{$testIcon['class']}/variants");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'class',
                    'variants' => [
                        'outline' => [
                            'class',
                            'description'
                        ],
                        'solid' => [
                            'class',
                            'description'
                        ]
                    ]
                ]
            ]);
        
        $data = $response->json();
        
        // 驗證圖標變體資訊
        $this->assertEquals($testIcon['class'], $data['data']['class']);
        $this->assertIsArray($data['data']['variants']);
        $this->assertArrayHasKey('outline', $data['data']['variants']);
        $this->assertArrayHasKey('solid', $data['data']['variants']);
    }
    
    /**
     * 測試不存在的圖標變體返回 404
     */
    public function test_non_existent_bootstrap_icon_variants_returns_404()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons/icon/non-existent-icon/variants');
        
        $response->assertStatus(404)
            ->assertJsonStructure([
                'error'
            ]);
    }
    
    /**
     * 測試檢查圖標是否支援特定樣式 API
     */
    public function test_can_check_bootstrap_icon_has_variant()
    {
        // 先取得一個測試用的圖標
        $allResponse = $this->getJson('/api/config/icon/bootstrap-icons');
        $allData = $allResponse->json();
        
        // 取得第一個分類的第一個圖標
        $firstCategory = array_values($allData['data'])[0];
        $testIcon = $firstCategory[0];
        
        // 測試支援的樣式
        $response = $this->getJson("/api/config/icon/bootstrap-icons/icon/{$testIcon['class']}/variant/outline");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'class',
                    'style',
                    'hasVariant',
                    'variantClass'
                ]
            ]);
        
        $data = $response->json();
        $this->assertEquals($testIcon['class'], $data['data']['class']);
        $this->assertEquals('outline', $data['data']['style']);
        $this->assertTrue($data['data']['hasVariant']);
        $this->assertIsString($data['data']['variantClass']);
    }
    
    /**
     * 測試檢查圖標不支援的樣式
     */
    public function test_bootstrap_icon_does_not_have_invalid_variant()
    {
        // 先取得一個測試用的圖標
        $allResponse = $this->getJson('/api/config/icon/bootstrap-icons');
        $allData = $allResponse->json();
        
        // 取得第一個分類的第一個圖標
        $firstCategory = array_values($allData['data'])[0];
        $testIcon = $firstCategory[0];
        
        $response = $this->getJson("/api/config/icon/bootstrap-icons/icon/{$testIcon['class']}/variant/invalid");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'class',
                    'style',
                    'hasVariant',
                    'variantClass'
                ]
            ]);
        
        $data = $response->json();
        $this->assertEquals($testIcon['class'], $data['data']['class']);
        $this->assertEquals('invalid', $data['data']['style']);
        $this->assertFalse($data['data']['hasVariant']);
        $this->assertNull($data['data']['variantClass']);
    }
    
    /**
     * 測試不存在的圖標檢查樣式支援
     */
    public function test_non_existent_bootstrap_icon_variant_check()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons/icon/non-existent-icon/variant/outline');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'class',
                    'style',
                    'hasVariant',
                    'variantClass'
                ]
            ]);
        
        $data = $response->json();
        $this->assertEquals('non-existent-icon', $data['data']['class']);
        $this->assertEquals('outline', $data['data']['style']);
        $this->assertFalse($data['data']['hasVariant']);
        $this->assertNull($data['data']['variantClass']);
    }
    
    /**
     * 測試變體 API 回應時間
     */
    public function test_bootstrap_variant_api_response_time()
    {
        // 先取得一個測試用的圖標
        $allResponse = $this->getJson('/api/config/icon/bootstrap-icons');
        $allData = $allResponse->json();
        $firstCategory = array_values($allData['data'])[0];
        $testIcon = $firstCategory[0];
        
        $endpoints = [
            '/api/config/icon/bootstrap-icons/variants',
            '/api/config/icon/bootstrap-icons/style/outline',
            "/api/config/icon/bootstrap-icons/icon/{$testIcon['class']}/variants",
            "/api/config/icon/bootstrap-icons/icon/{$testIcon['class']}/variant/outline"
        ];
        
        foreach ($endpoints as $endpoint) {
            $startTime = microtime(true);
            
            $response = $this->getJson($endpoint);
            
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000; // 轉換為毫秒
            
            $response->assertStatus(200);
            
            // 變體 API 回應時間應該小於 500ms
            $this->assertLessThan(500, $responseTime, "Bootstrap variant API endpoint {$endpoint} response time is too slow");
        }
    }
    
    /**
     * 測試 -fill 後綴處理邏輯在 API 中的表現
     */
    public function test_fill_suffix_handling_in_api()
    {
        // 先取得所有圖標
        $allResponse = $this->getJson('/api/config/icon/bootstrap-icons');
        $allData = $allResponse->json();
        
        // 尋找有 -fill 變體的圖標
        $fillIcon = null;
        $baseIcon = null;
        
        foreach ($allData['data'] as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                if (str_contains($icon['class'], '-fill')) {
                    $fillIcon = $icon;
                    // 尋找對應的基礎圖標
                    $baseClassName = str_replace('-fill', '', $icon['class']);
                    foreach ($categoryIcons as $baseCandidate) {
                        if ($baseCandidate['class'] === $baseClassName) {
                            $baseIcon = $baseCandidate;
                            break;
                        }
                    }
                    break 2;
                }
            }
        }
        
        if ($fillIcon && $baseIcon) {
            // 測試基礎圖標的變體 API
            $baseResponse = $this->getJson("/api/config/icon/bootstrap-icons/icon/{$baseIcon['class']}/variants");
            $baseResponse->assertStatus(200);
            $baseData = $baseResponse->json();
            
            $this->assertEquals($baseIcon['class'], $baseData['data']['variants']['outline']['class']);
            $this->assertEquals($fillIcon['class'], $baseData['data']['variants']['solid']['class']);
            
            // 測試 -fill 圖標的變體 API
            $fillResponse = $this->getJson("/api/config/icon/bootstrap-icons/icon/{$fillIcon['class']}/variants");
            $fillResponse->assertStatus(200);
            $fillData = $fillResponse->json();
            
            $this->assertEquals($baseIcon['class'], $fillData['data']['variants']['outline']['class']);
            $this->assertEquals($fillIcon['class'], $fillData['data']['variants']['solid']['class']);
        }
    }
}