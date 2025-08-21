<?php

/**
 * 修正版本：從 all.php 匯出圖標到各分類檔案
 * 修正變體處理邏輯 - 不增加圖標數量，只標記變體關係
 */

echo "🚀 開始修正版本的圖標分類匯出\n";
echo "=================================\n\n";

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

// 第一步：建立圖標名稱索引，用於查找配對
$iconMap = [];
foreach ($allConfig['icons'] as $icon) {
    $iconMap[$icon['name']] = $icon;
}

echo "📊 開始處理變體關係和分類...\n";

// 第二步：處理每個圖標的變體關係
$processedIcons = [];
foreach ($allConfig['icons'] as $icon) {
    $iconName = $icon['name'];
    $iconCategory = $icon['category'] ?? 'all';
    
    // 如果圖標還是 'all' 分類，歸類到 'others'
    if ($iconCategory === 'all') {
        $iconCategory = 'others';
        echo "⚠️  {$iconName} 仍為 'all' 分類，歸類到 'others'\n";
    }
    
    // 檢查分類是否存在
    if (!isset($categoryData[$iconCategory])) {
        echo "⚠️  未知分類 '{$iconCategory}' 的圖標: {$iconName}，歸類到 'others'\n";
        $iconCategory = 'others';
    }
    
    // 處理變體關係
    $variants = [];
    $hasVariants = false;
    
    if (substr($iconName, -5) === '-fill') {
        // 這是 fill 版本，查找對應的 outline 版本
        $outlineName = substr($iconName, 0, -5); // 移除 '-fill'
        if (isset($iconMap[$outlineName])) {
            // 找到配對，標記為有變體
            $hasVariants = true;
            $variants = [
                'solid' => ['class' => $icon['class']],
                'outline' => ['class' => $iconMap[$outlineName]['class']]
            ];
        }
    } else {
        // 這是普通版本，查找對應的 fill 版本
        $fillName = $iconName . '-fill';
        if (isset($iconMap[$fillName])) {
            // 找到配對，標記為有變體
            $hasVariants = true;
            $variants = [
                'outline' => ['class' => $icon['class']],
                'solid' => ['class' => $iconMap[$fillName]['class']]
            ];
        }
    }
    
    // 如果沒有找到配對，創建單一變體
    if (!$hasVariants) {
        $variantType = (substr($iconName, -5) === '-fill') ? 'solid' : 'outline';
        $variants = [
            $variantType => ['class' => $icon['class']]
        ];
    }
    
    // 創建處理後的圖標數據
    $processedIcon = [
        'name' => $iconName,
        'displayName' => $icon['displayName'] ?? ucwords(str_replace(['-', '_'], ' ', $iconName)),
        'class' => $icon['class'],
        'keywords' => $icon['keywords'] ?? [str_replace('-', '', $iconName)],
        'type' => 'bootstrap',
        'category' => $iconCategory,
        'variants' => $variants
    ];
    
    // 添加到對應分類
    $categoryData[$iconCategory]['icons'][] = $processedIcon;
    $processedIcons[] = $iconName;
}

// 統計各分類圖標數量
$stats = [];
foreach ($categoryData as $categoryId => $data) {
    $stats[$categoryId] = count($data['icons']);
}

echo "\n📈 修正後分類統計結果:\n";
$totalIcons = 0;
foreach ($stats as $categoryId => $count) {
    echo sprintf("  %-15s: %4d 個圖標\n", $categories[$categoryId]['name'], $count);
    $totalIcons += $count;
}
echo sprintf("  %-15s: %4d 個圖標\n", "總計", $totalIcons);

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
        
        $displayName = addslashes($icon['displayName']);
        $fileContent[] = "            'displayName' => '{$displayName}',";
        
        $fileContent[] = "            'class' => '{$icon['class']}',";
        
        $keywords = json_encode($icon['keywords']);
        $fileContent[] = "            'keywords' => {$keywords},";
        
        $fileContent[] = "            'type' => '{$icon['type']}',";
        $fileContent[] = "            'category' => '{$icon['category']}',";
        
        $fileContent[] = '            \'variants\' => [';
        foreach ($icon['variants'] as $variantType => $variantData) {
            $variantClass = $variantData['class'];
            $fileContent[] = "                '{$variantType}' => ['class' => '{$variantClass}'],";
        }
        $fileContent[] = '            ]';
        
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

echo "\n🎉 修正版本的 Bootstrap Icons 分類檔案匯出完成！\n";
echo "📊 圖標總數維持: {$totalIcons} 個（與原始 all.php 一致）\n";
echo "🔧 變體關係已正確處理：只標記配對關係，不增加圖標數量\n";

?>