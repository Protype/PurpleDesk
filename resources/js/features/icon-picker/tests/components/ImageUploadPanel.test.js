import { describe, it, expect, beforeEach, vi } from 'vitest'
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

      // Mock FileReader
      const mockFileReader = {
        readAsDataURL: vi.fn(),
        onload: null,
        onerror: null,
        result: 'data:image/jpeg;base64,test'
      }

      vi.spyOn(window, 'FileReader').mockImplementation(() => mockFileReader)

      // 觸發檔案選擇
      Object.defineProperty(fileInput.element, 'files', {
        value: [file],
        configurable: true
      })

      await fileInput.trigger('change')

      // 觸發 FileReader onload
      mockFileReader.onload({ target: { result: 'data:image/jpeg;base64,test' } })

      expect(wrapper.emitted('icon-select')).toBeTruthy()
      expect(wrapper.emitted('file-selected')).toBeTruthy()
      
      const iconSelectEvent = wrapper.emitted('icon-select')[0][0]
      expect(iconSelectEvent).toEqual({
        type: 'image',
        value: 'data:image/jpeg;base64,test',
        iconType: 'upload'
      })

      const fileSelectedEvent = wrapper.emitted('file-selected')[0][0]
      expect(fileSelectedEvent).toBe(file)
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

      // Mock DataTransfer 和 FileReader
      const mockDataTransfer = {
        files: [file]
      }

      const mockFileReader = {
        readAsDataURL: vi.fn(),
        onload: null,
        onerror: null
      }

      vi.spyOn(window, 'FileReader').mockImplementation(() => mockFileReader)

      // 觸發 drop 事件
      await uploadArea.trigger('drop', {
        dataTransfer: mockDataTransfer
      })

      // 觸發 FileReader onload
      mockFileReader.onload({ target: { result: 'data:image/jpeg;base64,test' } })

      expect(wrapper.emitted('icon-select')).toBeTruthy()
      expect(wrapper.emitted('file-selected')).toBeTruthy()
    })
  })

  describe('錯誤處理', () => {
    it('FileReader 錯誤應該顯示錯誤訊息', async () => {
      const file = new File(['test'], 'test.jpg', { type: 'image/jpeg' })
      const fileInput = wrapper.find('input[type="file"]')

      const mockFileReader = {
        readAsDataURL: vi.fn(),
        onload: null,
        onerror: null
      }

      vi.spyOn(window, 'FileReader').mockImplementation(() => mockFileReader)

      Object.defineProperty(fileInput.element, 'files', {
        value: [file],
        configurable: true
      })

      await fileInput.trigger('change')

      // 觸發 FileReader onerror
      mockFileReader.onerror()

      await nextTick()

      expect(wrapper.find('.bg-red-50').exists()).toBe(true)
      expect(wrapper.text()).toContain('檔案讀取失敗，請重試')
    })
  })
})