import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import IconPickerProxy from '../../components/IconPickerProxy.vue'

// Mock the IconPicker components
vi.mock('../../components/IconPicker.vue', () => ({
  default: {
    name: 'IconPicker',
    template: '<div data-testid="new-iconpicker">New IconPicker</div>'
  }
}))

vi.mock('@/components/common/IconPickerOri.vue', () => ({
  default: {
    name: 'IconPickerOri', 
    template: '<div data-testid="original-iconpicker">Original IconPicker</div>'
  }
}))

// Mock localStorage
const localStorageMock = {
  getItem: vi.fn(),
  setItem: vi.fn(),
  clear: vi.fn()
}
global.localStorage = localStorageMock

// Mock window.location
const locationMock = {
  search: ''
}
global.window = { 
  location: locationMock,
  localStorage: localStorageMock
}

describe('IconPickerProxy', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    locationMock.search = ''
    
    // Mock import.meta.env
    vi.stubGlobal('import.meta', {
      env: {
        DEV: true,
        VITE_ICONPICKER_VERSION: undefined
      }
    })
  })
  
  it('預設使用原版 IconPicker', () => {
    localStorageMock.getItem.mockReturnValue(null)
    
    const wrapper = mount(IconPickerProxy)
    expect(wrapper.find('[data-testid="original-iconpicker"]').exists()).toBe(true)
    expect(wrapper.find('[data-testid="new-iconpicker"]').exists()).toBe(false)
  })
  
  it('當 localStorage 設定為 false 時使用新版', () => {
    localStorageMock.getItem.mockReturnValue('false')
    
    const wrapper = mount(IconPickerProxy)
    expect(wrapper.find('[data-testid="new-iconpicker"]').exists()).toBe(true)
    expect(wrapper.find('[data-testid="original-iconpicker"]').exists()).toBe(false)
  })
  
  it('當 localStorage 設定為 true 時使用原版', () => {
    localStorageMock.getItem.mockReturnValue('true')
    
    const wrapper = mount(IconPickerProxy)
    expect(wrapper.find('[data-testid="original-iconpicker"]').exists()).toBe(true)
    expect(wrapper.find('[data-testid="new-iconpicker"]').exists()).toBe(false)
  })
  
  it('當 URL 參數為 iconpicker=new 時使用新版', () => {
    locationMock.search = '?iconpicker=new'
    localStorageMock.getItem.mockReturnValue(null)
    
    const wrapper = mount(IconPickerProxy)
    expect(wrapper.find('[data-testid="new-iconpicker"]').exists()).toBe(true)
    expect(wrapper.find('[data-testid="original-iconpicker"]').exists()).toBe(false)
  })
  
  it('當 URL 參數為 iconpicker=original 時使用原版', () => {
    locationMock.search = '?iconpicker=original'
    localStorageMock.getItem.mockReturnValue(null)
    
    const wrapper = mount(IconPickerProxy)
    expect(wrapper.find('[data-testid="original-iconpicker"]').exists()).toBe(true)
    expect(wrapper.find('[data-testid="new-iconpicker"]').exists()).toBe(false)
  })
  
  it('當環境變數設定為 original 時使用原版', () => {
    vi.stubGlobal('import.meta', {
      env: {
        DEV: true,
        VITE_ICONPICKER_VERSION: 'original'
      }
    })
    
    localStorageMock.getItem.mockReturnValue(null)
    locationMock.search = ''
    
    const wrapper = mount(IconPickerProxy)
    expect(wrapper.find('[data-testid="original-iconpicker"]').exists()).toBe(true)
    expect(wrapper.find('[data-testid="new-iconpicker"]').exists()).toBe(false)
  })
  
  it('localStorage 設定優先於 URL 參數', () => {
    localStorageMock.getItem.mockReturnValue('true') // 原版
    locationMock.search = '?iconpicker=new' // 新版
    
    const wrapper = mount(IconPickerProxy)
    expect(wrapper.find('[data-testid="original-iconpicker"]').exists()).toBe(true)
    expect(wrapper.find('[data-testid="new-iconpicker"]').exists()).toBe(false)
  })
  
  it('正確轉發所有 events', () => {
    const wrapper = mount(IconPickerProxy)
    
    // 檢查是否正確設定了 emits
    expect(wrapper.emitted()).toEqual({})
    
    // 模擬事件觸發（這需要實際的子元件支援，這裡只是確認結構正確）
    expect(wrapper.vm.$options.emits).toContain('update:modelValue')
    expect(wrapper.vm.$options.emits).toContain('update:iconType')
    expect(wrapper.vm.$options.emits).toContain('file-selected')
    expect(wrapper.vm.$options.emits).toContain('close')
    expect(wrapper.vm.$options.emits).toContain('background-color-change')
  })
})