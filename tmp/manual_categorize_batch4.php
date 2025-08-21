<?php

/**
 * 第四批手動分類 - 60個圖標
 */

echo "📋 第四批手動圖標分類\n";
echo "=====================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第四批手動分類定義
$manualCategories = [
    // 書籤相關 -> general
    'bookmarks-fill' => 'general',
    'bookmarks' => 'general',
    
    // 邊框/UI設計相關 -> ui
    'border-all' => 'ui',
    'border-bottom' => 'ui',
    'border-center' => 'ui',
    'border-inner' => 'ui',
    'border-left' => 'ui',
    'border-middle' => 'ui',
    'border-outer' => 'ui',
    'border-right' => 'ui',
    'border-style' => 'ui',
    'border-top' => 'ui',
    'border-width' => 'ui',
    'border' => 'ui',
    
    // 盒子/容器 -> ui
    'box-seam' => 'ui',
    'box' => 'ui',
    
    // 程式符號 -> alphanumeric
    'braces' => 'alphanumeric',
    
    // 建築材料 -> others
    'bricks' => 'others',
    
    // 公事包/工作相關 -> others
    'briefcase-fill' => 'others',
    'briefcase' => 'others',
    
    // 亮度控制 -> ui
    'brightness-alt-high-fill' => 'ui',
    'brightness-alt-high' => 'ui',
    'brightness-alt-low-fill' => 'ui',
    'brightness-alt-low' => 'ui',
    'brightness-high-fill' => 'ui',
    'brightness-high' => 'ui',
    'brightness-low-fill' => 'ui',
    'brightness-low' => 'ui',
    
    // 廣播相關 -> communications
    'broadcast-pin' => 'communications',
    'broadcast' => 'communications',
    
    // 筆刷/繪圖 -> others
    'brush-fill' => 'others',
    'brush' => 'others',
    
    // 水桶工具 -> others
    'bucket-fill' => 'others',
    'bucket' => 'others',
    
    // Bug/程式相關 -> others
    'bug-fill' => 'others',
    'bug' => 'others',
    
    // 建築物 -> others
    'building' => 'others',
    
    // 靶心 -> others
    'bullseye' => 'others',
    
    // 計算機 -> others
    'calculator-fill' => 'others',
    'calculator' => 'others',
    
    // 日曆 -> general
    'calendar-check-fill' => 'general',
    'calendar-check' => 'general',
    'calendar-date-fill' => 'general',
    'calendar-date' => 'general',
    'calendar-day-fill' => 'general',
    'calendar-day' => 'general',
    'calendar-event-fill' => 'general',
    'calendar-event' => 'general',
    'calendar-fill' => 'general',
    'calendar-heart-fill' => 'general',
    'calendar-heart' => 'general',
    'calendar-minus-fill' => 'general',
    'calendar-minus' => 'general',
    'calendar-month-fill' => 'general',
    'calendar-month' => 'general',
    'calendar-plus-fill' => 'general',
    'calendar-plus' => 'general',
    'calendar-range-fill' => 'general',
    'calendar-range' => 'general',
    'calendar-week-fill' => 'general',
    'calendar-week' => 'general',
    'calendar-x-fill' => 'general',
    'calendar-x' => 'general',
    'calendar' => 'general',
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

echo "\n🎉 第四批手動分類完成！\n";

?>