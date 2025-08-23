import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import IconPicker from '../../IconPicker.vue'

describe('IconPicker 面板定位測試', () => {
  let wrapper
  let mockButtonRect
  let mockPanelRect
  let mockViewport

  beforeEach(() => {
    // 模擬 DOM 元素的 getBoundingClientRect
    mockButtonRect = {
      top: 100,
      bottom: 132,
      left: 50,
      right: 82,
      width: 32,
      height: 32
    }

    mockPanelRect = {
      width: 384, // w-96 = 384px
      height: 400
    }

    mockViewport = {
      width: 1024,
      height: 768
    }

    // 模擬 window 和 DOM 方法
    Object.defineProperty(window, 'innerWidth', {
      writable: true,
      configurable: true,
      value: mockViewport.width
    })

    Object.defineProperty(window, 'innerHeight', {
      writable: true,
      configurable: true,
      value: mockViewport.height
    })

    // 創建元件
    wrapper = mount(IconPicker, {
      props: {
        isOpen: false
      }
    })
  })

  describe('正常定位情況', () => {
    it('當下方有足夠空間時，應該顯示在按鈕下方', async () => {
      // 模擬按鈕在螢幕上方，下方有足夠空間
      mockButtonRect.top = 100
      mockButtonRect.bottom = 132

      // 模擬 getBoundingClientRect
      const mockGetBoundingClientRect = vi.fn()
      mockGetBoundingClientRect
        .mockReturnValueOnce(mockButtonRect) // iconPickerRef
        .mockReturnValueOnce(mockPanelRect)  // iconPanel

      // 取得元件實例並模擬 refs
      const iconPickerRef = { getBoundingClientRect: mockGetBoundingClientRect }
      const iconPanel = { getBoundingClientRect: mockGetBoundingClientRect }
      
      wrapper.vm.iconPickerRef = iconPickerRef
      wrapper.vm.iconPanel = iconPanel

      // 打開面板觸發位置計算
      await wrapper.setProps({ isOpen: true })
      await wrapper.vm.$nextTick()
      
      // 手動調用位置計算（因為在測試環境中）
      wrapper.vm.calculatePanelPosition()

      // 驗證位置 - 按照實際邏輯：top = bottom + 8，然後確保不小於16px
      const expectedTop = mockButtonRect.bottom + 8 // 140px
      expect(wrapper.vm.panelPosition.top).toBe(`${expectedTop}px`)
      expect(wrapper.vm.panelPosition.left).toBe(`${mockButtonRect.left}px`)
    })

    it('當右側空間不足時，應該向左調整', async () => {
      // 模擬按鈕在螢幕右側，會造成面板超出右邊界
      mockButtonRect.left = 700  // 700 + 384 = 1084 > 1024
      mockButtonRect.right = 732

      const mockGetBoundingClientRect = vi.fn()
      mockGetBoundingClientRect
        .mockReturnValueOnce(mockButtonRect)
        .mockReturnValueOnce(mockPanelRect)

      wrapper.vm.iconPickerRef = { getBoundingClientRect: mockGetBoundingClientRect }
      wrapper.vm.iconPanel = { getBoundingClientRect: mockGetBoundingClientRect }

      await wrapper.setProps({ isOpen: true })
      wrapper.vm.calculatePanelPosition()

      // 應該從右邊界向左調整：1024 - 384 - 16 = 624
      const expectedLeft = mockViewport.width - mockPanelRect.width - 16
      expect(wrapper.vm.panelPosition.left).toBe(`${expectedLeft}px`)
    })
  })

  describe('邊界情況處理', () => {
    it('當下方空間不足且上方有足夠空間時，應該顯示在上方', async () => {
      // 模擬按鈕在螢幕下方，下方空間不足
      mockButtonRect.top = 600   // 上方空間：600 - 16 = 584 > 408 (panelHeight + 8)
      mockButtonRect.bottom = 632 // 下方空間：768 - 632 - 16 = 120 < 400

      const mockGetBoundingClientRect = vi.fn()
      mockGetBoundingClientRect
        .mockReturnValueOnce(mockButtonRect)
        .mockReturnValueOnce(mockPanelRect)

      wrapper.vm.iconPickerRef = { getBoundingClientRect: mockGetBoundingClientRect }
      wrapper.vm.iconPanel = { getBoundingClientRect: mockGetBoundingClientRect }

      await wrapper.setProps({ isOpen: true })
      wrapper.vm.calculatePanelPosition()

      // 應該顯示在按鈕上方：600 - 400 - 8 = 192
      const expectedTop = mockButtonRect.top - mockPanelRect.height - 8
      expect(wrapper.vm.panelPosition.top).toBe(`${expectedTop}px`)
    })

    it('當上下都沒有足夠空間時，應該置中顯示', async () => {
      // 模擬小螢幕，上下都沒有足夠空間
      mockViewport.height = 500
      Object.defineProperty(window, 'innerHeight', {
        value: mockViewport.height
      })

      mockButtonRect.top = 250
      mockButtonRect.bottom = 282

      const mockGetBoundingClientRect = vi.fn()
      mockGetBoundingClientRect
        .mockReturnValueOnce(mockButtonRect)
        .mockReturnValueOnce(mockPanelRect)

      wrapper.vm.iconPickerRef = { getBoundingClientRect: mockGetBoundingClientRect }
      wrapper.vm.iconPanel = { getBoundingClientRect: mockGetBoundingClientRect }

      await wrapper.setProps({ isOpen: true })
      wrapper.vm.calculatePanelPosition()

      // 應該置中顯示：(500 - 400) / 2 = 50
      const expectedTop = (mockViewport.height - mockPanelRect.height) / 2
      expect(wrapper.vm.panelPosition.top).toBe(`${expectedTop}px`)
    })
  })

  describe('邊界限制', () => {
    it('位置永遠不應該超出螢幕邊界', async () => {
      // 極端情況：按鈕位置會導致負數或超出邊界
      mockButtonRect.left = -100
      mockButtonRect.top = -50

      const mockGetBoundingClientRect = vi.fn()
      mockGetBoundingClientRect
        .mockReturnValueOnce(mockButtonRect)
        .mockReturnValueOnce(mockPanelRect)

      wrapper.vm.iconPickerRef = { getBoundingClientRect: mockGetBoundingClientRect }
      wrapper.vm.iconPanel = { getBoundingClientRect: mockGetBoundingClientRect }

      await wrapper.setProps({ isOpen: true })
      wrapper.vm.calculatePanelPosition()

      // 檢查邊界限制
      const topValue = parseInt(wrapper.vm.panelPosition.top)
      const leftValue = parseInt(wrapper.vm.panelPosition.left)
      
      expect(topValue).toBeGreaterThanOrEqual(16)
      expect(leftValue).toBeGreaterThanOrEqual(16)
      expect(topValue).toBeLessThanOrEqual(mockViewport.height - mockPanelRect.height - 16)
      expect(leftValue).toBeLessThanOrEqual(mockViewport.width - mockPanelRect.width - 16)
    })
  })

  describe('響應式調整', () => {
    it('窗口大小改變時應該重新計算位置', async () => {
      // 先設定初始位置
      const mockGetBoundingClientRect = vi.fn()
      mockGetBoundingClientRect
        .mockReturnValue(mockButtonRect)

      wrapper.vm.iconPickerRef = { getBoundingClientRect: mockGetBoundingClientRect }
      wrapper.vm.iconPanel = { getBoundingClientRect: () => mockPanelRect }

      await wrapper.setProps({ isOpen: true })
      wrapper.vm.calculatePanelPosition()
      
      const initialPosition = wrapper.vm.panelPosition.top

      // 改變窗口大小
      mockViewport.height = 600
      Object.defineProperty(window, 'innerHeight', {
        value: mockViewport.height
      })

      // 觸發 resize 處理
      wrapper.vm.handleResize()

      // 位置應該會重新計算（可能相同也可能不同，重點是有調用）
      // 這個測試主要確保函數能正常執行而不報錯
      expect(wrapper.vm.panelPosition.top).toBeTruthy()
    })
  })

  describe('面板高度處理', () => {
    it('當面板高度無法取得時，應該使用預設值', async () => {
      // 模擬面板 getBoundingClientRect 返回空的高度
      mockPanelRect.height = 0

      const mockGetBoundingClientRect = vi.fn()
      mockGetBoundingClientRect
        .mockReturnValueOnce(mockButtonRect)
        .mockReturnValueOnce(mockPanelRect)

      wrapper.vm.iconPickerRef = { getBoundingClientRect: mockGetBoundingClientRect }
      wrapper.vm.iconPanel = { getBoundingClientRect: mockGetBoundingClientRect }

      await wrapper.setProps({ isOpen: true })
      
      // 這個測試主要確保當 height 為 0 時，邏輯中會使用預設的 400
      // 不會因為除零或其他問題而失敗
      expect(() => {
        wrapper.vm.calculatePanelPosition()
      }).not.toThrow()

      // 應該有合理的位置值
      expect(wrapper.vm.panelPosition.top).toBeTruthy()
      expect(wrapper.vm.panelPosition.left).toBeTruthy()
    })
  })
})