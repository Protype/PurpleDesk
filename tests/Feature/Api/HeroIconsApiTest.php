<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeroIconsApiTest extends TestCase
{
    /**
     * 測試 HeroIcons API 回傳正確的基本結構
     */
    public function test_heroicons_api_returns_correct_structure()
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
    }

    /**
     * 測試 data 按分類分組
     */
    public function test_heroicons_data_is_grouped_by_category()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
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
        $this->assertEquals('heroicons', $icon['type']);
        $this->assertIsArray($icon['keywords']);
        $this->assertIsString($icon['category']);
        $this->assertIsBool($icon['has_variants']);
    }

    /**
     * 測試有變體的 HeroIcon 具有 variant_type
     */
    public function test_heroicon_with_variants_has_variant_type()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json('data');
        
        // 尋找有變體的 icon
        $iconWithVariants = null;
        
        foreach ($data as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                if ($icon['has_variants'] === true) {
                    $iconWithVariants = $icon;
                    break 2;
                }
            }
        }
        
        $this->assertNotNull($iconWithVariants, 'Should find at least one icon with variants');
        
        // 檢查 variant_type 欄位
        $this->assertArrayHasKey('variant_type', $iconWithVariants);
        $this->assertIsString($iconWithVariants['variant_type']);
        $this->assertContains($iconWithVariants['variant_type'], ['outline', 'solid']);
    }

    /**
     * 測試沒有變體的 HeroIcon 不包含 variant_type
     */
    public function test_heroicon_without_variants_has_no_variant_type()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
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
        
        // 如果找到沒有變體的 icon，檢查不應該有 variant_type
        if ($iconWithoutVariants !== null) {
            $this->assertArrayNotHasKey('variant_type', $iconWithoutVariants);
        } else {
            // 如果所有 HeroIcon 都有變體，那也是正常的
            $this->markTestSkipped('All HeroIcons have variants, which is expected');
        }
    }

    /**
     * 測試 outline 和 solid 變體是分開的項目
     */
    public function test_outline_and_solid_variants_are_separate_items()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json('data');
        
        // 收集所有 icon 的基本名稱（移除 -outline, -solid 後綴）
        $iconGroups = [];
        
        foreach ($data as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                if ($icon['has_variants']) {
                    // 從 id 中提取基本名稱
                    $baseName = preg_replace('/-outline$|-solid$/', '', $icon['id']);
                    
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
                
                // 檢查是否有 outline 和 solid 變體
                $variantTypes = array_column($variants, 'variant_type');
                $this->assertContains('outline', $variantTypes, "Icon group {$baseName} should have outline variant");
                $this->assertContains('solid', $variantTypes, "Icon group {$baseName} should have solid variant");
            }
        }
        
        $this->assertTrue($hasMultipleVariants, 'Should have at least one icon with multiple variants');
    }

    /**
     * 測試每個變體都是完整的 icon
     */
    public function test_each_variant_is_complete_icon()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json('data');
        
        foreach ($data as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                if ($icon['has_variants']) {
                    // 每個變體都應該是完整的 icon，具有所有必要欄位
                    $this->assertArrayHasKey('id', $icon);
                    $this->assertArrayHasKey('name', $icon);
                    $this->assertArrayHasKey('value', $icon);
                    $this->assertArrayHasKey('variant_type', $icon);
                    
                    // value 應該對應變體類型
                    if ($icon['variant_type'] === 'solid') {
                        // solid 變體的 component 名稱應該是有效的
                        $this->assertIsString($icon['value']);
                        $this->assertNotEmpty($icon['value']);
                    }
                }
            }
        }
    }

    /**
     * 測試變體類型正確
     */
    public function test_variant_types_are_correct()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json('data');
        
        $validVariantTypes = ['outline', 'solid'];
        
        foreach ($data as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                if ($icon['has_variants']) {
                    $this->assertContains(
                        $icon['variant_type'], 
                        $validVariantTypes,
                        "Invalid variant_type: {$icon['variant_type']}"
                    );
                }
            }
        }
    }

    /**
     * 測試 meta 包含正確的 type
     */
    public function test_meta_contains_type_heroicons()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $meta = $response->json('meta');
        
        $this->assertArrayHasKey('type', $meta);
        $this->assertEquals('heroicons', $meta['type']);
    }

    /**
     * 測試總數計算正確（包含展開的變體）
     */
    public function test_total_includes_expanded_variants()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
        $data = $response->json('data');
        $meta = $response->json('meta');
        
        // 計算 data 中的總數
        $actualTotal = 0;
        foreach ($data as $categoryIcons) {
            $actualTotal += count($categoryIcons);
        }
        
        $this->assertEquals($meta['total'], $actualTotal);
        
        // 因為每個 HeroIcon 都有 outline 和 solid 變體，
        // 所以總數應該是原始 HeroIcon 數量的兩倍左右
        $this->assertGreaterThan(400, $actualTotal, 'Total should be around 460 (230 * 2 variants)');
    }

    /**
     * 測試分類一致性
     */
    public function test_category_consistency()
    {
        $response = $this->getJson('/api/config/icon/heroicons');
        
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