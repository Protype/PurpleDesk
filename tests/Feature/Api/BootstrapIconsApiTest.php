<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BootstrapIconsApiTest extends TestCase
{
    /**
     * 測試獲取所有 Bootstrap Icons API 結構
     */
    public function test_get_all_bootstrap_icons_returns_correct_structure()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');

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
        $this->assertEquals('bootstrap-icons', $meta['type']);
        $this->assertIsInt($meta['total']);
        $this->assertGreaterThan(0, $meta['total']);
        $this->assertIsArray($meta['categories']);
    }

    /**
     * 測試 Bootstrap Icon 項目具有必要欄位
     */
    public function test_bootstrap_icon_item_has_required_fields()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json('data');
        
        // 取得第一個分類的第一個 icon
        $firstCategory = array_values($data)[0];
        $this->assertGreaterThan(0, count($firstCategory));
        
        $icon = $firstCategory[0];
        
        // 檢查必要欄位
        $this->assertArrayHasKey('name', $icon);
        $this->assertArrayHasKey('value', $icon);
        $this->assertArrayHasKey('type', $icon);
        $this->assertArrayHasKey('keywords', $icon);
        $this->assertArrayHasKey('category', $icon);
        $this->assertArrayHasKey('has_variants', $icon);
        
        // 檢查欄位類型
        $this->assertIsString($icon['name']);
        $this->assertIsString($icon['value']);
        $this->assertEquals('bootstrap-icons', $icon['type']);
        $this->assertIsArray($icon['keywords']);
        $this->assertIsString($icon['category']);
        $this->assertIsBool($icon['has_variants']);
        
        // 只有有變體的圖標才需要 variant_type 欄位
        if ($icon['has_variants']) {
            $this->assertArrayHasKey('variant_type', $icon);
            $this->assertContains($icon['variant_type'], ['solid', 'outline']);
        }
    }

    /**
     * 測試獲取分類清單
     */
    public function test_get_categories_returns_correct_structure()
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
     * 測試按分類獲取 Bootstrap Icons
     */
    public function test_get_bootstrap_icons_by_category()
    {
        // 先取得所有分類
        $categoriesResponse = $this->getJson('/api/config/icon/bootstrap-icons/categories');
        $categories = $categoriesResponse->json('data');
        
        // 取得第一個分類 ID
        $firstCategoryId = array_keys($categories)[0];
        
        // 測試該分類的端點
        $response = $this->getJson("/api/config/icon/bootstrap-icons/category/{$firstCategoryId}");
        
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
        
        // 檢查該分類下的圖標
        $categoryIcons = $data[$firstCategoryId];
        foreach ($categoryIcons as $icon) {
            $this->assertEquals($firstCategoryId, $icon['category']);
            $this->assertEquals('bootstrap-icons', $icon['type']);
            $this->assertContains($icon['variant_type'], ['solid', 'outline']);
        }
        
        // 檢查 meta
        $meta = $response->json('meta');
        $this->assertEquals('bootstrap-icons', $meta['type']);
        $this->assertEquals(count($categoryIcons), $meta['total']);
    }
    
    /**
     * 測試無效分類返回錯誤
     */
    public function test_invalid_category_returns_error()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons/category/invalid-category');
        
        $response->assertStatus(400)
            ->assertJsonStructure([
                'error'
            ]);
    }

    /**
     * 測試 Bootstrap Icons 變體配對正確性
     */
    public function test_bootstrap_icons_variant_pairing()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json('data');
        
        // 收集所有圖標
        $allIcons = [];
        foreach ($data as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                $allIcons[$icon['value']] = $icon;
            }
        }
        
        $validatedOutlines = []; // 記錄已驗證的 outline 圖標
        
        // 1. 以 solid 圖標為基準進行測試
        foreach ($allIcons as $icon) {
            if (isset($icon['variant_type']) && $icon['variant_type'] === 'solid') {
                // solid 圖標必須有 has_variants: true
                $this->assertTrue($icon['has_variants'], 
                    "Solid icon {$icon['value']} should have has_variants: true");
                
                // 找出對應的 outline 版本：A.replace('-fill', '') -> B
                $outlineValue = str_replace('-fill', '', $icon['value']);
                
                // 測試 B 存在
                $this->assertArrayHasKey($outlineValue, $allIcons,
                    "Solid icon {$icon['value']} should have outline variant {$outlineValue}");
                
                $outlineIcon = $allIcons[$outlineValue];
                
                // 驗證 B 的 has_variant / variant_type 正確
                $this->assertTrue($outlineIcon['has_variants'],
                    "Data quality issue: Outline icon {$outlineValue} should have has_variants: true");
                $this->assertEquals('outline', $outlineIcon['variant_type'],
                    "Data quality issue: Outline icon {$outlineValue} should have variant_type: outline");
                
                // A/B 均驗證成功，標記 B 為已驗證
                $validatedOutlines[$outlineValue] = true;
            }
        }
        
        // 2. 檢查剩餘未標記 pass 的 has_variants=true, variant_type=outline
        $unpairedOutlines = [];
        foreach ($allIcons as $icon) {
            if ($icon['has_variants'] && 
                isset($icon['variant_type']) && 
                $icon['variant_type'] === 'outline' && 
                !isset($validatedOutlines[$icon['value']])) {
                $unpairedOutlines[] = $icon['value'];
            }
        }
        
        // 如果有未配對的 outline 圖標，回報資料錯誤
        if (!empty($unpairedOutlines)) {
            $this->fail("Data quality issue: Unpaired outline icons with has_variants=true missing solid variants:\n" . 
                       implode("\n", array_slice($unpairedOutlines, 0, 10)));
        }
        
        // 確保至少測試了一些配對
        $this->assertGreaterThan(0, count($validatedOutlines), 
            'Should have validated at least some variant pairs');
    }

    /**
     * 測試分類一致性
     */
    public function test_category_consistency()
    {
        // 取得所有 Bootstrap Icons
        $allResponse = $this->getJson('/api/config/icon/bootstrap-icons');
        $allData = $allResponse->json('data');
        $allMeta = $allResponse->json('meta');
        
        // 取得分類清單
        $categoriesResponse = $this->getJson('/api/config/icon/bootstrap-icons/categories');
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

    /**
     * 測試 Bootstrap Icon 的 value 欄位是 CSS 類名
     */
    public function test_bootstrap_icon_value_is_css_class()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json('data');
        
        $firstCategory = array_values($data)[0];
        $icon = $firstCategory[0];
        
        // Bootstrap Icon 的 value 應該是 CSS 類名，以 "bi-" 開頭
        $this->assertStringStartsWith('bi-', $icon['value']);
        $this->assertMatchesRegularExpression('/^bi-[a-z0-9-]+$/', $icon['value']);
    }
}