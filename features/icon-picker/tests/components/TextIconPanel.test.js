import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import TextIconPanel from '../../components/TextIconPanel.vue'

describe('TextIconPanel', () => {
  it('應該正確渲染基本元件', () => {
    const wrapper = mount(TextIconPanel)
    
    // 檢查基本元素是否存在
    expect(wrapper.find('input').exists()).toBe(true)
    expect(wrapper.find('button').exists()).toBe(true)
    expect(wrapper.find('.w-24.h-24').exists()).toBe(true) // 預覽區域
  })

  it('應該限制輸入為3個字元', async () => {
    const wrapper = mount(TextIconPanel)
    const input = wrapper.find('input')
    
    // 輸入超過3個字元
    await input.setValue('ABCDE')
    await input.trigger('input')
    
    // 應該只保留前3個字元
    expect(wrapper.vm.customInitials).toBe('ABC')
  })

  it('應該自動轉換為大寫', async () => {
    const wrapper = mount(TextIconPanel)
    const input = wrapper.find('input')
    
    // 輸入小寫字母
    await input.setValue('abc')
    await input.trigger('input')
    
    // 應該轉換為大寫
    expect(wrapper.vm.customInitials).toBe('ABC')
  })

  it('應該在有輸入時啟用應用按鈕', async () => {
    const wrapper = mount(TextIconPanel)
    const button = wrapper.find('button')
    const input = wrapper.find('input')
    
    // 初始狀態應該禁用
    expect(button.attributes('disabled')).toBeDefined()
    
    // 輸入文字後應該啟用
    await input.setValue('AB')
    await input.trigger('input')
    
    expect(button.attributes('disabled')).toBeUndefined()
  })

  it('應該正確計算文字顏色 - 淺色背景使用深色文字', () => {
    const wrapper = mount(TextIconPanel, {
      props: {
        backgroundColor: '#ffffff' // 白色背景
      }
    })
    
    const result = wrapper.vm.getTextColorClass('#ffffff')
    expect(result).toBe('text-gray-800')
  })

  it('應該正確計算文字顏色 - 深色背景使用淺色文字', () => {
    const wrapper = mount(TextIconPanel, {
      props: {
        backgroundColor: '#000000' // 黑色背景
      }
    })
    
    const result = wrapper.vm.getTextColorClass('#000000')
    expect(result).toBe('text-white')
  })

  it('應該在預覽區域顯示輸入的文字', async () => {
    const wrapper = mount(TextIconPanel)
    const input = wrapper.find('input')
    const preview = wrapper.find('.w-24.h-24')
    
    // 輸入文字
    await input.setValue('XY')
    await input.trigger('input')
    
    // 預覽區域應該顯示輸入的文字
    expect(preview.text()).toBe('XY')
  })

  it('應該在沒有輸入時顯示預設文字', () => {
    const wrapper = mount(TextIconPanel)
    const preview = wrapper.find('.w-24.h-24')
    
    // 預覽區域應該顯示預設的 "AB"
    expect(preview.text()).toBe('AB')
  })

  it('應該正確發送 text-selected 事件', async () => {
    const wrapper = mount(TextIconPanel, {
      props: {
        backgroundColor: '#6366f1'
      }
    })
    
    const input = wrapper.find('input')
    const button = wrapper.find('button')
    
    // 輸入文字
    await input.setValue('XY')
    await input.trigger('input')
    
    // 點擊應用按鈕
    await button.trigger('click')
    
    // 檢查事件是否正確發送
    const textSelectedEvents = wrapper.emitted('text-selected')
    expect(textSelectedEvents).toHaveLength(1)
    expect(textSelectedEvents[0][0]).toEqual({
      text: 'XY',
      backgroundColor: '#6366f1'
    })
  })

  it('應該正確發送 update:modelValue 事件', async () => {
    const wrapper = mount(TextIconPanel)
    const input = wrapper.find('input')
    
    // 輸入文字
    await input.setValue('XY')
    await input.trigger('input')
    
    // 檢查 modelValue 更新事件（可能會有多次，因為每個字元都會觸發）
    const modelValueEvents = wrapper.emitted('update:modelValue')
    expect(modelValueEvents).toBeTruthy()
    expect(modelValueEvents[modelValueEvents.length - 1][0]).toBe('XY')
  })

  it('應該同步 modelValue prop 到輸入框', async () => {
    const wrapper = mount(TextIconPanel, {
      props: {
        modelValue: 'CD'
      }
    })
    
    // 輸入框應該顯示 prop 的值
    expect(wrapper.vm.customInitials).toBe('CD')
    expect(wrapper.find('input').element.value).toBe('CD')
  })

  it('應該正確處理背景顏色樣式', () => {
    const backgroundColor = '#ff0000'
    const wrapper = mount(TextIconPanel, {
      props: {
        backgroundColor
      }
    })
    
    const preview = wrapper.find('.w-24.h-24')
    
    // 檢查背景顏色是否正確應用
    expect(preview.attributes('style')).toContain(`background-color: ${backgroundColor}`)
  })

  it('應該在沒有輸入時禁用應用按鈕的點擊', async () => {
    const wrapper = mount(TextIconPanel)
    const button = wrapper.find('button')
    
    // 點擊禁用的按鈕
    await button.trigger('click')
    
    // 沒有事件發送（因為 customInitials 為空）
    expect(wrapper.emitted('text-selected')).toBeFalsy()
    expect(wrapper.emitted('update:modelValue')).toBeFalsy()
  })
})