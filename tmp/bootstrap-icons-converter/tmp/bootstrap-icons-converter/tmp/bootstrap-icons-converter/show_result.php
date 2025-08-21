<?php

/**
 * 展示轉換結果
 */

// 內嵌所有類別以避免路徑問題
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
}

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
            
            $commonFillIcons = ['alarm', 'house', 'cart', 'heart', 'star', 'bell', 'bookmark', 'calendar', 'envelope', 'folder'];
            
            if (in_array($iconName, $commonFillIcons)) {
                $variants['solid'] = ['class' => "bi-$iconName-fill"];
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
    
    public function generateIconConfig($iconData)
    {
        $indent = $this->getIndent();
        $config = $indent . "[\n";
        
        $this->indentLevel++;
        $innerIndent = $this->getIndent();
        
        $config .= $innerIndent . "'name' => '" . $iconData['name'] . "',\n";
        $config .= $innerIndent . "'displayName' => '" . $iconData['displayName'] . "',\n";
        $config .= $innerIndent . "'class' => '" . $iconData['class'] . "',\n";
        $config .= $innerIndent . "'keywords' => ['" . implode("', '", $iconData['keywords']) . "'],\n";
        $config .= $innerIndent . "'type' => 'bootstrap',\n";
        $config .= $innerIndent . "'category' => 'general',\n";
        $config .= $innerIndent . "'variants' => [\n";
        
        if (!empty($iconData['variants'])) {
            $this->indentLevel++;
            $variantIndent = $this->getIndent();
            
            foreach ($iconData['variants'] as $variantType => $variantData) {
                $config .= $variantIndent . "'$variantType' => ['class' => '" . $variantData['class'] . "'],\n";
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
}

// 執行轉換
echo "🔄 Bootstrap Icons 轉換器 - 最終展示\n";
echo "====================================\n\n";

try {
    $parser = new JsonParser();
    $success = $parser->loadFromFile(__DIR__ . '/data/bootstrap-icons.json');
    
    if (!$success) {
        throw new Exception("無法載入 JSON 檔案");
    }
    
    $allIcons = $parser->getIconNames();
    echo "✅ 載入 " . count($allIcons) . " 個官方圖標\n\n";
    
    // 處理前 20 個圖標作為範例
    $processor = new IconProcessor();
    $generator = new PhpConfigGenerator();
    
    $sampleIcons = array_slice($allIcons, 0, 20);
    $processedIcons = [];
    
    foreach ($sampleIcons as $iconName) {
        $processedIcons[] = $processor->processIcon($iconName);
    }
    
    $phpConfig = $generator->generateCompleteConfig($processedIcons, 'general');
    
    echo "📄 生成的 PHP 配置檔案內容:\n";
    echo str_repeat("=", 60) . "\n";
    echo $phpConfig;
    echo "\n" . str_repeat("=", 60) . "\n\n";
    
    echo "📊 轉換統計:\n";
    echo "- 官方圖標總數: " . count($allIcons) . "\n";
    echo "- 範例轉換數量: " . count($processedIcons) . "\n";
    echo "- 生成檔案大小: " . number_format(strlen($phpConfig)) . " 字元\n";
    echo "- 平均每圖標: " . number_format(strlen($phpConfig) / count($processedIcons)) . " 字元\n\n";
    
    echo "🎉 TDD 開發的 Bootstrap Icons 轉換器開發完成！\n";
    echo "這個工具可以將官方的 2078 個圖標轉換為你的專案格式。\n";
    
} catch (Exception $e) {
    echo "❌ 展示失敗: " . $e->getMessage() . "\n";
}

?>