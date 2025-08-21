<?php

/**
 * 第十三批手動分類 - 60個圖標
 */

echo "📋 第十三批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第十三批手動分類定義
$manualCategories = [
    // 寶石 -> others
    'gem' => 'others',
    
    // 地理位置相關 -> general
    'geo-alt-fill' => 'general',
    'geo-alt' => 'general',
    'geo-fill' => 'general',
    'geo' => 'general',
    
    // 禮物 -> others
    'gift-fill' => 'others',
    'gift' => 'others',
    
    // 社群媒體 -> communications
    'github' => 'communications',
    'google' => 'communications',
    
    // 地球/全球 -> communications
    'globe' => 'communications',
    'globe2' => 'communications',
    
    // 圖表相關 -> ui
    'graph-down' => 'ui',
    'graph-up' => 'ui',
    
    // 網格/佈局相關 -> ui
    'grid-1x2-fill' => 'ui',
    'grid-1x2' => 'ui',
    'grid-3x2-gap-fill' => 'ui',
    'grid-3x2-gap' => 'ui',
    'grid-3x2' => 'ui',
    'grid-3x3-gap-fill' => 'ui',
    'grid-3x3-gap' => 'ui',
    'grid-3x3' => 'ui',
    'grid-fill' => 'ui',
    'grid' => 'ui',
    
    // 拖曳手柄 -> ui
    'grip-horizontal' => 'ui',
    'grip-vertical' => 'ui',
    
    // 工具 -> others
    'hammer' => 'others',
    
    // 手勢相關 -> people
    'hand-index-fill' => 'people',
    'hand-index-thumb-fill' => 'people',
    'hand-index-thumb' => 'people',
    'hand-index' => 'people',
    'hand-thumbs-down-fill' => 'people',
    'hand-thumbs-down' => 'people',
    'hand-thumbs-up-fill' => 'people',
    'hand-thumbs-up' => 'people',
    
    // 手提包 -> others
    'handbag-fill' => 'others',
    'handbag' => 'others',
    
    // 井號/標籤 -> alphanumeric
    'hash' => 'alphanumeric',
    
    // 硬碟/存儲相關 -> others
    'hdd-fill' => 'others',
    'hdd-network-fill' => 'others',
    'hdd-network' => 'others',
    'hdd-rack-fill' => 'others',
    'hdd-rack' => 'others',
    'hdd-stack-fill' => 'others',
    'hdd-stack' => 'others',
    'hdd' => 'others',
    
    // 音頻設備 -> media
    'headphones' => 'media',
    'headset' => 'media',
    
    // 愛心 -> general
    'heart-fill' => 'general',
    'heart-half' => 'general',
    'heart' => 'general',
    
    // 幾何圖形 -> ui
    'heptagon-fill' => 'ui',
    'heptagon-half' => 'ui',
    'heptagon' => 'ui',
    'hexagon-fill' => 'ui',
    'hexagon-half' => 'ui',
    'hexagon' => 'ui',
    
    // 沙漏/時間 -> general
    'hourglass-bottom' => 'general',
    'hourglass-split' => 'general',
    'hourglass-top' => 'general',
    'hourglass' => 'general',
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

echo "\n🎉 第十三批手動分類完成！\n";

?>