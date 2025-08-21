<?php

/**
 * Bootstrap Icons General 類別自包含匯出器
 * 包含所有必要的類別，無需外部依賴
 */

// 確保輸出目錄存在
$outputDir = __DIR__ . '/output';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

echo "🔄 Bootstrap Icons General 類別自包含匯出器\n";
echo "===============================================\n\n";

// JSON 解析器類別
class JsonParser
{
    private $data = null;
    private $iconNames = [];
    
    public function loadFromFile($filePath)
    {
        if (!file_exists($filePath)) {
            return false;
        }
        
        $jsonContent = file_get_contents($filePath);
        if ($jsonContent === false) {
            return false;
        }
        
        $this->data = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->data = null;
            return false;
        }
        
        $this->iconNames = array_keys($this->data);
        return true;
    }
    
    public function getIconNames()
    {
        return $this->iconNames;
    }
    
    public function hasIcon($iconName)
    {
        return isset($this->data[$iconName]);
    }
}

// 圖標處理器類別
class IconProcessor
{
    public function iconNameToDisplayName($iconName)
    {
        return ucwords(str_replace('-', ' ', $iconName));
    }
    
    public function generateKeywords($iconName)
    {
        return explode('-', $iconName);
    }
    
    public function detectVariants($iconName)
    {
        $variants = [];
        
        if (substr($iconName, -5) === '-fill') {
            $baseIconName = substr($iconName, 0, -5);
            $variants['solid'] = ['class' => "bi-$iconName"];
            $variants['outline'] = ['class' => "bi-$baseIconName"];
        } else {
            $variants['outline'] = ['class' => "bi-$iconName"];
            
            // 檢查常見的 fill 變體
            $commonFillIcons = [
                'alarm', 'house', 'cart', 'heart', 'star', 'bell', 'bookmark', 
                'calendar', 'envelope', 'folder', 'person', 'shield', 'clock', 
                'badge', 'circle', 'square', 'triangle', 'arrow', 'chevron'
            ];
            
            foreach ($commonFillIcons as $fillIcon) {
                if (strpos($iconName, $fillIcon) !== false) {
                    $variants['solid'] = ['class' => "bi-$iconName-fill"];
                    break;
                }
            }
        }
        
        return $variants;
    }
    
    public function processIcon($iconName, $category = 'general')
    {
        return [
            'name' => $iconName,
            'displayName' => $this->iconNameToDisplayName($iconName),
            'class' => "bi-$iconName",
            'keywords' => $this->generateKeywords($iconName),
            'type' => 'bootstrap',
            'category' => $category,
            'variants' => $this->detectVariants($iconName)
        ];
    }
}

// PHP 配置生成器類別
class PhpConfigGenerator
{
    private $indentLevel = 0;
    private $indentSize = 4;
    
    public function generateCompleteConfig($icons, $category)
    {
        $this->indentLevel = 0;
        
        $config = "<?php\n\n";
        $config .= "return [\n";
        
        $this->indentLevel++;
        $indent = $this->getIndent();
        
        $config .= $indent . "'id' => 'general',\n";
        $config .= $indent . "'name' => '通用圖標',\n";
        $config .= $indent . "'description' => '最常用的基礎圖標',\n";
        $config .= $indent . "'priority' => 'immediate',\n\n";
        
        $config .= $indent . "'icons' => [\n";
        
        if (!empty($icons)) {
            $this->indentLevel++;
            
            foreach ($icons as $i => $icon) {
                $config .= $this->generateIconConfig($icon);
                if ($i < count($icons) - 1) {
                    $config .= ",\n";
                } else {
                    $config .= "\n";
                }
            }
            
            $this->indentLevel--;
            $config .= $indent . "]\n";
        } else {
            $config = str_replace("'icons' => [\n", "'icons' => []", $config);
        }
        
        $this->indentLevel--;
        $config .= "];";
        
        return $config;
    }
    
    private function generateIconConfig($iconData)
    {
        $indent = $this->getIndent();
        $config = $indent . "[\n";
        
        $this->indentLevel++;
        $innerIndent = $this->getIndent();
        
        $config .= $innerIndent . "'name' => '" . $this->escapeString($iconData['name']) . "',\n";
        $config .= $innerIndent . "'displayName' => '" . $this->escapeString($iconData['displayName']) . "',\n";
        $config .= $innerIndent . "'class' => '" . $this->escapeString($iconData['class']) . "',\n";
        $config .= $innerIndent . "'keywords' => ['" . implode("', '", array_map([$this, 'escapeString'], $iconData['keywords'])) . "'],\n";
        $config .= $innerIndent . "'type' => 'bootstrap',\n";
        $config .= $innerIndent . "'category' => 'general',\n";
        $config .= $innerIndent . "'variants' => [\n";
        
        if (!empty($iconData['variants'])) {
            $this->indentLevel++;
            $variantIndent = $this->getIndent();
            
            foreach ($iconData['variants'] as $variantType => $variantData) {
                $config .= $variantIndent . "'" . $this->escapeString($variantType) . "' => ['class' => '" . $this->escapeString($variantData['class']) . "'],\n";
            }
            
            $this->indentLevel--;
        }
        $config .= $innerIndent . "]\n";
        
        $this->indentLevel--;
        $config .= $indent . "]";
        
        return $config;
    }
    
