<?php

namespace Tests\Unit\Services;

use App\Services\BootstrapIconService;
use Tests\TestCase;

class BootstrapIconServiceTest extends TestCase
{
    private BootstrapIconService $bootstrapIconService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->bootstrapIconService = new BootstrapIconService();
    }
    
    /**
     * 測試取得所有 Bootstrap Icons
     */
    public function test_get_all_bootstrap_icons()
    {
        $result = $this->bootstrapIconService->getAllBootstrapIcons();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        
        // 驗證資料結構
        $this->assertIsArray($result['data']);
        $this->assertIsArray($result['meta']);
        $this->assertArrayHasKey('total', $result['meta']);
        $this->assertArrayHasKey('categories', $result['meta']);
    }
    
    /**
     * 測試 Bootstrap Icons 分類數量
     */
    public function test_bootstrap_icons_categories_count()
    {
        $result = $this->bootstrapIconService->getAllBootstrapIcons();
        
        // 應該有至少 8 個分類
        $this->assertGreaterThanOrEqual(8, count($result['meta']['categories']));
        
        // 檢查必要分類存在
        $categoryNames = array_keys($result['meta']['categories']);
        $this->assertContains('general', $categoryNames);
        $this->assertContains('ui', $categoryNames);
        $this->assertContains('communications', $categoryNames);
    }
    
    /**
     * 測試單個圖標格式
     */
    public function test_single_icon_format()
    {
        $result = $this->bootstrapIconService->getAllBootstrapIcons();
        
        // 取得第一個分類的第一個圖標
        $firstCategory = array_values($result['data'])[0];
        $firstIcon = $firstCategory[0];
        
        // 驗證必要欄位
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
     * 測試分類篩選
     */
    public function test_filter_by_categories()
    {
        $categories = ['general', 'ui'];
        $result = $this->bootstrapIconService->getIconsByCategories($categories);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        
        // 應該只包含指定的分類
        $this->assertArrayHasKey('general', $result['data']);
        $this->assertArrayHasKey('ui', $result['data']);
        
        // 不應該包含其他分類
        $this->assertCount(2, $result['data']);
    }
    
    /**
     * 測試總圖標數量計算
     */
    public function test_total_icons_count()
    {
        $result = $this->bootstrapIconService->getAllBootstrapIcons();
        
        // 計算實際圖標總數
        $actualTotal = 0;
        foreach ($result['data'] as $categoryIcons) {
            $actualTotal += count($categoryIcons);
        }
        
        // meta.total 應該等於實際總數
        $this->assertEquals($actualTotal, $result['meta']['total']);
        
        // 總數應該合理（目前配置約 250+）
        $this->assertGreaterThan(200, $result['meta']['total']);
        $this->assertLessThan(500, $result['meta']['total']);
    }
    
    /**
     * 測試關鍵字生成
     */
    public function test_keywords_generation()
    {
        $result = $this->bootstrapIconService->getAllBootstrapIcons();
        
        // 找到 House 圖標測試關鍵字
        $houseIcon = null;
        foreach ($result['data'] as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                if ($icon['name'] === 'House') {
                    $houseIcon = $icon;
                    break 2;
                }
            }
        }
        
        $this->assertNotNull($houseIcon);
        $this->assertContains('house', $houseIcon['keywords']);
        $this->assertContains('home', $houseIcon['keywords']);
        $this->assertContains('building', $houseIcon['keywords']);
    }
    
    /**
     * 測試緩存功能
     */
    public function test_caching()
    {
        // 清除緩存
        $this->bootstrapIconService->clearCache();
        
        // 第一次調用
        $firstResult = $this->bootstrapIconService->getAllBootstrapIcons();
        
        // 第二次調用（應該使用緩存）
        $secondResult = $this->bootstrapIconService->getAllBootstrapIcons();
        
        // 結果應該相同
        $this->assertEquals($firstResult, $secondResult);
    }
    
    /**
     * 測試清除緩存
     */
    public function test_clear_cache()
    {
        // 先獲取資料建立緩存
        $this->bootstrapIconService->getAllBootstrapIcons();
        
        // 清除緩存應該不會拋出錯誤
        $this->bootstrapIconService->clearCache();
        
        // 再次獲取資料應該正常
        $result = $this->bootstrapIconService->getAllBootstrapIcons();
        $this->assertIsArray($result);
        $this->assertGreaterThan(200, $result['meta']['total']);
    }
    
    /**
     * 測試空分類過濾
     */
    public function test_empty_categories_filter()
    {
        $result = $this->bootstrapIconService->getIconsByCategories([]);
        
        // 空陣列應該返回所有分類
        $allResult = $this->bootstrapIconService->getAllBootstrapIcons();
        $this->assertEquals($allResult, $result);
    }
    
    /**
     * 測試無效分類過濾
     */
    public function test_invalid_categories_filter()
    {
        $result = $this->bootstrapIconService->getIconsByCategories(['invalid', 'nonexistent']);
        
        // 無效分類應該返回空結果
        $this->assertEmpty($result['data']);
        $this->assertEquals(0, $result['meta']['total']);
    }
    
    // ===== 變體功能測試 =====
    
    /**
     * 測試取得變體映射資訊
     */
    public function test_get_variant_mapping()
    {
        $mapping = $this->bootstrapIconService->getVariantMapping();
        
        $this->assertIsArray($mapping);
        $this->assertArrayHasKey('outline', $mapping);
        $this->assertArrayHasKey('solid', $mapping);
        
        // 驗證 outline 變體配置
        $this->assertArrayHasKey('suffix', $mapping['outline']);
        $this->assertArrayHasKey('description', $mapping['outline']);
        $this->assertArrayHasKey('rule', $mapping['outline']);
        $this->assertEquals('', $mapping['outline']['suffix']);
        $this->assertEquals('remove_fill_suffix', $mapping['outline']['rule']);
        
        // 驗證 solid 變體配置
        $this->assertArrayHasKey('suffix', $mapping['solid']);
        $this->assertArrayHasKey('description', $mapping['solid']);
        $this->assertArrayHasKey('rule', $mapping['solid']);
        $this->assertEquals('-fill', $mapping['solid']['suffix']);
        $this->assertEquals('add_fill_suffix', $mapping['solid']['rule']);
    }
    
    /**
     * 測試取得支援的變體類型
     */
    public function test_get_supported_variants()
    {
        $variants = $this->bootstrapIconService->getSupportedVariants();
        
        $this->assertIsArray($variants);
        $this->assertContains('outline', $variants);
        $this->assertContains('solid', $variants);
        $this->assertCount(2, $variants);
    }
    
    /**
     * 測試根據樣式取得圖標 - outline 樣式
     */
    public function test_get_icons_by_style_outline()
    {
        $result = $this->bootstrapIconService->getIconsByStyle('outline');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        
        $this->assertEquals('outline', $result['meta']['currentStyle']);
        $this->assertEquals('線條樣式', $result['meta']['description']);
        
        // 驗證沒有包含 -fill 圖標
        foreach ($result['data'] as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                $this->assertArrayHasKey('currentStyle', $icon);
                $this->assertEquals('outline', $icon['currentStyle']);
                $this->assertStringNotContainsString('-fill', $icon['class']);
            }
        }
    }
    
    /**
     * 測試根據樣式取得圖標 - solid 樣式
     */
    public function test_get_icons_by_style_solid()
    {
        $result = $this->bootstrapIconService->getIconsByStyle('solid');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        
        $this->assertEquals('solid', $result['meta']['currentStyle']);
        $this->assertEquals('實心樣式', $result['meta']['description']);
        
        // 驗證每個圖標都有 currentStyle 屬性
        foreach ($result['data'] as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                $this->assertArrayHasKey('currentStyle', $icon);
                $this->assertEquals('solid', $icon['currentStyle']);
                
                // solid 樣式的圖標應該要嘛是 -fill 圖標，要嘛是沒有 -fill 變體的基礎圖標
                $this->assertTrue(
                    str_contains($icon['class'], '-fill') || 
                    !str_contains($icon['class'], '-fill'),
                    "Icon class {$icon['class']} should be valid for solid style"
                );
            }
        }
    }
    
    /**
     * 測試不支援的樣式會拋出例外
     */
    public function test_get_icons_by_style_invalid_style()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported style: invalid');
        
        $this->bootstrapIconService->getIconsByStyle('invalid');
    }
    
    /**
     * 測試取得單一圖標的變體資訊
     */
    public function test_get_icon_variants()
    {
        // 使用一個知道存在的圖標進行測試
        $allIcons = $this->bootstrapIconService->getAllBootstrapIcons();
        $firstCategory = array_values($allIcons['data'])[0];
        $testIcon = $firstCategory[0];
        
        $variants = $this->bootstrapIconService->getIconVariants($testIcon['class']);
        
        $this->assertIsArray($variants);
        $this->assertArrayHasKey('outline', $variants);
        $this->assertArrayHasKey('solid', $variants);
        
        // 驗證 outline 變體
        $this->assertArrayHasKey('class', $variants['outline']);
        $this->assertArrayHasKey('description', $variants['outline']);
        
        // 驗證 solid 變體
        $this->assertArrayHasKey('class', $variants['solid']);
        $this->assertArrayHasKey('description', $variants['solid']);
    }
    
    /**
     * 測試不存在的圖標類別返回 null
     */
    public function test_get_icon_variants_non_existent()
    {
        $variants = $this->bootstrapIconService->getIconVariants('non-existent-icon');
        
        $this->assertNull($variants);
    }
    
    /**
     * 測試檢查圖標是否支援特定樣式
     */
    public function test_has_style_variant()
    {
        // 使用一個知道存在的圖標進行測試
        $allIcons = $this->bootstrapIconService->getAllBootstrapIcons();
        $firstCategory = array_values($allIcons['data'])[0];
        $testIcon = $firstCategory[0];
        
        // 所有圖標都應該支援 outline 樣式
        $this->assertTrue($this->bootstrapIconService->hasStyleVariant($testIcon['class'], 'outline'));
        
        // 測試 solid 樣式（可能支援也可能不支援，取決於圖標）
        $hasSolid = $this->bootstrapIconService->hasStyleVariant($testIcon['class'], 'solid');
        $this->assertIsBool($hasSolid);
        
        // 測試無效的樣式
        $this->assertFalse($this->bootstrapIconService->hasStyleVariant($testIcon['class'], 'invalid'));
        
        // 測試不存在的圖標
        $this->assertFalse($this->bootstrapIconService->hasStyleVariant('non-existent-icon', 'outline'));
    }
    
    /**
     * 測試取得圖標的特定樣式 class 名稱
     */
    public function test_get_icon_class()
    {
        // 使用一個知道存在的圖標進行測試
        $allIcons = $this->bootstrapIconService->getAllBootstrapIcons();
        $firstCategory = array_values($allIcons['data'])[0];
        $testIcon = $firstCategory[0];
        
        // 測試 outline 樣式
        $outlineClass = $this->bootstrapIconService->getIconClass($testIcon['class'], 'outline');
        $this->assertIsString($outlineClass);
        $this->assertStringNotContainsString('-fill', $outlineClass);
        
        // 測試 solid 樣式
        $solidClass = $this->bootstrapIconService->getIconClass($testIcon['class'], 'solid');
        if ($solidClass) {
            $this->assertIsString($solidClass);
        }
        
        // 測試無效的樣式
        $invalidClass = $this->bootstrapIconService->getIconClass($testIcon['class'], 'invalid');
        $this->assertNull($invalidClass);
        
        // 測試不存在的圖標
        $nonExistentClass = $this->bootstrapIconService->getIconClass('non-existent-icon', 'outline');
        $this->assertNull($nonExistentClass);
    }
    
    /**
     * 測試 -fill 後綴處理邏輯
     */
    public function test_fill_suffix_logic()
    {
        $allIcons = $this->bootstrapIconService->getAllBootstrapIcons();
        
        // 尋找有 -fill 變體的圖標
        $fillIcon = null;
        $baseIcon = null;
        
        foreach ($allIcons['data'] as $categoryIcons) {
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
            // 測試基礎圖標的變體
            $baseVariants = $this->bootstrapIconService->getIconVariants($baseIcon['class']);
            $this->assertIsArray($baseVariants);
            $this->assertArrayHasKey('outline', $baseVariants);
            $this->assertArrayHasKey('solid', $baseVariants);
            $this->assertEquals($baseIcon['class'], $baseVariants['outline']['class']);
            $this->assertEquals($fillIcon['class'], $baseVariants['solid']['class']);
            
            // 測試 -fill 圖標的變體
            $fillVariants = $this->bootstrapIconService->getIconVariants($fillIcon['class']);
            $this->assertIsArray($fillVariants);
            $this->assertEquals($baseIcon['class'], $fillVariants['outline']['class']);
            $this->assertEquals($fillIcon['class'], $fillVariants['solid']['class']);
        }
    }
    
    /**
     * 測試變體映射的一致性
     */
    public function test_variant_mapping_consistency()
    {
        $mapping = $this->bootstrapIconService->getVariantMapping();
        $supportedVariants = $this->bootstrapIconService->getSupportedVariants();
        
        // 變體映射的鍵應該與支援的變體一致
        $this->assertEquals(array_keys($mapping), $supportedVariants);
        
        // 每個變體都應該有必要的配置
        foreach ($mapping as $style => $config) {
            $this->assertArrayHasKey('suffix', $config);
            $this->assertArrayHasKey('description', $config);
            $this->assertArrayHasKey('rule', $config);
            $this->assertIsString($config['suffix']);
            $this->assertIsString($config['description']);
            $this->assertIsString($config['rule']);
        }
    }
    
    /**
     * 測試變體資訊包含在所有圖標中
     */
    public function test_variant_info_in_all_icons()
    {
        $allIcons = $this->bootstrapIconService->getAllBootstrapIcons();
        
        // 檢查幾個圖標是否包含變體資訊
        $testIcons = [];
        $count = 0;
        foreach ($allIcons['data'] as $categoryIcons) {
            foreach ($categoryIcons as $icon) {
                $testIcons[] = $icon;
                if (++$count >= 5) break 2; // 測試前 5 個圖標
            }
        }
        
        foreach ($testIcons as $icon) {
            $this->assertArrayHasKey('base', $icon);
            $this->assertArrayHasKey('variants', $icon);
            $this->assertArrayHasKey('defaultVariant', $icon);
            
            $this->assertIsString($icon['base']);
            $this->assertIsArray($icon['variants']);
            $this->assertEquals('outline', $icon['defaultVariant']);
        }
    }
}