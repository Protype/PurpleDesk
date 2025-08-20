<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class BootstrapIconControllerTest extends TestCase
{
    /**
     * 測試取得所有 Bootstrap Icons 資料的 API - 新的 data/meta 格式
     */
    public function test_can_get_all_bootstrap_icons()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [ // 分類 ID 作為 key
                        '*' => [ // bootstrap icons 陣列
                            'id',
                            'name',
                            'value', // Bootstrap Icon 使用 CSS class
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
     * 測試 Bootstrap Icons meta 資料格式正確
     */
    public function test_bootstrap_icons_meta_data_format()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json();
        
        // 驗證 meta 資料結構
        $this->assertArrayHasKey('meta', $data);
        $this->assertArrayHasKey('total', $data['meta']);
        $this->assertArrayHasKey('type', $data['meta']);
        $this->assertArrayHasKey('categories', $data['meta']);
        
        // 驗證 type 為 bootstrap-icons
        $this->assertEquals('bootstrap-icons', $data['meta']['type']);
        
        // 驗證 total 是正整數
        $this->assertIsInt($data['meta']['total']);
        $this->assertGreaterThan(0, $data['meta']['total']);
        
        // 驗證分類數量合理（應該有 8 個分類）
        $this->assertGreaterThanOrEqual(8, count($data['meta']['categories']));
    }
    
    /**
     * 測試 Bootstrap Icons 資料格式正確
     */
    public function test_bootstrap_icons_data_format()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json();
        
        // 取得第一個分類的第一個 Bootstrap Icon 來驗證格式
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
        $this->assertIsString($firstIcon['value']); // CSS class
        $this->assertEquals('bootstrap-icons', $firstIcon['type']);
        $this->assertIsArray($firstIcon['keywords']);
        $this->assertIsString($firstIcon['category']);
        $this->assertIsBool($firstIcon['has_variants']);
        $this->assertContains($firstIcon['variant_type'], ['outline', 'solid']);
    }
    
    /**
     * 測試分類 API
     */
    public function test_can_get_bootstrap_icons_categories()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons/categories');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'description'
                    ]
                ]
            ]);
            
        $data = $response->json();
        
        // 驗證包含預期的分類
        $expectedCategories = ['general', 'ui', 'communications'];
        foreach ($expectedCategories as $category) {
            $this->assertArrayHasKey($category, $data['data'], "Missing category: {$category}");
        }
    }
    
    /**
     * 測試取得特定分類的 Bootstrap Icons
     */
    public function test_can_get_bootstrap_icons_by_category()
    {
        // 先取得分類列表
        $categoriesResponse = $this->getJson('/api/config/icon/bootstrap-icons/categories');
        $categoriesData = $categoriesResponse->json();
        
        if (empty($categoriesData['data'])) {
            $this->markTestSkipped('No categories available');
        }
        
        $firstCategoryId = array_keys($categoriesData['data'])[0];
        
        $response = $this->getJson("/api/config/icon/bootstrap-icons/category/{$firstCategoryId}");
        
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
        
        // 驗證每個 Bootstrap Icon 都屬於正確分類
        foreach ($data['data'][$firstCategoryId] as $icon) {
            $this->assertEquals($firstCategoryId, $icon['category']);
        }
    }
    
    /**
     * 測試無效分類回應 400 錯誤
     */
    public function test_invalid_bootstrap_icons_category_returns_error()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons/category/invalid_category');
        
        $response->assertStatus(400)
            ->assertJsonStructure([
                'error'
            ]);
            
        $data = $response->json();
        $this->assertStringContainsString('Invalid category', $data['error']);
    }
    
    /**
     * 測試 Bootstrap Icons 包含主要分類
     */
    public function test_bootstrap_icons_contains_expected_categories()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json();
        
        // 驗證包含主要分類
        $expectedCategories = [
            'general', 'ui', 'communications', 'files',
            'media', 'people', 'alphanumeric', 'others'
        ];
        
        foreach ($expectedCategories as $category) {
            $this->assertArrayHasKey($category, $data['meta']['categories'], "Missing category: {$category}");
        }
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
        
        // API 回應時間應該小於 500ms
        $this->assertLessThan(500, $responseTime, 'API response time is too slow');
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
    
    /**
     * 測試 Bootstrap Icons 變體功能
     */
    public function test_bootstrap_icons_variants()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
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
        
        // 驗證有 outline 和 solid 變體
        $this->assertNotEmpty($outlineIcons);
        $this->assertNotEmpty($solidIcons);
        
        // Bootstrap Icons 的 outline 通常比 solid 多（因為不是所有圖標都有 -fill 版本）
        $this->assertGreaterThanOrEqual(count($solidIcons), count($outlineIcons));
    }
    
    /**
     * 測試總圖標數量合理
     */
    public function test_bootstrap_icons_total_count()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json();
        
        // 總數應該合理（目前配置約 3800+ 個）
        $this->assertGreaterThan(3000, $data['meta']['total']);
        $this->assertLessThan(4000, $data['meta']['total']);
    }
    
    /**
     * 測試 -fill 後綴處理邏輯
     */
    public function test_bootstrap_icons_fill_suffix_handling()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        $data = $response->json();
        
        // 尋找有 -fill 變體的圖標
        $fillIcons = [];
        $baseIcons = [];
        
        foreach ($data['data'] as $category) {
            foreach ($category as $icon) {
                if (str_contains($icon['value'], '-fill')) {
                    $fillIcons[] = $icon;
                } else {
                    $baseIcons[] = $icon;
                }
            }
        }
        
        // 驗證有基礎圖標和 fill 變體
        $this->assertNotEmpty($baseIcons);
        $this->assertNotEmpty($fillIcons);
        
        // 驗證 fill 圖標的 variant_type 為 solid
        foreach ($fillIcons as $fillIcon) {
            $this->assertEquals('solid', $fillIcon['variant_type'], 
                "Fill icon {$fillIcon['value']} should have variant_type 'solid'");
        }
        
        // 驗證基礎圖標的 variant_type 為 outline  
        foreach ($baseIcons as $baseIcon) {
            if (!str_contains($baseIcon['value'], '-fill')) {
                $this->assertEquals('outline', $baseIcon['variant_type'],
                    "Base icon {$baseIcon['value']} should have variant_type 'outline'");
            }
        }
    }
}