    private function getIndent()
    {
        return str_repeat(' ', $this->indentLevel * $this->indentSize);
    }
    
    private function escapeString($string)
    {
        return str_replace("'", "\\'", $string);
    }
}

try {
    // 1. 載入官方 JSON
    echo "1. 載入官方 Bootstrap Icons JSON...\n";
    $parser = new JsonParser();
    $success = $parser->loadFromFile(__DIR__ . '/data/bootstrap-icons.json');
    
    if (!$success) {
        // 如果本地文件不存在，嘗試下載
        echo "   本地檔案不存在，正在下載官方 JSON...\n";
        $jsonUrl = 'https://raw.githubusercontent.com/twbs/icons/main/font/bootstrap-icons.json';
        $jsonContent = file_get_contents($jsonUrl);
        
        if ($jsonContent === false) {
            throw new Exception("無法下載官方 JSON 檔案");
        }
        
        if (!is_dir(__DIR__ . '/data')) {
            mkdir(__DIR__ . '/data', 0777, true);
        }
        
        file_put_contents(__DIR__ . '/data/bootstrap-icons.json', $jsonContent);
        $success = $parser->loadFromFile(__DIR__ . '/data/bootstrap-icons.json');
        
        if (!$success) {
            throw new Exception("無法解析下載的 JSON 檔案");
        }
    }
    
    $allIcons = $parser->getIconNames();
    echo "   ✅ 成功載入 " . count($allIcons) . " 個官方圖標\n\n";
    
    // 2. 篩選 general 類別相關的圖標
    echo "2. 篩選 general 類別相關圖標...\n";
    
    $generalPatterns = [
        'house', 'home', 'search', 'gear', 'cog', 'settings',
        'alarm', 'bell', 'clock', 'time',
        'star', 'heart', 'bookmark', 'flag',
        'hand', 'thumbs', 'finger',
        'eye', 'visible', 'view',
        'circle', 'square', 'triangle',
        'arrow', 'chevron', 'caret',
        'play', 'pause', 'stop', 'skip',
        'plus', 'minus', 'x', 'check',
        'menu', 'list', 'grid'
    ];
    
    $generalIcons = [];
    
    foreach ($allIcons as $iconName) {
        foreach ($generalPatterns as $pattern) {
            if (strpos($iconName, $pattern) !== false) {
                $generalIcons[] = $iconName;
                break;
            }
        }
    }
    
    $generalIcons = array_unique($generalIcons);
    
    echo "   ✅ 篩選出 " . count($generalIcons) . " 個 general 類別圖標\n";
    echo "   📋 前 10 個範例: " . implode(', ', array_slice($generalIcons, 0, 10)) . "\n\n";
    
    // 3. 處理圖標資料
    echo "3. 處理圖標資料...\n";
    $processor = new IconProcessor();
    
    $processedIcons = [];
    foreach ($generalIcons as $iconName) {
        $processedIcons[] = $processor->processIcon($iconName, 'general');
    }
    
    echo "   ✅ 完成處理 " . count($processedIcons) . " 個圖標\n\n";
    
    // 4. 生成 PHP 配置檔案
    echo "4. 生成 PHP 配置檔案...\n";
    $generator = new PhpConfigGenerator();
    $phpConfig = $generator->generateCompleteConfig($processedIcons, 'general');
    
    echo "   ✅ PHP 配置檔案生成成功\n";
    echo "   ✅ 檔案大小: " . number_format(strlen($phpConfig)) . " 字元\n\n";
    
    // 5. 寫入檔案
    $outputPath = $outputDir . '/general.php';
    $bytesWritten = file_put_contents($outputPath, $phpConfig);
    
    if ($bytesWritten === false) {
        throw new Exception("無法寫入檔案: $outputPath");
    }
    
    echo "5. 檔案輸出成功:\n";
    echo "   📁 路徑: $outputPath\n";
    echo "   📏 大小: " . number_format($bytesWritten) . " 字節\n\n";
    
    // 6. 驗證 PHP 語法
    echo "6. 驗證 PHP 語法...\n";
    $output = [];
    $returnCode = 0;
    exec("php8.4 -l \"$outputPath\" 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "   ✅ PHP 語法正確\n\n";
    } else {
        echo "   ❌ PHP 語法錯誤:\n";
        foreach ($output as $line) {
            echo "   $line\n";
        }
        throw new Exception("生成的 PHP 檔案語法有誤");
    }
    
    echo "🎉 General 類別匯出完成！\n\n";
    echo "📊 匯出統計:\n";
    echo "- 官方圖標總數: " . number_format(count($allIcons)) . "\n";
    echo "- General 圖標: " . number_format(count($processedIcons)) . "\n";
    echo "- 覆蓋率: " . number_format((count($processedIcons) / count($allIcons)) * 100, 1) . "%\n";
    echo "- 輸出檔案: $outputPath\n\n";
    
    // 返回檔案路徑供後續測試使用
    echo "📁 生成的配置檔案: $outputPath\n";
    
} catch (Exception $e) {
    echo "❌ 匯出失敗: " . $e->getMessage() . "\n";
    exit(1);
}

?>