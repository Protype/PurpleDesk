import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { nextTick } from 'vue'
import ImageUploadPanel from '../../components/ImageUploadPanel.vue'

describe('ImageUploadPanel', () => {
  let wrapper

  beforeEach(() => {
    wrapper = mount(ImageUploadPanel, {
      props: {
        selectedIcon: null
      }
    })
  })

  afterEach(() => {
    wrapper.unmount()
  })

  describe('元件渲染', () => {
    it('應該正確渲染上傳區域', () => {
      expect(wrapper.find('.image-upload-panel').exists()).toBe(true)
      expect(wrapper.find('.bi-cloud-arrow-up-fill').exists()).toBe(true)
      expect(wrapper.text()).toContain('Upload an image')
      expect(wrapper.text()).toContain('or drag and drop')
    })

    it('應該包含隱藏的檔案輸入', () => {
      const fileInput = wrapper.find('input[type="file"]')
      expect(fileInput.exists()).toBe(true)
      expect(fileInput.attributes('accept')).toBe('image/*')
      expect(fileInput.classes()).toContain('hidden')
    })
  })

  describe('載入狀態測試', () => {
    it('上傳時應該顯示載入動畫', async () => {
      const file = new File(['test'], 'test.jpg', { type: 'image/jpeg' })
      const fileInput = wrapper.find('input[type="file"]')

      vi.spyOn(URL, 'createObjectURL').mockReturnValue('blob:test')
      vi.spyOn(URL, 'revokeObjectURL').mockImplementation(() => {})

      Object.defineProperty(fileInput.element, 'files', {
        value: [file],
        configurable: true
      })

      await fileInput.trigger('change')
      await nextTick()

      // 確認顯示載入狀態
      expect(wrapper.find('.loading-spinner').exists()).toBe(true)
      expect(wrapper.text()).toContain('處理圖片中...')
      
      // 等待載入完成
      await new Promise(resolve => setTimeout(resolve, 1600))
      await nextTick()
      
      // 確認載入狀態消失
      expect(wrapper.find('.loading-spinner').exists()).toBe(false)
      
      URL.createObjectURL.mockRestore()
      URL.revokeObjectURL.mockRestore()
    })
    
    it('載入時應該阻止新的上傳', async () => {
      const file = new File(['test'], 'test.jpg', { type: 'image/jpeg' })
      const fileInput = wrapper.find('input[type="file"]')
      const uploadArea = wrapper.find('.image-upload-panel > div')

      vi.spyOn(URL, 'createObjectURL').mockReturnValue('blob:test')
      vi.spyOn(URL, 'revokeObjectURL').mockImplementation(() => {})

      // 開始第一次上傳
      Object.defineProperty(fileInput.element, 'files', {
        value: [file],
        configurable: true
      })
      await fileInput.trigger('change')
      await nextTick()
      
      // 在載入時嘗試再次上傳
      const clickSpy = vi.spyOn(fileInput.element, 'click')
      await uploadArea.trigger('click')
      
      // 應該不會觸發新的檔案選擇
      expect(clickSpy).not.toHaveBeenCalled()
      
      URL.createObjectURL.mockRestore()
      URL.revokeObjectURL.mockRestore()
    })
  })

  describe('檔案上傳功能', () => {
    it('點擊上傳區域應該觸發檔案選擇', async () => {
      const fileInput = wrapper.find('input[type="file"]')
      const clickSpy = vi.spyOn(fileInput.element, 'click')

      await wrapper.find('.image-upload-panel > div').trigger('click')
      
      expect(clickSpy).toHaveBeenCalled()
    })

    it('選擇有效圖片檔案應該觸發相關事件', async () => {
      const file = new File(['test'], 'test.jpg', { type: 'image/jpeg' })
      const fileInput = wrapper.find('input[type="file"]')

      // Mock URL.createObjectURL
      const mockObjectURL = 'blob:http://localhost:3000/test-uuid'
      vi.spyOn(URL, 'createObjectURL').mockReturnValue(mockObjectURL)
      vi.spyOn(URL, 'revokeObjectURL').mockImplementation(() => {})

      // 觸發檔案選擇
      Object.defineProperty(fileInput.element, 'files', {
        value: [file],
        configurable: true
      })

      await fileInput.trigger('change')
      
      // 等待處理完成 (模擬的 1500ms + 緩衝)
      await new Promise(resolve => setTimeout(resolve, 1600))
      await nextTick()

      expect(wrapper.emitted('icon-select')).toBeTruthy()
      expect(wrapper.emitted('file-selected')).toBeTruthy()
      
      const iconSelectEvent = wrapper.emitted('icon-select')[0][0]
      expect(iconSelectEvent.type).toBe('image')
      expect(iconSelectEvent.value.startsWith('blob:')).toBe(true) // 檢查是 Blob URL
      expect(iconSelectEvent.file).toBe(file)
      expect(iconSelectEvent.iconType).toBe('upload')

      const fileSelectedEvent = wrapper.emitted('file-selected')[0][0]
      expect(fileSelectedEvent).toBe(file)
      
      // 清理 mock
      URL.createObjectURL.mockRestore()
      URL.revokeObjectURL.mockRestore()
    })

    it('選擇非圖片檔案應該顯示錯誤', async () => {
      const file = new File(['test'], 'test.txt', { type: 'text/plain' })
      const fileInput = wrapper.find('input[type="file"]')

      Object.defineProperty(fileInput.element, 'files', {
        value: [file],
        configurable: true
      })

      await fileInput.trigger('change')
      await nextTick()

      expect(wrapper.find('.bg-red-50').exists()).toBe(true)
      expect(wrapper.text()).toContain('請選擇圖片檔案')
    })

    it('選擇超大檔案應該顯示錯誤', async () => {
      // 建立超過 10MB 的檔案
      const largeFile = new File(['x'.repeat(11 * 1024 * 1024)], 'large.jpg', { type: 'image/jpeg' })
      const fileInput = wrapper.find('input[type="file"]')

      Object.defineProperty(fileInput.element, 'files', {
        value: [largeFile],
        configurable: true
      })

      await fileInput.trigger('change')
      await nextTick()

      expect(wrapper.find('.bg-red-50').exists()).toBe(true)
      expect(wrapper.text()).toContain('檔案大小不能超過 10MB')
    })
  })

  describe('拖拉上傳功能', () => {
    it('拖曳 dragover 應該改變樣式', async () => {
      const uploadArea = wrapper.find('.image-upload-panel > div')
      
      await uploadArea.trigger('dragover')
      
      expect(uploadArea.classes()).toContain('border-primary-400')
      expect(uploadArea.classes()).toContain('bg-primary-50')
    })

    it('拖曳 dragleave 應該復原樣式', async () => {
      const uploadArea = wrapper.find('.image-upload-panel > div')
      
      // 先觸發 dragover
      await uploadArea.trigger('dragover')
      expect(uploadArea.classes()).toContain('border-primary-400')
      
      // 再觸發 dragleave
      await uploadArea.trigger('dragleave')
      
      expect(uploadArea.classes()).toContain('border-gray-200')
      expect(uploadArea.classes()).toContain('bg-gray-50')
    })

    it('拖放檔案應該處理上傳', async () => {
      const file = new File(['test'], 'test.jpg', { type: 'image/jpeg' })
      const uploadArea = wrapper.find('.image-upload-panel > div')

      // Mock DataTransfer 和 URL.createObjectURL
      const mockDataTransfer = {
        files: [file]
      }
      
      vi.spyOn(URL, 'createObjectURL').mockImplementation(() => 'blob:http://localhost:3000/' + Math.random())
      vi.spyOn(URL, 'revokeObjectURL').mockImplementation(() => {})

      // 觸發 drop 事件
      await uploadArea.trigger('drop', {
        dataTransfer: mockDataTransfer
      })
      
      // 等待處理完成
      await new Promise(resolve => setTimeout(resolve, 1600))
      await nextTick()

      expect(wrapper.emitted('icon-select')).toBeTruthy()
      expect(wrapper.emitted('file-selected')).toBeTruthy()
      
      // 清理 mock
      URL.createObjectURL.mockRestore()
      URL.revokeObjectURL.mockRestore()
    })
  })

  describe('錯誤處理', () => {
    it('圖片處理錯誤應該顯示錯誤訊息', async () => {
      const file = new File(['test'], 'test.jpg', { type: 'image/jpeg' })
      const fileInput = wrapper.find('input[type="file"]')

      // Mock URL.createObjectURL 択出錯誤
      vi.spyOn(URL, 'createObjectURL').mockImplementation(() => {
        throw new Error('Mock error')
      })
      vi.spyOn(URL, 'revokeObjectURL').mockImplementation(() => {})

      Object.defineProperty(fileInput.element, 'files', {
        value: [file],
        configurable: true
      })

      await fileInput.trigger('change')
      
      // 等待錯誤處理完成
      await new Promise(resolve => setTimeout(resolve, 1600))
      await nextTick()

      expect(wrapper.find('.bg-red-50').exists()).toBe(true)
      expect(wrapper.text()).toContain('圖片處理失敗，請重試')
      
      // 清理 mock
      URL.createObjectURL.mockRestore()
      URL.revokeObjectURL.mockRestore()
    })
  })
})