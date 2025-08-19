# ST-014-4-FIX: IconLibraryPanel 簡化架構重構

**建立日期**：2025-08-19  
**狀態**：In Progress  
**優先級**：P0 (修正問題)  
**預估工時**：6小時  
**開發方法**：TDD

## 📋 Story 概述

### 問題背景

ST-014-4 實作的 IconLibraryPanel 過度複雜化，基於架構師分析決定採用簡化方案：
1. 標籤切換邏輯不符合圖標庫統一設計理念
2. 複雜的變體系統實際上可以用簡單篩選替代
3. 使用者不需要知道圖標來源，只需要選擇適合的圖標

### 設計理念更新

**核心原則**：圖標作為統一資源池，使用者無需關心來源
- 圖標庫是統一的視覺語言系統
- 按名稱排序讓相關圖標自然相鄰 (`bi-heart` 和 `bi-heart-fill`)
- 簡單篩選：所有圖標/線條圖標/填充圖標

### Story 定義

**作為**：使用者  
**我想要**：在統一的圖標瀏覽介面中快速篩選和選擇圖標  
**所以**：可以專注於圖標的視覺效果和語意，而不用在意技術來源

## 🎯 驗收條件

### 核心架構簡化
- [ ] 移除所有標籤切換邏輯 (activeLibrary)
- [ ] 實作 `isSolid` 標記系統
- [ ] 使用簡單篩選邏輯替代複雜變體系統
- [ ] 圖標按名稱統一排序
- [ ] 保持分類結構顯示

### 篩選功能
- [ ] 實作三種顯示模式：全部/線條/填充
- [ ] `outline` 模式篩選 `!isSolid` 圖標
- [ ] `solid` 模式篩選 `isSolid` 圖標
- [ ] 篩選時保持圖標相對位置穩定

### 技術實作
- [ ] 使用 IconStyleSelector 替代 VariantSelector
- [ ] 實作 `processedIcons` computed (標記 + 排序)
- [ ] 實作 `filteredIcons` computed (簡單篩選)
- [ ] 保持 VirtualScrollGrid fullRow 架構
- [ ] 搜尋時返回扁平化結果

### 介面保持
- [ ] 搜尋欄位在頂部
- [ ] IconStyleSelector 在搜尋欄旁邊
- [ ] 分類標題使用 fullRow 顯示
- [ ] 圖標選中狀態視覺反饋
- [ ] 載入/錯誤/空狀態處理

## 🔧 簡化架構技術規格

### 1. 圖標資料處理 - 標記 + 排序

```javascript
// 處理所有圖標：標記 isSolid + 統一排序
const processedIcons = computed(() => {
  const allIcons = [...heroicons, ...bootstrapIcons]
  
  return allIcons.map(icon => ({
    ...icon,
    // 簡單標記：是否為 solid 樣式
    isSolid: icon.class?.endsWith('-fill') || 
              icon.component?.includes('Solid') ||
              false
  }))
  .sort((a, b) => {
    // 按名稱排序，相關圖標自然相鄰
    const nameA = a.name || a.class || a.component || ''
    const nameB = b.name || b.class || b.component || ''
    return nameA.localeCompare(nameB)
  })
})
```

### 2. 簡單篩選邏輯

```javascript
// 純篩選，無複雜邏輯
const filteredIcons = computed(() => {
  const style = selectedStyle.value
  
  if (style === 'all') {
    return processedIcons.value
  }
  
  if (style === 'outline') {
    return processedIcons.value.filter(icon => !icon.isSolid)
  }
  
  if (style === 'solid') {
    return processedIcons.value.filter(icon => icon.isSolid)
  }
  
  return processedIcons.value
})
```

### 3. 分類結構組織

