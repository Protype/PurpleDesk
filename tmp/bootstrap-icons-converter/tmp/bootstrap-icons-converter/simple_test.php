<?php

echo "🧪 Bootstrap Icons 轉換器簡化測試\n";
echo "====================================\n\n";

// 測試 JSON 解析器
require_once __DIR__ . '/src/JsonParser.php';

try {
    echo "1. 測試 JSON 解析器...\n";
    $parser = new JsonParser();
    $success = $parser->loadFromFile(__DIR__ . '/data/bootstrap-icons.json');
    
    if ($success) {
        $iconCount = $parser->getIconCount();
        echo "   ✅ JSON 檔案載入成功，共 $iconCount 個圖標\n";
        
        // 測試幾個圖標
        $testIcons = ['alarm', 'house', 'search'];
        foreach ($testIcons as $icon) {
            if ($parser->hasIcon($icon)) {
                echo "   ✅ 找到圖標: $icon\n";
            }
        }
    } else {
        echo "   ❌ JSON 檔案載入失敗\n";
    }
} catch (Exception $e) {
    echo "   ❌ JSON 解析器測試失敗: " . $e->getMessage() . "\n";
}

echo "\n";

// 測試圖標處理器
require_once __DIR__ . '/src/IconProcessor.php';

try {
    echo "2. 測試圖標處理器...\n";
    $processor = new IconProcessor();
    
    $testIcon = $processor->processIcon('house-door');
    echo "   ✅ 處理圖標 'house-door':\n";
    echo "      - 顯示名稱: {$testIcon['displayName']}\n";
    echo "      - 關鍵字: " . implode(', ', $testIcon['keywords']) . "\n";
    echo "      - 變體數量: " . count($testIcon['variants']) . "\n";
    
} catch (Exception $e) {
    echo "   ❌ 圖標處理器測試失敗: " . $e->getMessage() . "\n";
}

echo "\n";

// 測試 PHP 配置生成器
require_once __DIR__ . '/src/PhpConfigGenerator.php';

try {
    echo "3. 測試 PHP 配置生成器...\n";
    $generator = new PhpConfigGenerator();
    $processor = new IconProcessor();
    
    // 處理幾個測試圖標
    $testIcons = ['house', 'alarm', 'search'];
    $processedIcons = [];
    foreach ($testIcons as $iconName) {
        $processedIcons[] = $processor->processIcon($iconName);
    }
    
    $phpConfig = $generator->generateCompleteConfig($processedIcons, 'test');
    
    // 驗證 PHP 語法
    $tempFile = __DIR__ . '/output/test_config.php';
    file_put_contents($tempFile, $phpConfig);
    
    $output = [];
    $returnCode = 0;
    exec("php -l $tempFile 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "   ✅ PHP 配置檔案生成成功，語法正確\n";
        echo "   ✅ 配置檔案大小: " . strlen($phpConfig) . " 字元\n";
    } else {
        echo "   ❌ PHP 語法錯誤\n";
        echo "   " . implode("\n   ", $output) . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ PHP 配置生成器測試失敗: " . $e->getMessage() . "\n";
}

echo "\n🎉 簡化測試完成！\n";