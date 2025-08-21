<?php

/**
 * 第五批手動分類 - 60個圖標
 */

echo "📋 第五批手動圖標分類\n";
echo "=====================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第五批手動分類定義 - 主要是日曆和相機相關圖標
$manualCategories = [
    // Calendar2 系列 -> general
    'calendar2-check-fill' => 'general',
    'calendar2-check' => 'general',
    'calendar2-date-fill' => 'general',
    'calendar2-date' => 'general',
    'calendar2-day-fill' => 'general',
    'calendar2-day' => 'general',
    'calendar2-event-fill' => 'general',
    'calendar2-event' => 'general',
    'calendar2-fill' => 'general',
    'calendar2-minus-fill' => 'general',
    'calendar2-minus' => 'general',
    'calendar2-month-fill' => 'general',
    'calendar2-month' => 'general',
    'calendar2-plus-fill' => 'general',
    'calendar2-plus' => 'general',
    'calendar2-range-fill' => 'general',
    'calendar2-range' => 'general',
    'calendar2-week-fill' => 'general',
    'calendar2-week' => 'general',
    'calendar2-x-fill' => 'general',
    'calendar2-x' => 'general',
    'calendar2' => 'general',
    
    // Calendar3 系列 -> general
    'calendar3-event-fill' => 'general',
    'calendar3-event' => 'general',
    'calendar3-fill' => 'general',
    'calendar3-range-fill' => 'general',
    'calendar3-range' => 'general',
    'calendar3-week-fill' => 'general',
    'calendar3-week' => 'general',
    'calendar3' => 'general',
    
    // 相機相關 -> media
    'camera-fill' => 'media',
    'camera-reels-fill' => 'media',
    'camera-reels' => 'media',
    'camera-video-fill' => 'media',
    'camera-video-off-fill' => 'media',
    'camera-video-off' => 'media',
    'camera-video' => 'media',
    'camera' => 'media',
    'camera2' => 'media',
    
    // 大寫鎖定 -> others
    'capslock-fill' => 'others',
    'capslock' => 'others',
    
    // 插入符號 -> ui
    'caret-down-fill' => 'ui',
    'caret-down-square-fill' => 'ui',
    'caret-down-square' => 'ui',
    'caret-down' => 'ui',
    'caret-left-fill' => 'ui',
    'caret-left-square-fill' => 'ui',
    'caret-left-square' => 'ui',
    'caret-left' => 'ui',
    'caret-right-fill' => 'ui',
    'caret-right-square-fill' => 'ui',
    'caret-right-square' => 'ui',
    'caret-right' => 'ui',
    'caret-up-fill' => 'ui',
    'caret-up-square-fill' => 'ui',
    'caret-up-square' => 'ui',
    'caret-up' => 'ui',
    
    // 購物車 -> others
    'cart-check-fill' => 'others',
    'cart-check' => 'others',
    'cart-dash-fill' => 'others',
    'cart-dash' => 'others',
    'cart-fill' => 'others',
    'cart-plus-fill' => 'others',
    'cart-plus' => 'others',
    'cart-x-fill' => 'others',
    'cart-x' => 'others',
    'cart' => 'others',
    'cart2' => 'others',
    'cart3' => 'others',
    'cart4' => 'others',
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
    exec('cd ' . dirname(__DIR__) . ' && php artisan cache:clear', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ 快取清除成功\n";
    } else {
        echo "⚠️  快取清除可能失敗\n";
    }
    
} else {
    echo "❌ 更新 all.php 失敗\n";
    exit(1);
}

echo "\n🎉 第五批手動分類完成！\n";

?>