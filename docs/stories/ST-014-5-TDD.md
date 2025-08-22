# ST-014-5-TDD: IconLibraryPanel TDD 剩餘開發指南

**建立日期**：2025-08-19  
**更新日期**：2025-08-22
**開發方法**：Test-Driven Development  
**相關 Story**：ST-014-4-FIX  
**開發者**：開發團隊
**狀態**：部分完成，剩餘 15% 工作

## 🏗️ 目前開發狀況

### ✅ 已完成的重構 (85%)
- 標籤切換邏輯已移除
- 扁平化 API 格式已適配
- IconStyleSelector 已整合
- VirtualScrollGrid 架構完整保持
- 18 個測試案例全部通過

### 🎯 剩餘 TDD 開發目標 (15%)

基於目前程式碼狀態，完成最後的簡化重構：
1. 加入 `isSolid` 標記系統
2. IconStyleSelector 加入 "all" 選項
3. 簡化篩選邏輯
4. 調整排序為按名稱統一排序

## 🔴 Phase 1: Red - 編寫剩餘功能的失敗測試

### 📋 基線檢查

```bash
# 目前基線：18 個測試全部通過
npm test -- IconLibraryPanel.test.js

# ✅ 結果：18 passed，架構已大幅簡化
```

### 步驟 1.1：編寫 isSolid 標記系統測試

在既有的 `IconLibraryPanel.test.js` 中新增：

```javascript
describe('🚧 剩餘重構項目', () => {
  describe('isSolid 標記系統', () => {
    it('所有圖標都應該有 isSolid 布林屬性', async () => {
      const wrapper = mount(IconLibraryPanel, { props: defaultProps })
      await flushPromises()
      
      const icons = wrapper.vm.processedIconsData || []
      expect(icons.length).toBeGreaterThan(0)
      icons.forEach(icon => {
        expect(icon).toHaveProperty('isSolid')
        expect(typeof icon.isSolid).toBe('boolean')
      })
    })

    it('Bootstrap Icons 以 -fill 結尾的應標記為 isSolid: true', async () => {
      const wrapper = mount(IconLibraryPanel, { props: defaultProps })
      await flushPromises()
      
      const icons = wrapper.vm.processedIconsData || []
      const fillIcons = icons.filter(icon => 
        icon.type === 'bootstrap-icons' && 
        (icon.value || '').endsWith('-fill')
      )
      expect(fillIcons.length).toBeGreaterThan(0)
      fillIcons.forEach(icon => {
        expect(icon.isSolid).toBe(true)
      })
    })

    it('HeroIcons variant_type 為 solid 的應標記為 isSolid: true', async () => {
      const wrapper = mount(IconLibraryPanel, { props: defaultProps })
      await flushPromises()
      
      const icons = wrapper.vm.processedIconsData || []
      const solidIcons = icons.filter(icon => 
        icon.type === 'heroicons' && 
        icon.variant_type === 'solid'
      )
      expect(solidIcons.length).toBeGreaterThan(0)
      solidIcons.forEach(icon => {
        expect(icon.isSolid).toBe(true)
      })
    })

    it('其他圖標應標記為 isSolid: false', async () => {
      const wrapper = mount(IconLibraryPanel, { props: defaultProps })
      await flushPromises()
      
      const icons = wrapper.vm.processedIconsData || []
      const outlineIcons = icons.filter(icon => 
        (icon.type === 'heroicons' && icon.variant_type === 'outline') ||
        (icon.type === 'bootstrap-icons' && !icon.value?.endsWith('-fill'))
      )
      expect(outlineIcons.length).toBeGreaterThan(0)
      outlineIcons.forEach(icon => {
        expect(icon.isSolid).toBe(false)
      })
    })
  })
  
  describe('IconStyleSelector all 選項', () => {
    it('IconStyleSelector 應該支援 all 選項', async () => {
      const wrapper = mount(IconLibraryPanel, { props: defaultProps })
      
      // 檢查 IconStyleSelector 是否支援 all 模式
      wrapper.vm.selectedStyle = 'all'
      await nextTick()
      
      const allIcons = wrapper.vm.styleFilteredIcons
      const processedIcons = wrapper.vm.processedIconsData
      expect(allIcons.length).toBe(processedIcons.length)
    })
  })

  describe('簡化篩選邏輯', () => {
    it('篩選邏輯應基於 isSolid 屬性而非複雜條件', async () => {
      const wrapper = mount(IconLibraryPanel, { props: defaultProps })
      await flushPromises()
      
      // outline 模式測試
      wrapper.vm.selectedStyle = 'outline'
      await nextTick()
      const outlineIcons = wrapper.vm.styleFilteredIcons
      outlineIcons.forEach(icon => {
        expect(icon.isSolid).toBe(false)
      })
      
      // solid 模式測試
      wrapper.vm.selectedStyle = 'solid'
      await nextTick()
      const solidIcons = wrapper.vm.styleFilteredIcons
      solidIcons.forEach(icon => {
        expect(icon.isSolid).toBe(true)
      })
    })
  })
  
  describe('統一排序', () => {
    it('圖標應該按名稱統一排序，不分類型', async () => {
      const wrapper = mount(IconLibraryPanel, { props: defaultProps })
      await flushPromises()
      
      const icons = wrapper.vm.processedIconsData
      for (let i = 1; i < icons.length; i++) {
        const nameA = icons[i-1].displayName || icons[i-1].name || icons[i-1].value || ''
        const nameB = icons[i].displayName || icons[i].name || icons[i].value || ''
        expect(nameA.toLowerCase().localeCompare(nameB.toLowerCase())).toBeLessThanOrEqual(0)
      }
    })
  })
})
```

