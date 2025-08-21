<?php

/**
 * 第三十四批手動分類 - 60個圖標 (衝向終點！98%目標！)
 */

echo "📋 第三十四批手動圖標分類 - 衝向終點！98%目標！\n";
echo "==========================================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第三十四批手動分類定義 - 衝向終點
$manualCategories = [
    // 表情符號 -> others
    'emoji-grin-fill' => 'others',
    'emoji-grin' => 'others',
    'emoji-surprise-fill' => 'others',
    'emoji-surprise' => 'others',
    'emoji-tear-fill' => 'others',
    'emoji-tear' => 'others',
    
    // 信封箭頭 -> communications
    'envelope-arrow-down-fill' => 'communications',
    'envelope-arrow-down' => 'communications',
    'envelope-arrow-up-fill' => 'communications',
    'envelope-arrow-up' => 'communications',
    
    // 開發平台 -> communications
    'gitlab' => 'communications',
    'opencollective' => 'communications',
    'sourceforge' => 'communications',
    'substack' => 'communications',
    'twitter-x' => 'communications',
    'threads-fill' => 'communications',
    'threads' => 'communications',
    
    // 工具 -> others
    'highlighter' => 'others',
    'marker-tip' => 'others',
    
    // 硬體 -> others
    'nvme-fill' => 'others',
    'nvme' => 'others',
    'pci-card-network' => 'others',
    'pci-card-sound' => 'others',
    'radar' => 'others',
    
    // 發送箭頭 -> communications
    'send-arrow-down-fill' => 'communications',
    'send-arrow-down' => 'communications',
    'send-arrow-up-fill' => 'communications',
    'send-arrow-up' => 'communications',
    
    // SIM卡 -> communications
    'sim-slash-fill' => 'communications',
    'sim-slash' => 'communications',
    
    // 透明度 -> ui
    'transparency' => 'ui',
    
    // 標題標籤 -> ui
    'type-h4' => 'ui',
    'type-h5' => 'ui',
    'type-h6' => 'ui',
    
    // 背包系列 -> others
    'backpack-fill' => 'others',
    'backpack' => 'others',
    'backpack2-fill' => 'others',
    'backpack2' => 'others',
    'backpack3-fill' => 'others',
    'backpack3' => 'others',
    'backpack4-fill' => 'others',
    'backpack4' => 'others',
    
    // 蛋糕（填滿版） -> others
    'cake-fill' => 'others',
    'cake2-fill' => 'others',
    
    // 旅行用品 -> others
    'duffle-fill' => 'others',
    'duffle' => 'others',
    'luggage-fill' => 'others',
    'luggage' => 'others',
    'suitcase-fill' => 'others',
    'suitcase-lg-fill' => 'others',
    'suitcase-lg' => 'others',
    'suitcase' => 'others',
    'suitcase2-fill' => 'others',
    'suitcase2' => 'others',
    
    // 護照 -> others
    'passport-fill' => 'others',
    'passport' => 'others',
    
    // 郵箱旗幟 -> communications
    'mailbox-flag' => 'communications',
    'mailbox2-flag' => 'communications',
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

echo "\n🎉 第三十四批手動分類完成！\n";
echo "🎯 98%即將達成！終點就在眼前！\n";

?>