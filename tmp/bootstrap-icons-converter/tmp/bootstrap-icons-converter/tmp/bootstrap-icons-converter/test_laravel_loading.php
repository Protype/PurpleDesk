<?php

/**
 * 測試生成的 general.php 檔案是否能正確在 Laravel 環境中載入
 * 模擬 Laravel 配置載入和 BootstrapIconService 處理
 */

echo "🧪 Laravel 配置載入測試\n";
echo "========================\n\n";

try {
    // 1. 測試基本 PHP 語法
    echo "1. 測試 PHP 檔案語法...\n";
    $configPath = __DIR__ . '/generated_general.php';
    
    if (!file_exists($configPath)) {
        throw new Exception("配置檔案不存在: $configPath");
    }
    
    $output = [];
    $returnCode = 0;
    exec("php8.4 -l \"$configPath\" 2>&1", $output, $returnCode);
    
    if ($returnCode !== 0) {
        throw new Exception("PHP 語法錯誤: " . implode("\n", $output));
    }
    
    echo "   ✅ PHP 語法正確\n\n";
    
    // 2. 測試配置載入
    echo "2. 測試配置檔案載入...\n";
    $config = include $configPath;
    
    if (!is_array($config)) {
        throw new Exception("配置檔案未返回陣列");
    }
    
    echo "   ✅ 配置檔案載入成功\n";
    echo "   📋 配置類型: " . gettype($config) . "\n";
    
    // 3. 驗證配置結構
    echo "\n3. 驗證配置檔案結構...\n";
    
    $requiredKeys = ['id', 'name', 'description', 'priority', 'icons'];
    foreach ($requiredKeys as $key) {
        if (!isset($config[$key])) {
            throw new Exception("缺少必要的配置鍵: $key");
        }
        echo "   ✅ 存在必要鍵: $key\n";
    }
    
    if (!is_array($config['icons'])) {
        throw new Exception("icons 必須是陣列");
    }
    
    $iconCount = count($config['icons']);
    echo "   ✅ 圖標陣列載入成功，包含 $iconCount 個圖標\n\n";
    
    // 4. 驗證圖標結構
    echo "4. 驗證圖標資料結構...\n";
    
    if ($iconCount === 0) {
        throw new Exception("圖標陣列為空");
    }
    
    // 檢查前 5 個圖標的結構
    $sampleIcons = array_slice($config['icons'], 0, 5);
    $requiredIconKeys = ['name', 'displayName', 'class', 'keywords', 'type', 'category', 'variants'];
    
    foreach ($sampleIcons as $i => $icon) {
        foreach ($requiredIconKeys as $key) {
            if (!isset($icon[$key])) {
                throw new Exception("圖標 #$i 缺少必要鍵: $key");
            }
        }
        echo "   ✅ 圖標 #{$i} ({$icon['name']}) 結構正確\n";
    }
    
    echo "\n";
    
    // 5. 模擬 BootstrapIconService 處理
    echo "5. 模擬 BootstrapIconService 處理...\n";
    
    class MockBootstrapIconService
    {
        public function expandIconVariants($iconData)
        {
            $expandedIcons = [];
            
            if (!isset($iconData['variants']) || !is_array($iconData['variants'])) {
                return [$iconData];
            }
            
            foreach ($iconData['variants'] as $variantType => $variantData) {
                $expandedIcon = $iconData;
                $expandedIcon['variant'] = $variantType;
                $expandedIcon['class'] = $variantData['class'];
                $expandedIcons[] = $expandedIcon;
            }
            
            return $expandedIcons;
        }
        
        public function processIcons($icons)
        {
            $allExpandedIcons = [];
            
            foreach ($icons as $icon) {
                $expandedIcons = $this->expandIconVariants($icon);
                $allExpandedIcons = array_merge($allExpandedIcons, $expandedIcons);
            }
            
            return $allExpandedIcons;
        }
    }
    
    $service = new MockBootstrapIconService();
    $processedIcons = $service->processIcons($config['icons']);
    
    echo "   ✅ BootstrapIconService 模擬處理成功\n";
    echo "   📊 原始圖標: $iconCount 個\n";
    echo "   📊 展開後圖標: " . count($processedIcons) . " 個\n\n";
    
    // 6. 測試 API 格式輸出
    echo "6. 測試 API 格式輸出...\n";
    
    $apiResponse = [
        'data' => array_slice($processedIcons, 0, 10), // 取前 10 個作為範例
        'meta' => [
            'total' => count($processedIcons),
            'category' => $config['id'],
            'name' => $config['name'],
            'description' => $config['description']
        ]
    ];
    
    $jsonOutput = json_encode($apiResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON 編碼失敗: " . json_last_error_msg());
    }
    
    echo "   ✅ API JSON 輸出成功\n";
    echo "   📏 JSON 大小: " . number_format(strlen($jsonOutput)) . " 字元\n\n";
    
    // 7. 展示 API 輸出範例
    echo "7. API 輸出範例 (前 3 個圖標):\n";
    echo str_repeat("=", 50) . "\n";
    
    $sampleApiData = [
        'data' => array_slice($processedIcons, 0, 3),
        'meta' => [
            'total' => count($processedIcons),
            'category' => $config['id'],
            'showing' => 3
        ]
    ];
    
    echo json_encode($sampleApiData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n" . str_repeat("=", 50) . "\n\n";
    
    // 8. 最終統計
    echo "📊 測試完成統計:\n";
    echo "- 配置檔案: " . basename($configPath) . "\n";
    echo "- 檔案大小: " . number_format(filesize($configPath)) . " 字節\n";
    echo "- 圖標數量: $iconCount\n";
    echo "- 展開後數量: " . count($processedIcons) . "\n";
    echo "- 平均每圖標變體: " . number_format(count($processedIcons) / $iconCount, 1) . "\n";
    echo "- JSON 輸出: ✅ 正常\n";
    echo "- Laravel 相容: ✅ 相容\n\n";
    
    echo "🎉 所有測試通過！生成的 PHP 配置檔案完全符合 Laravel 和 API 使用需求。\n";
    
} catch (Exception $e) {
    echo "❌ 測試失敗: " . $e->getMessage() . "\n";
    exit(1);
}

?>