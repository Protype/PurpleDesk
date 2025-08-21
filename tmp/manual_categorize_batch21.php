<?php

/**
 * 第二十一批手動分類 - 60個圖標
 */

echo "📋 第二十一批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第二十一批手動分類定義
$manualCategories = [
    // 文字相關 -> ui
    'text-indent-right' => 'ui',
    'text-left' => 'ui',
    'text-paragraph' => 'ui',
    'text-right' => 'ui',
    
    // 文字區域 -> ui
    'textarea-resize' => 'ui',
    'textarea-t' => 'ui',
    'textarea' => 'ui',
    
    // 溫度計/天氣 -> others
    'thermometer-half' => 'others',
    'thermometer-high' => 'others',
    'thermometer-low' => 'others',
    'thermometer-snow' => 'others',
    'thermometer-sun' => 'others',
    'thermometer' => 'others',
    
    // 三個點/選單 -> ui
    'three-dots-vertical' => 'ui',
    'three-dots' => 'ui',
    
    // 切換開關 -> ui
    'toggle-off' => 'ui',
    'toggle-on' => 'ui',
    'toggle2-off' => 'ui',
    'toggle2-on' => 'ui',
    'toggles' => 'ui',
    'toggles2' => 'ui',
    
    // 工具 -> others
    'tools' => 'others',
    
    // 龍捲風 -> others
    'tornado' => 'others',
    
    // 垃圾桶 -> general
    'trash-fill' => 'general',
    'trash' => 'general',
    'trash2-fill' => 'general',
    'trash2' => 'general',
    
    // 樹木 -> others
    'tree-fill' => 'others',
    'tree' => 'others',
    
    // 三角形 -> ui
    'triangle-fill' => 'ui',
    'triangle-half' => 'ui',
    'triangle' => 'ui',
    
    // 獎杯 -> others
    'trophy-fill' => 'others',
    'trophy' => 'others',
    
    // 熱帶風暴 -> others
    'tropical-storm' => 'others',
    
    // 卡車 -> others
    'truck-flatbed' => 'others',
    'truck' => 'others',
    
    // 海嘯 -> others
    'tsunami' => 'others',
    
    // 電視 -> media
    'tv-fill' => 'media',
    'tv' => 'media',
    
    // 社群媒體 -> communications
    'twitch' => 'communications',
    'twitter' => 'communications',
    
    // 字體樣式相關 -> alphanumeric
    'type-bold' => 'alphanumeric',
    'type-h1' => 'alphanumeric',
    'type-h2' => 'alphanumeric',
    'type-h3' => 'alphanumeric',
    'type-italic' => 'alphanumeric',
    'type-strikethrough' => 'alphanumeric',
    'type-underline' => 'alphanumeric',
    'type' => 'alphanumeric',
    
    // UI 元件 -> ui
    'ui-checks-grid' => 'ui',
    'ui-checks' => 'ui',
    'ui-radios-grid' => 'ui',
    'ui-radios' => 'ui',
    
    // 雨傘 -> others
    'umbrella-fill' => 'others',
    'umbrella' => 'others',
    
    // 聯合 -> ui
    'union' => 'ui',
    
    // 解鎖 -> general
    'unlock-fill' => 'general',
    'unlock' => 'general',
    
    // 條碼掃描 -> others
    'upc-scan' => 'others',
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

echo "\n🎉 第二十一批手動分類完成！\n";

?>