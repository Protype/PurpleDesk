# ST-014-4-FIX: 修正 IconLibraryPanel 圖標顯示邏輯

**建立日期**：2025-08-19  
**狀態**：Draft  
**優先級**：P0 (修正問題)  
**預估工時**：4小時

## 📋 Story 概述

### 問題背景

ST-014-4 實作的 IconLibraryPanel 存在以下問題：
1. 使用標籤切換 HeroIcons 和 Bootstrap Icons，不符合舊版設計
2. 使用複雜的 VariantSelector，應該使用簡單的 IconStyleSelector
3. 樣式過濾邏輯與舊版不一致

### Story 定義

**作為**：使用者  
**我想要**：在單一視圖中瀏覽所有圖標庫（先 HeroIcons，再 Bootstrap Icons 各分類）  
**所以**：可以快速找到需要的圖標，體驗與舊版一致

## 🎯 驗收條件

### 核心功能修正
- [ ] 移除 HeroIcons/Bootstrap Icons 標籤切換
- [ ] 實作連續顯示：先顯示 HeroIcons 分類，後顯示 Bootstrap Icons 各分類
- [ ] 使用 IconStyleSelector 替代 VariantSelector
- [ ] 實作正確的 outline/solid 篩選邏輯
- [ ] 保持與舊版一致的視覺呈現和分類標題

### 技術要求
- [ ] 參考舊版 `groupedIcons` computed 實作邏輯
- [ ] 使用舊版 `filterBootstrapIconsByStyle` 篩選方法
- [ ] 保持 VirtualScrollGrid 效能優化
- [ ] 維持搜尋功能正常運作
- [ ] 保持載入狀態、錯誤處理和空狀態

### 介面要求
- [ ] 搜尋欄位保持在頂部
- [ ] IconStyleSelector 在搜尋欄旁邊（僅在有圖標時顯示）
- [ ] 分類標題樣式與舊版一致
- [ ] 圖標按鈕樣式與舊版一致
- [ ] 選中狀態樣式與舊版一致

## 🔧 技術實作規格

### 1. 移除標籤邏輯

移除原有的：
```javascript
const activeLibrary = ref('heroicons')
```

移除標籤 HTML：
```html
<!-- 移除整個 library-tabs 區塊 -->
<div class="library-tabs">...</div>
```

### 2. 修正圖標資料組織

參考舊版 `groupedIcons` 實作：

```javascript
const groupedIcons = computed(() => {
  // 如果有搜尋查詢，返回篩選後的扁平陣列（不分組）
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    const filteredHeroIcons = heroIcons.filter(icon => 
      icon.name.toLowerCase().includes(query) || icon.component.toLowerCase().includes(query)
    )
    const filteredBsIcons = bsIcons.filter(icon => 
      icon.name.toLowerCase().includes(query) || icon.class.toLowerCase().includes(query)
    )
    return [...filteredHeroIcons, ...filteredBsIcons]
  }
  
  const result = []
  
  // 1. 添加 Heroicons 分類標題和圖標
  if (heroIcons.length > 0) {
    // 確保當前位置是 10 的倍數
    let currentLength = result.length
    let remainderInRow = currentLength % 10
    if (remainderInRow !== 0) {
      const fillersNeeded = 10 - remainderInRow
      for (let i = 0; i < fillersNeeded; i++) {
        result.push({ type: 'row-filler' })
      }
    }
    
    // 添加 Heroicons 標題
    result.push({
      type: 'category-header',
      categoryId: 'heroicons',
      name: 'Hero Icons',
      icon: '✨'
    })
    
    // 添加 9 個空項目來填滿標題行
    for (let i = 1; i < 10; i++) {
      result.push({ type: 'category-header-filler' })
    }
    
    // 添加 Heroicons
    result.push(...heroIcons)
  }
  
  // 2. 按分類添加 Bootstrap Icons
  const categoryOrder = ['general', 'ui', 'communications', 'files', 'media', 'people', 'alphanumeric', 'others']
  
  categoryOrder.forEach(categoryId => {
    const categoryIcons = bsIcons.filter(icon => icon.category === categoryId)
    
    if (categoryIcons.length > 0) {
      // 確保當前位置是 10 的倍數
      const currentLength = result.length
      const remainderInRow = currentLength % 10
      if (remainderInRow !== 0) {
        const fillersNeeded = 10 - remainderInRow
        for (let i = 0; i < fillersNeeded; i++) {
          result.push({ type: 'row-filler' })
        }
      }
      
      // 添加分類標題
      result.push({
        type: 'category-header',
        categoryId: categoryId,
        name: getCategoryDisplayName(categoryId),
        icon: getCategoryIcon(categoryId)
      })
      
      // 添加 9 個空項目來填滿標題行
      for (let i = 1; i < 10; i++) {
        result.push({ type: 'category-header-filler' })
      }
      
      // 根據選擇的樣式過濾 Bootstrap Icons
      const filteredCategoryIcons = filterBootstrapIconsByStyle(categoryIcons, selectedIconStyle.value)
      result.push(...filteredCategoryIcons)
    }
  })
  
  return result
})
```