### 步驟 1.2：執行測試確認紅燈狀態

```bash
# 運行新增的測試，確認失敗（紅燈）
npm test -- IconLibraryPanel.test.js

# 預期結果：
# ❌ 新增的 8 個測試應該失敗
# ✅ 現有的 18 個測試應該通過
# 總計：18 passed, 8 failed, 26 total
```

## 🟢 Phase 2: Green - 實作剩餘最小功能

### 基於現況的實作策略

目前程式碼已經有完整的基礎架構，只需要針對剩餘 4 個項目進行調整：

### 步驟 2.1：在 processedIconsData 中加入 isSolid 標記

```javascript
// 在 IconLibraryPanel.vue 的 processedIconsData computed 中
const processedIconsData = computed(() => {
  if (!allIcons.value?.data) return []
  
  const allIconsList = [
    ...(allIcons.value.data.heroicons || []), 
    ...Object.values(allIcons.value.data.bootstrap || {}).flat()
  ]
  
  // 處理圖標：加入 isSolid 標記 + 統一排序
  const processedIcons = allIconsList.map(icon => {
    return {
      ...icon,
      displayName: icon.name || icon.value || icon.class || icon.component,
      // ✨ 新增：isSolid 標記系統
      isSolid: (icon.value && icon.value.endsWith('-fill')) || 
               (icon.variant_type === 'solid') ||
               false
    }
  })
  
  // ✨ 調整：按名稱統一排序（不分類型）
  return processedIcons.sort((a, b) => {
    const nameA = a.displayName || ''
    const nameB = b.displayName || ''
    return nameA.toLowerCase().localeCompare(nameB.toLowerCase())
  })
})
```

### 步驟 2.2：簡化篩選邏輯

```javascript
// 替換複雜的 styleFilteredIcons computed
const styleFilteredIcons = computed(() => {
  const style = selectedStyle.value
  
  // ✨ 新增：支援 all 模式
  if (style === 'all') {
    return processedIconsData.value
  }
  
  // ✨ 簡化：基於 isSolid 的簡單篩選
  return processedIconsData.value.filter(icon => {
    if (style === 'outline') {
      return !icon.isSolid
    } else if (style === 'solid') {
      return icon.isSolid
    }
    return true
  })
})
```

### 步驟 2.3：更新 IconStyleSelector 支援 all 選項

```javascript
// 在 IconStyleSelector.vue 中加入 all 選項
props: {
  modelValue: {
    type: String,
    default: 'outline',
    validator: (value) => ['outline', 'solid', 'all'].includes(value)  // ✨ 加入 all
  }
}
```

```html
<!-- 在模板中加入 All 選項 -->
<button
  @click="selectStyle('all')"
  :class="[
    modelValue === 'all' ? 'bg-primary-50 text-primary-700' : 'text-gray-700 hover:bg-gray-50',
    'w-full px-3 py-2 text-left text-sm flex items-center space-x-2 transition-colors'
  ]"
>
  <component :is="AllIcon" class="w-5 h-5" />
  <span>All</span>
  <!-- 勾選圖標 -->
</button>
```

### 步驟 2.4：執行測試確認綠燈

```bash
# 運行所有測試，目標：26 passed (18 + 8)
npm test -- IconLibraryPanel.test.js

# 記錄成功結果
echo "Phase 2 綠燈測試結果：" >> TDD_LOG.md  
npm test -- IconLibraryPanel.test.js >> TDD_LOG.md
```

## 🔵 Phase 3: Blue - 重構優化和清理

### 步驟 3.1：程式碼清理

- [ ] 移除 `useIconVariants` composable（不再需要）
- [ ] 清理複雜的條件判斷邏輯
- [ ] 統一變數命名風格  
- [ ] 移除未使用的 imports

### 步驟 3.2：效能驗證

```bash  
# 效能測試
npm test -- IconLibraryPanel.test.js --coverage

# 確認覆蓋率 ≥ 90%
# 確認新的篩選邏輯效能不劣化
```

### 步驟 3.3：最終整合測試

```bash
# 完整測試套件  
npm test

# Linting 檢查
npm run lint

# TypeScript 檢查  
npm run typecheck
```

## ⏱️ 剩餘開發時間估算

### TDD 階段預估
- Phase 1 (Red): 20 分鐘 - 編寫 8 個新測試
- Phase 2 (Green): 45 分鐘 - 實作 4 個調整項目  
- Phase 3 (Blue): 25 分鐘 - 重構和清理

**總計**：約 1.5 小時完成剩餘 15% 工作

## ✅ 完成後的預期狀態

### 最終測試結果
- **測試數量**：26 個（18 個既有 + 8 個新增）
- **通過率**：100%  
- **覆蓋率**：≥ 90%
- **效能**：篩選響應 < 50ms

### 程式碼品質
- **複雜度**：大幅降低（移除複雜條件判斷）
- **可讀性**：顯著提升（isSolid 語意明確）
- **維護性**：更易擴展（統一的標記系統）

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