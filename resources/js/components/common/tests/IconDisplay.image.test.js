import { describe, it, expect, beforeEach, afterEach } from 'vitest'
import { mount } from '@vue/test-utils'
import IconDisplay from '../IconDisplay.vue'

describe('IconDisplay - Image Support', () => {
  let wrapper

  afterEach(() => {
    if (wrapper) {
      wrapper.unmount()
    }
  })

  describe('圖片顯示支援', () => {
    it('應該支援 path 屬性的圖片顯示', () => {
      wrapper = mount(IconDisplay, {
        props: {
          iconData: {
            type: 'image',
            path: 'uploads/test-image.jpg'
          },
          size: 'md'
        }
      })

      const img = wrapper.find('img')
      expect(img.exists()).toBe(true)
      expect(img.attributes('src')).toBe('/storage/uploads/test-image.jpg')
    })

    it('應該支援 url 屬性的圖片顯示', () => {
      wrapper = mount(IconDisplay, {
        props: {
          iconData: {
            type: 'image',
            url: 'https://example.com/image.jpg'
          },
          size: 'md'
        }
      })

      const img = wrapper.find('img')
      expect(img.exists()).toBe(true)
      expect(img.attributes('src')).toBe('https://example.com/image.jpg')
    })

    it('應該支援 value 屬性的 Blob URL 顯示', () => {
      const blobUrl = 'blob:http://localhost:3000/test-uuid'
      
      wrapper = mount(IconDisplay, {
        props: {
          iconData: {
            type: 'image',
            value: blobUrl,
            file: new File(['test'], 'test.jpg', { type: 'image/jpeg' }),
            iconType: 'upload'
          },
          size: 'md'
        }
      })

      const img = wrapper.find('img')
      expect(img.exists()).toBe(true)
      expect(img.attributes('src')).toBe(blobUrl)
    })

    it('應該支援 value 屬性的 Base64 顯示', () => {
      const base64 = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQ'
      
      wrapper = mount(IconDisplay, {
        props: {
          iconData: {
            type: 'image',
            value: base64
          },
          size: 'md'
        }
      })

      const img = wrapper.find('img')
      expect(img.exists()).toBe(true)
      expect(img.attributes('src')).toBe(base64)
    })

    it('優先級應該是 path > url > value', () => {
      wrapper = mount(IconDisplay, {
        props: {
          iconData: {
            type: 'image',
            path: 'uploads/priority-test.jpg',
            url: 'https://example.com/url-test.jpg',
            value: 'blob:http://localhost:3000/value-test'
          },
          size: 'md'
        }
      })

      const img = wrapper.find('img')
      expect(img.exists()).toBe(true)
      expect(img.attributes('src')).toBe('/storage/uploads/priority-test.jpg')
    })

    it('當只有 url 和 value 時，應該優先使用 url', () => {
      wrapper = mount(IconDisplay, {
        props: {
          iconData: {
            type: 'image',
            url: 'https://example.com/url-priority.jpg',
            value: 'blob:http://localhost:3000/value-test'
          },
          size: 'md'
        }
      })

      const img = wrapper.find('img')
      expect(img.exists()).toBe(true)
      expect(img.attributes('src')).toBe('https://example.com/url-priority.jpg')
    })

    it('當沒有任何圖片屬性時，應該顯示預設圖標', () => {
      wrapper = mount(IconDisplay, {
        props: {
          iconData: {
            type: 'image'
            // 沒有 path、url 或 value
          },
          size: 'md'
        }
      })

      const img = wrapper.find('img')
      const defaultIcon = wrapper.find('i.bi-person-fill')
      
      expect(img.exists()).toBe(false)
      expect(defaultIcon.exists()).toBe(true)
    })

    it('圖片載入失敗後應該顯示預設圖標', async () => {
      wrapper = mount(IconDisplay, {
        props: {
          iconData: {
            type: 'image',
            value: 'invalid-url'
          },
          size: 'md'
        }
      })

      const img = wrapper.find('img')
      expect(img.exists()).toBe(true)

      // 模擬圖片載入失敗
      await img.trigger('error')

      // 重新渲染後應該顯示預設圖標
      await wrapper.vm.$nextTick()
      
      const updatedImg = wrapper.find('img')
      const defaultIcon = wrapper.find('i.bi-person-fill')
      
      expect(updatedImg.exists()).toBe(false)
      expect(defaultIcon.exists()).toBe(true)
    })
  })

  describe('ImageUploadPanel 整合測試', () => {
    it('應該正確渲染 ImageUploadPanel 產生的資料格式', () => {
      // 模擬 ImageUploadPanel 發送的事件資料
      const imageData = {
        type: 'image',
        value: 'blob:http://localhost:3000/uuid-12345',
        file: new File(['test content'], 'uploaded-image.jpg', { 
          type: 'image/jpeg' 
        }),
        iconType: 'upload'
      }

      wrapper = mount(IconDisplay, {
        props: {
          iconData: imageData,
          size: 'lg'
        }
      })

      const img = wrapper.find('img')
      expect(img.exists()).toBe(true)
      expect(img.attributes('src')).toBe('blob:http://localhost:3000/uuid-12345')
      expect(img.classes()).toContain('w-full')
      expect(img.classes()).toContain('h-full')
      expect(img.classes()).toContain('object-cover')
    })
  })
})