<?php

/**
 * 快速演示 Bootstrap Icons 轉換器
 */

// 載入所有組件
require_once './src/JsonParser.php';
require_once './src/IconProcessor.php';  
require_once './src/PhpConfigGenerator.php';

echo "🔄 Bootstrap Icons 轉換器 - 快速演示\n";
echo "===================================\n\n";

try {
    // 1. 載入官方 JSON
    echo "1. 載入官方 JSON 檔案...\n";
    $parser = new JsonParser();
    $success = $parser->loadFromFile('./data/bootstrap-icons.json');
    
    if (!$success) {
        throw new Exception("無法載入 JSON 檔案");
    }
    
    $allIcons = $parser->getIconNames();
    echo "   ✅ 成功載入 " . count($allIcons) . " 個圖標\n\n";
    
    // 2. 展示前 10 個圖標的處理結果
    echo "2. 處理前 10 個圖標...\n";
    $processor = new IconProcessor();
    $generator = new PhpConfigGenerator();
    
    $testIcons = array_slice($allIcons, 0, 10);
    $processedIcons = [];
    
    foreach ($testIcons as $iconName) {
        $processed = $processor->processIcon($iconName, 'general');
        $processedIcons[] = $processed;
        echo "   - $iconName → {$processed['displayName']} (" . count($processed['variants']) . " 變體)\n";
    }
    
    echo "\n3. 生成 PHP 配置檔案範例...\n";
    $phpConfig = $generator->generateCompleteConfig($processedIcons, 'general');
    
    echo "   ✅ PHP 配置檔案生成成功\n";
    echo "   ✅ 檔案大小: " . strlen($phpConfig) . " 字元\n\n";
    
    // 4. 展示生成的 PHP 代碼的前幾行
    echo "4. 生成的 PHP 代碼範例:\n";
    echo "========================\n";
    $lines = explode("\n", $phpConfig);
    foreach (array_slice($lines, 0, 20) as $line) {
        echo "$line\n";
    }
    echo "... (省略剩餘 " . (count($lines) - 20) . " 行)\n\n";
    
    // 5. 寫入到 output 目錄（如果存在）
    if (!is_dir('./output')) {
        mkdir('./output', 0777, true);
    }
    
    $outputPath = './output/general_demo.php';
    file_put_contents($outputPath, $phpConfig);
    echo "5. 配置檔案已儲存至: $outputPath\n\n";
    
    echo "🎉 演示完成！\n";
    echo "轉換器運作正常，可以將官方 JSON 轉換為 PHP 配置檔案格式。\n";
    
} catch (Exception $e) {
    echo "❌ 演示失敗: " . $e->getMessage() . "\n";
    exit(1);
}