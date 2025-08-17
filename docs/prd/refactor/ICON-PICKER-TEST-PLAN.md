# IconPicker 重構 - TDD 測試計劃

**專案**：IconPicker 元件重構  
**PRD 參考**：[ICON-PICKER-BROWNFIELD-PRD.md](./ICON-PICKER-BROWNFIELD-PRD.md)  
**Stories 參考**：[ICON-PICKER-STORIES.md](./ICON-PICKER-STORIES.md)  
**建立日期**：2025-08-17  
**測試框架**：Vitest + Vue Test Utils  

## 📋 測試策略總覽

### 🎯 測試目標
1. **確保重構後功能完全一致** - 與原版 100% 相容
2. **建立安全的重構環境** - 任何修改都有測試保護
3. **支援未來維護** - 新功能開發時有測試基礎
4. **效能保證** - 確保重構不影響效能

### 📊 測試覆蓋率目標
- **單元測試覆蓋率**: > 80%
- **整合測試覆蓋率**: > 90%
- **E2E 測試覆蓋率**: 100% 核心流程

### 🔧 測試框架配置
```javascript
// vitest.config.js
export default {
  test: {
    environment: 'jsdom',
    globals: true,
    coverage: {
      provider: 'v8',
      reporter: ['text', 'html', 'lcov'],
      threshold: {
        global: {
          branches: 80,
          functions: 80,
          lines: 80,
          statements: 80
        }
      }
    }
  }
}
```

---

## 🧪 測試分層策略

### 1. 單元測試 (Unit Tests)
**目標**：測試每個函數、方法、組件的獨立功能

#### 測試對象
- Services (IconDataLoader)
- Composables (useIconPickerState, useIconPosition 等)
- Utils (顏色計算、資料轉換等)
- 獨立元件 (VirtualScrollGrid, ColorPickerPanel 等)

#### 測試原則
- 每個函數都有測試
- Mock 外部相依性
- 測試正常情況和邊界情況
- 測試錯誤處理

### 2. 整合測試 (Integration Tests)
**目標**：測試元件間的協作和資料流

#### 測試對象
- IconPicker 與子元件整合
- IconDataLoader 與 API 整合
- VirtualScrollGrid 與面板元件整合

#### 測試原則
- 測試真實的使用者互動
- 測試資料流向正確
- 測試事件傳遞

### 3. E2E 測試 (End-to-End Tests)
**目標**：測試完整的使用者流程

#### 測試對象
- 完整的圖標選擇流程
- 搜尋功能
- 顏色選擇流程
- 圖片上傳流程

---

## 📝 TDD 開發流程

### 🔴 Red Phase - 寫失敗的測試

#### 1. 分析 Story 需求
```javascript
// 範例：ST-005 IconDataLoader 基礎架構
describe('IconDataLoader', () => {
  it('should create instance with correct initial state', () => {
    const loader = new IconDataLoader()
    expect(loader).toBeDefined()
    expect(loader.cache).toBeInstanceOf(Map)
  })
  
  it('should throw error when getEmojiData fails', async () => {
    // 這個測試會失敗，因為方法還沒實作
    const loader = new IconDataLoader()
    await expect(loader.getEmojiData()).rejects.toThrow()
  })
})
```

#### 2. 確保測試失敗
```bash
npm run test -- IconDataLoader.test.js
# 應該看到紅色的失敗訊息
```

### 🟢 Green Phase - 實作最小可用代碼

#### 1. 實作最簡單的通過版本
```javascript
// IconDataLoader.js
export class IconDataLoader {
  constructor() {
    this.cache = new Map()
  }
  
  async getEmojiData() {
    throw new Error('Not implemented')
  }
}
```

#### 2. 確保測試通過
```bash
npm run test -- IconDataLoader.test.js
# 應該看到綠色的通過訊息
```

### 🔵 Refactor Phase - 重構和優化

#### 1. 改善代碼品質
- 抽取重複邏輯
- 優化效能
- 改善可讀性

#### 2. 確保測試仍然通過
```bash
npm run test
# 所有測試都應該保持通過
```

---

## 🎯 各 Epic 測試策略

### EP-001: 基礎架構測試

#### ST-001: IconPickerOri 備份測試
```javascript
// tests/components/IconPickerOri.test.js
describe('IconPickerOri', () => {
  it('should maintain original functionality', () => {
    // 測試所有原始功能是否正常
  })
  
  it('should handle all icon types', () => {
    // 測試 5 種圖標類型
  })
})
```

#### ST-003: 測試框架配置測試
```javascript
// tests/setup/test-framework.test.js
describe('Test Framework Setup', () => {
  it('should support Vue component testing', () => {
    // 測試 Vue Test Utils 配置
  })
  
  it('should support mocking', () => {
    // 測試 mock 功能
  })
})
```

