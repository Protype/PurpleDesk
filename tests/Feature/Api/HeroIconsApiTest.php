<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeroIconsApiTest extends TestCase
{
    /**
     * 測試獲取所有 HeroIcons API 結構
     */
    public function test_get_all_heroicons_returns_correct_structure()
    {
        $response = $this->getJson('/api/config/icon/heroicons');

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
        $this->assertEquals('heroicons', $meta['type']);
        $this->assertIsInt($meta['total']);
        $this->assertGreaterThan(0, $meta['total']);
        $this->assertIsArray($meta['categories']);
    }

    /**
     * 測試 HeroIcon 項目具有必要欄位
     */
    public function test_heroicon_item_has_required_fields()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
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
        $this->assertArrayHasKey('variant_type', $icon);
        
        // 檢查欄位類型
        $this->assertIsString($icon['name']);
        $this->assertIsString($icon['value']);
        $this->assertEquals('heroicons', $icon['type']);
        $this->assertIsArray($icon['keywords']);
        $this->assertIsString($icon['category']);
        $this->assertIsBool($icon['has_variants']);
        $this->assertContains($icon['variant_type'], ['outline', 'solid']);
    }

    /**
     * 測試獲取分類清單
     */
    public function test_get_categories_returns_correct_structure()
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
     * 測試按分類獲取 HeroIcons
     */
    public function test_get_heroicons_by_category()
    {
        // 先取得所有分類
        $categoriesResponse = $this->getJson('/api/config/icon/heroicons/categories');
        $categories = $categoriesResponse->json('data');
        
        // 取得第一個分類 ID
        $firstCategoryId = array_keys($categories)[0];
        
        // 測試該分類的端點
        $response = $this->getJson("/api/config/icon/heroicons/category/{$firstCategoryId}");
        
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
            $this->assertEquals('heroicons', $icon['type']);
            $this->assertContains($icon['variant_type'], ['outline', 'solid']);
        }
        
        // 檢查 meta
        $meta = $response->json('meta');
        $this->assertEquals('heroicons', $meta['type']);
        $this->assertEquals(count($categoryIcons), $meta['total']);
    }

    /**
     * 測試無效分類返回錯誤
     */
    public function test_invalid_category_returns_error()
    {
        $response = $this->getJson('/api/config/icon/heroicons/category/invalid-category');
        
        $response->assertStatus(400)
            ->assertJsonStructure([
                'error'
            ]);
    }

    /**
     * 測試 HeroIcons 變體配對正確性
     */
    public function test_heroicons_variant_pairing()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json('data');
        
        // 收集所有有變體的圖標，按 value 分組
        $iconGroups = [];
        foreach ($data as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                if ($icon['has_variants']) {
                    $iconGroups[$icon['value']][] = $icon['variant_type'];
                }
            }
        }
        
        // 驗證每個組都有完整的變體配對
        $testedCount = 0;
        foreach ($iconGroups as $value => $types) {
            $this->assertContains('outline', $types, 
                "Icon {$value} should have outline variant");
            $this->assertContains('solid', $types,
                "Icon {$value} should have solid variant");
            $this->assertCount(2, $types,
                "Icon {$value} should have exactly 2 variants (outline and solid)");
            $testedCount++;
        }
        
        // 確保至少測試了一些圖標
        $this->assertGreaterThan(0, $testedCount, 'Should have tested at least some icon variants');
    }

    /**
     * 測試分類一致性
     */
    public function test_category_consistency()
    {
        // 取得所有 HeroIcons
        $allResponse = $this->getJson('/api/config/icon/heroicons');
        $allData = $allResponse->json('data');
        $allMeta = $allResponse->json('meta');
        
        // 取得分類清單
        $categoriesResponse = $this->getJson('/api/config/icon/heroicons/categories');
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
     * 測試 HeroIcon 的 value 欄位是 component 名稱
     */
    public function test_heroicon_value_is_component_name()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json('data');
        
        $firstCategory = array_values($data)[0];
        $icon = $firstCategory[0];
        
        // HeroIcon 的 value 應該是 component 名稱，以 "Icon" 結尾
        $this->assertStringEndsWith('Icon', $icon['value']);
        $this->assertMatchesRegularExpression('/^[A-Z][a-zA-Z]*Icon$/', $icon['value']);
    }
}