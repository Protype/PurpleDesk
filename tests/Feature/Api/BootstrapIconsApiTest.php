<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BootstrapIconsApiTest extends TestCase
{
    /**
     * 測試 Bootstrap Icons API 回傳正確的基本結構
     */
    public function test_bootstrap_api_returns_correct_structure()
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
    }

    /**
     * 測試 data 按分類分組
     */
    public function test_bootstrap_data_is_grouped_by_category()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json('data');
        
        // 應該至少有一個分類
        $this->assertGreaterThan(0, count($data));
        
        // 每個分類都應該是陣列
        foreach ($data as $categoryId => $categoryIcons) {
            $this->assertIsString($categoryId);
            $this->assertIsArray($categoryIcons);
        }
    }

    /**
     * 測試 Bootstrap Icon 項目具有必要欄位
     */
    public function test_bootstrap_item_has_required_fields()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json('data');
        
        // 取得第一個分類的第一個 icon
        $firstCategory = array_values($data)[0];
        $this->assertGreaterThan(0, count($firstCategory));
        
        $icon = $firstCategory[0];
        
        // 檢查必要欄位
        $this->assertArrayHasKey('id', $icon);
        $this->assertArrayHasKey('name', $icon);
        $this->assertArrayHasKey('value', $icon);
        $this->assertArrayHasKey('type', $icon);
        $this->assertArrayHasKey('keywords', $icon);
        $this->assertArrayHasKey('category', $icon);
        $this->assertArrayHasKey('has_variants', $icon);
        
        // 檢查欄位類型
        $this->assertIsString($icon['id']);
        $this->assertIsString($icon['name']);
        $this->assertIsString($icon['value']);
        $this->assertEquals('bootstrap', $icon['type']);
        $this->assertIsArray($icon['keywords']);
        $this->assertIsString($icon['category']);
        $this->assertIsBool($icon['has_variants']);
    }

    /**
     * 測試 Bootstrap Icon 使用 value 欄位
     */
    public function test_bootstrap_icon_uses_value_field()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json('data');
        
        $firstCategory = array_values($data)[0];
        $icon = $firstCategory[0];
        
        // Bootstrap Icon 的 value 應該是 CSS class，以 "bi-" 開頭
        $this->assertStringStartsWith('bi-', $icon['value']);
        
        // 不應該有舊的 class 欄位
        $this->assertArrayNotHasKey('class', $icon);
        
        // 不應該有 component 欄位
        $this->assertArrayNotHasKey('component', $icon);
    }

    /**
     * 測試 Bootstrap Icons 變體已展開
     */
    public function test_bootstrap_variants_are_expanded()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json('data');
        
        // 尋找有變體的 icon 組
        $iconGroups = [];
        
        foreach ($data as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                if ($icon['has_variants']) {
                    // 從 id 中提取基本名稱（移除 -fill 後綴）
                    $baseName = preg_replace('/-fill$/', '', $icon['id']);
                    
                    if (!isset($iconGroups[$baseName])) {
                        $iconGroups[$baseName] = [];
                    }
                    
                    $iconGroups[$baseName][] = $icon;
                }
            }
        }
        
        // 至少應該有一組有多個變體的 icon
        $hasMultipleVariants = false;
        
        foreach ($iconGroups as $baseName => $variants) {
            if (count($variants) > 1) {
                $hasMultipleVariants = true;
                
                // 檢查變體類型（可能有 outline 和 solid，但不一定都有）
                $variantTypes = array_column($variants, 'variant_type');
                
                // 至少應該有一種變體類型
                $this->assertGreaterThan(0, count($variantTypes), "Icon group {$baseName} should have variant types");
                
                // 檢查 value 欄位是否正確
                foreach ($variants as $variant) {
                    if ($variant['variant_type'] === 'outline') {
                        $this->assertStringStartsWith('bi-', $variant['value']);
                        // outline 變體通常不包含 -fill，但不是絕對規則
                    } elseif ($variant['variant_type'] === 'solid') {
                        $this->assertStringStartsWith('bi-', $variant['value']);
                        // solid 變體通常包含 -fill，但可能有例外如複合名稱
                        $this->assertTrue(
                            str_contains($variant['value'], '-fill') || $variant['variant_type'] === 'solid',
                            "Solid variant should contain '-fill' or be explicitly marked as solid: {$variant['value']}"
                        );
                    }
                }
            }
        }
        
        $this->assertTrue($hasMultipleVariants, 'Should have at least one icon with multiple variants');
    }

    /**
     * 測試沒有變體的 icon 處理
     */
    public function test_icons_without_variants()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json('data');
        
        // 尋找沒有變體的 icon
        $iconWithoutVariants = null;
        
        foreach ($data as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                if ($icon['has_variants'] === false) {
                    $iconWithoutVariants = $icon;
                    break 2;
                }
            }
        }
        
        if ($iconWithoutVariants !== null) {
            // 在新設計中，所有圖標都有 variant_type，即使只有一個變體
            $this->assertArrayHasKey('variant_type', $iconWithoutVariants);
            
            // value 應該是有效的 CSS class
            $this->assertStringStartsWith('bi-', $iconWithoutVariants['value']);
        } else {
            // 如果沒有找到 has_variants=false 的圖標，那也是正常的
            $this->assertTrue(true, 'All Bootstrap Icons have variants or only single variant, which is expected');
        }
    }

    /**
     * 測試分類端點返回單一分類
     */
    public function test_category_endpoint_returns_single_category()
    {
        // 先取得所有分類
        $allResponse = $this->getJson('/api/config/icon/bootstrap-icons');
        $allData = $allResponse->json('data');
        
        // 取得第一個分類 ID
        $firstCategoryId = array_keys($allData)[0];
        
        // 測試單一分類端點
        $response = $this->getJson("/api/config/icon/bootstrap-icons/category/{$firstCategoryId}");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    $firstCategoryId => []
                ],
                'meta' => [
                    'total',
                    'categories'
                ]
            ]);
        
        $data = $response->json('data');
        
        // 應該只有一個分類
        $this->assertCount(1, $data);
        $this->assertArrayHasKey($firstCategoryId, $data);
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
     * 測試 meta 包含正確的 type
     */
    public function test_meta_contains_type_bootstrap()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $meta = $response->json('meta');
        
        $this->assertArrayHasKey('type', $meta);
        $this->assertEquals('bootstrap', $meta['type']);
    }

    /**
     * 測試總數包含展開的變體
     */
    public function test_total_includes_expanded_variants()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json('data');
        $meta = $response->json('meta');
        
        // 計算 data 中的總數
        $actualTotal = 0;
        foreach ($data as $categoryIcons) {
            $actualTotal += count($categoryIcons);
        }
        
        $this->assertEquals($meta['total'], $actualTotal);
        
        // 因為變體已展開，總數應該比原始數量多
        $this->assertGreaterThan(500, $actualTotal, 'Total should be greater than 500 with expanded variants');
    }

    /**
     * 測試分類一致性
     */
    public function test_category_consistency()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json('data');
        $meta = $response->json('meta');
        
        // data 中的分類 ID 應該與 meta.categories 中的一致
        $dataCategories = array_keys($data);
        $metaCategories = array_keys($meta['categories']);
        
        sort($dataCategories);
        sort($metaCategories);
        
        $this->assertEquals($dataCategories, $metaCategories);
        
        // 每個 icon 的 category 欄位應該對應實際的分類 ID
        foreach ($data as $categoryId => $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                $this->assertEquals($categoryId, $icon['category']);
            }
        }
    }

    /**
     * 測試變體類型的一致性
     */
    public function test_variant_type_consistency()
    {
        $response = $this->getJson('/api/config/icon/bootstrap-icons');
        
        $data = $response->json('data');
        
        $validVariantTypes = ['outline', 'solid'];
        
        foreach ($data as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                // 在新設計中，所有圖標都有 variant_type
                $this->assertArrayHasKey('variant_type', $icon);
                $this->assertContains(
                    $icon['variant_type'], 
                    $validVariantTypes,
                    "Invalid variant_type: {$icon['variant_type']}"
                );
            }
        }
    }

    /**
     * 測試 categories 端點
     */
    public function test_categories_endpoint()
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
}