### EP-002: 服務層測試

#### ST-005: IconDataLoader 基礎測試
```javascript
// tests/services/IconDataLoader.test.js
describe('IconDataLoader', () => {
  let loader
  
  beforeEach(() => {
    loader = new IconDataLoader()
  })
  
  afterEach(() => {
    loader.clearCache()
  })
  
  describe('constructor', () => {
    it('should initialize with empty cache', () => {
      expect(loader.cache.size).toBe(0)
    })
  })
  
  describe('getEmojiData', () => {
    it('should fetch emoji data from API', async () => {
      // Mock API 回應
      const mockData = { categories: {} }
      vi.spyOn(axios, 'get').mockResolvedValue({ data: mockData })
      
      const result = await loader.getEmojiData()
      expect(result).toEqual(mockData)
    })
    
    it('should cache emoji data', async () => {
      // 測試快取機制
    })
    
    it('should handle API errors', async () => {
      // 測試錯誤處理
    })
  })
  
  describe('getIconLibraryData', () => {
    it('should merge HeroIcon and Bootstrap Icon data', async () => {
      // 測試資料合併
    })
    
    it('should maintain data format consistency', async () => {
      // 測試資料格式
    })
  })
})
```

#### ST-006: Emoji API 整合測試
```javascript
// tests/integration/EmojiAPI.test.js
describe('Emoji API Integration', () => {
  it('should load real emoji data from API', async () => {
    // 真實 API 測試（可選）
  })
  
  it('should handle network failures gracefully', async () => {
    // 網路失敗測試
  })
})
```

#### ST-008: IconPickerSearch 元件測試
```javascript
// tests/components/IconPickerSearch.test.js
describe('IconPickerSearch', () => {
  it('should render search input', () => {
    const wrapper = mount(IconPickerSearch)
    expect(wrapper.find('input').exists()).toBe(true)
  })
  
  it('should emit search query changes', async () => {
    const wrapper = mount(IconPickerSearch)
    await wrapper.find('input').setValue('test')
    expect(wrapper.emitted('update:modelValue')).toBeTruthy()
  })
  
  it('should show clear button when has value', async () => {
    const wrapper = mount(IconPickerSearch, {
      props: { modelValue: 'test' }
    })
    expect(wrapper.find('.clear-button').exists()).toBe(true)
  })
})
```

### EP-003: 共用元件測試

#### ST-009: VirtualScrollGrid 核心測試
```javascript
// tests/components/shared/VirtualScrollGrid.test.js
describe('VirtualScrollGrid', () => {
  const mockItems = Array.from({ length: 100 }, (_, i) => ({ id: i, name: `Item ${i}` }))
  
  it('should render visible items only', () => {
    const wrapper = mount(VirtualScrollGrid, {
      props: {
        items: mockItems,
        itemsPerRow: 10,
        rowHeight: 36,
        containerHeight: 180
      }
    })
    
    // 應該只渲染可見的項目（約 5 行）
    const renderedItems = wrapper.findAll('.grid-item')
    expect(renderedItems.length).toBeLessThan(100)
  })
  
  it('should handle scrolling correctly', async () => {
    // 測試滾動邏輯
  })
  
  it('should support custom item rendering via slots', () => {
    // 測試 slot 功能
  })
  
  it('should maintain performance with large datasets', () => {
    // 效能測試
  })
})
```

#### ST-010: VirtualScrollGrid Slots 測試
```javascript
describe('VirtualScrollGrid Slots', () => {
  it('should render custom content via item slot', () => {
    const wrapper = mount(VirtualScrollGrid, {
      props: { items: [{ id: 1, name: 'Test' }] },
      slots: {
        item: '<div class="custom-item">{{ item.name }}</div>'
      }
    })
    
    expect(wrapper.find('.custom-item').text()).toBe('Test')
  })
  
  it('should pass correct props to slot', () => {
    // 測試 slot props
  })
})
```

### EP-004: 面板元件測試

#### ST-012: TextIconPanel 測試
```javascript
// tests/components/TextIconPanel.test.js
describe('TextIconPanel', () => {
  it('should limit text input to 3 characters', async () => {
    const wrapper = mount(TextIconPanel)
    const input = wrapper.find('input')
    
    await input.setValue('ABCD')
    expect(input.element.value).toBe('ABC')
  })
  
  it('should calculate text color based on background', () => {
    const wrapper = mount(TextIconPanel, {
      props: { backgroundColor: '#ffffff' }
    })
    
    // 白色背景應該使用深色文字
    expect(wrapper.vm.textColor).toBe('#1f2937')
  })
  
  it('should emit text selection event', async () => {
    const wrapper = mount(TextIconPanel)
    await wrapper.find('button.apply').trigger('click')
    expect(wrapper.emitted('text-selected')).toBeTruthy()
  })
})
```

