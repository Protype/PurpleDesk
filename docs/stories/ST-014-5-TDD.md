# ST-014-5-TDD: IconLibraryPanel TDD 開發指南

**建立日期**：2025-08-19  
**開發方法**：Test-Driven Development  
**相關 Story**：ST-014-4-FIX  
**開發者**：開發團隊

## 🎯 TDD 開發目標

將 IconLibraryPanel 從「標籤切換 + 複雜變體」架構重構為「統一篩選」架構，使用 TDD 方法確保品質。

## 🔴 Phase 1: Red - 編寫失敗測試

### 步驟 1.1：建立測試骨架

```bash
# 建立測試分支
git checkout -b feat/icon-library-panel-tdd-refactor

# 運行現有測試（建立基線）
npm test -- IconLibraryPanel.test.js
```

### 步驟 1.2：編寫架構簡化測試

在 `IconLibraryPanel.test.js` 新增：

```javascript
describe('簡化架構重構', () => {
  describe('移除標籤切換', () => {
    it('不應該有 activeLibrary 響應式狀態', () => {
      const wrapper = mount(IconLibraryPanel, { props: defaultProps })
      expect(wrapper.vm.activeLibrary).toBeUndefined()
    })

    it('不應該渲染標籤切換 UI', () => {
      const wrapper = mount(IconLibraryPanel, { props: defaultProps })
      expect(wrapper.find('.library-tabs').exists()).toBe(false)
    })

    it('不應該有標籤切換按鈕', () => {
      const wrapper = mount(IconLibraryPanel, { props: defaultProps })
      const buttons = wrapper.findAll('button')
      const hasLibraryButtons = buttons.some(btn => 
        btn.text().includes('HeroIcons') || btn.text().includes('Bootstrap Icons')
      )
      expect(hasLibraryButtons).toBe(false)
    })
  })

  describe('isSolid 標記系統', () => {
    it('所有圖標都應該有 isSolid 屬性', async () => {
      const wrapper = mount(IconLibraryPanel, { props: defaultProps })
      await flushPromises()
      
      const icons = wrapper.vm.processedIcons || []
      expect(icons.length).toBeGreaterThan(0)
      icons.forEach(icon => {
        expect(icon).toHaveProperty('isSolid')
        expect(typeof icon.isSolid).toBe('boolean')
      })
    })

    it('Bootstrap -fill 圖標應該標記為 isSolid: true', async () => {
      const wrapper = mount(IconLibraryPanel, { props: defaultProps })
      await flushPromises()
      
      const icons = wrapper.vm.processedIcons || []
      const fillIcons = icons.filter(icon => icon.class?.endsWith('-fill'))
      fillIcons.forEach(icon => {
        expect(icon.isSolid).toBe(true)
      })
    })

    it('HeroIcons Solid 圖標應該標記為 isSolid: true', async () => {
      const wrapper = mount(IconLibraryPanel, { props: defaultProps })
      await flushPromises()
      
      const icons = wrapper.vm.processedIcons || []
      const solidIcons = icons.filter(icon => icon.component?.includes('Solid'))
      solidIcons.forEach(icon => {
        expect(icon.isSolid).toBe(true)
      })
    })
  })
})
```

### 步驟 1.3：編寫篩選邏輯測試

```javascript
describe('篩選邏輯', () => {
  let wrapper

  beforeEach(async () => {
    wrapper = mount(IconLibraryPanel, { props: defaultProps })
    await flushPromises()
  })

  it('all 模式應該顯示所有圖標', () => {
    wrapper.vm.selectedStyle = 'all'
    const filtered = wrapper.vm.filteredIcons
    const processed = wrapper.vm.processedIcons
    expect(filtered.length).toBe(processed.length)
  })

  it('outline 模式應該只顯示 isSolid: false 的圖標', () => {
    wrapper.vm.selectedStyle = 'outline'
    const filtered = wrapper.vm.filteredIcons
    filtered.forEach(icon => {
      expect(icon.isSolid).toBe(false)
    })
  })

  it('solid 模式應該只顯示 isSolid: true 的圖標', () => {
    wrapper.vm.selectedStyle = 'solid'
    const filtered = wrapper.vm.filteredIcons
    filtered.forEach(icon => {
      expect(icon.isSolid).toBe(true)
    })
  })

  it('篩選不應該修改原始圖標資料', () => {
    const originalCount = wrapper.vm.processedIcons.length
    wrapper.vm.selectedStyle = 'outline'
    expect(wrapper.vm.processedIcons.length).toBe(originalCount)
  })
})
```

### 步驟 1.4：編寫排序和顯示測試

```javascript
describe('排序和分類顯示', () => {
  let wrapper

  beforeEach(async () => {
    wrapper = mount(IconLibraryPanel, { props: defaultProps })
    await flushPromises()
  })

  it('圖標應該按名稱字母順序排序', () => {
    const icons = wrapper.vm.processedIcons
    for (let i = 1; i < icons.length; i++) {
      const nameA = icons[i-1].name || icons[i-1].class || icons[i-1].component || ''
      const nameB = icons[i].name || icons[i].class || icons[i].component || ''
      expect(nameA.localeCompare(nameB)).toBeLessThanOrEqual(0)
    }
  })

  it('搜尋時應該返回扁平化結果（無分類標題）', () => {
    wrapper.vm.searchQuery = 'heart'
    const grouped = wrapper.vm.groupedIcons
    const hasCategoryHeader = grouped.some(item => item.type === 'category-header')
    expect(hasCategoryHeader).toBe(false)
  })

  it('正常顯示應該包含分類標題', () => {
    wrapper.vm.searchQuery = ''
    const grouped = wrapper.vm.groupedIcons
    const hasCategoryHeader = grouped.some(item => item.type === 'category-header')
    expect(hasCategoryHeader).toBe(true)
  })

  it('分類標題應該有 fullRow 屬性', () => {
    wrapper.vm.searchQuery = ''
    const grouped = wrapper.vm.groupedIcons
    const categoryHeaders = grouped.filter(item => item.type === 'category-header')
    categoryHeaders.forEach(header => {
      expect(header.fullRow).toBe(true)
    })
  })
})
```

