<?php

/**
 * PHP 配置檔案生成器
 * 負責將圖標資料轉換為 Laravel 配置檔案格式
 */
class PhpConfigGenerator
{
    private $indentLevel = 0;
    private $indentSize = 4;
    
    /**
     * 生成單個圖標的配置
     * @param array $iconData 圖標資料
     * @return string PHP 配置字串
     */
    public function generateIconConfig($iconData)
    {
        $indent = $this->getIndent();
        $config = $indent . "[\n";
        
        $this->indentLevel++;
        $innerIndent = $this->getIndent();
        
        // 必要欄位
        $config .= $innerIndent . "'name' => '" . $this->escapeString($iconData['name']) . "',\n";
        $config .= $innerIndent . "'displayName' => '" . $this->escapeString($iconData['displayName']) . "',\n";
        $config .= $innerIndent . "'class' => '" . $this->escapeString($iconData['class']) . "',\n";
        
        // 關鍵字陣列
        $config .= $innerIndent . "'keywords' => [";
        if (!empty($iconData['keywords'])) {
            $keywords = array_map(function($keyword) {
                return "'" . $this->escapeString($keyword) . "'";
            }, $iconData['keywords']);
            $config .= implode(', ', $keywords);
        }
        $config .= "],\n";
        
        $config .= $innerIndent . "'type' => '" . $this->escapeString($iconData['type']) . "',\n";
        $config .= $innerIndent . "'category' => '" . $this->escapeString($iconData['category']) . "',\n";
        
        // 變體陣列
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
    
    /**
     * 生成完整的 PHP 配置檔案
     * @param array $icons 圖標陣列
     * @param string $category 分類名稱
     * @return string 完整的 PHP 檔案內容
     */
    public function generateCompleteConfig($icons, $category)
    {
        $this->indentLevel = 0;
        
        $config = "<?php\n\n";
        $config .= "return [\n";
        
        $this->indentLevel++;
        $indent = $this->getIndent();
        
        // 分類資訊
        $config .= $indent . "'id' => '" . $this->escapeString($category) . "',\n";
        $config .= $indent . "'name' => '" . $this->escapeString($this->formatCategoryName($category)) . "',\n";
        $config .= $indent . "'description' => '" . $this->escapeString($this->generateCategoryDescription($category)) . "',\n";
        $config .= $indent . "'priority' => 'immediate',\n\n";
        
        // 圖標陣列
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
        } else {
            // 空陣列的處理
            $config = str_replace("'icons' => [\n", "'icons' => []", $config);
        }
        
        if (!empty($icons)) {
            $config .= $indent . "]\n";
        }
        
        $this->indentLevel--;
        $config .= "];";
        
        return $config;
    }
    
    /**
     * 獲取當前縮排字串
     * @return string 縮排字串
     */
    private function getIndent()
    {
        return str_repeat(' ', $this->indentLevel * $this->indentSize);
    }
    
    /**
     * 轉義 PHP 字串
     * @param string $string 原始字串
     * @return string 轉義後的字串
     */
    private function escapeString($string)
    {
        return str_replace("'", "\\'", $string);
    }
    
    /**
     * 格式化分類名稱
     * @param string $category 分類 ID
     * @return string 格式化的分類名稱
     */
    private function formatCategoryName($category)
    {
        $names = [
            'general' => '通用圖標',
            'ui' => '使用者介面',
            'communications' => '通訊圖標',
            'files' => '檔案圖標',
            'others' => '其他圖標',
            'people' => '人物圖標',
            'media' => '媒體圖標',
            'alphanumeric' => '文數字圖標'
        ];
        
        return isset($names[$category]) ? $names[$category] : ucfirst($category);
    }
    
    /**
     * 生成分類描述
     * @param string $category 分類 ID
     * @return string 分類描述
     */
    private function generateCategoryDescription($category)
    {
        $descriptions = [
            'general' => '最常用的基礎圖標',
            'ui' => '使用者介面相關圖標',
            'communications' => '通訊與聯繫相關圖標',
            'files' => '檔案與文件相關圖標',
            'others' => '其他分類的圖標',
            'people' => '人物與身份相關圖標',
            'media' => '媒體與娛樂相關圖標',
            'alphanumeric' => '數字與字母相關圖標'
        ];
        
        return isset($descriptions[$category]) ? $descriptions[$category] : "Generated $category icons";
    }
}