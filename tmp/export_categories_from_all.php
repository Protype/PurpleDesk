<?php

/**
 * 從 all.php 匯出圖標到各分類檔案
 * 根據圖標的 category 欄位重新分配到各分類檔案中
 */

echo "🚀 開始從 all.php 匯出圖標到各分類檔案\n";
echo "================================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

// 定義分類配置
$categories = [
    'general' => [
        'id' => 'general',
        'name' => '通用圖標',
        'description' => '最常用的基礎圖標',
        'priority' => 'immediate'
    ],
    'ui' => [
        'id' => 'ui',
        'name' => 'UI 元件',
        'description' => '用戶界面相關圖標',
        'priority' => 'high'
    ],
    'communications' => [
        'id' => 'communications',
        'name' => '通訊與社群',
        'description' => '通訊、社群媒體相關圖標',
        'priority' => 'high'
    ],
    'files' => [
        'id' => 'files',
        'name' => '檔案格式',
        'description' => '各種檔案類型圖標',
        'priority' => 'normal'
    ],
    'media' => [
        'id' => 'media',
        'name' => '媒體控制',
        'description' => '音視頻播放控制圖標',
        'priority' => 'normal'
    ],
    'people' => [
        'id' => 'people',
        'name' => '人物與角色',
        'description' => '人物、用戶相關圖標',
        'priority' => 'normal'
    ],
    'alphanumeric' => [
        'id' => 'alphanumeric',
        'name' => '字母數字',
        'description' => '字母、數字、符號圖標',
        'priority' => 'low'
    ],
    'others' => [
        'id' => 'others',
        'name' => '其他圖標',
        'description' => '未分類的其他圖標',
        'priority' => 'low'
    ]
];

// 初始化分類數據結構
$categoryData = [];
foreach ($categories as $categoryId => $categoryConfig) {
    $categoryData[$categoryId] = [
        'config' => $categoryConfig,
        'icons' => []
    ];
}

// 統計各分類圖標數量
$stats = [];
foreach (array_keys($categories) as $categoryId) {
    $stats[$categoryId] = 0;
}

// 處理所有圖標，按 category 欄位分類
echo "📊 開始分類圖標...\n";
foreach ($allConfig['icons'] as $icon) {
    $iconCategory = $icon['category'] ?? 'all';
    
    // 如果圖標還是 'all' 分類，歸類到 'others'
    if ($iconCategory === 'all') {
        $iconCategory = 'others';
        echo "⚠️  {$icon['name']} 仍為 'all' 分類，歸類到 'others'\n";
    }
    
    // 檢查分類是否存在
    if (!isset($categoryData[$iconCategory])) {
        echo "⚠️  未知分類 '{$iconCategory}' 的圖標: {$icon['name']}，歸類到 'others'\n";
        $iconCategory = 'others';
    }
    
    // 添加到對應分類
    $categoryData[$iconCategory]['icons'][] = $icon;
    $stats[$iconCategory]++;
}

echo "\n📈 分類統計結果:\n";
$totalIcons = 0;
foreach ($stats as $categoryId => $count) {
    echo sprintf("  %-15s: %4d 個圖標\n", $categories[$categoryId]['name'], $count);
    $totalIcons += $count;
}
echo sprintf("  %-15s: %4d 個圖標\n", "總計", $totalIcons);

// 生成並寫入各分類檔案
echo "\n💾 寫入分類檔案...\n";
foreach ($categoryData as $categoryId => $data) {
    $categoryConfig = $data['config'];
    $categoryIcons = $data['icons'];
    
    // 構建檔案內容
    $fileContent = [
        '<?php',
        '',
        'return [',
        "    'id' => '{$categoryConfig['id']}',",
        "    'name' => '{$categoryConfig['name']}',",
        "    'description' => '{$categoryConfig['description']}',",
        "    'priority' => '{$categoryConfig['priority']}',",
        '',
        '    \'icons\' => ['
    ];
    
    // 添加圖標數據
    foreach ($categoryIcons as $icon) {
        $fileContent[] = '        [';
        $fileContent[] = "            'name' => '{$icon['name']}',";
        
        if (isset($icon['displayName'])) {
            $displayName = addslashes($icon['displayName']);
            $fileContent[] = "            'displayName' => '{$displayName}',";
        }
        
        $fileContent[] = "            'class' => '{$icon['class']}',";
        
        if (isset($icon['keywords']) && is_array($icon['keywords'])) {
            $keywords = json_encode($icon['keywords']);
            $fileContent[] = "            'keywords' => {$keywords},";
        }
        
        if (isset($icon['type'])) {
            $fileContent[] = "            'type' => '{$icon['type']}',";
        }
        
        $fileContent[] = "            'category' => '{$icon['category']}',";
        
        if (isset($icon['variants']) && is_array($icon['variants'])) {
            $fileContent[] = '            \'variants\' => [';
            foreach ($icon['variants'] as $variantType => $variantData) {
                $variantClass = $variantData['class'];
                $fileContent[] = "                '{$variantType}' => ['class' => '{$variantClass}'],";
            }
            $fileContent[] = '            ]';
        }
        
        $fileContent[] = '        ],';
    }
    
    $fileContent[] = '    ]';
    $fileContent[] = '];';
    
    // 寫入檔案
    $filePath = __DIR__ . "/../config/icon/bootstrap-icons/{$categoryId}.php";
    $content = implode("\n", $fileContent);
    
    if (file_put_contents($filePath, $content)) {
        echo "✅ {$categoryId}.php - {$stats[$categoryId]} 個圖標\n";
    } else {
        echo "❌ 寫入 {$categoryId}.php 失敗\n";
    }
}

echo "\n🔄 清除 Laravel 快取...\n";
exec('cd ' . dirname(__DIR__) . ' && php8.4 artisan cache:clear', $output, $returnCode);

if ($returnCode === 0) {
    echo "✅ 快取清除成功\n";
} else {
    echo "⚠️  快取清除可能失敗\n";
}

echo "\n🎉 Bootstrap Icons 分類檔案匯出完成！\n";
echo "📂 所有圖標已按分類重新整理到對應檔案中\n";
echo "🚀 API 端點現在應該可以正確返回各分類的圖標數據\n";

?>