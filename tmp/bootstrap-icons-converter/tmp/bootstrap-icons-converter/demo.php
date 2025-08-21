<?php

/**
 * Bootstrap Icons 轉換器 - 完整演示
 */

// 載入所有組件
require_once __DIR__ . '/src/JsonParser.php';
require_once __DIR__ . '/src/IconProcessor.php';  
require_once __DIR__ . '/src/PhpConfigGenerator.php';

echo "🔄 Bootstrap Icons 轉換器 - 完整演示\n";
echo "===================================\n\n";

try {
    // 1. 載入官方 JSON
    echo "1. 載入官方 JSON 檔案...\n";
    $parser = new JsonParser();
    $success = $parser->loadFromFile(__DIR__ . '/data/bootstrap-icons.json');
    
    if (!$success) {
        throw new Exception("無法載入 JSON 檔案");
    }
    
    $allIcons = $parser->getIconNames();
    echo "   ✅ 成功載入 " . count($allIcons) . " 個圖標\n\n";
    
    // 2. 演示圖標處理
    echo "2. 處理前 10 個圖標範例...\n";
    $processor = new IconProcessor();
    
    $testIcons = array_slice($allIcons, 0, 10);
    $processedIcons = [];
    
    foreach ($testIcons as $iconName) {
        $processed = $processor->processIcon($iconName, 'general');
        $processedIcons[] = $processed;
        echo "   - $iconName → {$processed['displayName']} (" . count($processed['variants']) . " 變體)\n";
    }
    
    echo "\n3. 生成 PHP 配置檔案...\n";
    $generator = new PhpConfigGenerator();
    $phpConfig = $generator->generateCompleteConfig($processedIcons, 'general');
    
    echo "   ✅ PHP 配置檔案生成成功\n";
    echo "   ✅ 檔案大小: " . strlen($phpConfig) . " 字元\n\n";
    
    // 4. 展示生成的 PHP 代碼的前幾行
    echo "4. 生成的 PHP 代碼範例 (前 20 行):\n";
    echo str_repeat("=", 50) . "\n";
    $lines = explode("\n", $phpConfig);
    foreach (array_slice($lines, 0, 20) as $line) {
        echo "$line\n";
    }
    echo "... (省略剩餘 " . (count($lines) - 20) . " 行)\n";
    echo str_repeat("=", 50) . "\n\n";
    
    // 5. 寫入檔案
    $outputPath = __DIR__ . '/output/general_demo.php';
    file_put_contents($outputPath, $phpConfig);
    echo "5. 配置檔案已儲存至: $outputPath\n\n";
    
    // 6. 驗證 PHP 語法
    echo "6. 驗證 PHP 語法...\n";
    $output = [];
    $returnCode = 0;
    exec("php8.4 -l $outputPath 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "   ✅ PHP 語法正確\n";
    } else {
        echo "   ❌ PHP 語法錯誤:\n";
        foreach ($output as $line) {
            echo "   $line\n";
        }
    }
    
    echo "\n🎉 演示完成！\n";
    echo "轉換器已成功將官方 Bootstrap Icons JSON 轉換為 PHP 配置檔案格式。\n";
    echo "這個工具可以用來同步官方圖標到你的專案配置中。\n\n";
    
    echo "📊 統計資訊:\n";
    echo "- 官方圖標總數: " . count($allIcons) . "\n";
    echo "- 演示處理數量: " . count($processedIcons) . "\n";
    echo "- 生成的配置檔案: $outputPath\n";
    
} catch (Exception $e) {
    echo "❌ 演示失敗: " . $e->getMessage() . "\n";
    exit(1);
}