<?php

/**
 * Bootstrap Icons 自動分類工具
 * 將 all.php 中的圖標重新分類到適當的分類中
 */

echo "🔄 Bootstrap Icons 自動分類工具\n";
echo "================================\n\n";

// 載入 all.php 配置
$allConfigPath = __DIR__ . '/../config/icon/bootstrap-icons/all.php';
$allConfig = include $allConfigPath;

if (!isset($allConfig['icons'])) {
    echo "❌ 無法載入 all.php 配置\n";
    exit(1);
}

$icons = $allConfig['icons'];
echo "📊 載入 " . count($icons) . " 個圖標\n\n";

// 定義分類規則
$categoryRules = [
    'ui' => [
        'keywords' => ['button', 'menu', 'nav', 'tab', 'panel', 'modal', 'dropdown', 'toggle', 'switch', 'slider', 'input', 'form', 'field', 'layout', 'grid', 'column', 'row', 'border', 'window', 'dialog', 'popup', 'tooltip', 'badge', 'tag', 'chevron', 'arrow', 'caret', 'list', 'table', 'view', 'display', 'aspect'],
        'names' => ['chevron', 'arrow', 'caret', 'list', 'table', 'view', 'grid', 'layout', 'border', 'window', 'aspect-ratio', 'badge']
    ],
    'files' => [
        'keywords' => ['file', 'folder', 'document', 'doc', 'pdf', 'zip', 'archive', 'download', 'upload', 'save', 'backup'],
        'names' => ['file', 'folder', 'archive', 'save', 'download', 'upload', 'document']
    ],
    'media' => [
        'keywords' => ['play', 'pause', 'stop', 'record', 'volume', 'music', 'video', 'camera', 'image', 'photo', 'picture', 'media', 'audio', 'sound'],
        'names' => ['play', 'pause', 'stop', 'volume', 'music', 'video', 'camera', 'image']
    ],
    'communications' => [
        'keywords' => ['mail', 'email', 'message', 'chat', 'phone', 'call', 'send', 'share', 'link', 'wifi', 'bluetooth', 'signal'],
        'names' => ['mail', 'email', 'chat', 'phone', 'send', 'share', 'link', 'wifi', 'bluetooth']
    ],
    'people' => [
        'keywords' => ['person', 'people', 'user', 'group', 'team', 'profile', 'avatar', 'face', 'heart', 'hand', 'body'],
        'names' => ['person', 'people', 'user', 'heart', 'hand']
    ],
    'alphanumeric' => [
        'keywords' => ['text', 'font', 'type', 'alpha', 'numeric', 'number', 'letter', 'justify', 'align'],
        'names' => ['123', 'text', 'type', 'font', 'align', 'justify']
    ],
    'general' => [
        'keywords' => ['home', 'house', 'search', 'find', 'star', 'bookmark', 'favorite', 'like', 'check', 'cross', 'plus', 'minus', 'times', 'circle', 'square', 'triangle', 'gear', 'settings', 'config', 'tool', 'edit', 'pencil', 'pen', 'delete', 'trash', 'bin', 'refresh', 'reload', 'sync', 'lock', 'unlock', 'key', 'shield', 'security', 'eye', 'visible', 'hidden', 'clock', 'time', 'calendar', 'date', 'alarm', 'bell', 'notification'],
        'names' => ['home', 'house', 'search', 'star', 'heart', 'check', 'plus', 'minus', 'gear', 'clock', 'alarm', 'bell', 'calendar']
    ],
    'others' => [
        // 所有其他不符合上述分類的圖標
        'keywords' => ['weather', 'cloud', 'sun', 'rain', 'snow', 'business', 'money', 'cart', 'shop', 'store', 'bag', 'basket', 'gift', 'award', 'trophy', 'map', 'location', 'geo', 'pin', 'marker', 'car', 'truck', 'train', 'plane', 'ship', 'transport', 'vehicle'],
        'names' => ['weather', 'cloud', 'sun', 'cart', 'bag', 'basket', 'award', 'map', 'geo', 'car', 'truck']
    ]
];

/**
 * 根據圖標名稱和關鍵字判斷最適合的分類
 */
function categorizeIcon($icon, $categoryRules) {
    $name = strtolower($icon['name']);
    $displayName = strtolower($icon['displayName'] ?? $icon['name']);
    $keywords = array_map('strtolower', $icon['keywords'] ?? []);
    
    $scores = [];
    
    foreach ($categoryRules as $category => $rules) {
        $score = 0;
        
        // 檢查名稱匹配
        foreach ($rules['names'] ?? [] as $ruleName) {
            if (strpos($name, $ruleName) !== false || strpos($displayName, $ruleName) !== false) {
                $score += 10; // 名稱匹配權重更高
            }
        }
        
        // 檢查關鍵字匹配
        foreach ($rules['keywords'] ?? [] as $ruleKeyword) {
            foreach ($keywords as $keyword) {
                if (strpos($keyword, $ruleKeyword) !== false || strpos($ruleKeyword, $keyword) !== false) {
                    $score += 3;
                }
            }
            
            // 檢查名稱中是否包含關鍵字
            if (strpos($name, $ruleKeyword) !== false || strpos($displayName, $ruleKeyword) !== false) {
                $score += 5;
            }
        }
        
        $scores[$category] = $score;
    }
    
    // 找出得分最高的分類
    arsort($scores);
    $topCategory = array_keys($scores)[0];
    $topScore = $scores[$topCategory];
    
    // 如果最高分小於等於 0，歸類為 others
    if ($topScore <= 0) {
        return 'others';
    }
    
    return $topCategory;
}

// 進行分類處理
$categorizedIcons = [
    'ui' => [],
    'files' => [],
    'media' => [],
    'communications' => [],
    'people' => [],
    'alphanumeric' => [],
    'general' => [],
    'others' => []
];

$processedCount = 0;
$batchSize = 60;

echo "🚀 開始分類處理...\n\n";

foreach ($icons as $icon) {
    // 如果已經被分類過（category 不是 'all'），則跳過
    if (isset($icon['category']) && $icon['category'] !== 'all') {
        continue;
    }
    
    $category = categorizeIcon($icon, $categoryRules);
    $categorizedIcons[$category][] = $icon;
    
    echo "📋 {$icon['name']} -> $category\n";
    
    $processedCount++;
    
    // 如果達到批次大小，停止
    if ($processedCount >= $batchSize) {
        break;
    }
}

echo "\n📊 分類結果:\n";
foreach ($categorizedIcons as $category => $categoryIcons) {
    if (!empty($categoryIcons)) {
        echo "  $category: " . count($categoryIcons) . " 個圖標\n";
    }
}

echo "\n總計處理: $processedCount 個圖標\n\n";

// 將分類結果存儲到臨時檔案供後續處理
$resultPath = __DIR__ . '/categorization_result.json';
file_put_contents($resultPath, json_encode($categorizedIcons, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✅ 分類結果已儲存到: $resultPath\n";
echo "💡 下一步：執行 apply_categorization.php 來應用這些變更\n";

?>