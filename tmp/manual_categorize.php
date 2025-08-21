<?php

/**
 * 手動分類工具
 * 一次處理 30 個圖標，手動指定分類
 */

echo "📋 手動圖標分類工具\n";
echo "===================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 找出前 30 個仍為 'all' 分類的圖標
$allIcons = [];
foreach ($allConfig['icons'] as $icon) {
    if ($icon['category'] === 'all') {
        $allIcons[] = $icon;
        if (count($allIcons) >= 30) break;
    }
}

if (empty($allIcons)) {
    echo "🎉 所有圖標都已分類完成！\n";
    exit(0);
}

echo "📊 找到 " . count($allIcons) . " 個待分類圖標\n\n";

// 手動分類定義
$manualCategories = [
    // 交通號誌 -> others
    'sign-turn-slight-left' => 'others',
    'sign-turn-slight-right-fill' => 'others', 
    'sign-turn-slight-right' => 'others',
    'sign-yield-fill' => 'others',
    'sign-yield' => 'others',
    'sign-dead-end-fill' => 'others',
    'sign-dead-end' => 'others',
    'sign-do-not-enter-fill' => 'others',
    
    // 加油站/充電站 -> others
    'ev-station-fill' => 'others',
    'ev-station' => 'others',
    'fuel-pump-diesel-fill' => 'others',
    'fuel-pump-diesel' => 'others',
    'fuel-pump-fill' => 'others',
    'fuel-pump' => 'others',
    
    // 數字圖標 -> alphanumeric
    '0-circle-fill' => 'alphanumeric',
    '0-circle' => 'alphanumeric',
    '0-square-fill' => 'alphanumeric',
    '0-square' => 'alphanumeric',
    
    // 火箭 -> others
    'rocket-fill' => 'others',
    'rocket-takeoff-fill' => 'others',
    'rocket-takeoff' => 'others',
    'rocket' => 'others',
    
    // 品牌/服務 -> others
    'stripe' => 'others',
    'trello' => 'others',
    
    // 文字格式 -> alphanumeric
    'subscript' => 'alphanumeric',
    'superscript' => 'alphanumeric',
    'text-wrap' => 'alphanumeric',
    'regex' => 'alphanumeric',
    
    // 郵件 -> communications
    'envelope-at-fill' => 'communications',
    'envelope-at' => 'communications',
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

// 寫入檔案
$phpContent = "<?php\n\nreturn " . var_export($allConfig, true) . ";\n";

if (file_put_contents($allConfigPath, $phpContent)) {
    echo "✅ 成功更新 all.php\n";
    
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

echo "\n🎉 手動分類完成！\n";

?>