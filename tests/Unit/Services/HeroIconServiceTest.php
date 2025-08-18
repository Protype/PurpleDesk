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
}