<?php

/**
 * 第十四批手動分類 - 60個圖標
 */

echo "📋 第十四批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第十四批手動分類定義
$manualCategories = [
    // 房屋相關 -> others
    'house-door-fill' => 'others',
    'house-door' => 'others',
    'house-fill' => 'others',
    'house' => 'others',
    
    // 水平線 -> ui
    'hr' => 'ui',
    
    // 颶風/天氣 -> others
    'hurricane' => 'others',
    
    // 圖片/影像相關 -> media
    'image-alt' => 'media',
    'image-fill' => 'media',
    'image' => 'media',
    'images' => 'media',
    
    // 收件箱相關 -> communications
    'inbox-fill' => 'communications',
    'inbox' => 'communications',
    'inboxes-fill' => 'communications',
    'inboxes' => 'communications',
    
    // 資訊圖標 -> general
    'info-circle-fill' => 'general',
    'info-circle' => 'general',
    'info-square-fill' => 'general',
    'info-square' => 'general',
    'info' => 'general',
    
    // 輸入游標 -> ui
    'input-cursor-text' => 'ui',
    'input-cursor' => 'ui',
    
    // 社群媒體 -> communications
    'instagram' => 'communications',
    
    // 交集 -> ui
    'intersect' => 'ui',
    
    // 日誌/期刊系列 -> files
    'journal-album' => 'files',
    'journal-arrow-down' => 'files',
    'journal-arrow-up' => 'files',
    'journal-bookmark-fill' => 'files',
    'journal-bookmark' => 'files',
    'journal-check' => 'files',
    'journal-code' => 'files',
    'journal-medical' => 'files',
    'journal-minus' => 'files',
    'journal-plus' => 'files',
    'journal-richtext' => 'files',
    'journal-text' => 'files',
    'journal-x' => 'files',
    'journal' => 'files',
    'journals' => 'files',
    
    // 遊戲手把 -> others
    'joystick' => 'others',
    
    // 對齊方式 -> ui
    'justify-left' => 'ui',
    'justify-right' => 'ui',
    'justify' => 'ui',
    
    // 看板管理 -> ui
    'kanban-fill' => 'ui',
    'kanban' => 'ui',
    
    // 鑰匙/安全 -> general
    'key-fill' => 'general',
    'key' => 'general',
    
    // 鍵盤 -> others
    'keyboard-fill' => 'others',
    'keyboard' => 'others',
    
    // 梯子 -> others
    'ladder' => 'others',
    
    // 燈具 -> others
    'lamp-fill' => 'others',
    'lamp' => 'others',
    
    // 筆電 -> others
    'laptop-fill' => 'others',
    'laptop' => 'others',
    
    // 圖層相關 -> ui
    'layer-backward' => 'ui',
    'layer-forward' => 'ui',
    'layers-fill' => 'ui',
    'layers-half' => 'ui',
    'layers' => 'ui',
    
    // 佈局相關 -> ui
    'layout-sidebar-inset-reverse' => 'ui',
    'layout-sidebar-inset' => 'ui',
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

echo "\n🎉 第十四批手動分類完成！\n";

?>