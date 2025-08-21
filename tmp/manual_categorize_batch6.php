<?php

/**
 * 第六批手動分類 - 60個圖標
 */

echo "📋 第六批手動圖標分類\n";
echo "=====================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 第六批手動分類定義 - 主要是日曆4、卡片、現金和聊天相關圖標
$manualCategories = [
    // Calendar4 系列 -> general
    'calendar4-event' => 'general',
    'calendar4-range' => 'general',
    'calendar4-week' => 'general',
    'calendar4' => 'general',
    
    // 卡片/UI元件 -> ui
    'card-checklist' => 'ui',
    'card-heading' => 'ui',
    'card-image' => 'ui',
    'card-list' => 'ui',
    'card-text' => 'ui',
    
    // 現金/金錢 -> others
    'cash-stack' => 'others',
    'cash' => 'others',
    
    // 投射/廣播 -> communications
    'cast' => 'communications',
    
    // 聊天/通訊相關 -> communications
    'chat-dots-fill' => 'communications',
    'chat-dots' => 'communications',
    'chat-fill' => 'communications',
    'chat-left-dots-fill' => 'communications',
    'chat-left-dots' => 'communications',
    'chat-left-fill' => 'communications',
    'chat-left-quote-fill' => 'communications',
    'chat-left-quote' => 'communications',
    'chat-left-text-fill' => 'communications',
    'chat-left-text' => 'communications',
    'chat-left' => 'communications',
    'chat-quote-fill' => 'communications',
    'chat-quote' => 'communications',
    'chat-right-dots-fill' => 'communications',
    'chat-right-dots' => 'communications',
    'chat-right-fill' => 'communications',
    'chat-right-quote-fill' => 'communications',
    'chat-right-quote' => 'communications',
    'chat-right-text-fill' => 'communications',
    'chat-right-text' => 'communications',
    'chat-right' => 'communications',
    'chat-square-dots-fill' => 'communications',
    'chat-square-dots' => 'communications',
    'chat-square-fill' => 'communications',
    'chat-square-quote-fill' => 'communications',
    'chat-square-quote' => 'communications',
    'chat-square-text-fill' => 'communications',
    'chat-square-text' => 'communications',
    'chat-square' => 'communications',
    'chat-text-fill' => 'communications',
    'chat-text' => 'communications',
    'chat' => 'communications',
    
    // 檢查/核選 -> general
    'check-all' => 'general',
    'check-circle-fill' => 'general',
    'check-circle' => 'general',
    'check-lg' => 'general',
    'check-square-fill' => 'general',
    'check-square' => 'general',
    'check' => 'general',
    'check2-all' => 'general',
    'check2-circle' => 'general',
    'check2-square' => 'general',
    'check2' => 'general',
    
    // V形符號/導航 -> ui
    'chevron-bar-contract' => 'ui',
    'chevron-bar-down' => 'ui',
    'chevron-bar-expand' => 'ui',
    'chevron-bar-left' => 'ui',
    'chevron-bar-right' => 'ui',
    'chevron-bar-up' => 'ui',
    'chevron-compact-down' => 'ui',
    'chevron-compact-left' => 'ui',
    'chevron-compact-right' => 'ui',
    'chevron-compact-up' => 'ui',
    'chevron-contract' => 'ui',
    'chevron-double-down' => 'ui',
    'chevron-double-left' => 'ui',
    'chevron-double-right' => 'ui',
    'chevron-double-up' => 'ui',
    'chevron-down' => 'ui',
    'chevron-expand' => 'ui',
    'chevron-left' => 'ui',
    'chevron-right' => 'ui',
    'chevron-up' => 'ui',
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

echo "\n🎉 第六批手動分類完成！\n";

?>