#### ST-013: EmojiPanel 測試
```javascript
// tests/components/EmojiPanel.test.js
describe('EmojiPanel', () => {
  it('should integrate with VirtualScrollGrid', () => {
    const wrapper = mount(EmojiPanel)
    expect(wrapper.findComponent(VirtualScrollGrid).exists()).toBe(true)
  })
  
  it('should load emoji data via IconDataLoader', async () => {
    const mockData = { categories: {} }
    vi.spyOn(IconDataLoader.prototype, 'getEmojiData').mockResolvedValue(mockData)
    
    const wrapper = mount(EmojiPanel)
    await wrapper.vm.$nextTick()
    
    expect(IconDataLoader.prototype.getEmojiData).toHaveBeenCalled()
  })
  
  it('should handle skin tone selection', async () => {
    // 測試膚色選擇
  })
  
  it('should filter emojis by search query', async () => {
    // 測試搜尋過濾
  })
})
```

#### ST-014: IconLibraryPanel 測試
```javascript
// tests/components/IconLibraryPanel.test.js
describe('IconLibraryPanel', () => {
  it('should display both HeroIcon and Bootstrap Icon', async () => {
    const wrapper = mount(IconLibraryPanel)
    await wrapper.vm.$nextTick()
    
    // 應該包含兩種類型的圖標
    expect(wrapper.find('.hero-icon').exists()).toBe(true)
    expect(wrapper.find('.bootstrap-icon').exists()).toBe(true)
  })
  
  it('should switch icon styles', async () => {
    // 測試樣式切換（outline/solid/fill）
  })
  
  it('should maintain category organization', () => {
    // 測試分類顯示
  })
})
```

### EP-005: 整合測試

#### ST-017: Composables 測試
```javascript
// tests/composables/useIconPickerState.test.js
describe('useIconPickerState', () => {
  it('should manage picker state correctly', () => {
    const { isOpen, activeTab, togglePicker } = useIconPickerState()
    
    expect(isOpen.value).toBe(false)
    togglePicker()
    expect(isOpen.value).toBe(true)
  })
  
  it('should sync with localStorage', () => {
    // 測試狀態持久化
  })
})
```

#### ST-018: 主 IconPicker 整合測試
```javascript
// tests/integration/IconPicker.test.js
describe('IconPicker Integration', () => {
  it('should integrate all panel components', () => {
    const wrapper = mount(IconPicker)
    
    expect(wrapper.findComponent(TextIconPanel).exists()).toBe(true)
    expect(wrapper.findComponent(EmojiPanel).exists()).toBe(true)
    expect(wrapper.findComponent(IconLibraryPanel).exists()).toBe(true)
    expect(wrapper.findComponent(ImageUploadPanel).exists()).toBe(true)
    expect(wrapper.findComponent(ColorPickerPanel).exists()).toBe(true)
  })
  
  it('should maintain original interface compatibility', () => {
    // 測試 props 和 events 相容性
  })
  
  it('should handle tab switching correctly', async () => {
    // 測試頁籤切換
  })
})
```

### EP-006: E2E 和效能測試

#### ST-019: E2E 測試套件
```javascript
// tests/e2e/IconPicker.e2e.test.js
describe('IconPicker E2E', () => {
  it('should complete full icon selection flow', async () => {
    // 測試完整的圖標選擇流程
    const page = await browser.newPage()
    await page.goto('/profile')
    
    // 點擊圖標選擇器
    await page.click('.icon-picker-button')
    
    // 切換到 emoji 頁籤
    await page.click('[data-tab="emoji"]')
    
    // 選擇一個 emoji
    await page.click('.emoji-button:first-child')
    
    // 驗證選擇結果
    const selectedIcon = await page.$eval('.selected-icon', el => el.textContent)
    expect(selectedIcon).toBeTruthy()
  })
  
  it('should search and filter icons', async () => {
    // 測試搜尋功能
  })
})
```

#### ST-020: 效能測試
```javascript
// tests/performance/IconPicker.perf.test.js
describe('IconPicker Performance', () => {
  it('should load within acceptable time', async () => {
    const startTime = performance.now()
    
    const wrapper = mount(IconPicker)
    await wrapper.vm.$nextTick()
    
    const loadTime = performance.now() - startTime
    expect(loadTime).toBeLessThan(1000) // 1 秒內載入
  })
  
  it('should maintain smooth scrolling', async () => {
    // 測試滾動效能
  })
  
  it('should not cause memory leaks', () => {
    // 測試記憶體洩漏
  })
})
```

