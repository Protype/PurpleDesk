<?php

/**
 * Bootstrap Icons 轉換器 - 最終版本
 * 將官方 JSON 轉換為完整的 PHP 配置檔案格式
 */

// 載入所有組件
require_once __DIR__ . '/src/JsonParser.php';
require_once __DIR__ . '/src/IconProcessor.php';  
require_once __DIR__ . '/src/PhpConfigGenerator.php';

// 確保輸出目錄存在
if (!is_dir(__DIR__ . '/output')) {
    mkdir(__DIR__ . '/output', 0777, true);
}

echo "🔄 Bootstrap Icons 轉換器 - 最終版本\n";
echo "=====================================\n\n";

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
    
    // 2. 處理所有圖標並生成 general 分類
    echo "2. 處理圖標並生成 general.php 配置...\n";
    $processor = new IconProcessor();
    $generator = new PhpConfigGenerator();
    
    // 處理所有圖標（限制在合理範圍內以避免記憶體問題）
    $iconLimit = min(500, count($allIcons)); // 最多處理 500 個圖標
    $iconsToProcess = array_slice($allIcons, 0, $iconLimit);
    
    echo "   處理中... ($iconLimit 個圖標)\n";
    
    $processedIcons = [];
    $progressCount = 0;
    
    foreach ($iconsToProcess as $iconName) {
        $processedIcons[] = $processor->processIcon($iconName, 'general');
        $progressCount++;
        
        if ($progressCount % 50 == 0) {
            echo "   - 已處理 $progressCount 個圖標\n";
        }
    }
    
    echo "   ✅ 完成處理 " . count($processedIcons) . " 個圖標\n\n";
    
    // 3. 生成完整的 PHP 配置檔案
    echo "3. 生成完整的 PHP 配置檔案...\n";
    $phpConfig = $generator->generateCompleteConfig($processedIcons, 'general');
    
    echo "   ✅ PHP 配置檔案生成成功\n";
    echo "   ✅ 檔案大小: " . number_format(strlen($phpConfig)) . " 字元\n";
    echo "   ✅ 包含圖標: " . count($processedIcons) . " 個\n\n";
    
    // 4. 輸出到檔案
    $outputPath = __DIR__ . '/output/general.php';
    $bytesWritten = file_put_contents($outputPath, $phpConfig);
    
    if ($bytesWritten !== false) {
        echo "4. 檔案輸出成功:\n";
        echo "   ✅ 路徑: $outputPath\n";
        echo "   ✅ 大小: " . number_format($bytesWritten) . " 字節\n\n";
        
        // 5. 驗證 PHP 語法
        echo "5. 驗證 PHP 語法...\n";
        $output = [];
        $returnCode = 0;
        exec("php8.4 -l \"$outputPath\" 2>&1", $output, $returnCode);
        
        if ($returnCode === 0) {
            echo "   ✅ PHP 語法正確\n\n";
        } else {
            echo "   ❌ PHP 語法錯誤:\n";
            foreach ($output as $line) {
                echo "   $line\n";
            }
            echo "\n";
        }
        
        // 6. 顯示使用範例
        echo "6. 使用範例:\n";
        echo "   // 在 Laravel 中使用\n";
        echo "   \$config = include '$outputPath';\n";
        echo "   \$icons = \$config['icons'];\n";
        echo "   echo \"載入了 \" . count(\$icons) . \" 個圖標\";\n\n";
        
    } else {
        echo "4. ❌ 檔案輸出失敗\n\n";
    }
    
    // 7. 統計報告
    echo "📊 轉換完成統計:\n";
    echo str_repeat("=", 30) . "\n";
    echo "官方圖標總數: " . number_format(count($allIcons)) . "\n";
    echo "已轉換圖標: " . number_format(count($processedIcons)) . "\n";
    echo "轉換覆蓋率: " . number_format((count($processedIcons) / count($allIcons)) * 100, 1) . "%\n";
    echo "輸出檔案: $outputPath\n";
    echo str_repeat("=", 30) . "\n\n";
    
    echo "🎉 Bootstrap Icons 轉換器執行完成！\n";
    echo "現在可以使用生成的 PHP 配置檔案來替換或補充你的專案中的圖標配置。\n";
    
} catch (Exception $e) {
    echo "❌ 轉換失敗: " . $e->getMessage() . "\n";
    echo "請檢查檔案路徑和權限設定。\n";
    exit(1);
}