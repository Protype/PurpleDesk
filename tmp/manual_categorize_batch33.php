<?php

/**
 * 第三十三批手動分類 - 60個圖標 (最終衝刺！95%達成！)
 */

echo "📋 第三十三批手動圖標分類 - 最終衝刺！95%達成！\n";
echo "=============================================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第三十三批手動分類定義 - 最終衝刺階段
$manualCategories = [
    // AMD 處理器 -> communications
    'amd' => 'communications',
    
    // 數據庫系列 -> others
    'database-add' => 'others',
    'database-check' => 'others',
    'database-dash' => 'others',
    'database-down' => 'others',
    'database-exclamation' => 'others',
    'database-fill-add' => 'others',
    'database-fill-check' => 'others',
    'database-fill-dash' => 'others',
    'database-fill-down' => 'others',
    'database-fill-exclamation' => 'others',
    'database-fill-gear' => 'others',
    'database-fill-lock' => 'others',
    'database-fill-slash' => 'others',
    'database-fill-up' => 'others',
    'database-fill-x' => 'others',
    'database-fill' => 'others',
    'database-gear' => 'others',
    'database-lock' => 'others',
    'database-slash' => 'others',
    'database-up' => 'others',
    'database-x' => 'others',
    'database' => 'others',
    
    // 房屋複數 -> others
    'houses-fill' => 'others',
    'houses' => 'others',
    
    // NVIDIA -> communications
    'nvidia' => 'communications',
    
    // 人物名片 -> people
    'person-vcard-fill' => 'people',
    'person-vcard' => 'people',
    
    // 社群平台 -> communications
    'sina-weibo' => 'communications',
    'tencent-qq' => 'communications',
    'wikipedia' => 'communications',
    
    // 字母表 -> alphanumeric
    'alphabet-uppercase' => 'alphanumeric',
    'alphabet' => 'alphanumeric',
    
    // Amazon -> communications
    'amazon' => 'communications',
    
    // 箭頭系列 -> ui
    'arrows-collapse-vertical' => 'ui',
    'arrows-expand-vertical' => 'ui',
    'arrows-vertical' => 'ui',
    'arrows' => 'ui',
    
    // 禁止標誌 -> general
    'ban-fill' => 'general',
    'ban' => 'general',
    
    // Bing 搜尋 -> communications
    'bing' => 'communications',
    
    // 蛋糕 -> others
    'cake' => 'others',
    'cake2' => 'others',
    
    // Cookie -> others
    'cookie' => 'others',
    
    // 十字準星 -> ui
    'crosshair' => 'ui',
    'crosshair2' => 'ui',
    
    // 表情符號 -> others
    'emoji-astonished-fill' => 'others',
    'emoji-astonished' => 'others',
    'emoji-grimace-fill' => 'others',
    'emoji-grimace' => 'others',
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

echo "\n🎉 第三十三批手動分類完成！\n";
echo "🚀 即將突破95%！離終點越來越近！\n";

?>