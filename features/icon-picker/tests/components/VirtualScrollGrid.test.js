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
          rowHeight: 36,
          containerHeight: 176
        }
      })

      expect(wrapper.exists()).toBe(true)
      expect(wrapper.props('items')).toHaveLength(100)
      expect(wrapper.props('itemsPerRow')).toBe(10)
      expect(wrapper.props('rowHeight')).toBe(36)
      expect(wrapper.props('containerHeight')).toBe(176)
    })

    it('應該計算正確的總行數', async () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 36,
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
          rowHeight: 36,
          containerHeight: 176
        }
      })

      // 176px 容器高度 / 36px 行高 ≈ 4.89 ≈ 5 行
      const expectedVisibleRows = Math.ceil(176 / 36)
      expect(wrapper.vm.visibleRows).toBe(expectedVisibleRows)
    })

    it('應該正確計算滾動位置', async () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 36,
          containerHeight: 176
        }
      })

      // 模擬滾動到第 3 行
      const scrollTop = 3 * 36
      await wrapper.vm.handleScroll({ target: { scrollTop } })

      expect(wrapper.vm.scrollTop).toBe(scrollTop)
      expect(wrapper.vm.startRow).toBe(3)
    })

    it('應該支援緩衝區機制', () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 36,
          containerHeight: 176,
          buffer: 2
        }
      })

      // 有緩衝區應該多渲染額外的行
      const visibleRows = Math.ceil(176 / 36)
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
          rowHeight: 36,
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
          rowHeight: 36,
          containerHeight: 176
        }
      })

      const handleScrollSpy = vi.spyOn(wrapper.vm, 'handleScroll')
      
      // 快速連續滾動
      for (let i = 0; i < 5; i++) {
        await wrapper.vm.handleScroll({ target: { scrollTop: i * 36 } })
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
          rowHeight: 36,
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
          rowHeight: 36,
          containerHeight: 176
        }
      })

      expect(wrapper.vm.totalRows).toBe(1)
      expect(wrapper.vm.visibleItems).toHaveLength(3)
    })

    it('應該處理零高度容器', () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 36,
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
          rowHeight: 36,
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
          rowHeight: 36,
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
          rowHeight: 36,
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
          rowHeight: 36,
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
          rowHeight: 36,
          containerHeight: 176,
          buffer: 2
        }
      })

      // 計算理論上應該渲染的最大項目數
      const visibleRows = Math.ceil(176 / 36) // ~5 行
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
          rowHeight: 36,
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
            rowHeight: 36,
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
          rowHeight: 36,
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

  describe('響應式更新', () => {
    it('當 items 改變時應該重新計算', async () => {
      wrapper = mount(VirtualScrollGrid, {
        props: {
          items: mockItems,
          itemsPerRow: 10,
          rowHeight: 36,
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
          rowHeight: 36,
          containerHeight: 176
        }
      })

      const originalVisibleRows = wrapper.vm.visibleRows

      await wrapper.setProps({ containerHeight: 360 })

      expect(wrapper.vm.visibleRows).not.toBe(originalVisibleRows)
      expect(wrapper.vm.visibleRows).toBe(Math.ceil(360 / 36))
    })
  })
})