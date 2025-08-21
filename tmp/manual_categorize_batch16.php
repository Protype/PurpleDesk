<?php

/**
 * 第十六批手動分類 - 60個圖標 (突破50%里程碑！)
 */

echo "📋 第十六批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第十六批手動分類定義
$manualCategories = [
    // 滑鼠系列 -> others
    'mouse2-fill' => 'others',
    'mouse2' => 'others',
    'mouse3-fill' => 'others',
    'mouse3' => 'others',
    
    // 音樂相關 -> media
    'music-note-beamed' => 'media',
    'music-note-list' => 'media',
    'music-note' => 'media',
    'music-player-fill' => 'media',
    'music-player' => 'media',
    
    // 報紙 -> communications
    'newspaper' => 'communications',
    
    // 節點相關 -> ui
    'node-minus-fill' => 'ui',
    'node-minus' => 'ui',
    'node-plus-fill' => 'ui',
    'node-plus' => 'ui',
    
    // 螺帽 -> others
    'nut-fill' => 'others',
    'nut' => 'others',
    
    // 八角形 -> ui
    'octagon-fill' => 'ui',
    'octagon-half' => 'ui',
    'octagon' => 'ui',
    
    // Option 鍵 -> alphanumeric
    'option' => 'alphanumeric',
    
    // 插座 -> others
    'outlet' => 'others',
    
    // 繪圖工具 -> others
    'paint-bucket' => 'others',
    'palette-fill' => 'others',
    'palette' => 'others',
    'palette2' => 'others',
    
    // 迴紋針 -> others
    'paperclip' => 'others',
    
    // 段落 -> alphanumeric
    'paragraph' => 'alphanumeric',
    
    // 補丁系列 -> ui
    'patch-check-fill' => 'ui',
    'patch-check' => 'ui',
    'patch-exclamation-fill' => 'ui',
    'patch-exclamation' => 'ui',
    'patch-minus-fill' => 'ui',
    'patch-minus' => 'ui',
    'patch-plus-fill' => 'ui',
    'patch-plus' => 'ui',
    'patch-question-fill' => 'ui',
    'patch-question' => 'ui',
    
    // 暫停相關 -> media
    'pause-btn-fill' => 'media',
    'pause-btn' => 'media',
    'pause-circle-fill' => 'media',
    'pause-circle' => 'media',
    'pause-fill' => 'media',
    'pause' => 'media',
    
    // 和平符號 -> others
    'peace-fill' => 'others',
    'peace' => 'others',
    
    // 筆/鉛筆相關 -> others
    'pen-fill' => 'others',
    'pen' => 'others',
    'pencil-fill' => 'others',
    'pencil-square' => 'others',
    'pencil' => 'others',
    
    // 五角形 -> ui
    'pentagon-fill' => 'ui',
    'pentagon-half' => 'ui',
    'pentagon' => 'ui',
    
    // 人群/人物相關 -> people
    'people-fill' => 'people',
    'people' => 'people',
    'person-badge-fill' => 'people',
    'person-badge' => 'people',
    'person-bounding-box' => 'people',
    'person-check-fill' => 'people',
    
    // 百分比符號 -> alphanumeric
    'percent' => 'alphanumeric',
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

echo "\n🎉 第十六批手動分類完成！\n";
echo "🏆 恭喜！我們即將突破50%里程碑！\n";

?>