import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import IconPicker from '../../components/IconPicker.vue'

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

      // 驗證位置
      const expectedTop = mockButtonRect.bottom + 8 // 140px
      expect(wrapper.vm.panelPosition.top).toBe(`${expectedTop}px`)
      expect(wrapper.vm.panelPosition.left).toBe(`${mockButtonRect.left}px`)
    })

    it('當右側空間不足時，應該向左調整', async () => {
      // 模擬按鈕在螢幕右側
      mockButtonRect.left = 700
      mockButtonRect.right = 732

      const mockGetBoundingClientRect = vi.fn()
      mockGetBoundingClientRect
        .mockReturnValueOnce(mockButtonRect)
        .mockReturnValueOnce(mockPanelRect)

      wrapper.vm.iconPickerRef = { getBoundingClientRect: mockGetBoundingClientRect }
      wrapper.vm.iconPanel = { getBoundingClientRect: mockGetBoundingClientRect }

      await wrapper.setProps({ isOpen: true })
      wrapper.vm.calculatePanelPosition()

      // 應該從右邊界向左調整
      const expectedLeft = mockViewport.width - mockPanelRect.width - 16 // 1024 - 384 - 16 = 624
      expect(wrapper.vm.panelPosition.left).toBe(`${expectedLeft}px`)
    })
  })

  describe('邊界情況處理', () => {
    it('當下方空間不足且上方有足夠空間時，應該顯示在上方', async () => {
      // 模擬按鈕在螢幕下方
      mockButtonRect.top = 600
      mockButtonRect.bottom = 632

      const mockGetBoundingClientRect = vi.fn()
      mockGetBoundingClientRect
        .mockReturnValueOnce(mockButtonRect)
        .mockReturnValueOnce(mockPanelRect)

      wrapper.vm.iconPickerRef = { getBoundingClientRect: mockGetBoundingClientRect }
      wrapper.vm.iconPanel = { getBoundingClientRect: mockGetBoundingClientRect }

      await wrapper.setProps({ isOpen: true })
      wrapper.vm.calculatePanelPosition()

      // 應該顯示在按鈕上方
      const expectedTop = mockButtonRect.top - mockPanelRect.height - 8 // 600 - 400 - 8 = 192
      expect(wrapper.vm.panelPosition.top).toBe(`${expectedTop}px`)
    })

    it('當上下都沒有足夠空間時，應該置中顯示', async () => {
      // 模擬小螢幕或按鈕位置導致上下都沒有足夠空間
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

      // 應該置中顯示（考慮邊界限制）
      const centerY = (mockViewport.height - mockPanelRect.height) / 2 // (500 - 400) / 2 = 50
      const expectedTop = Math.max(16, Math.min(centerY, mockViewport.height - mockPanelRect.height - 16))
      expect(wrapper.vm.panelPosition.top).toBe(`${expectedTop}px`)
    })

    it('位置永遠不應該超出螢幕邊界', async () => {
      // 測試極端情況
      mockButtonRect.top = -50  // 按鈕在螢幕外
      mockButtonRect.bottom = -18
      mockButtonRect.left = -100
      mockButtonRect.right = -68

      const mockGetBoundingClientRect = vi.fn()
      mockGetBoundingClientRect
        .mockReturnValueOnce(mockButtonRect)
        .mockReturnValueOnce(mockPanelRect)

      wrapper.vm.iconPickerRef = { getBoundingClientRect: mockGetBoundingClientRect }
      wrapper.vm.iconPanel = { getBoundingClientRect: mockGetBoundingClientRect }

      await wrapper.setProps({ isOpen: true })
      wrapper.vm.calculatePanelPosition()

      // 位置應該被限制在安全範圍內
      const top = parseInt(wrapper.vm.panelPosition.top)
      const left = parseInt(wrapper.vm.panelPosition.left)

      expect(top).toBeGreaterThanOrEqual(16)
      expect(left).toBeGreaterThanOrEqual(16)
      expect(top).toBeLessThanOrEqual(mockViewport.height - mockPanelRect.height - 16)
      expect(left).toBeLessThanOrEqual(mockViewport.width - mockPanelRect.width - 16)
    })
  })

  describe('響應式調整', () => {
    it('窗口大小改變時應該重新計算位置', async () => {
      const mockGetBoundingClientRect = vi.fn()
      mockGetBoundingClientRect
        .mockReturnValue(mockButtonRect)
        .mockReturnValue(mockPanelRect)

      wrapper.vm.iconPickerRef = { getBoundingClientRect: mockGetBoundingClientRect }
      wrapper.vm.iconPanel = { getBoundingClientRect: mockGetBoundingClientRect }

      await wrapper.setProps({ isOpen: true })
      const originalLeft = wrapper.vm.panelPosition.left

      // 模擬窗口變小
      Object.defineProperty(window, 'innerWidth', {
        value: 800
      })

      // 觸發 resize 事件
      wrapper.vm.handleResize()

      // 位置應該重新計算
      expect(wrapper.vm.panelPosition.left).not.toBe(originalLeft)
    })
  })

  describe('面板高度處理', () => {
    it('當面板高度無法取得時，應該使用預設值', async () => {
      // 模擬 panelRect.height 為 0 或 undefined
      mockPanelRect.height = 0

      const mockGetBoundingClientRect = vi.fn()
      mockGetBoundingClientRect
        .mockReturnValueOnce(mockButtonRect)
        .mockReturnValueOnce(mockPanelRect)

      wrapper.vm.iconPickerRef = { getBoundingClientRect: mockGetBoundingClientRect }
      wrapper.vm.iconPanel = { getBoundingClientRect: mockGetBoundingClientRect }

      await wrapper.setProps({ isOpen: true })
      wrapper.vm.calculatePanelPosition()

      // 應該使用預設高度 400px 進行計算
      expect(wrapper.vm.panelPosition.top).toBeDefined()
      expect(wrapper.vm.panelPosition.left).toBeDefined()
    })
  })
})