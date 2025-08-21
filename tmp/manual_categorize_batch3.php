<?php

/**
 * 第三批手動分類 - 60個圖標
 */

echo "📋 第三批手動圖標分類\n";
echo "=====================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第三批手動分類定義
$manualCategories = [
    // 箭頭/UI控制相關 -> ui
    'arrows-angle-contract' => 'ui',
    'arrows-angle-expand' => 'ui',
    'arrows-collapse' => 'ui',
    'arrows-expand' => 'ui',
    'arrows-fullscreen' => 'ui',
    'arrows-move' => 'ui',
    
    // 符號 -> general
    'at' => 'general',
    
    // 導航 -> files
    'back' => 'files',
    
    // 購物袋系列 -> others
    'bag-check-fill' => 'others',
    'bag-check' => 'others',
    'bag-plus-fill' => 'others',
    'bag-plus' => 'others',
    'bag-x-fill' => 'others',
    'bag-x' => 'others',
    
    // 圖表 -> ui
    'bar-chart-steps' => 'ui',
    
    // 電池相關 -> others
    'battery-charging' => 'others',
    'battery-full' => 'others',
    'battery-half' => 'others',
    'battery' => 'others',
    
    // 鈴鐺/通知 -> general
    'bell-fill' => 'general',
    'bell' => 'general',
    
    // 設計工具 -> others
    'bezier' => 'others',
    'bezier2' => 'others',
    
    // 交通工具 -> others
    'bicycle' => 'others',
    
    // 觀看工具 -> others
    'binoculars-fill' => 'others',
    'binoculars' => 'others',
    
    // 文字引用 -> alphanumeric
    'blockquote-left' => 'alphanumeric',
    'blockquote-right' => 'alphanumeric',
    
    // 書籍/文件 -> files
    'book-fill' => 'files',
    'book-half' => 'files',
    'book' => 'files',
    
    // 書籤 -> general
    'bookmark-check-fill' => 'general',
    'bookmark-check' => 'general',
    'bookmark-dash-fill' => 'general',
    'bookmark-dash' => 'general',
    'bookmark-fill' => 'general',
    'bookmark-heart-fill' => 'general',
    'bookmark-heart' => 'general',
    'bookmark-plus-fill' => 'general',
    'bookmark-plus' => 'general',
    'bookmark-star-fill' => 'general',
    'bookmark-star' => 'general',
    'bookmark-x-fill' => 'general',
    'bookmark-x' => 'general',
    'bookmark' => 'general',
    
    // 書架 -> files
    'bookshelf' => 'files',
    
    // 品牌 -> others
    'bootstrap-fill' => 'others',
    'bootstrap-reboot' => 'others',
    'bootstrap' => 'others',
    
    // 邊框/UI -> ui
    'bounding-box-circles' => 'ui',
    'bounding-box' => 'ui',
    
    // 導出/箭頭 -> ui
    'box-arrow-down-left' => 'ui',
    'box-arrow-down-right' => 'ui',
    'box-arrow-down' => 'ui',
    'box-arrow-in-down-left' => 'ui',
    'box-arrow-in-down-right' => 'ui',
    'box-arrow-in-down' => 'ui',
    'box-arrow-in-left' => 'ui',
    'box-arrow-in-right' => 'ui',
    'box-arrow-in-up-left' => 'ui',
    'box-arrow-in-up-right' => 'ui',
    'box-arrow-in-up' => 'ui',
    'box-arrow-left' => 'ui',
    'box-arrow-right' => 'ui',
    'box-arrow-up-left' => 'ui',
    'box-arrow-up-right' => 'ui',
    'box-arrow-up' => 'ui',
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

echo "\n🎉 第三批手動分類完成！\n";

?>