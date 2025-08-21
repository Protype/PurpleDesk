<?php

/**
 * 第二十六批手動分類 - 60個圖標
 */

echo "📋 第二十六批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第二十六批手動分類定義
$manualCategories = [
    // 氣球系列 -> others
    'balloon-fill' => 'others',
    'balloon-heart-fill' => 'others',
    'balloon-heart' => 'others',
    'balloon' => 'others',
    
    // 盒子系列 -> others
    'box2-fill' => 'others',
    'box2-heart-fill' => 'others',
    'box2-heart' => 'others',
    'box2' => 'others',
    
    // 大括號星號 -> alphanumeric
    'braces-asterisk' => 'alphanumeric',
    
    // 愛心日曆 -> general
    'calendar2-heart-fill' => 'general',
    'calendar2-heart' => 'general',
    
    // 愛心聊天 -> communications
    'chat-heart-fill' => 'communications',
    'chat-heart' => 'communications',
    'chat-left-heart-fill' => 'communications',
    'chat-left-heart' => 'communications',
    'chat-right-heart-fill' => 'communications',
    'chat-right-heart' => 'communications',
    'chat-square-heart-fill' => 'communications',
    'chat-square-heart' => 'communications',
    
    // 剪貼板系列 -> files
    'clipboard-check-fill' => 'files',
    'clipboard-data-fill' => 'files',
    'clipboard-fill' => 'files',
    'clipboard-heart-fill' => 'files',
    'clipboard-heart' => 'files',
    'clipboard-minus-fill' => 'files',
    'clipboard-plus-fill' => 'files',
    'clipboard-pulse' => 'files',
    'clipboard-x-fill' => 'files',
    'clipboard2-check-fill' => 'files',
    'clipboard2-check' => 'files',
    'clipboard2-data-fill' => 'files',
    'clipboard2-data' => 'files',
    'clipboard2-fill' => 'files',
    'clipboard2-heart-fill' => 'files',
    'clipboard2-heart' => 'files',
    'clipboard2-minus-fill' => 'files',
    'clipboard2-minus' => 'files',
    'clipboard2-plus-fill' => 'files',
    'clipboard2-plus' => 'files',
    'clipboard2-pulse-fill' => 'files',
    'clipboard2-pulse' => 'files',
    'clipboard2-x-fill' => 'files',
    'clipboard2-x' => 'files',
    'clipboard2' => 'files',
    
    // 表情符號親吻 -> people
    'emoji-kiss-fill' => 'people',
    'emoji-kiss' => 'people',
    
    // 愛心信封 -> communications
    'envelope-heart-fill' => 'communications',
    'envelope-heart' => 'communications',
    'envelope-open-heart-fill' => 'communications',
    'envelope-open-heart' => 'communications',
    'envelope-paper-fill' => 'communications',
    'envelope-paper-heart-fill' => 'communications',
    'envelope-paper-heart' => 'communications',
    'envelope-paper' => 'communications',
    
    // 檔案類型系列 -> files
    'filetype-aac' => 'files',
    'filetype-ai' => 'files',
    'filetype-bmp' => 'files',
    'filetype-cs' => 'files',
    'filetype-css' => 'files',
    'filetype-csv' => 'files',
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

echo "\n🎉 第二十六批手動分類完成！\n";

?>