```javascript
// 保持分類結構，使用 VirtualScrollGrid fullRow
const groupedIcons = computed(() => {
  const query = searchQuery.value.toLowerCase().trim()
  
  // 搜尋時返回扁平結果
  if (query) {
    return filteredIcons.value.filter(icon => {
      const name = (icon.name || '').toLowerCase()
      const keywords = (icon.keywords || []).join(' ').toLowerCase()
      return name.includes(query) || keywords.includes(query)
    })
  }
  
  // 正常顯示：分類結構
  const result = []
  
  // HeroIcons 區塊
  const heroIconsFiltered = filteredIcons.value.filter(i => i.type === 'heroicons')
  if (heroIconsFiltered.length > 0) {
    result.push({
      type: 'category-header',
      fullRow: true,
      itemHeight: 40,
      data: { title: 'Hero Icons', count: heroIconsFiltered.length }
    })
    result.push(...heroIconsFiltered.map(icon => ({ type: 'icon', data: icon })))
  }
  
  // Bootstrap Icons 各分類
  const categories = ['general', 'ui', 'communications', 'files', 'media', 'people', 'alphanumeric', 'others']
  
  categories.forEach(category => {
    const categoryIcons = filteredIcons.value.filter(i => i.category === category)
    if (categoryIcons.length > 0) {
      result.push({
        type: 'category-header',
        fullRow: true,
        itemHeight: 40,
        data: { title: getCategoryName(category), count: categoryIcons.length }
      })
      result.push(...categoryIcons.map(icon => ({ type: 'icon', data: icon })))
    }
  })
  
  return result
})
```

### 4. IconStyleSelector 整合

```bash
# 複製元件到正確位置
cp resources/js/components/common/IconStyleSelector.vue \
   resources/js/features/icon-picker/components/IconStyleSelector.vue
```

```html
<!-- 在模板中替換 VariantSelector -->
<div class="panel-toolbar flex items-center space-x-3 mb-4">
  <IconPickerSearch
    v-model="searchQuery"
    placeholder="搜尋圖標..."
    class="flex-1"
  />
  
  <!-- 使用 IconStyleSelector 替代 VariantSelector -->
  <IconStyleSelector
    v-model="selectedStyle"
    @update:modelValue="handleStyleChange"
  />
</div>
```

```javascript
// 更新 import 和元件註冊
import IconStyleSelector from './IconStyleSelector.vue'

export default {
  components: {
    VirtualScrollGrid,
    IconPickerSearch,
    IconStyleSelector  // 替代 VariantSelector
  },

  setup(props, { emit }) {
    // 移除 VariantSelector 相關邏輯
    // const iconVariants = useIconVariants() // 移除
    // const iconStyleOptions = computed(() => ...) // 移除
    
    // 簡化樣式狀態管理
    const selectedStyle = ref('outline')
    
    // 簡化事件處理
    const handleStyleChange = (newStyle) => {
      selectedStyle.value = newStyle
      // filteredIcons 會通過 computed 自動重新計算
    }

    return {
      // 移除不需要的返回值
      // iconStyleOptions, // 移除
      // iconVariants,     // 移除
      
      // 保持簡潔的狀態
      selectedStyle,
      handleStyleChange,
      // ... 其他必要的返回值
    }
  }
}
```

### 5. 移除複雜邏輯

```javascript
// 移除的複雜功能（不再需要）
// ❌ const activeLibrary = ref('heroicons')
// ❌ const iconVariants = useIconVariants()
// ❌ const filterBootstrapIconsByStyle = (icons, style) => { ... }
// ❌ watch(selectedStyle, async (newStyle) => { await loadIcons() })

// 保留的核心功能
// ✅ const isLoading = ref(true)
// ✅ const error = ref(null) 
// ✅ const allIcons = ref({ data: { heroicons: [], bootstrap: {} }, meta: {} })
// ✅ const iconDataLoader = new IconDataLoader()
// ✅ const loadIcons = async () => { ... }
```

## 🧪 TDD 測試驅動開發

### Phase 1: Red - 編寫失敗測試

#### 1.1 架構簡化測試
```javascript
describe('架構簡化', () => {
  test('不應該有 activeLibrary 狀態', () => {
    expect(wrapper.vm.activeLibrary).toBeUndefined()
  })
  
  test('不應該有標籤切換 UI', () => {
    expect(wrapper.find('.library-tabs').exists()).toBe(false)
  })
  
  test('所有圖標應該有 isSolid 標記', () => {
    const icons = wrapper.vm.processedIcons
    icons.forEach(icon => {
      expect(icon).toHaveProperty('isSolid')
      expect(typeof icon.isSolid).toBe('boolean')
    })
  })
})
```

