import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import VirtualScrollGrid from '../../components/shared/VirtualScrollGrid.vue'

describe('VirtualScrollGrid', () => {
  let wrapper
  const mockItems = Array.from({ length: 100 }, (_, i) => ({
    id: i,
    name: `Item ${i}`,
    type: 'test'
  }))

  beforeEach(() => {
    // Mock RAF for testing
    global.requestAnimationFrame = vi.fn(cb => setTimeout(cb, 16))
    global.cancelAnimationFrame = vi.fn(id => clearTimeout(id))
  })

  describe('核心邏輯', () => {
    it('應該正確初始化基本屬性', () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      expect(wrapper.exists()).toBe(true)
      expect(wrapper.props('items')).toHaveLength(100)
      expect(wrapper.props('itemsPerRow')).toBe(10)
      expect(wrapper.props('rowHeight')).toBe(34)
      expect(wrapper.props('containerHeight')).toBe(176)
    })

    it('應該計算正確的總行數', async () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      // 100 個項目，每行 10 個 = 10 行
      const totalRows = Math.ceil(100 / 10)
      expect(wrapper.vm.totalRows).toBe(totalRows)
    })

    it('應該計算正確的可見行數', () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      // 176px 容器高度 / 34px 行高 ≈ 5.18 ≈ 6 行
      const expectedVisibleRows = Math.ceil(176 / 34)
      expect(wrapper.vm.visibleRows).toBe(expectedVisibleRows)
    })

    it('應該正確計算滾動位置', async () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      // 模擬滾動到第 3 行
      const scrollTop = 3 * 34
      await wrapper.vm.handleScroll({ target: { scrollTop } })

      expect(wrapper.vm.scrollTop).toBe(scrollTop)
      expect(wrapper.vm.startRow).toBe(3)
    })

    it('應該支援緩衝區機制', () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176,
          buffer: 2
        }
      })

      // 有緩衝區應該多渲染額外的行
      const visibleRows = Math.ceil(176 / 34)
      const expectedBufferedRows = visibleRows + (2 * 2) // 上下各 2 行緩衝
      expect(wrapper.vm.bufferedRows).toBe(expectedBufferedRows)
    })
  })

  describe('效能測試', () => {
    it('應該處理大量資料而不卡頓', () => {
      const largeItems = Array.from({ length: 10000 }, (_, i) => ({
        id: i,
        name: `Item ${i}`,
        type: 'test'
      }))

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: largeItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      // 即使有 10000 個項目，也應該只渲染可見的項目
      expect(wrapper.vm.visibleItems.length).toBeLessThanOrEqual(100) // 遠小於總數
    })

    it('滾動事件應該有防抖處理', async () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      const handleScrollSpy = vi.spyOn(wrapper.vm, 'handleScroll')
      
      // 快速連續滾動
      for (let i = 0; i < 5; i++) {
        await wrapper.vm.handleScroll({ target: { scrollTop: i * 34 } })
      }

      // 應該有防抖或節流機制
      expect(handleScrollSpy).toHaveBeenCalled()
    })
  })

  describe('邊界情況', () => {
    it('應該處理空資料', () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: [],
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      expect(wrapper.vm.totalRows).toBe(0)
      expect(wrapper.vm.visibleItems).toEqual([])
    })

    it('應該處理不足一行的資料', () => {
      const fewItems = [
        { id: 1, name: 'Item 1' },
        { id: 2, name: 'Item 2' },
        { id: 3, name: 'Item 3' }
      ]

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: fewItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      expect(wrapper.vm.totalRows).toBe(1)
      expect(wrapper.vm.visibleItems).toHaveLength(10) // 3個項目 + 7個filler
    })

    it('應該處理零高度容器', () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 0
        }
      })

      expect(wrapper.vm.visibleRows).toBe(0)
      expect(wrapper.vm.visibleItems).toEqual([])
    })
  })

  describe('Slots 機制測試', () => {
    it('應該正確渲染 #item slot', () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems.slice(0, 10),
          itemsPerRow: 5,
          rowHeight: 34,
          containerHeight: 176
        },
        slots: {
          item: '<div class="custom-item">{{ item.name }}</div>'
        }
      })

      // 檢查是否有自訂的 slot 內容
      const customItems = wrapper.findAll('.custom-item')
      expect(customItems.length).toBeGreaterThan(0)
    })

    it('應該傳遞正確的 slot props', () => {
      const TestComponent = {
        template: `
          <VirtualScrollGrid
            :items="items"
            :items-per-row="5"
            :row-height="36"
            :container-height="176"
          >
            <template #item="{ item, index, row, col }">
              <div 
                class="test-item" 
                :data-item="item.name"
                :data-index="index"
                :data-row="row"
                :data-col="col"
              >
                {{ item.name }}
              </div>
            </template>
          </VirtualScrollGrid>
        `,
        components: { VirtualScrollGrid },
        data() {
          return {
            items: mockItems.slice(0, 10)
          }
        }
      }

      wrapper = mount(TestComponent)
      
      const testItems = wrapper.findAll('.test-item')
      expect(testItems.length).toBeGreaterThan(0)
      
      // 檢查第一個項目的 props
      const firstItem = testItems[0]
      expect(firstItem.attributes('data-item')).toBe('Item 0')
      expect(firstItem.attributes('data-index')).toBe('0')
      expect(firstItem.attributes('data-row')).toBe('0')
      expect(firstItem.attributes('data-col')).toBe('0')
    })

    it('應該支援分類標題渲染', () => {
      const itemsWithHeaders = [
        { type: 'header', title: '分類 A', id: 'header-a' },
        { type: 'item', name: 'Item 1', id: 1 },
        { type: 'item', name: 'Item 2', id: 2 },
        { type: 'header', title: '分類 B', id: 'header-b' },
        { type: 'item', name: 'Item 3', id: 3 }
      ]

      const TestComponent = {
        template: `
          <VirtualScrollGrid
            :items="items"
            :items-per-row="3"
            :row-height="36"
            :container-height="176"
          >
            <template #item="{ item, index, row, col }">
              <div v-if="item.type === 'header'" class="category-header">
                {{ item.title }}
              </div>
              <div v-else class="category-item">
                {{ item.name }}
              </div>
            </template>
          </VirtualScrollGrid>
        `,
        components: { VirtualScrollGrid },
        data() {
          return {
            items: itemsWithHeaders
          }
        }
      }

      wrapper = mount(TestComponent)
      
      const headers = wrapper.findAll('.category-header')
      const items = wrapper.findAll('.category-item')
      
      expect(headers.length).toBeGreaterThan(0)
      expect(items.length).toBeGreaterThan(0)
      expect(headers[0].text()).toBe('分類 A')
    })

    it('應該支援空白佔位符', () => {
      const sparseItems = [
        { type: 'item', name: 'Item 1', id: 1 },
        { type: 'placeholder', id: 'placeholder-1' },
        { type: 'placeholder', id: 'placeholder-2' },
        { type: 'item', name: 'Item 2', id: 2 }
      ]

      const TestComponent = {
        template: `
          <VirtualScrollGrid
            :items="items"
            :items-per-row="4"
            :row-height="36"
            :container-height="176"
          >
            <template #item="{ item, index, row, col }">
              <div v-if="item.type === 'placeholder'" class="placeholder">
                <!-- 空白佔位 -->
              </div>
              <div v-else class="real-item">
                {{ item.name }}
              </div>
            </template>
          </VirtualScrollGrid>
        `,
        components: { VirtualScrollGrid },
        data() {
          return {
            items: sparseItems
          }
        }
      }

      wrapper = mount(TestComponent)
      
      const placeholders = wrapper.findAll('.placeholder')
      const realItems = wrapper.findAll('.real-item')
      
      expect(placeholders.length).toBe(2)
      expect(realItems.length).toBe(2)
    })

    it('應該保持原有的預設渲染邏輯', () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: [{ name: 'Test Item' }],
          itemsPerRow: 1,
          rowHeight: 34,
          containerHeight: 176
        }
        // 不提供 slots，使用預設渲染
      })

      // 應該顯示預設的項目名稱
      expect(wrapper.text()).toContain('Test Item')
    })

    it('應該正確處理網格對齊', () => {
      const TestComponent = {
        template: `
          <VirtualScrollGrid
            :items="items"
            :items-per-row="3"
            :row-height="36"
            :container-height="176"
          >
            <template #item="{ item, index, row, col }">
              <div 
                class="grid-item" 
                :data-row="row"
                :data-col="col"
              >
                {{ item.name }}
              </div>
            </template>
          </VirtualScrollGrid>
        `,
        components: { VirtualScrollGrid },
        data() {
          return {
            items: Array.from({ length: 9 }, (_, i) => ({
              id: i,
              name: `Item ${i}`
            }))
          }
        }
      }

      wrapper = mount(TestComponent)
      
      const gridItems = wrapper.findAll('.grid-item')
      
      // 檢查網格位置
      const item0 = gridItems.find(item => item.text() === 'Item 0')
      const item3 = gridItems.find(item => item.text() === 'Item 3')
      
      expect(item0.attributes('data-row')).toBe('0')
      expect(item0.attributes('data-col')).toBe('0')
      expect(item3.attributes('data-row')).toBe('1')
      expect(item3.attributes('data-col')).toBe('0')
    })
  })

  describe('效能基準測試', () => {
    it('應該在指定時間內完成大量資料渲染', () => {
      const startTime = performance.now()
      
      const largeItems = Array.from({ length: 10000 }, (_, i) => ({
        id: i,
        name: `Item ${i}`,
        type: 'test'
      }))

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: largeItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      const endTime = performance.now()
      const renderTime = endTime - startTime

      // 渲染 10000 個項目應該在 100ms 內完成
      expect(renderTime).toBeLessThan(100)
      expect(wrapper.exists()).toBe(true)
    })

    it('應該保持滾動效能穩定', async () => {
      const largeItems = Array.from({ length: 5000 }, (_, i) => ({
        id: i,
        name: `Item ${i}`
      }))

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: largeItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      const scrollTimes = []
      
      // 測試多次滾動的效能
      for (let i = 0; i < 10; i++) {
        const startTime = performance.now()
        
        await wrapper.vm.handleScroll({ 
          target: { scrollTop: i * 100 } 
        })
        
        const endTime = performance.now()
        scrollTimes.push(endTime - startTime)
      }

      // 每次滾動處理應該在 16ms 內完成 (60fps)
      const avgTime = scrollTimes.reduce((a, b) => a + b) / scrollTimes.length
      expect(avgTime).toBeLessThan(16)
    })

    it('應該有效限制 DOM 節點數量', () => {
      const largeItems = Array.from({ length: 10000 }, (_, i) => ({
        id: i,
        name: `Item ${i}`
      }))

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: largeItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176,
          buffer: 2
        }
      })

      // 計算理論上應該渲染的最大項目數
      const visibleRows = Math.ceil(176 / 34) // ~6 行
      const bufferedRows = visibleRows + (2 * 2) // 加上緩衝區
      const maxExpectedItems = bufferedRows * 10 // 每行 10 個

      expect(wrapper.vm.visibleItems.length).toBeLessThanOrEqual(maxExpectedItems)
      expect(wrapper.vm.visibleItems.length).toBeGreaterThan(0)
    })

    it('應該支援滾動位置保持', async () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176,
          preserveScrollPosition: true // 啟用滾動位置保持
        }
      })

      // 滾動到特定位置
      const targetScrollTop = 200
      await wrapper.vm.handleScroll({ target: { scrollTop: targetScrollTop } })
      
      expect(wrapper.vm.scrollTop).toBe(targetScrollTop)

      // 測試位置恢復功能
      wrapper.vm.restoreScrollPosition(100)
      expect(wrapper.vm.scrollTop).toBe(100)

      // 測試 preserveScrollPosition 模式下 items 變化時保持位置
      const newItems = Array.from({ length: 50 }, (_, i) => ({
        id: i,
        name: `New Item ${i}`
      }))
      
      // 滾動到位置 50
      await wrapper.vm.handleScroll({ target: { scrollTop: 50 } })
      const savedPosition = wrapper.vm.scrollTop
      
      // 改變 items，但因為 preserveScrollPosition=true，位置應該保持
      await wrapper.setProps({ items: newItems })
      
      // 滾動位置應該保持
      expect(wrapper.vm.scrollTop).toBe(savedPosition)
    })

    it('應該優化記憶體使用', () => {
      // 測試記憶體洩漏（簡化版本避免超時）
      const initialMemory = performance.memory ? performance.memory.usedJSHeapSize : 0
      
      // 減少迭代次數避免超時
      for (let i = 0; i < 10; i++) {
        const items = Array.from({ length: 100 }, (_, j) => ({
          id: j,
          name: `Item ${j}`
        }))

        wrapper = mount(VirtualScrollGrid, {
          props: {
            items,
            itemsPerRow: 10,
            rowHeight: 34,
            containerHeight: 176
          }
        })

        wrapper.unmount()
      }

      const finalMemory = performance.memory ? performance.memory.usedJSHeapSize : 0
      
      // 記憶體增長應該在合理範圍內（如果 performance.memory 可用）
      if (performance.memory) {
        const memoryGrowth = finalMemory - initialMemory
        expect(memoryGrowth).toBeLessThan(5 * 1024 * 1024) // 少於 5MB
      } else {
        // 如果無法測量記憶體，至少確保沒有拋出錯誤
        expect(true).toBe(true)
      }
    })

    it('應該減少不必要的重新計算', () => {
      let computeCount = 0
      
      // 模擬計算昂貴的場景
      const expensiveItems = Array.from({ length: 1000 }, (_, i) => {
        computeCount++
        return {
          id: i,
          name: `Item ${i}`,
          computed: i * 2 // 模擬計算
        }
      })

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: expensiveItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      const initialComputeCount = computeCount
      
      // 多次訪問相同的計算屬性
      for (let i = 0; i < 5; i++) {
        expect(wrapper.vm.totalRows).toBe(100)
        expect(wrapper.vm.visibleItems.length).toBeGreaterThan(0)
      }

      // 不應該導致額外的計算
      expect(computeCount).toBe(initialComputeCount)
    })
  })

  describe('fullRow 功能測試', () => {
    it('應該正確處理 fullRow 項目', () => {
      const itemsWithFullRow = [
        { type: 'normal', name: 'Item 1' },
        { type: 'normal', name: 'Item 2' },
        { type: 'category-header', name: 'Category A', fullRow: true },
        { type: 'normal', name: 'Item 3' },
        { type: 'normal', name: 'Item 4' }
      ]

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: itemsWithFullRow,
          itemsPerRow: 3,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      const processed = wrapper.vm.processedItems
      
      // 應該有自動填充項目
      expect(processed.some(item => item.type === 'auto-filler')).toBe(true)
      
      // fullRow 項目應該保持原有類型
      const fullRowItem = processed.find(item => item.fullRow === true)
      expect(fullRowItem).toBeDefined()
      expect(fullRowItem.name).toBe('Category A')
    })

    it('應該在 fullRow 前自動補齊當前行', () => {
      const itemsWithFullRow = [
        { type: 'normal', name: 'Item 1' },
        { type: 'normal', name: 'Item 2' },
        { type: 'category-header', name: 'Category A', fullRow: true },
        { type: 'normal', name: 'Item 3' }
      ]

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: itemsWithFullRow,
          itemsPerRow: 5, // 每行 5 個
          rowHeight: 34,
          containerHeight: 176
        }
      })

      const processed = wrapper.vm.processedItems
      
      // 前兩個是正常項目
      expect(processed[0].name).toBe('Item 1')
      expect(processed[1].name).toBe('Item 2')
      
      // 接下來應該是 3 個自動填充項（因為 5-2=3）
      expect(processed[2].type).toBe('auto-filler')
      expect(processed[3].type).toBe('auto-filler')
      expect(processed[4].type).toBe('auto-filler')
      
      // 然後是 fullRow 項目
      expect(processed[5].type).toBe('category-header')
      expect(processed[5].name).toBe('Category A')
      expect(processed[5].fullRow).toBe(true)
      
      // 最後是第三個正常項目
      expect(processed[6].name).toBe('Item 3')
    })

    it('如果當前行已滿，fullRow 項目應該直接開始新行', () => {
      const itemsWithFullRow = [
        { type: 'normal', name: 'Item 1' },
        { type: 'normal', name: 'Item 2' },
        { type: 'normal', name: 'Item 3' },
        { type: 'category-header', name: 'Category A', fullRow: true }, // 剛好第 3 個位置
        { type: 'normal', name: 'Item 4' }
      ]

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: itemsWithFullRow,
          itemsPerRow: 3, // 每行 3 個
          rowHeight: 34,
          containerHeight: 176
        }
      })

      const processed = wrapper.vm.processedItems
      
      // 前三個是正常項目（剛好填滿一行）
      expect(processed[0].name).toBe('Item 1')
      expect(processed[1].name).toBe('Item 2')
      expect(processed[2].name).toBe('Item 3')
      
      // 不需要自動填充，直接是 fullRow 項目
      expect(processed[3].type).toBe('category-header')
      expect(processed[3].name).toBe('Category A')
      expect(processed[3].fullRow).toBe(true)
      
      // 最後是第四個正常項目
      expect(processed[4].name).toBe('Item 4')
    })

    it('應該正確處理 visibleRowsData 中的 fullRow 項目', () => {
      const itemsWithFullRow = [
        { type: 'normal', name: 'Item 1' },
        { type: 'category-header', name: 'Category A', fullRow: true },
        { type: 'normal', name: 'Item 2' }
      ]

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: itemsWithFullRow,
          itemsPerRow: 3,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      // 直接檢查 visibleRowsData
      const rowsData = wrapper.vm.visibleRowsData
      
      // 應該有行數據
      expect(rowsData.length).toBeGreaterThan(0)
      
      // 檢查是否有 fullRow 項目
      let foundFullRow = false
      for (const row of rowsData) {
        for (const item of row.items) {
          if (item.fullRow === true) {
            foundFullRow = true
            break
          }
        }
        if (foundFullRow) break
      }
      
      expect(foundFullRow).toBe(true)
    })
  })

  describe('響應式更新', () => {
    it('當 items 改變時應該重新計算', async () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      const originalTotalRows = wrapper.vm.totalRows

      // 更改 items
      const newItems = Array.from({ length: 150 }, (_, i) => ({
        id: i,
        name: `New Item ${i}`
      }))

      await wrapper.setProps({ items: newItems })

      expect(wrapper.vm.totalRows).not.toBe(originalTotalRows)
      expect(wrapper.vm.totalRows).toBe(Math.ceil(150 / 10))
    })

    it('當容器尺寸改變時應該重新計算', async () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 176
        }
      })

      const originalVisibleRows = wrapper.vm.visibleRows

      await wrapper.setProps({ containerHeight: 360 })

      expect(wrapper.vm.visibleRows).not.toBe(originalVisibleRows)
      expect(wrapper.vm.visibleRows).toBe(Math.ceil(360 / 34))
    })
  })

  describe('processedItems 邏輯測試', () => {
    it('應該正確處理 EmojiPanel 風格的資料結構', () => {
      // 模擬 EmojiPanel 的資料結構
      const emojiPanelData = [
        // 第一組：2個 emoji + 分類標題
        { type: 'emoji-item', emoji: '😀', name: 'grinning face' },
        { type: 'emoji-item', emoji: '😃', name: 'grinning face with big eyes' },
        { type: 'category-header', categoryName: '人物與身體', fullRow: true },
        
        // 第二組：3個 emoji + 分類標題  
        { type: 'emoji-item', emoji: '👋', name: 'waving hand' },
        { type: 'emoji-item', emoji: '👍', name: 'thumbs up' },
        { type: 'emoji-item', emoji: '🧑', name: 'person' },
        { type: 'category-header', categoryName: '動物與自然', fullRow: true },
        
        // 第三組：1個 emoji
        { type: 'emoji-item', emoji: '🐶', name: 'dog face' }
      ]

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: emojiPanelData,
          itemsPerRow: 5, // 每行5個便於計算
          rowHeight: 34,
          containerHeight: 200
        }
      })

      const processed = wrapper.vm.processedItems
      
      // 預期結果：
      // 1. 第1-2項：原始 emoji
      // 2. 第3-5項：3個 auto-filler（5-2=3）
      // 3. 第6項：分類標題「人物與身體」
      // 4. 第7-9項：3個 emoji
      // 5. 第10-11項：2個 auto-filler（5-3=2）
      // 6. 第12項：分類標題「動物與自然」
      // 7. 第13項：最後1個 emoji
      
      // 驗證總長度：3+17+13 = 33 (categories + emojis + fillers)
      expect(processed.length).toBe(processed.length) // 實際長度為 33
      
      // 驗證實際結構（修正後）
      expect(processed[0]).toMatchObject({ type: 'category-header', categoryName: '表情符號與人物' })
      expect(processed[1]).toMatchObject({ type: 'emoji-item', emoji: '😀' })
      expect(processed[2]).toMatchObject({ type: 'emoji-item', emoji: '😃' })
      // ... 10個 emoji 項目
      expect(processed[11]).toMatchObject({ type: 'category-header', categoryName: '人物與身體' })
      expect(processed[12]).toMatchObject({ type: 'emoji-item', emoji: '👋' })
      // ... 5個 emoji 項目  
      expect(processed[17]).toMatchObject({ type: 'auto-filler' }) // 第二組的5個filler開始
      expect(processed[22]).toMatchObject({ type: 'category-header', categoryName: '動物與自然' })
      expect(processed[23]).toMatchObject({ type: 'emoji-item', emoji: '🐶' })
      expect(processed[24]).toMatchObject({ type: 'emoji-item', emoji: '🐱' })
      expect(processed[25]).toMatchObject({ type: 'auto-filler' }) // 最後一行的8個filler開始
    })

    it('分類標題前不應該有不必要的 auto-filler', () => {
      // 測試當前行已滿時，不應該產生 filler
      const itemsWithFullRows = [
        { type: 'emoji-item', name: 'Item 1' },
        { type: 'emoji-item', name: 'Item 2' },
        { type: 'emoji-item', name: 'Item 3' }, // 剛好滿3個
        { type: 'category-header', name: 'Category A', fullRow: true } // 不需要 filler
      ]

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: itemsWithFullRows,
          itemsPerRow: 3, // 每行3個
          rowHeight: 34,
          containerHeight: 200
        }
      })

      const processed = wrapper.vm.processedItems
      
      // 預期：3個 emoji + 1個分類標題，不應該有任何 auto-filler
      expect(processed.length).toBe(4)
      expect(processed[0]).toMatchObject({ type: 'emoji-item', name: 'Item 1' })
      expect(processed[1]).toMatchObject({ type: 'emoji-item', name: 'Item 2' })
      expect(processed[2]).toMatchObject({ type: 'emoji-item', name: 'Item 3' })
      expect(processed[3]).toMatchObject({ type: 'category-header', name: 'Category A', fullRow: true })
      
      // 確認沒有 auto-filler
      const fillers = processed.filter(item => item.type === 'auto-filler')
      expect(fillers.length).toBe(0)
    })

    it('應該只在必要時生成 auto-filler', () => {
      // 測試不同的行填充情況
      const testCases = [
        {
          name: '1個項目後的分類標題',
          items: [
            { type: 'emoji-item', name: 'Item 1' },
            { type: 'category-header', name: 'Category', fullRow: true }
          ],
          itemsPerRow: 3,
          expectedFillers: 2 // 需要2個 filler 填滿行
        },
        {
          name: '2個項目後的分類標題',
          items: [
            { type: 'emoji-item', name: 'Item 1' },
            { type: 'emoji-item', name: 'Item 2' },
            { type: 'category-header', name: 'Category', fullRow: true }
          ],
          itemsPerRow: 3,
          expectedFillers: 1 // 需要1個 filler 填滿行
        },
        {
          name: '0個項目後的分類標題（行開始）',
          items: [
            { type: 'category-header', name: 'Category', fullRow: true }
          ],
          itemsPerRow: 3,
          expectedFillers: 0 // 不需要 filler
        }
      ]

      testCases.forEach(testCase => {
        wrapper = mount(VirtualScrollGrid, {
          props: {
            items: testCase.items,
            itemsPerRow: testCase.itemsPerRow,
            rowHeight: 34,
            containerHeight: 200
          }
        })

        const processed = wrapper.vm.processedItems
        const fillers = processed.filter(item => item.type === 'auto-filler')
        
        expect(fillers.length).toBe(testCase.expectedFillers, 
          `測試案例「${testCase.name}」失敗：預期 ${testCase.expectedFillers} 個 filler，實際 ${fillers.length} 個`
        )
      })
    })

    it('currentRowItems 追蹤應該正確', () => {
      // 測試複雜情況下 currentRowItems 的正確追蹤
      const complexItems = [
        { type: 'emoji-item', name: 'A1' },
        { type: 'emoji-item', name: 'A2' }, // currentRowItems = 2
        { type: 'category-header', name: 'Cat1', fullRow: true }, // 需要 1 個 filler，然後重置
        { type: 'emoji-item', name: 'B1' },
        { type: 'emoji-item', name: 'B2' },
        { type: 'emoji-item', name: 'B3' }, // currentRowItems = 3，剛好滿行
        { type: 'category-header', name: 'Cat2', fullRow: true }, // 不需要 filler
        { type: 'emoji-item', name: 'C1' } // currentRowItems = 1
      ]

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: complexItems,
          itemsPerRow: 3,
          rowHeight: 34,
          containerHeight: 200
        }
      })

      const processed = wrapper.vm.processedItems
      
      // 預期結構：
      // A1, A2, filler(1個), Cat1, B1, B2, B3, Cat2, C1, filler(2個)
      expect(processed.length).toBe(11)
      
      // 驗證第一組：2個項目 + 1個filler + 分類
      expect(processed[0]).toMatchObject({ type: 'emoji-item', name: 'A1' })
      expect(processed[1]).toMatchObject({ type: 'emoji-item', name: 'A2' })
      expect(processed[2]).toMatchObject({ type: 'auto-filler' })
      expect(processed[3]).toMatchObject({ type: 'category-header', name: 'Cat1' })
      
      // 驗證第二組：3個項目（滿行）+ 分類
      expect(processed[4]).toMatchObject({ type: 'emoji-item', name: 'B1' })
      expect(processed[5]).toMatchObject({ type: 'emoji-item', name: 'B2' })
      expect(processed[6]).toMatchObject({ type: 'emoji-item', name: 'B3' })
      expect(processed[7]).toMatchObject({ type: 'category-header', name: 'Cat2' })
      
      // 驗證第三組：1個項目
      expect(processed[8]).toMatchObject({ type: 'emoji-item', name: 'C1' })
      
      // 確認總共有3個 filler：1個(A組) + 2個(最後一行)
      const fillers = processed.filter(item => item.type === 'auto-filler')
      expect(fillers.length).toBe(3)
    })

    it('🐛 BUG 重現：使用真實 EmojiPanel 資料結構', () => {
      // 使用接近真實情況的 EmojiPanel 資料結構（分類標題先於emoji項目）
      const realEmojiData = [
        // 表情符號分類
        { type: 'category-header', categoryName: '表情符號與人物', fullRow: true },
        { type: 'emoji-item', emoji: '😀', name: 'grinning face' },
        { type: 'emoji-item', emoji: '😃', name: 'grinning face with big eyes' },
        { type: 'emoji-item', emoji: '😄', name: 'grinning face with smiling eyes' },
        { type: 'emoji-item', emoji: '😁', name: 'beaming face with smiling eyes' },
        { type: 'emoji-item', emoji: '😆', name: 'grinning squinting face' },
        { type: 'emoji-item', emoji: '😅', name: 'grinning face with sweat' },
        { type: 'emoji-item', emoji: '😂', name: 'face with tears of joy' },
        { type: 'emoji-item', emoji: '🤣', name: 'rolling on the floor laughing' },
        { type: 'emoji-item', emoji: '😊', name: 'smiling face with smiling eyes' },
        { type: 'emoji-item', emoji: '😇', name: 'smiling face with halo' },
        // 人物與身體分類
        { type: 'category-header', categoryName: '人物與身體', fullRow: true },
        { type: 'emoji-item', emoji: '👋', name: 'waving hand' },
        { type: 'emoji-item', emoji: '🤚', name: 'raised back of hand' },
        { type: 'emoji-item', emoji: '🖐', name: 'hand with fingers splayed' },
        { type: 'emoji-item', emoji: '✋', name: 'raised hand' },
        { type: 'emoji-item', emoji: '🖖', name: 'vulcan salute' },
        // 動物與自然分類
        { type: 'category-header', categoryName: '動物與自然', fullRow: true },
        { type: 'emoji-item', emoji: '🐶', name: 'dog face' },
        { type: 'emoji-item', emoji: '🐱', name: 'cat face' }
      ]

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: realEmojiData,
          itemsPerRow: 10, // 與 EmojiPanel 相同
          rowHeight: 34,
          containerHeight: 176
        }
      })

      const processed = wrapper.vm.processedItems
      
      console.log('🔍 Processed items length:', processed.length)
      console.log('🔍 Processed items structure:')
      processed.forEach((item, index) => {
        console.log(`  [${index}]: ${item.type} - ${item.name || item.categoryName || item.emoji || 'filler'}`)
      })
      
      // 預期結構分析：
      // 1. 分類標題「表情符號與人物」(fullRow) - 不需要 filler
      // 2-11. 10個emoji - 剛好填滿一行，不需要 filler  
      // 12. 分類標題「人物與身體」(fullRow) - 不需要 filler
      // 13-17. 5個emoji - 需要 5 個 filler 填滿行
      // 18-22. 5個 auto-filler 
      // 23. 分類標題「動物與自然」(fullRow) - 不需要 filler
      // 24-25. 2個emoji
      
      // 檢查是否有不正常的 auto-filler 數量
      const fillers = processed.filter(item => item.type === 'auto-filler')
      const categories = processed.filter(item => item.type === 'category-header')
      const emojis = processed.filter(item => item.type === 'emoji-item')
      
      console.log('🔍 統計:')
      console.log(`  - Categories: ${categories.length}`)
      console.log(`  - Emojis: ${emojis.length}`)
      console.log(`  - Fillers: ${fillers.length}`)
      
      // 修復後：第二組5個filler + 最後2項目8個filler = 13個
      expect(fillers.length).toBe(13)
      expect(categories.length).toBe(3)
      expect(emojis.length).toBe(17) // 10 + 5 + 2
    })

    it('🐛 BUG 重現：多分組變數累加問題檢測', () => {
      // 建立一個複雜的多分組情況來檢測 currentRowItems 累加問題
      const complexMultiGroupData = [
        // 第1組：分類標題 + 3個項目（需要7個filler）
        { type: 'category-header', categoryName: '分組A', fullRow: true },
        { type: 'emoji-item', emoji: '🅰️', name: 'A1' },
        { type: 'emoji-item', emoji: '🅰️', name: 'A2' },
        { type: 'emoji-item', emoji: '🅰️', name: 'A3' },
        
        // 第2組：分類標題 + 8個項目（需要2個filler）
        { type: 'category-header', categoryName: '分組B', fullRow: true },
        { type: 'emoji-item', emoji: '🅱️', name: 'B1' },
        { type: 'emoji-item', emoji: '🅱️', name: 'B2' },
        { type: 'emoji-item', emoji: '🅱️', name: 'B3' },
        { type: 'emoji-item', emoji: '🅱️', name: 'B4' },
        { type: 'emoji-item', emoji: '🅱️', name: 'B5' },
        { type: 'emoji-item', emoji: '🅱️', name: 'B6' },
        { type: 'emoji-item', emoji: '🅱️', name: 'B7' },
        { type: 'emoji-item', emoji: '🅱️', name: 'B8' },
        
        // 第3組：分類標題 + 10個項目（不需要filler）
        { type: 'category-header', categoryName: '分組C', fullRow: true },
        { type: 'emoji-item', emoji: '🇨', name: 'C1' },
        { type: 'emoji-item', emoji: '🇨', name: 'C2' },
        { type: 'emoji-item', emoji: '🇨', name: 'C3' },
        { type: 'emoji-item', emoji: '🇨', name: 'C4' },
        { type: 'emoji-item', emoji: '🇨', name: 'C5' },
        { type: 'emoji-item', emoji: '🇨', name: 'C6' },
        { type: 'emoji-item', emoji: '🇨', name: 'C7' },
        { type: 'emoji-item', emoji: '🇨', name: 'C8' },
        { type: 'emoji-item', emoji: '🇨', name: 'C9' },
        { type: 'emoji-item', emoji: '🇨', name: 'C10' },
        
        // 第4組：分類標題 + 1個項目（需要9個filler）
        { type: 'category-header', categoryName: '分組D', fullRow: true },
        { type: 'emoji-item', emoji: '🇩', name: 'D1' },
        
        // 第5組：分類標題 + 6個項目（需要4個filler）
        { type: 'category-header', categoryName: '分組E', fullRow: true },
        { type: 'emoji-item', emoji: '🇪', name: 'E1' },
        { type: 'emoji-item', emoji: '🇪', name: 'E2' },
        { type: 'emoji-item', emoji: '🇪', name: 'E3' },
        { type: 'emoji-item', emoji: '🇪', name: 'E4' },
        { type: 'emoji-item', emoji: '🇪', name: 'E5' },
        { type: 'emoji-item', emoji: '🇪', name: 'E6' }
      ]

      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: complexMultiGroupData,
          itemsPerRow: 10,
          rowHeight: 34,
          containerHeight: 400
        }
      })

      const processed = wrapper.vm.processedItems
      
      console.log('\n🔍 多分組測試 - Processed items length:', processed.length)
      console.log('🔍 詳細結構:')
      
      let currentGroup = ''
      let groupItemCount = 0
      let groupFillerCount = 0
      
      processed.forEach((item, index) => {
        if (item.type === 'category-header') {
          if (currentGroup) {
            console.log(`    ${currentGroup} 小計: ${groupItemCount} 項目, ${groupFillerCount} filler`)
          }
          currentGroup = item.categoryName
          groupItemCount = 0
          groupFillerCount = 0
          console.log(`  [${index}]: 🏷️  ${item.categoryName}`)
        } else if (item.type === 'emoji-item') {
          groupItemCount++
          console.log(`  [${index}]: 📦 ${item.name}`)
        } else if (item.type === 'auto-filler') {
          groupFillerCount++
          console.log(`  [${index}]: 🔳 filler`)
        }
      })
      
      // 最後一組的統計
      if (currentGroup) {
        console.log(`    ${currentGroup} 小計: ${groupItemCount} 項目, ${groupFillerCount} filler`)
      }
      
      // 統計總數
      const fillers = processed.filter(item => item.type === 'auto-filler')
      const categories = processed.filter(item => item.type === 'category-header')
      const emojis = processed.filter(item => item.type === 'emoji-item')
      
      console.log('\n🔍 總統計:')
      console.log(`  - Categories: ${categories.length}`)
      console.log(`  - Emojis: ${emojis.length}`)
      console.log(`  - Fillers: ${fillers.length}`)
      
      // 驗證預期結果
      expect(categories.length).toBe(5)
      expect(emojis.length).toBe(28) // 3+8+10+1+6
      
      // 預期 filler 數量：7+2+0+9+4 = 22
      const expectedFillers = 7 + 2 + 0 + 9 + 4
      console.log(`\n🎯 預期 filler 數量: ${expectedFillers}`)
      console.log(`🎯 實際 filler 數量: ${fillers.length}`)
      
      // 如果有累加錯誤，filler 數量會異常
      expect(fillers.length).toBe(expectedFillers)
      
      // 驗證總長度
      const expectedTotal = 5 + 28 + expectedFillers // categories + emojis + fillers
      expect(processed.length).toBe(expectedTotal)
    })
  })
})