<?php

/**
 * 第三十二批手動分類 - 60個圖標 (邁向完成！)
 */

echo "📋 第三十二批手動圖標分類 - 邁向完成！\n";
echo "====================================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第三十二批手動分類定義
$manualCategories = [
    // 建築物系列 (繼續) -> others
    'building-fill-lock' => 'others',
    'building-fill-slash' => 'others',
    'building-fill-up' => 'others',
    'building-fill-x' => 'others',
    'building-fill' => 'others',
    'building-gear' => 'others',
    'building-lock' => 'others',
    'building-slash' => 'others',
    'building-up' => 'others',
    'building-x' => 'others',
    'buildings-fill' => 'others',
    'buildings' => 'others',
    
    // 巴士前面 -> others
    'bus-front-fill' => 'others',
    'bus-front' => 'others',
    
    // 電動車前面 -> others
    'ev-front-fill' => 'others',
    'ev-front' => 'others',
    
    // 地球區域 -> communications
    'globe-americas' => 'communications',
    'globe-asia-australia' => 'communications',
    'globe-central-south-asia' => 'communications',
    'globe-europe-africa' => 'communications',
    
    // 房屋系列 -> others
    'house-add-fill' => 'others',
    'house-add' => 'others',
    'house-check-fill' => 'others',
    'house-check' => 'others',
    'house-dash-fill' => 'others',
    'house-dash' => 'others',
    'house-down-fill' => 'others',
    'house-down' => 'others',
    'house-exclamation-fill' => 'others',
    'house-exclamation' => 'others',
    'house-gear-fill' => 'others',
    'house-gear' => 'others',
    'house-lock-fill' => 'others',
    'house-lock' => 'others',
    'house-slash-fill' => 'others',
    'house-slash' => 'others',
    'house-up-fill' => 'others',
    'house-up' => 'others',
    'house-x-fill' => 'others',
    'house-x' => 'others',
    
    // 人物系列 -> people
    'person-add' => 'people',
    'person-down' => 'people',
    'person-exclamation' => 'people',
    'person-fill-add' => 'people',
    'person-fill-check' => 'people',
    'person-fill-dash' => 'people',
    'person-fill-down' => 'people',
    'person-fill-exclamation' => 'people',
    'person-fill-gear' => 'people',
    'person-fill-lock' => 'people',
    'person-fill-slash' => 'people',
    'person-fill-up' => 'people',
    'person-fill-x' => 'people',
    'person-gear' => 'people',
    'person-lock' => 'people',
    'person-slash' => 'people',
    'person-up' => 'people',
    
    // 滑板車 -> others
    'scooter' => 'others',
    
    // 計程車前面 -> others
    'taxi-front-fill' => 'others',
    'taxi-front' => 'others',
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

echo "\n🎉 第三十二批手動分類完成！\n";
echo "🏁 我們正在接近終點線！\n";

?>