### 步驟 1.5：執行測試（應該失敗）

```bash
# 運行測試，確認紅燈狀態
npm test -- IconLibraryPanel.test.js

# 記錄失敗的測試案例
echo "Phase 1 紅燈測試結果：" > TDD_LOG.md
npm test -- IconLibraryPanel.test.js >> TDD_LOG.md
```

## 🟢 Phase 2: Green - 實作最小功能

### 步驟 2.1：移除標籤切換邏輯

```javascript
// 在 IconLibraryPanel.vue setup() 中移除：
// const activeLibrary = ref('heroicons') // <- 刪除這行

// 移除模板中的 library-tabs 區塊
```

### 步驟 2.2：實作 processedIcons

```javascript
// 新增到 setup() 中
const processedIcons = computed(() => {
  const allIcons = [...(allIcons.value.data?.heroicons || []), 
                   ...Object.values(allIcons.value.data?.bootstrap || {}).flat()]
  
  return allIcons.map(icon => ({
    ...icon,
    isSolid: icon.class?.endsWith('-fill') || 
             icon.component?.includes('Solid') ||
             false
  }))
  .sort((a, b) => {
    const nameA = a.name || a.class || a.component || ''
    const nameB = b.name || b.class || b.component || ''
    return nameA.localeCompare(nameB)
  })
})
```

### 步驟 2.3：實作 filteredIcons

```javascript
const filteredIcons = computed(() => {
  const style = selectedStyle.value
  
  if (style === 'all' || !style) {
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

### 步驟 2.4：更新 groupedIcons 和 virtualGridItems

```javascript
// 基於 filteredIcons 重構現有邏輯
const groupedIcons = computed(() => {
  const query = searchQuery.value.toLowerCase().trim()
  
  if (query) {
    return filteredIcons.value.filter(icon => {
      const name = (icon.name || '').toLowerCase()
      const keywords = (icon.keywords || []).join(' ').toLowerCase()
      return name.includes(query) || keywords.includes(query)
    })
  }
  
  // 分類結構邏輯...
})
```

### 步驟 2.5：引入 IconStyleSelector

```javascript
// 複製元件檔案
cp resources/js/components/common/IconStyleSelector.vue \
   resources/js/features/icon-picker/components/

// 更新 imports
import IconStyleSelector from './IconStyleSelector.vue'

// 替換模板中的 VariantSelector
```

### 步驟 2.6：測試綠燈狀態

```bash
# 運行測試，目標：所有新測試通過
npm test -- IconLibraryPanel.test.js

# 記錄成功結果
echo "Phase 2 綠燈測試結果：" >> TDD_LOG.md
npm test -- IconLibraryPanel.test.js >> TDD_LOG.md
```

## 🔵 Phase 3: Blue - 重構優化

### 步驟 3.1：程式碼清理

- [ ] 移除未使用的 import
- [ ] 刪除 activeLibrary 相關程式碼
- [ ] 清理註解和 console.log
- [ ] 統一變數命名

### 步驟 3.2：效能優化

- [ ] 確認 computed 屬性沒有不必要的重複計算
- [ ] 檢查 VirtualScrollGrid 整合是否最佳化
- [ ] 驗證大量圖標下的效能表現

### 步驟 3.3：最終測試

```bash
# 完整測試套件
npm test

# Linting 檢查
npm run lint

# TypeScript 檢查
npm run typecheck

# 記錄最終結果
echo "Phase 3 重構完成測試結果：" >> TDD_LOG.md
npm test >> TDD_LOG.md
```

## ✅ 完成檢查清單

### Phase 1 (Red) 檢查點
- [ ] 所有新測試編寫完成
- [ ] 測試執行確認失敗（紅燈）
- [ ] 失敗原因符合預期
- [ ] 測試覆蓋所有關鍵功能點

### Phase 2 (Green) 檢查點  
- [ ] 所有測試通過（綠燈）
- [ ] 功能正確實作
- [ ] 沒有破壞現有功能
- [ ] 程式碼可以正常運行

### Phase 3 (Blue) 檢查點
- [ ] 程式碼整理完成
- [ ] 效能指標達標
- [ ] 所有檢查通過
- [ ] 程式碼 review ready

## 📊 TDD 指標追蹤

| 階段 | 測試數量 | 通過率 | 覆蓋率 | 時間 |
|------|----------|--------|--------|------|
| Red  | X        | 0%     | -      | -    |
| Green| X        | 100%   | >80%   | -    |  
| Blue | X        | 100%   | >90%   | -    |

---

**TDD 原則**：
1. 不寫產品代碼，除非有失敗的測試要求你這麼做
2. 不寫超過一個測試，除非它剛好失敗
3. 不寫超過剛好讓測試通過的產品代碼