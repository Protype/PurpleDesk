<?php

/**
 * 第十七批手動分類 - 60個圖標
 */

echo "📋 第十七批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第十七批手動分類定義
$manualCategories = [
    // 人物系列 -> people
    'person-check' => 'people',
    'person-circle' => 'people',
    'person-dash-fill' => 'people',
    'person-dash' => 'people',
    'person-fill' => 'people',
    'person-lines-fill' => 'people',
    'person-plus-fill' => 'people',
    'person-plus' => 'people',
    'person-square' => 'people',
    'person-x-fill' => 'people',
    'person-x' => 'people',
    'person' => 'people',
    
    // 手機/電話相關 -> communications
    'phone-fill' => 'communications',
    'phone-landscape-fill' => 'communications',
    'phone-landscape' => 'communications',
    'phone-vibrate-fill' => 'communications',
    'phone-vibrate' => 'communications',
    'phone' => 'communications',
    
    // 圓餅圖 -> ui
    'pie-chart-fill' => 'ui',
    'pie-chart' => 'ui',
    
    // 圖釘相關 -> general
    'pin-angle-fill' => 'general',
    'pin-angle' => 'general',
    'pin-fill' => 'general',
    'pin' => 'general',
    
    // 畫中畫 -> ui
    'pip-fill' => 'ui',
    'pip' => 'ui',
    
    // 播放相關 -> media
    'play-btn-fill' => 'media',
    'play-btn' => 'media',
    'play-circle-fill' => 'media',
    'play-circle' => 'media',
    'play-fill' => 'media',
    'play' => 'media',
    
    // 插頭 -> others
    'plug-fill' => 'others',
    'plug' => 'others',
    
    // 加號系列 -> general
    'plus-circle-dotted' => 'general',
    'plus-circle-fill' => 'general',
    'plus-circle' => 'general',
    'plus-square-dotted' => 'general',
    'plus-square-fill' => 'general',
    'plus-square' => 'general',
    'plus' => 'general',
    
    // 電源 -> others
    'power' => 'others',
    
    // 列印機 -> others
    'printer-fill' => 'others',
    'printer' => 'others',
    
    // 拼圖 -> others
    'puzzle-fill' => 'others',
    'puzzle' => 'others',
    
    // 問號系列 -> general
    'question-circle-fill' => 'general',
    'question-circle' => 'general',
    'question-diamond-fill' => 'general',
    'question-diamond' => 'general',
    'question-octagon-fill' => 'general',
    'question-octagon' => 'general',
    'question-square-fill' => 'general',
    'question-square' => 'general',
    'question' => 'general',
    
    // 彩虹 -> others
    'rainbow' => 'others',
    
    // 收據相關 -> files
    'receipt-cutoff' => 'files',
    'receipt' => 'files',
    
    // 訊號強度 -> communications
    'reception-0' => 'communications',
    'reception-1' => 'communications',
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

echo "\n🎉 第十七批手動分類完成！\n";

?>