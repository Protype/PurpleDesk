import { describe, it, expect, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import IconPickerSearch from '../../components/IconPickerSearch.vue'

describe('IconPickerSearch', () => {
  let wrapper

  beforeEach(() => {
    wrapper = mount(IconPickerSearch, {
      props: {
        modelValue: '',
        placeholder: 'Filter...'
      }
    })
  })

  afterEach(() => {
    wrapper?.unmount()
  })

  describe('基本渲染', () => {
    it('可以正確渲染搜尋輸入框', () => {
      expect(wrapper.find('input[type="text"]').exists()).toBe(true)
      expect(wrapper.find('input').attributes('placeholder')).toBe('Filter...')
    })

    it('可以設定自訂的 placeholder', async () => {
      await wrapper.setProps({ placeholder: 'Search icons...' })
      expect(wrapper.find('input').attributes('placeholder')).toBe('Search icons...')
    })

    it('輸入框有正確的 CSS 類別', () => {
      const input = wrapper.find('input')
      expect(input.classes()).toContain('icon-filter')
      expect(input.classes()).toContain('w-full')
      expect(input.classes()).toContain('text-sm')
    })
  })

  describe('v-model 雙向綁定', () => {
    it('可以顯示初始值', async () => {
      await wrapper.setProps({ modelValue: 'test query' })
      expect(wrapper.find('input').element.value).toBe('test query')
    })

    it('輸入時可以觸發 update:modelValue 事件', async () => {
      const input = wrapper.find('input')
      await input.setValue('new search')
      
      expect(wrapper.emitted('update:modelValue')).toBeTruthy()
      expect(wrapper.emitted('update:modelValue')[0]).toEqual(['new search'])
    })

    it('可以處理空字串', async () => {
      await wrapper.setProps({ modelValue: 'test' })
      expect(wrapper.find('input').element.value).toBe('test')
      
      const input = wrapper.find('input')
      await input.setValue('')
      
      expect(wrapper.emitted('update:modelValue')).toBeTruthy()
      expect(wrapper.emitted('update:modelValue')[0]).toEqual([''])
    })
  })

  describe('清除按鈕功能', () => {
    it('沒有搜尋內容時不顯示清除按鈕', () => {
      expect(wrapper.find('.clear-button').exists()).toBe(false)
    })

    it('有搜尋內容時顯示清除按鈕', async () => {
      await wrapper.setProps({ modelValue: 'test' })
      expect(wrapper.find('.clear-button').exists()).toBe(true)
    })

    it('點擊清除按鈕可以清空搜尋內容', async () => {
      await wrapper.setProps({ modelValue: 'test query' })
      const clearButton = wrapper.find('.clear-button')
      
      expect(clearButton.exists()).toBe(true)
      await clearButton.trigger('click')
      
      expect(wrapper.emitted('update:modelValue')).toBeTruthy()
      expect(wrapper.emitted('update:modelValue')[0]).toEqual([''])
      expect(wrapper.emitted('clear')).toBeTruthy()
    })

    it('清除按鈕有正確的樣式', async () => {
      await wrapper.setProps({ modelValue: 'test' })
      const clearButton = wrapper.find('.clear-button')
      
      expect(clearButton.classes()).toContain('absolute')
      expect(clearButton.classes()).toContain('right-2')
      expect(clearButton.text()).toBe('×')
    })
  })

  describe('事件處理', () => {
    it('可以觸發 input 事件', async () => {
      const input = wrapper.find('input')
      await input.trigger('input')
      
      expect(wrapper.emitted('input')).toBeTruthy()
    })

    it('可以觸發 focus 事件', async () => {
      const input = wrapper.find('input')
      await input.trigger('focus')
      
      expect(wrapper.emitted('focus')).toBeTruthy()
    })

    it('可以觸發 blur 事件', async () => {
      const input = wrapper.find('input')
      await input.trigger('blur')
      
      expect(wrapper.emitted('blur')).toBeTruthy()
    })

    it('按下 Enter 鍵可以觸發 search 事件', async () => {
      // 先設定 props 值
      await wrapper.setProps({ modelValue: 'test query' })
      
      const input = wrapper.find('input')
      await input.trigger('keyup.enter')
      
      expect(wrapper.emitted('search')).toBeTruthy()
      expect(wrapper.emitted('search')[0]).toEqual(['test query'])
    })

    it('按下 Escape 鍵可以清除搜尋', async () => {
      await wrapper.setProps({ modelValue: 'test query' })
      const input = wrapper.find('input')
      await input.trigger('keyup.escape')
      
      expect(wrapper.emitted('update:modelValue')).toBeTruthy()
      expect(wrapper.emitted('update:modelValue')[0]).toEqual([''])
      expect(wrapper.emitted('clear')).toBeTruthy()
    })
  })

  describe('可訪問性', () => {
    it('輸入框有正確的 focus 樣式類別', () => {
      const input = wrapper.find('input')
      const classes = input.classes()
      
      expect(classes).toContain('focus:outline-none')
      expect(classes).toContain('focus:ring-2')
      expect(classes).toContain('focus:ring-primary-500')
    })

    it('清除按鈕有 hover 效果', async () => {
      await wrapper.setProps({ modelValue: 'test' })
      const clearButton = wrapper.find('.clear-button')
      
      expect(clearButton.classes()).toContain('hover:text-gray-600')
    })
  })

  describe('響應式設計', () => {
    it('輸入框有 flex-1 類別以佔滿可用空間', () => {
      const container = wrapper.find('.relative')
      expect(container.classes()).toContain('flex-1')
    })

    it('外層容器有正確的相對定位', () => {
      const container = wrapper.find('.relative')
      expect(container.classes()).toContain('relative')
    })
  })

  describe('邊界情況處理', () => {
    it('可以處理非常長的搜尋文字', async () => {
      const longText = 'a'.repeat(1000)
      const input = wrapper.find('input')
      await input.setValue(longText)
      
      expect(wrapper.emitted('update:modelValue')).toBeTruthy()
      expect(wrapper.emitted('update:modelValue')[0]).toEqual([longText])
    })

    it('可以處理特殊字元', async () => {
      const specialText = '!@#$%^&*()_+-=[]{}|;\':",./<>?'
      const input = wrapper.find('input')
      await input.setValue(specialText)
      
      expect(wrapper.emitted('update:modelValue')).toBeTruthy()
      expect(wrapper.emitted('update:modelValue')[0]).toEqual([specialText])
    })

    it('可以處理 Unicode 字元', async () => {
      const unicodeText = '🔍搜尋圖標😊'
      const input = wrapper.find('input')
      await input.setValue(unicodeText)
      
      expect(wrapper.emitted('update:modelValue')).toBeTruthy()
      expect(wrapper.emitted('update:modelValue')[0]).toEqual([unicodeText])
    })
  })
})