<?php

/**
 * 第二十二批手動分類 - 60個圖標
 */

echo "📋 第二十二批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第二十二批手動分類定義
$manualCategories = [
    // UPC 條碼 -> others
    'upc' => 'others',
    
    // 上傳 -> files
    'upload' => 'files',
    
    // 向量筆 -> others
    'vector-pen' => 'others',
    
    // 檢視方式 -> ui
    'view-list' => 'ui',
    'view-stacked' => 'ui',
    
    // 黑膠唱片 -> media
    'vinyl-fill' => 'media',
    'vinyl' => 'media',
    
    // 語音信箱 -> communications
    'voicemail' => 'communications',
    
    // 音量控制 -> media
    'volume-down-fill' => 'media',
    'volume-down' => 'media',
    'volume-mute-fill' => 'media',
    'volume-mute' => 'media',
    'volume-off-fill' => 'media',
    'volume-off' => 'media',
    'volume-up-fill' => 'media',
    'volume-up' => 'media',
    
    // VR/虛擬實境 -> others
    'vr' => 'others',
    
    // 錢包 -> others
    'wallet-fill' => 'others',
    'wallet' => 'others',
    'wallet2' => 'others',
    
    // 手錶 -> others
    'watch' => 'others',
    
    // 水 -> others
    'water' => 'others',
    
    // WhatsApp -> communications
    'whatsapp' => 'communications',
    
    // WiFi 相關 -> communications
    'wifi-1' => 'communications',
    'wifi-2' => 'communications',
    'wifi-off' => 'communications',
    'wifi' => 'communications',
    
    // 風 -> others
    'wind' => 'others',
    
    // 視窗相關 -> ui
    'window-dock' => 'ui',
    'window-sidebar' => 'ui',
    'window' => 'ui',
    
    // 扳手 -> others
    'wrench' => 'others',
    
    // X 符號系列 -> general
    'x-circle-fill' => 'general',
    'x-circle' => 'general',
    'x-diamond-fill' => 'general',
    'x-diamond' => 'general',
    'x-octagon-fill' => 'general',
    'x-octagon' => 'general',
    'x-square-fill' => 'general',
    'x-square' => 'general',
    'x' => 'general',
    
    // YouTube -> communications
    'youtube' => 'communications',
    
    // 縮放 -> ui
    'zoom-in' => 'ui',
    'zoom-out' => 'ui',
    
    // 銀行 -> others
    'bank' => 'others',
    'bank2' => 'others',
    
    // 鈴鐺斜線 -> communications
    'bell-slash-fill' => 'communications',
    'bell-slash' => 'communications',
    
    // 現金硬幣 -> others
    'cash-coin' => 'others',
    'coin' => 'others',
];

// 應用分類
$updatedCount = 0;
foreach ($allConfig['icons'] as &$icon) {
    if (isset($manualCategories[$icon['name']])) {
        $oldCategory = $icon['category'];
        $newCategory = $manualCategories[$icon['name']];
        $icon['category'] = $newCategory;
        echo "✅ {$icon['name']}: {$oldCategory} -> {$newCategory}\n";
        $updatedCount++;
    }
}

echo "\n📊 更新了 {$updatedCount} 個圖標的分類\n\n";

// 統計每個分類的數量
$categoryStats = [];
foreach ($manualCategories as $iconName => $category) {
    $categoryStats[$category] = ($categoryStats[$category] ?? 0) + 1;
}

echo "📈 分類統計:\n";
foreach ($categoryStats as $category => $count) {
    echo "  {$category}: {$count} 個圖標\n";
}

// 寫入檔案
$phpContent = "<?php\n\nreturn " . var_export($allConfig, true) . ";\n";

if (file_put_contents($allConfigPath, $phpContent)) {
    echo "\n✅ 成功更新 all.php\n";
    
    // 清除 Laravel 快取
    echo "\n🔄 清除 Laravel 快取...\n";
    exec('cd ' . dirname(__DIR__) . ' && php8.4 artisan cache:clear', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ 快取清除成功\n";
    } else {
        echo "⚠️  快取清除可能失敗\n";
    }
    
} else {
    echo "❌ 更新 all.php 失敗\n";
    exit(1);
}

echo "\n🎉 第二十二批手動分類完成！\n";

?>