---

## 🔧 測試工具和配置

### Mock 策略
```javascript
// tests/setup/mocks.js
// Mock IconService
vi.mock('@/services/IconService', () => ({
  IconService: vi.fn().mockImplementation(() => ({
    fetchEmojis: vi.fn().mockResolvedValue(mockEmojiData),
    fetchHeroIcons: vi.fn().mockResolvedValue(mockHeroIconData),
    fetchBootstrapIcons: vi.fn().mockResolvedValue(mockBootstrapIconData)
  }))
}))

// Mock VirtualScroll
vi.mock('@/components/common/VirtualScroll.vue', () => ({
  default: {
    name: 'VirtualScroll',
    template: '<div class="virtual-scroll-mock"><slot /></div>'
  }
}))
```

### 測試資料
```javascript
// tests/fixtures/iconData.js
export const mockEmojiData = {
  categories: {
    smileys_emotion: {
      name: '表情與情感',
      subgroups: {
        face_smiling: {
          emojis: [
            { emoji: '😀', name: 'grinning face' },
            { emoji: '😃', name: 'grinning face with big eyes' }
          ]
        }
      }
    }
  }
}

export const mockHeroIconData = [
  { name: 'academic-cap', component: 'AcademicCapIcon' },
  { name: 'adjustments', component: 'AdjustmentsIcon' }
]
```

### 測試輔助工具
```javascript
// tests/utils/testHelpers.js
export function createWrapper(component, options = {}) {
  return mount(component, {
    global: {
      plugins: [createTestingPinia()],
      stubs: ['Teleport']
    },
    ...options
  })
}

export function waitForVirtualScroll(wrapper) {
  return new Promise(resolve => {
    setTimeout(resolve, 100) // 等待虛擬滾動計算完成
  })
}
```

---

## 📊 測試執行和報告

### 測試指令
```bash
# 執行所有測試
npm run test

# 執行特定測試檔案
npm run test IconDataLoader.test.js

# 執行測試並生成覆蓋率報告
npm run test:coverage

# 監聽模式執行測試
npm run test:watch

# 執行 E2E 測試
npm run test:e2e
```

### 覆蓋率報告
```javascript
// 期望的覆蓋率報告結構
Coverage Summary:
├── Services: 95% coverage
│   ├── IconDataLoader: 98%
│   └── IconSearchService: 92%
├── Components: 85% coverage
│   ├── VirtualScrollGrid: 90%
│   ├── EmojiPanel: 88%
│   ├── IconLibraryPanel: 85%
│   ├── TextIconPanel: 90%
│   ├── ImageUploadPanel: 80%
│   └── ColorPickerPanel: 82%
├── Composables: 90% coverage
│   ├── useIconPickerState: 95%
│   ├── useIconPosition: 88%
│   ├── useIconSelection: 92%
│   └── useColorManagement: 85%
└── Overall: 87% coverage
```

---

## ⚠️ 測試注意事項

### 1. 測試隔離
- 每個測試都應該獨立執行
- 使用 beforeEach/afterEach 清理狀態
- Mock 外部相依性

### 2. 測試資料
- 使用固定的測試資料
- 避免依賴外部 API
- 測試邊界情況

### 3. 效能考量
- 測試執行時間應該 < 30 秒
- 避免過度複雜的測試設置
- 使用 mock 減少實際 API 調用

### 4. 維護性
- 測試程式碼也要保持可讀性
- 避免重複的測試邏輯
- 定期檢查和更新測試

---

## 🎯 測試檢查清單

### 開發前檢查
- [ ] 理解 Story 的驗收條件
- [ ] 確定測試範圍和邊界
- [ ] 準備測試資料和 Mock
- [ ] 寫出失敗的測試案例

### 開發中檢查
- [ ] 遵循 TDD 循環（Red-Green-Refactor）
- [ ] 測試覆蓋率保持在目標以上
- [ ] 每次提交前執行測試
- [ ] 確保所有測試都通過

### 開發後檢查
- [ ] 執行完整測試套件
- [ ] 檢查測試覆蓋率報告
- [ ] 執行效能基準測試
- [ ] 更新測試文件

---

**相關文件**：
- [PRD 文件](./ICON-PICKER-BROWNFIELD-PRD.md)
- [Epic 定義](./ICON-PICKER-EPICS.md)
- [User Stories](./ICON-PICKER-STORIES.md)
- [開發交接文件](./ICON-PICKER-HANDOVER.md) (即將建立)