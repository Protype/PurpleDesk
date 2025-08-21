<?php

/**
 * 第十九批手動分類 - 60個圖標
 */

echo "📋 第十九批手動圖標分類\n";
echo "======================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第十九批手動分類定義
$manualCategories = [
    // 跳過系列 -> media
    'skip-backward-btn' => 'media',
    'skip-backward-circle-fill' => 'media',
    'skip-backward-circle' => 'media',
    'skip-backward-fill' => 'media',
    'skip-backward' => 'media',
    'skip-end-btn-fill' => 'media',
    'skip-end-btn' => 'media',
    'skip-end-circle-fill' => 'media',
    'skip-end-circle' => 'media',
    'skip-end-fill' => 'media',
    'skip-end' => 'media',
    'skip-forward-btn-fill' => 'media',
    'skip-forward-btn' => 'media',
    'skip-forward-circle-fill' => 'media',
    'skip-forward-circle' => 'media',
    'skip-forward-fill' => 'media',
    'skip-forward' => 'media',
    'skip-start-btn-fill' => 'media',
    'skip-start-btn' => 'media',
    'skip-start-circle-fill' => 'media',
    'skip-start-circle' => 'media',
    'skip-start-fill' => 'media',
    'skip-start' => 'media',
    
    // Slack -> communications
    'slack' => 'communications',
    
    // 斜線系列 -> ui
    'slash-circle-fill' => 'ui',
    'slash-circle' => 'ui',
    'slash-square-fill' => 'ui',
    'slash-square' => 'ui',
    'slash' => 'ui',
    
    // 滑桿 -> ui
    'sliders' => 'ui',
    
    // 智慧手錶 -> others
    'smartwatch' => 'others',
    
    // 雪花/天氣 -> others
    'snow' => 'others',
    'snow2' => 'others',
    'snow3' => 'others',
    
    // 排序系列 -> ui
    'sort-alpha-down-alt' => 'ui',
    'sort-alpha-down' => 'ui',
    'sort-alpha-up-alt' => 'ui',
    'sort-alpha-up' => 'ui',
    'sort-down-alt' => 'ui',
    'sort-down' => 'ui',
    'sort-numeric-down-alt' => 'ui',
    'sort-numeric-down' => 'ui',
    'sort-numeric-up-alt' => 'ui',
    'sort-numeric-up' => 'ui',
    'sort-up-alt' => 'ui',
    'sort-up' => 'ui',
    
    // 聲波 -> media
    'soundwave' => 'media',
    
    // 揚聲器 -> media
    'speaker-fill' => 'media',
    'speaker' => 'media',
    
    // 速度計 -> others
    'speedometer' => 'others',
    'speedometer2' => 'others',
    
    // 拼字檢查 -> alphanumeric
    'spellcheck' => 'alphanumeric',
    
    // 正方形 -> ui
    'square-fill' => 'ui',
    'square-half' => 'ui',
    'square' => 'ui',
    
    // 堆疊 -> ui
    'stack' => 'ui',
    
    // 星星系列 -> general
    'star-fill' => 'general',
    'star-half' => 'general',
    'star' => 'general',
    'stars' => 'general',
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

echo "\n🎉 第十九批手動分類完成！\n";

?>