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
}