### 3. 使用 IconStyleSelector

替換 VariantSelector：
```html
<!-- 替換原有的 VariantSelector -->
<IconStyleSelector
  v-model="selectedIconStyle"
  @update:modelValue="handleIconStyleChange"
/>
```

### 4. 實作樣式篩選邏輯

```javascript
// 根據樣式過濾 Bootstrap Icons（參考舊版）
const filterBootstrapIconsByStyle = (icons, style) => {
  if (!icons || icons.length === 0) return []
  
  // 建立圖標映射來分析變體關係
  const iconMap = new Map()
  icons.forEach(icon => {
    const className = icon.class || ''
    iconMap.set(className, icon)
  })
  
  return icons.filter(icon => {
    const className = icon.class || ''
    const isFillIcon = className.includes('-fill')
    
    if (style === 'outline') {
      if (isFillIcon) {
        // 如果是 fill 圖標，不顯示
        return false
      } else {
        // 基礎圖標或特殊變體，都顯示
        return true
      }
    } else if (style === 'solid') {
      if (isFillIcon) {
        // 顯示所有 -fill 圖標
        return true
      } else {
        // 基礎圖標：檢查是否有對應的 fill 版本
        const fillVersion = className + '-fill'
        const hasFillVersion = iconMap.has(fillVersion)
        
        if (hasFillVersion) {
          // 如果有 fill 版本，不顯示基礎版本（優先顯示 fill）
          return false
        } else {
          // 沒有 fill 版本的特殊變體，顯示
          return true
        }
      }
    }
    
    return true // 預設顯示所有
  })
}
```

## 🧪 測試要求

### 單元測試更新
- [ ] 更新 IconLibraryPanel.test.js
- [ ] 移除標籤切換相關測試
- [ ] 添加連續顯示測試
- [ ] 添加樣式篩選測試
- [ ] 添加分類標題正確性測試

### 整合測試
- [ ] 測試與 IconPicker 主元件的整合
- [ ] 測試搜尋功能與新顯示邏輯的配合
- [ ] 測試樣式切換與圖標篩選的聯動

### 視覺測試
- [ ] 對比舊版介面，確保視覺一致性
- [ ] 測試分類標題的顯示效果
- [ ] 測試圖標選中狀態的視覺反饋

## 📦 交付物

1. **更新的元件檔案**
   - `IconLibraryPanel.vue` - 修正後的主元件
   - `IconLibraryPanel.test.js` - 更新的測試檔案

2. **新增元件**
   - 需要引入 `IconStyleSelector.vue` 元件

3. **測試報告**
   - 單元測試通過報告
   - 視覺對比測試結果

## 🎯 Definition of Done

- [ ] 所有驗收條件都已滿足
- [ ] 元件外觀與舊版完全一致
- [ ] 所有測試案例通過（至少 20 個測試）
- [ ] 搜尋功能在新架構下正常運作
- [ ] 樣式切換功能正常運作
- [ ] 無任何現有功能回歸問題
- [ ] 程式碼 review 通過
- [ ] 效能表現不低於舊版

---

**負責人**：開發團隊  
**審查者**：產品經理 + 技術主管  
**完成目標**：2025-08-19 當天完成