<?php

/**
 * 第十八批手動分類 - 60個圖標
 */

echo "📋 第十八批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第十八批手動分類定義
$manualCategories = [
    // 訊號強度系列 -> communications
    'reception-2' => 'communications',
    'reception-3' => 'communications',
    'reception-4' => 'communications',
    
    // 錄製相關 -> media
    'record-btn-fill' => 'media',
    'record-btn' => 'media',
    'record-circle-fill' => 'media',
    'record-circle' => 'media',
    'record-fill' => 'media',
    'record' => 'media',
    'record2-fill' => 'media',
    'record2' => 'media',
    
    // 回覆相關 -> communications
    'reply-all-fill' => 'communications',
    'reply-all' => 'communications',
    'reply-fill' => 'communications',
    'reply' => 'communications',
    
    // RSS -> communications
    'rss-fill' => 'communications',
    'rss' => 'communications',
    
    // 尺規 -> others
    'rulers' => 'others',
    
    // 儲存相關 -> files
    'save-fill' => 'files',
    'save' => 'files',
    'save2-fill' => 'files',
    'save2' => 'files',
    
    // 剪刀 -> others
    'scissors' => 'others',
    
    // 螺絲起子 -> others
    'screwdriver' => 'others',
    
    // 搜尋 -> general
    'search' => 'general',
    
    // 分段導航 -> ui
    'segmented-nav' => 'ui',
    
    // 伺服器 -> others
    'server' => 'others',
    
    // 分享 -> communications
    'share-fill' => 'communications',
    'share' => 'communications',
    
    // 盾牌/安全系列 -> general
    'shield-check' => 'general',
    'shield-exclamation' => 'general',
    'shield-fill-check' => 'general',
    'shield-fill-exclamation' => 'general',
    'shield-fill-minus' => 'general',
    'shield-fill-plus' => 'general',
    'shield-fill-x' => 'general',
    'shield-fill' => 'general',
    'shield-lock-fill' => 'general',
    'shield-lock' => 'general',
    'shield-minus' => 'general',
    'shield-plus' => 'general',
    'shield-shaded' => 'general',
    'shield-slash-fill' => 'general',
    'shield-slash' => 'general',
    'shield-x' => 'general',
    'shield' => 'general',
    
    // Shift 鍵 -> alphanumeric
    'shift-fill' => 'alphanumeric',
    'shift' => 'alphanumeric',
    
    // 商店相關 -> others
    'shop-window' => 'others',
    'shop' => 'others',
    
    // 隨機播放 -> media
    'shuffle' => 'media',
    
    // 指示牌相關 -> general
    'signpost-2-fill' => 'general',
    'signpost-2' => 'general',
    'signpost-fill' => 'general',
    'signpost-split-fill' => 'general',
    'signpost-split' => 'general',
    'signpost' => 'general',
    
    // SIM卡 -> others
    'sim-fill' => 'others',
    'sim' => 'others',
    
    // 跳過按鈕 -> media
    'skip-backward-btn-fill' => 'media',
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

echo "\n🎉 第十八批手動分類完成！\n";

?>