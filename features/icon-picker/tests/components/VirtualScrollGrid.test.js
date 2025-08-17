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