<?php

/**
 * 第三十一批手動分類 - 60個圖標 (最終衝刺階段！)
 */

echo "📋 第三十一批手動圖標分類 - 最終衝刺階段！\n";
echo "=========================================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第三十一批手動分類定義 - 最終衝刺階段
$manualCategories = [
    // 交通標誌系列 -> others
    'sign-turn-slight-right' => 'others',
    'sign-yield-fill' => 'others',
    'sign-yield' => 'others',
    'sign-dead-end-fill' => 'others',
    'sign-dead-end' => 'others',
    'sign-do-not-enter-fill' => 'others',
    'sign-do-not-enter' => 'others',
    'sign-intersection-fill' => 'others',
    'sign-intersection-side-fill' => 'others',
    'sign-intersection-side' => 'others',
    'sign-intersection-t-fill' => 'others',
    'sign-intersection-t' => 'others',
    'sign-intersection-y-fill' => 'others',
    'sign-intersection-y' => 'others',
    'sign-intersection' => 'others',
    'sign-merge-left-fill' => 'others',
    'sign-merge-left' => 'others',
    'sign-merge-right-fill' => 'others',
    'sign-merge-right' => 'others',
    'sign-no-left-turn-fill' => 'others',
    'sign-no-left-turn' => 'others',
    'sign-no-parking-fill' => 'others',
    'sign-no-parking' => 'others',
    'sign-no-right-turn-fill' => 'others',
    'sign-no-right-turn' => 'others',
    'sign-railroad-fill' => 'others',
    'sign-railroad' => 'others',
    
    // 電動車充電站 -> others
    'ev-station-fill' => 'others',
    'ev-station' => 'others',
    
    // 加油站/燃料 -> others
    'fuel-pump-diesel-fill' => 'others',
    'fuel-pump-diesel' => 'others',
    'fuel-pump-fill' => 'others',
    'fuel-pump' => 'others',
    
    // 數字 0 -> alphanumeric
    '0-circle-fill' => 'alphanumeric',
    '0-circle' => 'alphanumeric',
    '0-square-fill' => 'alphanumeric',
    '0-square' => 'alphanumeric',
    
    // 火箭 -> others
    'rocket-fill' => 'others',
    'rocket-takeoff-fill' => 'others',
    'rocket-takeoff' => 'others',
    'rocket' => 'others',
    
    // Stripe -> communications
    'stripe' => 'communications',
    
    // 上下標 -> alphanumeric
    'subscript' => 'alphanumeric',
    'superscript' => 'alphanumeric',
    
    // Trello -> communications
    'trello' => 'communications',
    
    // 電子郵件@ -> communications
    'envelope-at-fill' => 'communications',
    'envelope-at' => 'communications',
    
    // 正規表達式 -> alphanumeric
    'regex' => 'alphanumeric',
    
    // 文字換行 -> ui
    'text-wrap' => 'ui',
    
    // 建築物系列 -> others
    'building-add' => 'others',
    'building-check' => 'others',
    'building-dash' => 'others',
    'building-down' => 'others',
    'building-exclamation' => 'others',
    'building-fill-add' => 'others',
    'building-fill-check' => 'others',
    'building-fill-dash' => 'others',
    'building-fill-down' => 'others',
    'building-fill-exclamation' => 'others',
    'building-fill-gear' => 'others',
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

echo "\n🎉 第三十一批手動分類完成！\n";
echo "🚀 進入最終衝刺階段！距離100%越來越近！\n";

?>