<?php

/**
 * Bootstrap Icons General 類別匯出器
 * 專門匯出 general 類別的圖標並測試 API 使用
 */

// 載入所有組件
require_once __DIR__ . '/src/JsonParser.php';
require_once __DIR__ . '/src/IconProcessor.php';  
require_once __DIR__ . '/src/PhpConfigGenerator.php';

// 確保輸出目錄存在
if (!is_dir(__DIR__ . '/output')) {
    mkdir(__DIR__ . '/output', 0777, true);
}

echo "🔄 Bootstrap Icons General 類別匯出器\n";
echo "====================================\n\n";

try {
    // 1. 載入官方 JSON
    echo "1. 載入官方 Bootstrap Icons JSON...\n";
    $parser = new JsonParser();
    $success = $parser->loadFromFile(__DIR__ . '/data/bootstrap-icons.json');
    
    if (!$success) {
        throw new Exception("無法載入官方 JSON 檔案");
    }
    
    $allIcons = $parser->getIconNames();
    echo "   ✅ 成功載入 " . count($allIcons) . " 個官方圖標\n\n";
    
    // 2. 篩選 general 類別相關的圖標
    echo "2. 篩選 general 類別相關圖標...\n";
    
    // 定義 general 類別的關鍵字模式
    $generalPatterns = [
        // 基礎圖標
        'house', 'home', 'search', 'gear', 'cog', 'settings',
        // 狀態圖標  
        'alarm', 'bell', 'clock', 'time',
        // 標記圖標
        'star', 'heart', 'bookmark', 'flag',
        // 手勢圖標
        'hand', 'thumbs', 'finger',
        // 視覺圖標
        'eye', 'visible', 'view',
        // 基本形狀
        'circle', 'square', 'triangle',
        // 方向圖標
        'arrow', 'chevron', 'caret',
        // 控制圖標
        'play', 'pause', 'stop', 'skip',
        // 編輯圖標
        'plus', 'minus', 'x', 'check',
        // 導航圖標
        'menu', 'list', 'grid'
    ];
    
    $generalIcons = [];
    
    foreach ($allIcons as $iconName) {
        // 檢查圖標名稱是否包含 general 相關的關鍵字
        foreach ($generalPatterns as $pattern) {
            if (strpos($iconName, $pattern) !== false) {
                $generalIcons[] = $iconName;
                break; // 找到一個匹配就足夠
            }
        }
    }
    
    // 移除重複項目
    $generalIcons = array_unique($generalIcons);
    
    echo "   ✅ 篩選出 " . count($generalIcons) . " 個 general 類別圖標\n";
    echo "   📋 前 10 個範例: " . implode(', ', array_slice($generalIcons, 0, 10)) . "\n\n";
    
    // 3. 處理篩選出的圖標
    echo "3. 處理圖標資料...\n";
    $processor = new IconProcessor();
    
    $processedIcons = [];
    $progressCount = 0;
    
    foreach ($generalIcons as $iconName) {
        $processedIcons[] = $processor->processIcon($iconName, 'general');
        $progressCount++;
        
        if ($progressCount % 25 == 0) {
            echo "   - 已處理 $progressCount 個圖標\n";
        }
    }
    
    echo "   ✅ 完成處理 " . count($processedIcons) . " 個圖標\n\n";
    
    // 4. 生成 PHP 配置檔案
    echo "4. 生成 PHP 配置檔案...\n";
    $generator = new PhpConfigGenerator();
    $phpConfig = $generator->generateCompleteConfig($processedIcons, 'general');
    
    echo "   ✅ PHP 配置檔案生成成功\n";
    echo "   ✅ 檔案大小: " . number_format(strlen($phpConfig)) . " 字元\n\n";
    
    // 5. 寫入檔案
    $outputPath = __DIR__ . '/output/general.php';
    $bytesWritten = file_put_contents($outputPath, $phpConfig);
    
    if ($bytesWritten === false) {
        throw new Exception("無法寫入檔案: $outputPath");
    }
    
    echo "5. 檔案輸出成功:\n";
    echo "   📁 路徑: $outputPath\n";
    echo "   📏 大小: " . number_format($bytesWritten) . " 字節\n\n";
    
    // 6. 驗證 PHP 語法
    echo "6. 驗證 PHP 語法...\n";
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
        throw new Exception("生成的 PHP 檔案語法有誤");
    }
    
    echo "🎉 General 類別匯出完成！\n\n";
    echo "📊 匯出統計:\n";
    echo "- 官方圖標總數: " . number_format(count($allIcons)) . "\n";
    echo "- General 圖標: " . number_format(count($processedIcons)) . "\n";
    echo "- 覆蓋率: " . number_format((count($processedIcons) / count($allIcons)) * 100, 1) . "%\n";
    echo "- 輸出檔案: $outputPath\n\n";
    
    return $outputPath;
    
} catch (Exception $e) {
    echo "❌ 匯出失敗: " . $e->getMessage() . "\n";
    exit(1);
}