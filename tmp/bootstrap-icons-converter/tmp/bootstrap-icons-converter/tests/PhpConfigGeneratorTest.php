<?php

require_once __DIR__ . '/../src/PhpConfigGenerator.php';

class PhpConfigGeneratorTest
{
    private $generator;
    
    public function __construct()
    {
        $this->generator = new PhpConfigGenerator();
    }
    
    public function testGeneratesSingleIconConfig()
    {
        $iconData = [
            'name' => 'house',
            'displayName' => 'House',
            'class' => 'bi-house',
            'keywords' => ['house', 'home'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-house'],
                'solid' => ['class' => 'bi-house-fill']
            ]
        ];
        
        $result = $this->generator->generateIconConfig($iconData);
        
        // 驗證必要欄位存在
        $this->assertContains("'name' => 'house'", $result, "應包含 name 欄位");
        $this->assertContains("'displayName' => 'House'", $result, "應包含 displayName 欄位");
        $this->assertContains("'class' => 'bi-house'", $result, "應包含 class 欄位");
        $this->assertContains("'variants' => [", $result, "應包含 variants 欄位");
        
        echo "✅ 測試通過：單一圖標配置生成\n";
    }
    
    public function testGeneratesCompletePhpFile()
    {
        $icons = [
            [
                'name' => 'house',
                'displayName' => 'House',
                'class' => 'bi-house',
                'keywords' => ['house', 'home'],
                'type' => 'bootstrap',
                'category' => 'general',
                'variants' => ['outline' => ['class' => 'bi-house']]
            ],
            [
                'name' => 'alarm',
                'displayName' => 'Alarm',
                'class' => 'bi-alarm',
                'keywords' => ['alarm', 'clock'],
                'type' => 'bootstrap',
                'category' => 'general',
                'variants' => ['outline' => ['class' => 'bi-alarm']]
            ]
        ];
        
        $result = $this->generator->generateCompleteConfig($icons, 'general');
        
        // 驗證 PHP 檔案結構
        $this->assertStartsWith('<?php', $result, "應該以 PHP 標籤開始");
        $this->assertContains("'id' => 'general'", $result, "應包含分類 ID");
        $this->assertContains("'icons' => [", $result, "應包含圖標陣列");
        $this->assertEndsWith('];', trim($result), "應該以陣列結尾");
        
        // 驗證包含所有圖標
        $this->assertContains("'name' => 'house'", $result, "應包含 house 圖標");
        $this->assertContains("'name' => 'alarm'", $result, "應包含 alarm 圖標");
        
        echo "✅ 測試通過：完整 PHP 配置檔案生成\n";
    }
    
    public function testHandlesEmptyIconArray()
    {
        $result = $this->generator->generateCompleteConfig([], 'empty');
        
        $this->assertContains("'icons' => []", $result, "空陣列應正確處理");
        
        echo "✅ 測試通過：空圖標陣列處理\n";
    }
    
    public function testValidPhpSyntax()
    {
        $icons = [
            [
                'name' => 'test-icon',
                'displayName' => 'Test Icon',
                'class' => 'bi-test-icon',
                'keywords' => ['test'],
                'type' => 'bootstrap',
                'category' => 'test',
                'variants' => ['outline' => ['class' => 'bi-test-icon']]
            ]
        ];
        
        $phpCode = $this->generator->generateCompleteConfig($icons, 'test');
        
        // 寫入臨時檔案並驗證 PHP 語法
        $tempFile = './tmp/bootstrap-icons-converter/output/test_syntax.php';
        file_put_contents($tempFile, $phpCode);
        
        $output = [];
        $returnCode = 0;
        exec("php -l $tempFile 2>&1", $output, $returnCode);
        
        $this->assertEquals(0, $returnCode, "生成的 PHP 代碼應該語法正確");
        
        unlink($tempFile);
        echo "✅ 測試通過：PHP 語法驗證\n";
    }
    
    // 簡單的測試框架方法
    private function assertContains($needle, $haystack, $message = "")
    {
        if (strpos($haystack, $needle) === false) {
            throw new Exception("Assertion failed: $message. String does not contain: $needle");
        }
    }
    
    private function assertStartsWith($prefix, $string, $message = "")
    {
        if (substr($string, 0, strlen($prefix)) !== $prefix) {
            throw new Exception("Assertion failed: $message. String does not start with: $prefix");
        }
    }
    
    private function assertEndsWith($suffix, $string, $message = "")
    {
        if (substr($string, -strlen($suffix)) !== $suffix) {
            throw new Exception("Assertion failed: $message. String does not end with: $suffix");
        }
    }
    
    private function assertEquals($expected, $actual, $message = "")
    {
        if ($expected !== $actual) {
            throw new Exception("Assertion failed: $message. Expected: $expected, Actual: $actual");
        }
    }
    
    public function runAllTests()
    {
        echo "🧪 執行 PHP 配置生成器測試...\n";
        
        try {
            $this->testGeneratesSingleIconConfig();
            $this->testGeneratesCompletePhpFile();
            $this->testHandlesEmptyIconArray();
            $this->testValidPhpSyntax();
            echo "✅ 所有 PHP 配置生成器測試通過\n\n";
            return true;
        } catch (Exception $e) {
            echo "❌ 測試失敗: " . $e->getMessage() . "\n\n";
            return false;
        }
    }
}