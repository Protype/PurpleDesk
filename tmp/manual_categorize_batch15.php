<?php

/**
 * 第十五批手動分類 - 60個圖標
 */

echo "📋 第十五批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第十五批手動分類定義
$manualCategories = [
    // 佈局相關 -> ui
    'layout-sidebar-reverse' => 'ui',
    'layout-sidebar' => 'ui',
    'layout-split' => 'ui',
    'layout-text-sidebar-reverse' => 'ui',
    'layout-text-sidebar' => 'ui',
    'layout-text-window-reverse' => 'ui',
    'layout-text-window' => 'ui',
    'layout-three-columns' => 'ui',
    'layout-wtf' => 'ui',
    
    // 救生圈 -> others
    'life-preserver' => 'others',
    
    // 燈泡相關 -> others
    'lightbulb-fill' => 'others',
    'lightbulb-off-fill' => 'others',
    'lightbulb-off' => 'others',
    'lightbulb' => 'others',
    
    // 閃電相關 -> others
    'lightning-charge-fill' => 'others',
    'lightning-charge' => 'others',
    'lightning-fill' => 'others',
    'lightning' => 'others',
    
    // 連結相關 -> general
    'link-45deg' => 'general',
    'link' => 'general',
    
    // 社群媒體 -> communications
    'linkedin' => 'communications',
    
    // 清單相關 -> ui
    'list-check' => 'ui',
    'list-nested' => 'ui',
    'list-ol' => 'ui',
    'list-stars' => 'ui',
    'list-task' => 'ui',
    'list-ul' => 'ui',
    'list' => 'ui',
    
    // 鎖定相關 -> general
    'lock-fill' => 'general',
    'lock' => 'general',
    
    // 信箱 -> communications
    'mailbox' => 'communications',
    'mailbox2' => 'communications',
    
    // 地圖相關 -> general
    'map-fill' => 'general',
    'map' => 'general',
    
    // Markdown -> alphanumeric
    'markdown-fill' => 'alphanumeric',
    'markdown' => 'alphanumeric',
    
    // 遮罩 -> others
    'mask' => 'others',
    
    // 擴音器/廣播 -> communications
    'megaphone-fill' => 'communications',
    'megaphone' => 'communications',
    
    // 選單相關 -> ui
    'menu-app-fill' => 'ui',
    'menu-app' => 'ui',
    'menu-button-fill' => 'ui',
    'menu-button-wide-fill' => 'ui',
    'menu-button-wide' => 'ui',
    'menu-button' => 'ui',
    'menu-down' => 'ui',
    'menu-up' => 'ui',
    
    // 麥克風相關 -> media
    'mic-fill' => 'media',
    'mic-mute-fill' => 'media',
    'mic-mute' => 'media',
    'mic' => 'media',
    
    // 礦車 -> others
    'minecart-loaded' => 'others',
    'minecart' => 'others',
    
    // 濕度 -> others
    'moisture' => 'others',
    
    // 月亮/天體 -> others
    'moon-fill' => 'others',
    'moon-stars-fill' => 'others',
    'moon-stars' => 'others',
    'moon' => 'others',
    
    // 滑鼠 -> others
    'mouse-fill' => 'others',
    'mouse' => 'others',
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

echo "\n🎉 第十五批手動分類完成！\n";

?>