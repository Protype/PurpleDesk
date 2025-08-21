<?php

/**
 * 第二十八批手動分類 - 60個圖標
 */

echo "📋 第二十八批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第二十八批手動分類定義
$manualCategories = [
    // 明信片 -> communications
    'postcard-fill' => 'communications',
    'postcard-heart-fill' => 'communications',
    'postcard-heart' => 'communications',
    'postcard' => 'communications',
    
    // 愛心搜尋 -> general
    'search-heart-fill' => 'general',
    'search-heart' => 'general',
    
    // 滑桿2 -> ui
    'sliders2-vertical' => 'ui',
    'sliders2' => 'ui',
    
    // 垃圾桶3 -> general
    'trash3-fill' => 'general',
    'trash3' => 'general',
    
    // 情人節 -> others
    'valentine' => 'others',
    'valentine2' => 'others',
    
    // 可調扳手 -> others
    'wrench-adjustable-circle-fill' => 'others',
    'wrench-adjustable-circle' => 'others',
    'wrench-adjustable' => 'others',
    
    // 檔案類型 -> files
    'filetype-json' => 'files',
    'filetype-pptx' => 'files',
    'filetype-xlsx' => 'files',
    
    // 數字圓形系列 -> alphanumeric
    '1-circle-fill' => 'alphanumeric',
    '1-circle' => 'alphanumeric',
    '1-square-fill' => 'alphanumeric',
    '1-square' => 'alphanumeric',
    '2-circle-fill' => 'alphanumeric',
    '2-circle' => 'alphanumeric',
    '2-square-fill' => 'alphanumeric',
    '2-square' => 'alphanumeric',
    '3-circle-fill' => 'alphanumeric',
    '3-circle' => 'alphanumeric',
    '3-square-fill' => 'alphanumeric',
    '3-square' => 'alphanumeric',
    '4-circle-fill' => 'alphanumeric',
    '4-circle' => 'alphanumeric',
    '4-square-fill' => 'alphanumeric',
    '4-square' => 'alphanumeric',
    '5-circle-fill' => 'alphanumeric',
    '5-circle' => 'alphanumeric',
    '5-square-fill' => 'alphanumeric',
    '5-square' => 'alphanumeric',
    '6-circle-fill' => 'alphanumeric',
    '6-circle' => 'alphanumeric',
    '6-square-fill' => 'alphanumeric',
    '6-square' => 'alphanumeric',
    '7-circle-fill' => 'alphanumeric',
    '7-circle' => 'alphanumeric',
    '7-square-fill' => 'alphanumeric',
    '7-square' => 'alphanumeric',
    '8-circle-fill' => 'alphanumeric',
    '8-circle' => 'alphanumeric',
    '8-square-fill' => 'alphanumeric',
    '8-square' => 'alphanumeric',
    '9-circle-fill' => 'alphanumeric',
    '9-circle' => 'alphanumeric',
    '9-square-fill' => 'alphanumeric',
    '9-square' => 'alphanumeric',
    
    // 飛機 -> others
    'airplane-engines-fill' => 'others',
    'airplane-engines' => 'others',
    'airplane-fill' => 'others',
    'airplane' => 'others',
    
    // 語音助手/支付 -> communications
    'alexa' => 'communications',
    'alipay' => 'communications',
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

echo "\n🎉 第二十八批手動分類完成！\n";

?>