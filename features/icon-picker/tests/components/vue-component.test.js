import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import { defineComponent } from 'vue'

// 簡單的測試元件
const TestComponent = defineComponent({
  name: 'TestComponent',
  props: {
    message: {
      type: String,
      default: 'Hello'
    }
  },
  template: '<div>{{ message }}</div>'
})

describe('Vue 元件測試基礎', () => {
  it('可以掛載 Vue 元件', () => {
    const wrapper = mount(TestComponent)
    expect(wrapper.exists()).toBe(true)
  })
  
  it('可以測試 props', () => {
    const wrapper = mount(TestComponent, {
      props: { message: 'Test Message' }
    })
    expect(wrapper.text()).toBe('Test Message')
  })
  
  it('可以測試預設 props', () => {
    const wrapper = mount(TestComponent)
    expect(wrapper.text()).toBe('Hello')
  })
})