<?php

/**
 * 第九批手動分類 - 60個圖標
 */

echo "📋 第九批手動圖標分類\n";
echo "=====================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第九批手動分類定義
$manualCategories = [
    // 光碟 -> media
    'disc-fill' => 'media',
    'disc' => 'media',
    
    // Discord -> communications
    'discord' => 'communications',
    
    // 畫架 -> others
    'easel-fill' => 'others',
    'easel' => 'others',
    
    // 表情符號 -> people
    'emoji-angry-fill' => 'people',
    'emoji-angry' => 'people',
    'emoji-dizzy-fill' => 'people',
    'emoji-dizzy' => 'people',
    'emoji-expressionless-fill' => 'people',
    'emoji-expressionless' => 'people',
    'emoji-frown-fill' => 'people',
    'emoji-frown' => 'people',
    'emoji-heart-eyes-fill' => 'people',
    'emoji-heart-eyes' => 'people',
    'emoji-laughing-fill' => 'people',
    'emoji-laughing' => 'people',
    'emoji-neutral-fill' => 'people',
    'emoji-neutral' => 'people',
    'emoji-smile-fill' => 'people',
    'emoji-smile-upside-down-fill' => 'people',
    'emoji-smile-upside-down' => 'people',
    'emoji-smile' => 'people',
    'emoji-sunglasses-fill' => 'people',
    'emoji-sunglasses' => 'people',
    'emoji-wink-fill' => 'people',
    'emoji-wink' => 'people',
    
    // 信封/郵件 -> communications
    'envelope-fill' => 'communications',
    'envelope-open-fill' => 'communications',
    'envelope-open' => 'communications',
    'envelope' => 'communications',
    
    // 橡皮擦 -> others
    'eraser-fill' => 'others',
    'eraser' => 'others',
    
    // 感嘆號 -> general
    'exclamation-circle-fill' => 'general',
    'exclamation-circle' => 'general',
    'exclamation-diamond-fill' => 'general',
    'exclamation-diamond' => 'general',
    'exclamation-lg' => 'general',
    'exclamation-octagon-fill' => 'general',
    'exclamation-octagon' => 'general',
    'exclamation-square-fill' => 'general',
    'exclamation-square' => 'general',
    'exclamation-triangle-fill' => 'general',
    'exclamation-triangle' => 'general',
    'exclamation' => 'general',
    
    // 排除 -> ui
    'exclude' => 'ui',
    
    // 顯式標記 -> others
    'explicit-fill' => 'others',
    'explicit' => 'others',
    
    // 曝光 -> media
    'exposure' => 'media',
    
    // 眼睛相關 -> general
    'eye-fill' => 'general',
    'eye-slash-fill' => 'general',
    'eye-slash' => 'general',
    'eye' => 'general',
    
    // 滴管工具 -> others
    'eyedropper' => 'others',
    
    // 眼鏡 -> people
    'eyeglasses' => 'people',
    
    // 風扇 -> others
    'fan' => 'others',
    
    // 快進 -> media
    'fast-forward-btn-fill' => 'media',
    'fast-forward-btn' => 'media',
    
    // 羽毛 -> others
    'feather' => 'others',
    'feather2' => 'others',
    
    // 檔案相關 -> files
    'file-arrow-down-fill' => 'files',
    'file-arrow-down' => 'files',
    'file-arrow-up-fill' => 'files',
    'file-arrow-up' => 'files',
    'file-bar-graph-fill' => 'files',
    'file-bar-graph' => 'files',
    'file-binary-fill' => 'files',
    'file-binary' => 'files',
    'file-break-fill' => 'files',
    'file-break' => 'files',
    'file-check-fill' => 'files',
    'file-check' => 'files',
    'file-code-fill' => 'files',
    'file-code' => 'files',
    'file-diff-fill' => 'files',
    'file-diff' => 'files',
    'file-earmark-arrow-down-fill' => 'files',
    'file-earmark-arrow-down' => 'files',
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

echo "\n🎉 第九批手動分類完成！\n";

?>