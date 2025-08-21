<?php

/**
 * 第二十批手動分類 - 60個圖標
 */

echo "📋 第二十批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第二十批手動分類定義
$manualCategories = [
    // 便利貼/貼紙 -> others
    'stickies-fill' => 'others',
    'stickies' => 'others',
    'sticky-fill' => 'others',
    'sticky' => 'others',
    
    // 停止相關 -> media
    'stop-btn-fill' => 'media',
    'stop-btn' => 'media',
    'stop-circle-fill' => 'media',
    'stop-circle' => 'media',
    'stop-fill' => 'media',
    'stop' => 'media',
    
    // 交通燈 -> others
    'stoplights-fill' => 'others',
    'stoplights' => 'others',
    
    // 碼錶 -> general
    'stopwatch-fill' => 'general',
    'stopwatch' => 'general',
    
    // 減號 -> general
    'subtract' => 'general',
    
    // 撲克牌花色 -> others
    'suit-club-fill' => 'others',
    'suit-club' => 'others',
    'suit-diamond-fill' => 'others',
    'suit-diamond' => 'others',
    'suit-heart-fill' => 'others',
    'suit-heart' => 'others',
    'suit-spade-fill' => 'others',
    'suit-spade' => 'others',
    
    // 太陽/天體 -> others
    'sun-fill' => 'others',
    'sun' => 'others',
    'sunrise-fill' => 'others',
    'sunrise' => 'others',
    'sunset-fill' => 'others',
    'sunset' => 'others',
    
    // 太陽眼鏡 -> people
    'sunglasses' => 'people',
    
    // 對稱相關 -> ui
    'symmetry-horizontal' => 'ui',
    'symmetry-vertical' => 'ui',
    
    // 表格 -> ui
    'table' => 'ui',
    
    // 平板電腦 -> others
    'tablet-fill' => 'others',
    'tablet-landscape-fill' => 'others',
    'tablet-landscape' => 'others',
    'tablet' => 'others',
    
    // 標籤 -> general
    'tag-fill' => 'general',
    'tag' => 'general',
    'tags-fill' => 'general',
    'tags' => 'general',
    
    // Telegram -> communications
    'telegram' => 'communications',
    
    // 電話系列 -> communications
    'telephone-fill' => 'communications',
    'telephone-forward-fill' => 'communications',
    'telephone-forward' => 'communications',
    'telephone-inbound-fill' => 'communications',
    'telephone-inbound' => 'communications',
    'telephone-minus-fill' => 'communications',
    'telephone-minus' => 'communications',
    'telephone-outbound-fill' => 'communications',
    'telephone-outbound' => 'communications',
    'telephone-plus-fill' => 'communications',
    'telephone-plus' => 'communications',
    'telephone-x-fill' => 'communications',
    'telephone-x' => 'communications',
    'telephone' => 'communications',
    
    // 終端機 -> others
    'terminal-fill' => 'others',
    'terminal' => 'others',
    
    // 文字對齊相關 -> ui
    'text-center' => 'ui',
    'text-indent-left' => 'ui',
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

echo "\n🎉 第二十批手動分類完成！\n";

?>