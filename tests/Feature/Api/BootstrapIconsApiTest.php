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
        $this->assertArrayHasKey('variant_type', $icon);
        
        // 檢查欄位類型
        $this->assertIsString($icon['name']);
        $this->assertIsString($icon['value']);
        $this->assertEquals('bootstrap-icons', $icon['type']);
        $this->assertIsArray($icon['keywords']);
        $this->assertIsString($icon['category']);
        $this->assertIsBool($icon['has_variants']);
        $this->assertContains($icon['variant_type'], ['solid', 'outline']);
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
     * 測試 Bootstrap Icon 變體展開
     */
    public function test_bootstrap_icons_variants_are_expanded()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json('data');
        
        // 收集所有 icon 的基本名稱（移除 -outline, -solid 後綴）
        $iconGroups = [];
        
        foreach ($data as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                // 從 value 中提取基本名稱（移除 bi- 前綴和變體後綴）
                $baseName = preg_replace('/^bi-|(-fill|-solid)$/', '', $icon['value']);
                
                if (!isset($iconGroups[$baseName])) {
                    $iconGroups[$baseName] = [];
                }
                
                $iconGroups[$baseName][] = $icon;
            }
        }
        
        // 至少應該有一組有多個變體的 icon
        $hasMultipleVariants = false;
        
        foreach ($iconGroups as $baseName => $variants) {
            if (count($variants) > 1) {
                $hasMultipleVariants = true;
                
                // 檢查變體類型
                $variantTypes = array_column($variants, 'variant_type');
                $this->assertContains('outline', $variantTypes, "Icon group {$baseName} should have outline variant");
                $this->assertContains('solid', $variantTypes, "Icon group {$baseName} should have solid variant");
                break;
            }
        }
        
        $this->assertTrue($hasMultipleVariants, 'Should have at least one icon with multiple variants');
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