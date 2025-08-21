<?php

/**
 * 第三十批手動分類 - 30個圖標 (突破90%大關！)
 */

echo "📋 第三十批手動圖標分類 - 突破90%大關！\n";
echo "=======================================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第三十批手動分類定義 - 突破90%的歷史性批次
$manualCategories = [
    // 倒帶 -> media
    'rewind-fill' => 'media',
    'rewind' => 'media',
    
    // 火車系列 -> others
    'train-freight-front-fill' => 'others',
    'train-freight-front' => 'others',
    'train-front-fill' => 'others',
    'train-front' => 'others',
    'train-lightrail-front-fill' => 'others',
    'train-lightrail-front' => 'others',
    
    // 卡車前面 -> others
    'truck-front-fill' => 'others',
    'truck-front' => 'others',
    
    // Ubuntu -> communications
    'ubuntu' => 'communications',
    
    // 反縮排 -> ui
    'unindent' => 'ui',
    
    // Unity -> communications
    'unity' => 'communications',
    
    // 無障礙通用設計 -> general
    'universal-access-circle' => 'general',
    'universal-access' => 'general',
    
    // 病毒 -> others
    'virus' => 'others',
    'virus2' => 'others',
    
    // 社群平台 -> communications
    'wechat' => 'communications',
    'yelp' => 'communications',
    
    // 交通標誌系列 -> others
    'sign-stop-fill' => 'others',
    'sign-stop-lights-fill' => 'others',
    'sign-stop-lights' => 'others',
    'sign-stop' => 'others',
    'sign-turn-left-fill' => 'others',
    'sign-turn-left' => 'others',
    'sign-turn-right-fill' => 'others',
    'sign-turn-right' => 'others',
    'sign-turn-slight-left-fill' => 'others',
    'sign-turn-slight-left' => 'others',
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

echo "\n🎉 第三十批手動分類完成！\n";
echo "🏆 歷史性時刻：突破90%大關！\n";

?>