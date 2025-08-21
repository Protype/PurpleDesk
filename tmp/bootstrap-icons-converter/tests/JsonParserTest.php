<?php

require_once __DIR__ . '/../src/JsonParser.php';

class JsonParserTest
{
    private $parser;
    private $testDataPath;
    
    public function __construct()
    {
        $this->parser = new JsonParser();
        $this->testDataPath = __DIR__ . '/../data/test-icons.json';
    }
    
    public function testCanLoadJsonFile()
    {
        // 建立測試 JSON 檔案
        $testData = [
            "alarm" => 61698,
            "alarm-fill" => 61697,
            "house" => 61800,
            "house-door" => 61801
        ];
        file_put_contents($this->testDataPath, json_encode($testData, JSON_PRETTY_PRINT));
        
        $result = $this->parser->loadFromFile($this->testDataPath);
        
        $this->assertTrue($result, "應該成功載入 JSON 檔案");
        echo "✅ 測試通過：JSON 檔案載入\n";
        
        // 清理測試檔案
        unlink($this->testDataPath);
    }
    
    public function testCanParseIconNames()
    {
        $testData = [
            "alarm" => 61698,
            "alarm-fill" => 61697,
            "house" => 61800
        ];
        file_put_contents($this->testDataPath, json_encode($testData));
        
        $this->parser->loadFromFile($this->testDataPath);
        $icons = $this->parser->getIconNames();
        
        $this->assertEquals(3, count($icons), "應該解析出 3 個圖標");
        $this->assertContains("alarm", $icons, "應該包含 alarm 圖標");
        $this->assertContains("alarm-fill", $icons, "應該包含 alarm-fill 圖標");
        
        echo "✅ 測試通過：圖標名稱解析\n";
        
        unlink($this->testDataPath);
    }
    
    public function testRejectsInvalidJsonFile()
    {
        $invalidJsonPath = __DIR__ . '/../data/invalid.json';
        file_put_contents($invalidJsonPath, '{"invalid": json}');
        
        $result = $this->parser->loadFromFile($invalidJsonPath);
        
        $this->assertFalse($result, "應該拒絕無效的 JSON 檔案");
        echo "✅ 測試通過：無效 JSON 檔案處理\n";
        
        unlink($invalidJsonPath);
    }
    
    // 簡單的測試框架
    private function assertTrue($condition, $message = "")
    {
        if (!$condition) {
            throw new Exception("Assertion failed: $message");
        }
    }
    
    private function assertFalse($condition, $message = "")
    {
        if ($condition) {
            throw new Exception("Assertion failed: $message");
        }
    }
    
    private function assertEquals($expected, $actual, $message = "")
    {
        if ($expected !== $actual) {
            throw new Exception("Assertion failed: $message. Expected: $expected, Actual: $actual");
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
        echo "🧪 執行 JSON 解析器測試...\n";
        
        try {
            $this->testCanLoadJsonFile();
            $this->testCanParseIconNames();
            $this->testRejectsInvalidJsonFile();
            echo "✅ 所有 JSON 解析器測試通過\n\n";
            return true;
        } catch (Exception $e) {
            echo "❌ 測試失敗: " . $e->getMessage() . "\n\n";
            return false;
        }
    }
}