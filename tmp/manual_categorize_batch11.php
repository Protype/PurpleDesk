<?php

/**
 * 第十一批手動分類 - 60個圖標
 */

echo "📋 第十一批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第十一批手動分類定義 - 從頭開始的字母數字和箭頭系列
$manualCategories = [
    // 數字 123 -> alphanumeric
    '123' => 'alphanumeric',
    
    // 鬧鐘 -> general
    'alarm-fill' => 'general',
    'alarm' => 'general',
    
    // 對齊相關 -> ui
    'align-bottom' => 'ui',
    'align-center' => 'ui',
    'align-end' => 'ui',
    'align-middle' => 'ui',
    'align-start' => 'ui',
    'align-top' => 'ui',
    
    // Alt 鍵 -> alphanumeric
    'alt' => 'alphanumeric',
    
    // 應用程式相關 -> ui
    'app-indicator' => 'ui',
    'app' => 'ui',
    
    // 封存 -> files
    'archive-fill' => 'files',
    'archive' => 'files',
    
    // 箭頭系列 (所有箭頭相關) -> ui
    'arrow-90deg-down' => 'ui',
    'arrow-90deg-left' => 'ui',
    'arrow-90deg-right' => 'ui',
    'arrow-90deg-up' => 'ui',
    'arrow-bar-down' => 'ui',
    'arrow-bar-left' => 'ui',
    'arrow-bar-right' => 'ui',
    'arrow-bar-up' => 'ui',
    'arrow-clockwise' => 'ui',
    'arrow-counterclockwise' => 'ui',
    'arrow-down-circle-fill' => 'ui',
    'arrow-down-circle' => 'ui',
    'arrow-down-left-circle-fill' => 'ui',
    'arrow-down-left-circle' => 'ui',
    'arrow-down-left-square-fill' => 'ui',
    'arrow-down-left-square' => 'ui',
    'arrow-down-left' => 'ui',
    'arrow-down-right-circle-fill' => 'ui',
    'arrow-down-right-circle' => 'ui',
    'arrow-down-right-square-fill' => 'ui',
    'arrow-down-right-square' => 'ui',
    'arrow-down-right' => 'ui',
    'arrow-down-short' => 'ui',
    'arrow-down-square-fill' => 'ui',
    'arrow-down-square' => 'ui',
    'arrow-down-up' => 'ui',
    'arrow-down' => 'ui',
    'arrow-left-circle-fill' => 'ui',
    'arrow-left-circle' => 'ui',
    'arrow-left-right' => 'ui',
    'arrow-left-short' => 'ui',
    'arrow-left-square-fill' => 'ui',
    'arrow-left-square' => 'ui',
    'arrow-left' => 'ui',
    'arrow-repeat' => 'ui',
    'arrow-return-left' => 'ui',
    'arrow-return-right' => 'ui',
    'arrow-right-circle-fill' => 'ui',
    'arrow-right-circle' => 'ui',
    'arrow-right-short' => 'ui',
    'arrow-right-square-fill' => 'ui',
    'arrow-right-square' => 'ui',
    'arrow-right' => 'ui',
    'arrow-up-circle-fill' => 'ui',
    'arrow-up-circle' => 'ui',
    'arrow-up-left-circle-fill' => 'ui',
    'arrow-up-left-circle' => 'ui',
    'arrow-up-left-square-fill' => 'ui',
    'arrow-up-left-square' => 'ui',
    'arrow-up-left' => 'ui',
    'arrow-up-right-circle-fill' => 'ui',
    'arrow-up-right-circle' => 'ui',
    'arrow-up-right-square-fill' => 'ui',
    'arrow-up-right-square' => 'ui',
    'arrow-up-right' => 'ui',
    'arrow-up-short' => 'ui',
    'arrow-up-square-fill' => 'ui',
    'arrow-up-square' => 'ui',
    'arrow-up' => 'ui',
    'arrows-angle-contract' => 'ui',
    'arrows-angle-expand' => 'ui',
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

echo "\n🎉 第十一批手動分類完成！\n";

?>