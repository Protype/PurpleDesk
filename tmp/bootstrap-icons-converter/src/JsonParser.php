<?php

/**
 * Bootstrap Icons JSON 解析器
 * 負責讀取和解析官方 Bootstrap Icons JSON 檔案
 */
class JsonParser
{
    private $data = null;
    private $iconNames = [];
    
    /**
     * 從檔案載入 JSON 資料
     * @param string $filePath JSON 檔案路徑
     * @return bool 是否成功載入
     */
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
        
        // 提取圖標名稱
        $this->iconNames = array_keys($this->data);
        
        return true;
    }
    
    /**
     * 獲取所有圖標名稱
     * @return array 圖標名稱陣列
     */
    public function getIconNames()
    {
        return $this->iconNames;
    }
    
    /**
     * 獲取指定圖標的數字代碼
     * @param string $iconName 圖標名稱
     * @return int|null 數字代碼，不存在時返回 null
     */
    public function getIconCode($iconName)
    {
        return isset($this->data[$iconName]) ? $this->data[$iconName] : null;
    }
    
    /**
     * 檢查圖標是否存在
     * @param string $iconName 圖標名稱
     * @return bool 是否存在
     */
    public function hasIcon($iconName)
    {
        return isset($this->data[$iconName]);
    }
    
    /**
     * 獲取圖標總數
     * @return int 圖標總數
     */
    public function getIconCount()
    {
        return count($this->iconNames);
    }
    
    /**
     * 獲取原始資料
     * @return array|null 原始 JSON 資料
     */
    public function getRawData()
    {
        return $this->data;
    }
}