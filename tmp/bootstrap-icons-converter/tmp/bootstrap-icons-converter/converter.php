<?php

/**
 * Bootstrap Icons 轉換器主程式
 * 將官方 JSON 轉換為 PHP 配置檔案
 */

// 載入所有組件
require_once __DIR__ . '/src/JsonParser.php';
require_once __DIR__ . '/src/IconProcessor.php';  
require_once __DIR__ . '/src/PhpConfigGenerator.php';

echo "🔄 Bootstrap Icons 轉換器\n";
echo "========================\n\n";

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
    
    // 2. 處理圖標
    echo "2. 處理圖標資料...\n";
    $processor = new IconProcessor();
    
    // 先篩選 general 分類的圖標（前 50 個作為測試）
    $generalIcons = array_slice($allIcons, 0, 50);
    $processedIcons = [];
    
    foreach ($generalIcons as $iconName) {
        $processedIcons[] = $processor->processIcon($iconName, 'general');
    }
    
    echo "   ✅ 處理了 " . count($processedIcons) . " 個圖標\n\n";
    
    // 3. 生成 PHP 配置檔案
    echo "3. 生成 PHP 配置檔案...\n";
    $generator = new PhpConfigGenerator();
    $phpConfig = $generator->generateCompleteConfig($processedIcons, 'general');
    
    // 4. 寫入檔案
    $outputPath = __DIR__ . '/output/general.php';
    file_put_contents($outputPath, $phpConfig);
    
    echo "   ✅ 配置檔案已生成: $outputPath\n";
    echo "   ✅ 檔案大小: " . strlen($phpConfig) . " 字元\n\n";
    
    // 5. 驗證 PHP 語法
    echo "4. 驗證 PHP 語法...\n";
    $output = [];
    $returnCode = 0;
    exec("php -l $outputPath 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "   ✅ PHP 語法正確\n";
    } else {
        echo "   ❌ PHP 語法錯誤:\n";
        echo "   " . implode("\n   ", $output) . "\n";
    }
    
    echo "\n🎉 轉換完成！\n";
    echo "生成的檔案位於: $outputPath\n";
    
} catch (Exception $e) {
    echo "❌ 轉換失敗: " . $e->getMessage() . "\n";
    exit(1);
}