#### 1.2 篩選邏輯測試
```javascript
describe('篩選邏輯', () => {
  test('outline 模式應該只顯示 !isSolid 圖標', () => {
    wrapper.vm.selectedStyle = 'outline'
    const filtered = wrapper.vm.filteredIcons
    filtered.forEach(icon => expect(icon.isSolid).toBe(false))
  })
  
  test('solid 模式應該只顯示 isSolid 圖標', () => {
    wrapper.vm.selectedStyle = 'solid'
    const filtered = wrapper.vm.filteredIcons
    filtered.forEach(icon => expect(icon.isSolid).toBe(true))
  })
  
  test('all 模式應該顯示所有圖標', () => {
    wrapper.vm.selectedStyle = 'all'
    const filtered = wrapper.vm.filteredIcons
    const processed = wrapper.vm.processedIcons
    expect(filtered.length).toBe(processed.length)
  })
})
```

#### 1.3 排序和顯示測試
```javascript
describe('排序和顯示', () => {
  test('圖標應該按名稱排序', () => {
    const icons = wrapper.vm.processedIcons
    for (let i = 1; i < icons.length; i++) {
      const nameA = icons[i-1].name || icons[i-1].class || ''
      const nameB = icons[i].name || icons[i].class || ''
      expect(nameA.localeCompare(nameB)).toBeLessThanOrEqual(0)
    }
  })
  
  test('搜尋時應該返回扁平結果', () => {
    wrapper.vm.searchQuery = 'heart'
    const grouped = wrapper.vm.groupedIcons
    const hasCategory = grouped.some(item => item.type === 'category-header')
    expect(hasCategory).toBe(false)
  })
  
  test('正常顯示應該包含分類標題', () => {
    wrapper.vm.searchQuery = ''
    const grouped = wrapper.vm.groupedIcons
    const hasCategory = grouped.some(item => item.type === 'category-header')
    expect(hasCategory).toBe(true)
  })
})
```

### Phase 2: Green - 實作最小功能

#### 實作檢查點
- [ ] processedIcons 計算屬性
- [ ] filteredIcons 計算屬性  
- [ ] groupedIcons 計算屬性
- [ ] 移除 activeLibrary
- [ ] 引入 IconStyleSelector
- [ ] 更新 virtualGridItems

### Phase 3: Blue - 重構優化

#### 優化檢查點
- [ ] 程式碼清理
- [ ] 效能調優
- [ ] 移除未使用程式碼
- [ ] 更新文件註解

## 📋 完整測試案例清單

### A. 架構簡化驗證 (6 tests)
1. ✅ `不應該有 activeLibrary 響應式狀態`
2. ✅ `不應該渲染標籤切換 UI (.library-tabs)`
3. ✅ `不應該有 HeroIcons/Bootstrap Icons 切換按鈕`
4. ✅ `所有圖標都應該有 isSolid 布林屬性`
5. ✅ `Bootstrap -fill 圖標應該標記為 isSolid: true`
6. ✅ `HeroIcons Solid 圖標應該標記為 isSolid: true`

### B. 篩選邏輯驗證 (8 tests)
7. ✅ `all 模式應該顯示所有圖標`
8. ✅ `outline 模式應該只顯示 isSolid: false 的圖標`
9. ✅ `solid 模式應該只顯示 isSolid: true 的圖標`
10. ✅ `篩選不應該修改原始圖標資料`
11. ✅ `IconStyleSelector 改變時應該觸發篩選`
12. ✅ `篩選後的圖標仍應保持排序`
13. ✅ `空結果時篩選不應該拋錯`
14. ✅ `無效樣式值應該 fallback 到 all 模式`

### C. 排序和顯示驗證 (7 tests)
15. ✅ `圖標應該按名稱字母順序排序`
16. ✅ `相關圖標應該相鄰顯示 (bi-heart, bi-heart-fill)`
17. ✅ `搜尋時應該返回扁平化結果（無分類標題）`
18. ✅ `正常顯示應該包含分類標題`
19. ✅ `分類標題應該有 fullRow: true 屬性`
20. ✅ `分類標題應該顯示正確的圖標計數`
21. ✅ `空分類不應該顯示分類標題`

### D. 元件整合驗證 (5 tests)
22. ✅ `IconStyleSelector 應該正確整合`
23. ✅ `IconPickerSearch 功能應該正常`
24. ✅ `VirtualScrollGrid 應該正確處理 fullRow`
25. ✅ `圖標選擇事件應該正確觸發`
26. ✅ `載入狀態應該正確顯示`

