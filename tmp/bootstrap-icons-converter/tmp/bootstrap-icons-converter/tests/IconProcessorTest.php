<?php

require_once __DIR__ . '/../src/IconProcessor.php';

class IconProcessorTest
{
    private $processor;
    
    public function __construct()
    {
        $this->processor = new IconProcessor();
    }
    
    public function testConvertsIconNameToDisplayName()
    {
        $testCases = [
            'house' => 'House',
            'house-door' => 'House Door',
            'arrow-90deg-up' => 'Arrow 90deg Up',
            'sort-numeric-up-alt' => 'Sort Numeric Up Alt',
            'alarm-fill' => 'Alarm Fill'
        ];
        
        foreach ($testCases as $input => $expected) {
            $result = $this->processor->iconNameToDisplayName($input);
            $this->assertEquals($expected, $result, "轉換 '$input' 應得到 '$expected'");
        }
        
        echo "✅ 測試通過：圖標名稱轉換為顯示名稱\n";
    }
    
    public function testGeneratesKeywordsFromIconName()
    {
        $testCases = [
            'house' => ['house'],
            'house-door' => ['house', 'door'],
            'arrow-90deg-up' => ['arrow', '90deg', 'up'],
            'sort-numeric-up-alt' => ['sort', 'numeric', 'up', 'alt'],
            'alarm-fill' => ['alarm', 'fill']
        ];
        
        foreach ($testCases as $input => $expected) {
            $result = $this->processor->generateKeywords($input);
            $this->assertEquals($expected, $result, "生成 '$input' 的關鍵字應為 " . implode(', ', $expected));
        }
        
        echo "✅ 測試通過：關鍵字生成\n";
    }
    
    public function testDetectsIconVariants()
    {
        $fillIcons = ['alarm-fill', 'house-fill', 'cart-fill'];
        $outlineIcons = ['alarm', 'house', 'cart'];
        
        foreach ($fillIcons as $icon) {
            $variants = $this->processor->detectVariants($icon);
            $this->assertArrayHasKey('solid', $variants, "$icon 應該有 solid 變體");
            $this->assertArrayHasKey('outline', $variants, "$icon 應該有對應的 outline 變體");
        }
        
        foreach ($outlineIcons as $icon) {
            $variants = $this->processor->detectVariants($icon);
            $this->assertArrayHasKey('outline', $variants, "$icon 應該有 outline 變體");
        }
        
        echo "✅ 測試通過：圖標變體檢測\n";
    }
    
    public function testProcessesCompleteIconData()
    {
        $iconName = 'house-door';
        $result = $this->processor->processIcon($iconName);
        
        // 驗證必要欄位
        $this->assertEquals('house-door', $result['name'], "name 欄位應正確");
        $this->assertEquals('House Door', $result['displayName'], "displayName 應正確");
        $this->assertEquals('bi-house-door', $result['class'], "class 應包含 bi- 前綴");
        $this->assertEquals(['house', 'door'], $result['keywords'], "keywords 應正確");
        $this->assertEquals('bootstrap', $result['type'], "type 應為 bootstrap");
        $this->assertArrayHasKey('variants', $result, "應包含 variants");
        
        echo "✅ 測試通過：完整圖標資料處理\n";
    }
    
    public function testHandlesFillIconCorrectly()
    {
        $result = $this->processor->processIcon('alarm-fill');
        
        $this->assertEquals('alarm-fill', $result['name'], "fill 圖標名稱應保持原樣");
        $this->assertEquals('Alarm Fill', $result['displayName'], "displayName 應包含 Fill");
        $this->assertEquals(['alarm', 'fill'], $result['keywords'], "關鍵字應包含 fill");
        
        // 驗證變體
        $variants = $result['variants'];
        $this->assertArrayHasKey('solid', $variants, "fill 圖標應有 solid 變體");
        $this->assertArrayHasKey('outline', $variants, "應有對應的 outline 變體");
        $this->assertEquals('bi-alarm-fill', $variants['solid']['class'], "solid 變體類別應正確");
        $this->assertEquals('bi-alarm', $variants['outline']['class'], "outline 變體類別應正確");
        
        echo "✅ 測試通過：Fill 圖標處理\n";
    }
    
    public function testFiltersIconsByCategory()
    {
        $allIcons = [
            'house', 'house-door', 'search', // general
            'file-text', 'folder', // files
            'person', 'people', // people
        ];
        
        // 測試 general 分類
        $generalIcons = $this->processor->filterIconsByCategory($allIcons, 'general');
        
        $this->assertContains('house', $generalIcons, "general 應包含基本圖標");
        $this->assertContains('search', $generalIcons, "general 應包含搜尋圖標");
        
        echo "✅ 測試通過：圖標分類篩選\n";
    }
    
    // 測試框架方法
    private function assertEquals($expected, $actual, $message = "")
    {
        if ($expected !== $actual) {
            throw new Exception("Assertion failed: $message. Expected: " . var_export($expected, true) . ", Actual: " . var_export($actual, true));
        }
    }
    
    private function assertArrayHasKey($key, $array, $message = "")
    {
        if (!array_key_exists($key, $array)) {
            throw new Exception("Assertion failed: $message. Array does not have key: $key");
        }
    }
    
    private function assertContains($needle, $haystack, $message = "")
    {
        if (!in_array($needle, $haystack)) {
            throw new Exception("Assertion failed: $message. Array does not contain: $needle");
        }
    }
    
    public function runAllTests()
    {
        echo "🧪 執行圖標處理器測試...\n";
        
        try {
            $this->testConvertsIconNameToDisplayName();
            $this->testGeneratesKeywordsFromIconName();
            $this->testDetectsIconVariants();
            $this->testProcessesCompleteIconData();
            $this->testHandlesFillIconCorrectly();
            $this->testFiltersIconsByCategory();
            echo "✅ 所有圖標處理器測試通過\n\n";
            return true;
        } catch (Exception $e) {
            echo "❌ 測試失敗: " . $e->getMessage() . "\n\n";
            return false;
        }
    }
}