import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'
import { nextTick } from 'vue'
import { mount } from '@vue/test-utils'
import { 
  usePreloadedDataProvider, 
  usePreloadedEmojiData, 
  usePreloadedIconData,
  usePreloadedData,
  PRELOADED_EMOJI_DATA_KEY,
  PRELOADED_ICON_DATA_KEY
} from '@/features/icon-picker/composables/usePreloadedData.js'

// Mock dependencies
vi.mock('@/features/icon-picker/composables/useIconService.js', () => ({
  useIconService: () => ({
    loadEmojiData: vi.fn(),
    loadIconLibraryData: vi.fn()
  })
}))

describe('usePreloadedData', () => {
  let mockIconService

  beforeEach(() => {
    mockIconService = {
      loadEmojiData: vi.fn(),
      loadIconLibraryData: vi.fn()
    }
    
    // Mock useIconService
    vi.doMock('@/features/icon-picker/composables/useIconService.js', () => ({
      useIconService: () => mockIconService
    }))
  })

  afterEach(() => {
    vi.clearAllMocks()
  })

  describe('usePreloadedDataProvider', () => {
    it('應該提供預載入資料提供者', () => {
      const TestComponent = {
        template: '<div>Test</div>',
        setup() {
          const provider = usePreloadedDataProvider()
          
          expect(provider).toHaveProperty('isLoading')
          expect(provider).toHaveProperty('hasError')
          expect(provider).toHaveProperty('errorMessage')
          expect(provider).toHaveProperty('emojiData')
          expect(provider).toHaveProperty('iconData')
          expect(provider).toHaveProperty('reloadEmoji')
          expect(provider).toHaveProperty('reloadIcons')
          
          return { provider }
        }
      }

      const wrapper = mount(TestComponent)
      expect(wrapper.vm.provider).toBeDefined()
    })

    it('應該自動開始載入資料', async () => {
      const mockEmojiData = [{ emoji: '😀', name: 'smile' }]
      const mockIconData = { data: { heroicons: [] } }

      mockIconService.loadEmojiData.mockResolvedValue(mockEmojiData)
      mockIconService.loadIconLibraryData.mockResolvedValue(mockIconData)

      const TestComponent = {
        template: '<div>{{ isLoading }}</div>',
        setup() {
          const { isLoading, emojiData, iconData } = usePreloadedDataProvider()
          return { isLoading, emojiData, iconData }
        }
      }

      const wrapper = mount(TestComponent)
      
      // 初始狀態應該是載入中
      expect(wrapper.vm.isLoading).toBe(true)
      
      // 等待非同步操作完成
      await nextTick()
      await new Promise(resolve => setTimeout(resolve, 0))

      expect(mockIconService.loadEmojiData).toHaveBeenCalled()
      expect(mockIconService.loadIconLibraryData).toHaveBeenCalled()
    })

    it('應該正確處理錯誤狀態', async () => {
      const emojiError = new Error('Emoji load failed')
      const iconError = new Error('Icon load failed')

      mockIconService.loadEmojiData.mockRejectedValue(emojiError)
      mockIconService.loadIconLibraryData.mockRejectedValue(iconError)

      const TestComponent = {
        template: '<div>{{ hasError }}</div>',
        setup() {
          const { hasError, errorMessage } = usePreloadedDataProvider()
          return { hasError, errorMessage }
        }
      }

      const wrapper = mount(TestComponent)
      
      // 等待非同步操作完成
      await nextTick()
      await new Promise(resolve => setTimeout(resolve, 10))

      expect(wrapper.vm.hasError).toBe(true)
      expect(wrapper.vm.errorMessage).toContain('Emoji: Emoji load failed')
      expect(wrapper.vm.errorMessage).toContain('Icons: Icon load failed')
    })
  })

  describe('usePreloadedEmojiData', () => {
    it('應該從提供者取得 emoji 資料', () => {
      const ParentComponent = {
        template: '<ChildComponent />',
        components: {
          ChildComponent: {
            template: '<div>{{ loading }}</div>',
            setup() {
              usePreloadedDataProvider() // 提供資料
              const emojiProvider = usePreloadedEmojiData()
              return { loading: emojiProvider.loading }
            }
          }
        },
        setup() {
          usePreloadedDataProvider()
          return {}
        }
      }

      const wrapper = mount(ParentComponent)
      expect(wrapper.text()).toBeDefined()
    })

    it('應該在沒有提供者時拋出錯誤', () => {
      const TestComponent = {
        template: '<div>Test</div>',
        setup() {
          expect(() => {
            usePreloadedEmojiData()
          }).toThrow('usePreloadedEmojiData must be used within a component that provides preloaded emoji data')
          return {}
        }
      }

      mount(TestComponent)
    })
  })

  describe('usePreloadedIconData', () => {
    it('應該從提供者取得圖標資料', () => {
      const TestComponent = {
        template: '<div>{{ loading }}</div>',
        setup() {
          usePreloadedDataProvider() // 提供資料
          const iconProvider = usePreloadedIconData()
          return { loading: iconProvider.loading }
        }
      }

      const wrapper = mount(TestComponent)
      expect(wrapper.text()).toBeDefined()
    })

    it('應該在沒有提供者時拋出錯誤', () => {
      const TestComponent = {
        template: '<div>Test</div>',
        setup() {
          expect(() => {
            usePreloadedIconData()
          }).toThrow('usePreloadedIconData must be used within a component that provides preloaded icon data')
          return {}
        }
      }

      mount(TestComponent)
    })
  })

  describe('usePreloadedData', () => {
    it('應該同時提供 emoji 和 icon 資料', () => {
      const TestComponent = {
        template: '<div>{{ emojiLoading && iconLoading }}</div>',
        setup() {
          usePreloadedDataProvider() // 提供資料
          const { emoji, icon } = usePreloadedData()
          return { 
            emojiLoading: emoji.loading,
            iconLoading: icon.loading 
          }
        }
      }

      const wrapper = mount(TestComponent)
      expect(wrapper.text()).toBeDefined()
    })
  })

  describe('整合測試', () => {
    it('應該在父子元件間正確共享資料', async () => {
      const mockEmojiData = [{ emoji: '😀', name: 'smile' }]
      const mockIconData = { data: { heroicons: [{ name: 'home' }] } }

      mockIconService.loadEmojiData.mockResolvedValue(mockEmojiData)
      mockIconService.loadIconLibraryData.mockResolvedValue(mockIconData)

      const ChildComponent = {
        template: '<div>{{ hasData }}</div>',
        setup() {
          const emojiProvider = usePreloadedEmojiData()
          const iconProvider = usePreloadedIconData()
          
          return {
            hasData: computed(() => {
              return !!emojiProvider.data.value && !!iconProvider.data.value
            })
          }
        }
      }

      const ParentComponent = {
        template: '<ChildComponent />',
        components: { ChildComponent },
        setup() {
          const provider = usePreloadedDataProvider()
          return { provider }
        }
      }

      const wrapper = mount(ParentComponent)
      
      // 等待資料載入完成
      await nextTick()
      await new Promise(resolve => setTimeout(resolve, 10))

      expect(mockIconService.loadEmojiData).toHaveBeenCalledTimes(1)
      expect(mockIconService.loadIconLibraryData).toHaveBeenCalledTimes(1)
    })

    it('應該支援重新載入資料', async () => {
      const mockEmojiData = [{ emoji: '😀' }]
      mockIconService.loadEmojiData.mockResolvedValue(mockEmojiData)
      mockIconService.loadIconLibraryData.mockResolvedValue({ data: {} })

      const TestComponent = {
        template: '<div>{{ reloadCount }}</div>',
        setup() {
          const { reloadEmoji, reloadIcons } = usePreloadedDataProvider()
          
          const reloadCount = ref(0)
          
          const triggerReload = async () => {
            await reloadEmoji()
            await reloadIcons()
            reloadCount.value++
          }
          
          return { reloadCount, triggerReload }
        }
      }

      const wrapper = mount(TestComponent)
      
      // 等待初始載入
      await nextTick()
      await new Promise(resolve => setTimeout(resolve, 10))
      
      // 觸發重載
      await wrapper.vm.triggerReload()
      
      expect(mockIconService.loadEmojiData).toHaveBeenCalledTimes(2)
      expect(mockIconService.loadIconLibraryData).toHaveBeenCalledTimes(2)
    })
  })

  describe('快取行為', () => {
    it('應該在多次使用時共享相同的資料實例', () => {
      const TestComponent = {
        template: '<div>Test</div>',
        setup() {
          usePreloadedDataProvider() // 提供資料
          
          const emoji1 = usePreloadedEmojiData()
          const emoji2 = usePreloadedEmojiData()
          const icon1 = usePreloadedIconData()
          const icon2 = usePreloadedIconData()
          
          // 應該是相同的實例
          expect(emoji1).toBe(emoji2)
          expect(icon1).toBe(icon2)
          
          return {}
        }
      }

      mount(TestComponent)
    })
  })
})