### E. 效能和回歸驗證 (4 tests)
27. ✅ `大量圖標下篩選性能應該 < 100ms`
28. ✅ `搜尋響應時間應該 < 200ms`
29. ✅ `記憶體使用不應該顯著增加`
30. ✅ `現有的 icon-select 事件格式不變`

**目標**：≥ 30 個測試案例，覆蓋率 ≥ 90%

## 📦 交付物清單

### 1. 程式碼檔案
- [ ] `IconLibraryPanel.vue` - 簡化架構主元件
- [ ] `IconStyleSelector.vue` - 複製到 icon-picker/components
- [ ] `IconLibraryPanel.test.js` - 完整的 TDD 測試套件

### 2. 文件更新
- [x] `ST-014-4-FIX.md` - 本文件（架構決策記錄）
- [ ] TDD 開發日誌 - 記錄每個階段的進度

### 3. 測試報告
- [ ] 單元測試覆蓋率報告（目標 >90%）
- [ ] TDD 階段性報告（Red/Green/Blue）
- [ ] 效能基準測試（與舊版對比）

## 🎯 Definition of Done

### 功能完整性
- [ ] 移除所有標籤切換邏輯
- [ ] 篩選功能正常（全部/線條/填充）
- [ ] 搜尋功能保持原有表現
- [ ] 分類結構正確顯示

### 程式碼品質
- [ ] TDD 三階段完成（紅燈→綠燈→重構）
- [ ] 所有新測試通過
- [ ] 程式碼覆蓋率 ≥ 90%
- [ ] ESLint 檢查通過
- [ ] TypeScript 類型檢查通過

### 效能與使用者體驗
- [ ] VirtualScrollGrid 效能保持
- [ ] 圖標載入時間不增加
- [ ] 篩選切換響應時間 < 100ms
- [ ] 搜尋響應時間保持 < 200ms

### 整合驗證
- [ ] 與 IconPicker 主元件整合正常
- [ ] 現有功能無回歸問題
- [ ] 視覺外觀保持一致
- [ ] Code Review 通過

## 🚀 開發階段檢查點

### Phase 1: 準備階段 ⏱️ 30 分鐘
- [ ] 建立開發分支 `feat/icon-library-panel-tdd-refactor`
- [ ] 設置 TDD 環境和工具
- [ ] 運行基線測試，記錄初始狀態
- [ ] 閱讀技術規格，確認理解無誤

### Phase 2: 紅燈階段 ⏱️ 60 分鐘  
- [ ] 編寫 6 個架構簡化測試
- [ ] 編寫 8 個篩選邏輯測試
- [ ] 編寫 7 個排序和顯示測試
- [ ] 編寫 5 個元件整合測試
- [ ] 編寫 4 個效能回歸測試
- [ ] 確認所有測試失敗（紅燈狀態）

### Phase 3: 綠燈階段 ⏱️ 120 分鐘
- [ ] 移除標籤切換邏輯 (20 分鐘)
- [ ] 實作 processedIcons computed (30 分鐘)
- [ ] 實作 filteredIcons computed (20 分鐘)
- [ ] 更新 groupedIcons 和 virtualGridItems (40 分鐘)
- [ ] 引入 IconStyleSelector (10 分鐘)
- [ ] 確認所有測試通過（綠燈狀態）

### Phase 4: 藍燈階段 ⏱️ 60 分鐘
- [ ] 程式碼清理和優化 (30 分鐘)
- [ ] 效能測試和調優 (20 分鐘)
- [ ] 文件和註解更新 (10 分鐘)
- [ ] 最終測試套件運行

### Phase 5: 整合驗證 ⏱️ 30 分鐘
- [ ] 與 IconPicker 整合測試
- [ ] 視覺檢查和 UI 測試
- [ ] 效能基準對比
- [ ] 建立 PR 和 Code Review

**總預估時間**：5 小時 (300 分鐘)

---

## 📞 緊急聯絡和支援

**技術支援**：架構師團隊  
**產品決策**：Product Manager  
**阻塞升級**：技術主管

**開發方法**：TDD (Test-Driven Development)  
**負責人**：開發團隊  
**審查者**：架構師 + 產品經理  
**完成目標**：2025-08-19 TDD 完整週期  
**品質標準**：測試覆蓋率 ≥ 90%，效能不退化