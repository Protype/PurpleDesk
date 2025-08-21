<?php

/**
 * 第七批手動分類 - 60個圖標
 */

echo "📋 第七批手動圖標分類\n";
echo "=====================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第七批手動分類定義 - 主要是圓形、剪貼板、時鐘和雲端相關圖標
$manualCategories = [
    // 圓形/幾何圖形 -> ui
    'circle-fill' => 'ui',
    'circle-half' => 'ui',
    'circle-square' => 'ui',
    'circle' => 'ui',
    
    // 剪貼板/複製相關 -> files
    'clipboard-check' => 'files',
    'clipboard-data' => 'files',
    'clipboard-minus' => 'files',
    'clipboard-plus' => 'files',
    'clipboard-x' => 'files',
    'clipboard' => 'files',
    
    // 時鐘/時間 -> general
    'clock-fill' => 'general',
    'clock-history' => 'general',
    'clock' => 'general',
    
    // 雲端/天氣相關 -> others
    'cloud-arrow-down-fill' => 'others',
    'cloud-arrow-down' => 'others',
    'cloud-arrow-up-fill' => 'others',
    'cloud-arrow-up' => 'others',
    'cloud-check-fill' => 'others',
    'cloud-check' => 'others',
    'cloud-download-fill' => 'others',
    'cloud-download' => 'others',
    'cloud-drizzle-fill' => 'others',
    'cloud-drizzle' => 'others',
    'cloud-fill' => 'others',
    'cloud-fog-fill' => 'others',
    'cloud-fog' => 'others',
    'cloud-fog2-fill' => 'others',
    'cloud-fog2' => 'others',
    'cloud-hail-fill' => 'others',
    'cloud-hail' => 'others',
    'cloud-lightning-fill' => 'others',
    'cloud-lightning-rain-fill' => 'others',
    'cloud-lightning-rain' => 'others',
    'cloud-lightning' => 'others',
    'cloud-minus-fill' => 'others',
    'cloud-minus' => 'others',
    'cloud-moon-fill' => 'others',
    'cloud-moon' => 'others',
    'cloud-plus-fill' => 'others',
    'cloud-plus' => 'others',
    'cloud-rain-fill' => 'others',
    'cloud-rain-heavy-fill' => 'others',
    'cloud-rain-heavy' => 'others',
    'cloud-rain' => 'others',
    'cloud-sleet-fill' => 'others',
    'cloud-sleet' => 'others',
    'cloud-snow-fill' => 'others',
    'cloud-snow' => 'others',
    'cloud-sun-fill' => 'others',
    'cloud-sun' => 'others',
    'cloud-upload-fill' => 'others',
    'cloud-upload' => 'others',
    'cloud' => 'others',
    'clouds-fill' => 'others',
    'clouds' => 'others',
    'cloudy-fill' => 'others',
    'cloudy' => 'others',
    
    // 程式碼相關 -> alphanumeric
    'code-slash' => 'alphanumeric',
    'code-square' => 'alphanumeric',
    'code' => 'alphanumeric',
    
    // 收藏/星星 -> general
    'collection-fill' => 'general',
    'collection-play-fill' => 'general',
    'collection-play' => 'general',
    'collection' => 'general',
    
    // 顏色 -> ui
    'color-square' => 'ui',
    
    // 欄位/佈局 -> ui
    'columns-gap' => 'ui',
    'columns' => 'ui',
    
    // 指南針/導航 -> others
    'compass-fill' => 'others',
    'compass' => 'others',
    
    // 錐形 -> others
    'cone-striped' => 'others',
    'cone' => 'others',
    
    // 控制器/遊戲 -> others
    'controller' => 'others',
    
    // 複製 -> general
    'copy' => 'general',
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

echo "\n🎉 第七批手動分類完成！\n";

?>