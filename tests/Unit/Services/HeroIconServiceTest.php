<?php

namespace Tests\Unit\Services;

use App\Services\HeroIconService;
use Tests\TestCase;

class HeroIconServiceTest extends TestCase
{
    private HeroIconService $heroIconService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->heroIconService = new HeroIconService();
    }
    
    /**
     * 測試取得所有 HeroIcons
     */
    public function test_get_all_heroicons()
    {
        $result = $this->heroIconService->getAllHeroIcons();
        
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
     * 測試 HeroIcons 資料數量
     */
    public function test_heroicons_count()
    {
        $result = $this->heroIconService->getAllHeroIcons();
        
        // 應該有 230 個圖標
        $this->assertEquals(230, $result['meta']['total']);
        $this->assertCount(230, $result['data']);
    }
    
    /**
     * 測試單個圖標格式
     */
    public function test_single_icon_format()
    {
        $result = $this->heroIconService->getAllHeroIcons();
        
        $firstIcon = $result['data'][0];
        
        // 驗證必要欄位
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
     * 測試關鍵字生成
     */
    public function test_keywords_generation()
    {
        $result = $this->heroIconService->getAllHeroIcons();
        
        // 找到 Academic Cap 圖標測試關鍵字
        $academicCapIcon = null;
        foreach ($result['data'] as $icon) {
            if ($icon['name'] === 'Academic Cap') {
                $academicCapIcon = $icon;
                break;
            }
        }
        
        $this->assertNotNull($academicCapIcon);
        $this->assertContains('education', $academicCapIcon['keywords']);
        $this->assertContains('graduation', $academicCapIcon['keywords']);
        $this->assertContains('school', $academicCapIcon['keywords']);
    }
    
    /**
     * 測試分類設定
     */
    public function test_categories_setup()
    {
        $result = $this->heroIconService->getAllHeroIcons();
        
        $categories = $result['meta']['categories'];
        
        // 驗證分類陣列不為空
        $this->assertNotEmpty($categories);
        
        // 驗證至少包含 general 分類
        $this->assertContains('general', $categories);
        
        // 驗證所有圖標都有有效的分類
        foreach ($result['data'] as $icon) {
            $this->assertContains($icon['category'], $categories);
        }
    }
    
    /**
     * 測試緩存功能
     */
    public function test_caching()
    {
        // 清除緩存
        $this->heroIconService->clearCache();
        
        // 第一次調用
        $firstResult = $this->heroIconService->getAllHeroIcons();
        
        // 第二次調用（應該使用緩存）
        $secondResult = $this->heroIconService->getAllHeroIcons();
        
        // 結果應該相同
        $this->assertEquals($firstResult, $secondResult);
    }
    
    /**
     * 測試清除緩存
     */
    public function test_clear_cache()
    {
        // 先獲取資料建立緩存
        $this->heroIconService->getAllHeroIcons();
        
        // 清除緩存應該不會拋出錯誤
        $this->heroIconService->clearCache();
        
        // 再次獲取資料應該正常
        $result = $this->heroIconService->getAllHeroIcons();
        $this->assertIsArray($result);
        $this->assertEquals(230, $result['meta']['total']);
    }
    
    // ===== 變體功能測試 =====
    
    /**
     * 測試取得變體映射資訊
     */
    public function test_get_variant_mapping()
    {
        $mapping = $this->heroIconService->getVariantMapping();
        
        $this->assertIsArray($mapping);
        $this->assertArrayHasKey('outline', $mapping);
        $this->assertArrayHasKey('solid', $mapping);
        
        // 驗證 outline 變體配置
        $this->assertArrayHasKey('path', $mapping['outline']);
        $this->assertArrayHasKey('suffix', $mapping['outline']);
        $this->assertArrayHasKey('description', $mapping['outline']);
        $this->assertEquals('@heroicons/vue/outline', $mapping['outline']['path']);
        
        // 驗證 solid 變體配置
        $this->assertArrayHasKey('path', $mapping['solid']);
        $this->assertArrayHasKey('suffix', $mapping['solid']);
        $this->assertArrayHasKey('description', $mapping['solid']);
        $this->assertEquals('@heroicons/vue/solid', $mapping['solid']['path']);
    }
    
    /**
     * 測試取得支援的變體類型
     */
    public function test_get_supported_variants()
    {
        $variants = $this->heroIconService->getSupportedVariants();
        
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
        $result = $this->heroIconService->getIconsByStyle('outline');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        
        // 驗證資料總數
        $this->assertEquals(230, $result['meta']['total']);
        $this->assertEquals('outline', $result['meta']['currentStyle']);
        $this->assertEquals('線條樣式', $result['meta']['description']);
        
        // 驗證每個圖標都有 currentStyle 屬性
        foreach ($result['data'] as $icon) {
            $this->assertArrayHasKey('currentStyle', $icon);
            $this->assertEquals('outline', $icon['currentStyle']);
        }
    }
    
    /**
     * 測試根據樣式取得圖標 - solid 樣式
     */
    public function test_get_icons_by_style_solid()
    {
        $result = $this->heroIconService->getIconsByStyle('solid');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        
        // 驗證資料總數
        $this->assertEquals(230, $result['meta']['total']);
        $this->assertEquals('solid', $result['meta']['currentStyle']);
        $this->assertEquals('實心樣式', $result['meta']['description']);
        
        // 驗證每個圖標都有 currentStyle 屬性
        foreach ($result['data'] as $icon) {
            $this->assertArrayHasKey('currentStyle', $icon);
            $this->assertEquals('solid', $icon['currentStyle']);
        }
    }
    
    /**
     * 測試不支援的樣式會拋出例外
     */
    public function test_get_icons_by_style_invalid_style()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported style: invalid');
        
        $this->heroIconService->getIconsByStyle('invalid');
    }
    
    /**
     * 測試取得單一圖標的變體資訊
     */
    public function test_get_icon_variants()
    {
        // 測試有效的圖標組件名稱
        $variants = $this->heroIconService->getIconVariants('AcademicCapIcon');
        
        $this->assertIsArray($variants);
        $this->assertArrayHasKey('outline', $variants);
        $this->assertArrayHasKey('solid', $variants);
        
        // 驗證 outline 變體
        $this->assertArrayHasKey('component', $variants['outline']);
        $this->assertArrayHasKey('path', $variants['outline']);
        $this->assertArrayHasKey('description', $variants['outline']);
        $this->assertEquals('AcademicCapIcon', $variants['outline']['component']);
        $this->assertEquals('@heroicons/vue/outline', $variants['outline']['path']);
        
        // 驗證 solid 變體
        $this->assertArrayHasKey('component', $variants['solid']);
        $this->assertArrayHasKey('path', $variants['solid']);
        $this->assertArrayHasKey('description', $variants['solid']);
        $this->assertEquals('AcademicCapIcon', $variants['solid']['component']);
        $this->assertEquals('@heroicons/vue/solid', $variants['solid']['path']);
    }
    
    /**
     * 測試不存在的圖標組件返回 null
     */
    public function test_get_icon_variants_non_existent()
    {
        $variants = $this->heroIconService->getIconVariants('NonExistentIcon');
        
        $this->assertNull($variants);
    }
    
    /**
     * 測試檢查圖標是否支援特定樣式
     */
    public function test_has_style_variant()
    {
        // 測試有效的圖標和樣式
        $this->assertTrue($this->heroIconService->hasStyleVariant('AcademicCapIcon', 'outline'));
        $this->assertTrue($this->heroIconService->hasStyleVariant('AcademicCapIcon', 'solid'));
        
        // 測試無效的樣式
        $this->assertFalse($this->heroIconService->hasStyleVariant('AcademicCapIcon', 'invalid'));
        
        // 測試不存在的圖標
        $this->assertFalse($this->heroIconService->hasStyleVariant('NonExistentIcon', 'outline'));
    }
    
    /**
     * 測試變體映射的一致性
     */
    public function test_variant_mapping_consistency()
    {
        $mapping = $this->heroIconService->getVariantMapping();
        $supportedVariants = $this->heroIconService->getSupportedVariants();
        
        // 變體映射的鍵應該與支援的變體一致
        $this->assertEquals(array_keys($mapping), $supportedVariants);
        
        // 每個變體都應該有必要的配置
        foreach ($mapping as $style => $config) {
            $this->assertArrayHasKey('path', $config);
            $this->assertArrayHasKey('suffix', $config);
            $this->assertArrayHasKey('description', $config);
            $this->assertIsString($config['path']);
            $this->assertIsString($config['suffix']);
            $this->assertIsString($config['description']);
        }
    }
    
    /**
     * 測試所有圖標都支援所有樣式
     */
    public function test_all_icons_support_all_styles()
    {
        $allIcons = $this->heroIconService->getAllHeroIcons();
        $supportedVariants = $this->heroIconService->getSupportedVariants();
        
        // 取前 10 個圖標進行測試（避免測試時間過長）
        $testIcons = array_slice($allIcons['data'], 0, 10);
        
        foreach ($testIcons as $icon) {
            foreach ($supportedVariants as $style) {
                $this->assertTrue(
                    $this->heroIconService->hasStyleVariant($icon['component'], $style),
                    "Icon {$icon['component']} should support style {$style}"
                );
            }
        }
    }
}