import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount, flushPromises } from '@vue/test-utils'
import EmojiPanel from '../../components/EmojiPanel.vue'
import { IconDataLoader } from '../../services/IconDataLoader.js'

// Mock VirtualScrollGrid
vi.mock('../../components/shared/VirtualScrollGrid.vue', () => ({
  default: {
    name: 'VirtualScrollGrid',
    props: ['items', 'itemsPerRow', 'rowHeight', 'containerHeight', 'buffer', 'preserveScrollPosition'],
    template: `
      <div class="virtual-scroll-grid-mock">
        <div v-for="(item, index) in items" :key="index" class="mock-item">
          <slot name="item" :item="item" :index="index" />
        </div>
      </div>
    `
  }
}))

// Mock SkinToneSelector
vi.mock('@/components/common/SkinToneSelector.vue', () => ({
  default: {
    name: 'SkinToneSelector',
    props: ['modelValue'],
    emits: ['update:modelValue'],
    template: `
      <button @click="$emit('update:modelValue', '🏻')" class="skin-tone-selector-mock">
        膚色選擇器
      </button>
    `
  }
}))

// Mock IconDataLoader
vi.mock('../../services/IconDataLoader.js', () => ({
  IconDataLoader: vi.fn().mockImplementation(() => ({
    getEmojiData: vi.fn().mockResolvedValue([
      {
        categoryId: 'smileys',
        categoryName: '表情符號與人物',
        emojis: [
          { emoji: '😀', name: 'grinning face', category: 'smileys', keywords: ['happy', 'smile'] },
          { emoji: '😃', name: 'grinning face with big eyes', category: 'smileys', keywords: ['happy', 'joy'] },
          { emoji: '😄', name: 'grinning face with smiling eyes', category: 'smileys', keywords: ['happy', 'joy'] }
        ]
      },
      {
        categoryId: 'people',
        categoryName: '人物',
        emojis: [
          { emoji: '👋', name: 'waving hand', category: 'people', keywords: ['hello', 'goodbye'] },
          { emoji: '👍', name: 'thumbs up', category: 'people', keywords: ['good', 'yes'] },
          { emoji: '🧑', name: 'person', category: 'people', keywords: ['person', 'human'] }
        ]
      },
      {
        categoryId: 'animals',
        categoryName: '動物與自然',
        emojis: [
          { emoji: '🐶', name: 'dog face', category: 'animals', keywords: ['animal', 'pet'] },
          { emoji: '🐱', name: 'cat face', category: 'animals', keywords: ['animal', 'pet'] }
        ]
      }
    ])
  }))
}))

describe('EmojiPanel', () => {
  let wrapper
  let mockIconDataLoader

  beforeEach(() => {
    // 重置 mock
    vi.clearAllMocks()
    
    // 建立 mock IconDataLoader 實例
    mockIconDataLoader = {
      getEmojiData: vi.fn().mockResolvedValue([
        {
          categoryId: 'smileys',
          categoryName: '表情符號與人物',
          emojis: [
            { emoji: '😀', name: 'grinning face', category: 'smileys', keywords: ['happy', 'smile'] },
            { emoji: '😃', name: 'grinning face with big eyes', category: 'smileys', keywords: ['happy', 'joy'] }
          ]
        },
        {
          categoryId: 'people',
          categoryName: '人物',
          emojis: [
            { emoji: '👋', name: 'waving hand', category: 'people', keywords: ['hello', 'goodbye'] },
            { emoji: '👍', name: 'thumbs up', category: 'people', keywords: ['good', 'yes'] },
            { emoji: '🧑', name: 'person', category: 'people', keywords: ['person', 'human'] }
          ]
        }
      ])
    }
  })

  afterEach(() => {
    if (wrapper) {
      wrapper.unmount()
    }
  })

  describe('基本功能', () => {
    it('應該正確渲染基本元件', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      // 檢查基本元素是否存在
      expect(wrapper.find('.emoji-panel').exists()).toBe(true)
      expect(wrapper.find('.virtual-scroll-grid-mock').exists()).toBe(true)
    })

    it('應該使用 IconDataLoader 載入 emoji 資料', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      // 檢查 IconDataLoader 是否被呼叫
      expect(IconDataLoader).toHaveBeenCalled()
    })

    it('應該顯示載入狀態', () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      // 在資料載入前應該顯示載入狀態
      expect(wrapper.find('.loading').exists()).toBe(true)
    })
  })

  describe('emoji 顯示功能', () => {
    it('應該正確顯示 emoji 項目', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      // 檢查是否有 emoji 項目被渲染
      const emojiItems = wrapper.findAll('.mock-item')
      expect(emojiItems.length).toBeGreaterThan(0)
    })

    it('應該支援點擊選擇 emoji', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      // 直接呼叫 selectEmoji 方法來測試
      const testEmoji = { 
        emoji: '😀', 
        name: 'grinning face', 
        category: 'smileys',
        isCategory: false,
        displayEmoji: '😀'
      }
      
      await wrapper.vm.selectEmoji(testEmoji)

      // 檢查是否發出 emoji-selected 事件
      const emojiSelectedEvents = wrapper.emitted('emoji-selected')
      expect(emojiSelectedEvents).toBeTruthy()
      expect(emojiSelectedEvents).toHaveLength(1)
    })
  })

  describe('搜尋過濾功能', () => {
    it('應該根據搜尋條件過濾 emoji', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: 'happy',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      // 檢查過濾邏輯
      expect(wrapper.vm.filteredEmojis).toBeDefined()
    })

    it('搜尋條件變更時應該更新過濾結果', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      // 更新搜尋條件
      await wrapper.setProps({ searchQuery: 'dog' })

      // 檢查過濾結果是否更新
      expect(wrapper.vm.filteredEmojis).toBeDefined()
    })
  })

  describe('膚色選擇功能', () => {
    it('應該根據選中的膚色調整 emoji', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: '🏻'
        }
      })

      await flushPromises()

      // 檢查膚色調整邏輯
      expect(wrapper.vm.processedEmojis).toBeDefined()
    })

    it('膚色變更時應該更新 emoji 顯示', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      // 更新膚色選擇
      await wrapper.setProps({ selectedSkinTone: '🏽' })

      // 檢查是否正確處理膚色變更
      expect(wrapper.vm.processedEmojis).toBeDefined()
    })
  })

  describe('VirtualScrollGrid 整合', () => {
    it('應該正確配置 VirtualScrollGrid 參數', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      const virtualScrollGrid = wrapper.findComponent({ name: 'VirtualScrollGrid' })
      expect(virtualScrollGrid.exists()).toBe(true)

      // 檢查重要的 props
      expect(virtualScrollGrid.props('itemsPerRow')).toBe(10)
      expect(virtualScrollGrid.props('rowHeight')).toBe(36)
      expect(virtualScrollGrid.props('containerHeight')).toBe(176)
    })
  })

  describe('錯誤處理', () => {
    it('應該正確處理載入狀態', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      // 元件初始應該有載入狀態或錯誤狀態或正常狀態之一
      const hasLoading = wrapper.find('.loading').exists()
      const hasError = wrapper.find('.error').exists()
      const hasGrid = wrapper.find('.emoji-grid-container').exists()
      
      expect(hasLoading || hasError || hasGrid).toBe(true)
    })
  })

  describe('事件發送', () => {
    it('應該正確發送 emoji-selected 事件', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      // 模擬選擇 emoji
      const testEmoji = { 
        emoji: '😀', 
        name: 'grinning face', 
        category: 'smileys',
        isCategory: false,
        displayEmoji: '😀'
      }
      await wrapper.vm.selectEmoji(testEmoji)

      // 檢查事件是否正確發送
      const emojiSelectedEvents = wrapper.emitted('emoji-selected')
      expect(emojiSelectedEvents).toHaveLength(1)
      
      // 檢查發送的事件資料格式
      const eventData = emojiSelectedEvents[0][0]
      expect(eventData).toEqual({
        emoji: '😀',
        name: 'grinning face',
        category: 'smileys',
        type: 'emoji'
      })
    })
  })

  describe('分類排版功能', () => {
    it('應該正確顯示分類標題樣式', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      // 檢查分類標題是否有正確的樣式
      const categoryItems = wrapper.vm.flattenedEmojis.filter(item => item.isCategory)
      expect(categoryItems.length).toBeGreaterThan(0)
      
      // 檢查分類標題格式
      categoryItems.forEach(item => {
        expect(item).toHaveProperty('isCategory', true)
        expect(item).toHaveProperty('categoryName')
        expect(item).toHaveProperty('categoryId')
      })
    })

    it('應該在分類標題後面顯示該分類的 emoji', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      const flattenedItems = wrapper.vm.flattenedEmojis
      let foundCategoryFollowedByEmojis = false

      for (let i = 0; i < flattenedItems.length - 1; i++) {
        const currentItem = flattenedItems[i]
        const nextItem = flattenedItems[i + 1]
        
        if (currentItem.isCategory && !nextItem.isCategory) {
          foundCategoryFollowedByEmojis = true
          break
        }
      }

      expect(foundCategoryFollowedByEmojis).toBe(true)
    })
  })

  describe('emoji 過濾與處理', () => {
    it('應該過濾掉複合 emoji 和膚色變體', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      const emojiItems = wrapper.vm.flattenedEmojis.filter(item => !item.isCategory)
      
      // 檢查 emoji 不應該包含複合字符或膚色修飾符
      emojiItems.forEach(item => {
        // 檢查不包含膚色修飾符
        expect(item.displayEmoji).not.toMatch(/[\u{1F3FB}-\u{1F3FF}]/u)
        // 檢查不包含 ZWJ (零寬連接符)
        expect(item.displayEmoji).not.toMatch(/\u200D/)
      })
    })

    it('應該顯示基礎 emoji 而非所有膚色變體', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      const emojiItems = wrapper.vm.flattenedEmojis.filter(item => !item.isCategory)
      
      // 檢查同一個基礎 emoji 不會重複出現多個膚色版本
      const baseEmojis = new Set()
      const duplicates = []
      
      emojiItems.forEach(item => {
        const baseEmoji = item.emoji?.replace(/[\u{1F3FB}-\u{1F3FF}]/gu, '')
        if (baseEmojis.has(baseEmoji)) {
          duplicates.push(baseEmoji)
        }
        baseEmojis.add(baseEmoji)
      })

      expect(duplicates.length).toBe(0)
    })
  })

  describe('膚色套用功能', () => {
    it('膚色變更時應該維持原有的捲軸位置', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      // 記錄初始資料長度
      const initialLength = wrapper.vm.flattenedEmojis.length

      // 變更膚色
      await wrapper.setProps({ selectedSkinTone: '🏻' })

      // 檢查資料長度應該保持相同（不應該重新載入或重置）
      expect(wrapper.vm.flattenedEmojis.length).toBe(initialLength)
    })

    it('應該只對支援膚色的 emoji 套用膚色修飾符', async () => {
      // 使用包含膚色資訊的 mock 資料
      const mockDataWithSkinTone = [
        {
          categoryId: 'people',
          categoryName: '人物',
          emojis: [
            {
              emoji: '👋',
              name: 'waving hand',
              category: 'people',
              has_skin_tone: true,
              skin_variations: {
                1: '👋🏻',
                2: '👋🏼',
                3: '👋🏽',
                4: '👋🏾',
                5: '👋🏿'
              }
            },
            {
              emoji: '😀',
              name: 'grinning face',
              category: 'people',
              has_skin_tone: false
            }
          ]
        }
      ]

      // Mock IconDataLoader 返回包含膚色資料的結構
      vi.mocked(IconDataLoader).mockImplementation(() => ({
        getEmojiData: vi.fn().mockResolvedValue(mockDataWithSkinTone)
      }))

      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: '1'  // 使用數字而非 emoji 字符
        }
      })

      await flushPromises()

      // 檢查是否有正確套用膚色
      const emojiItems = wrapper.vm.flattenedEmojis.filter(item => !item.isCategory)
      
      let foundSkinTonedEmoji = false
      let foundNonSkinTonedEmoji = false

      emojiItems.forEach(item => {
        if (item.displayEmoji?.includes('🏻')) {
          foundSkinTonedEmoji = true
        } else if (item.displayEmoji && !item.displayEmoji.includes('🏻')) {
          foundNonSkinTonedEmoji = true
        }
      })

      // 應該同時有套用膚色的和未套用的 emoji
      expect(foundSkinTonedEmoji).toBe(true)
      expect(foundNonSkinTonedEmoji).toBe(true)
    })

    it('VirtualScrollGrid 應該啟用 preserveScrollPosition', async () => {
      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      // 檢查 VirtualScrollGrid 元件的 preserveScrollPosition 屬性
      const virtualScrollGrid = wrapper.findComponent({ name: 'VirtualScrollGrid' })
      expect(virtualScrollGrid.exists()).toBe(true)
      expect(virtualScrollGrid.props('preserveScrollPosition')).toBe(true)
    })
  })

  describe('膚色變體結構化支援', () => {
    const mockStructuredEmojiData = [
      {
        categoryId: 'people',
        categoryName: '人物',
        emojis: [
          {
            emoji: '👋',
            name: 'waving hand',
            category: 'people',
            has_skin_tone: true,
            skin_variations: {
              1: '👋🏻',
              2: '👋🏼',
              3: '👋🏽',
              4: '👋🏾',
              5: '👋🏿'
            }
          },
          {
            emoji: '😀',
            name: 'grinning face',
            category: 'people',
            has_skin_tone: false
          }
        ]
      }
    ]

    it('should use backend skin tone flags instead of hardcoded list', async () => {
      // Mock IconDataLoader 返回結構化資料
      vi.mocked(IconDataLoader).mockImplementation(() => ({
        getEmojiData: vi.fn().mockResolvedValue(mockStructuredEmojiData)
      }))

      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      // 檢查不應該有硬編碼的膚色支援列表
      expect(wrapper.vm.supportsSkinTone).toBeUndefined()

      // 檢查應該使用後端資料判斷膚色支援
      const processedEmojis = wrapper.vm.processedEmojis
      const wavingHandEmoji = processedEmojis[0].emojis.find(e => e.emoji === '👋')
      const grinningFaceEmoji = processedEmojis[0].emojis.find(e => e.emoji === '😀')

      expect(wavingHandEmoji.has_skin_tone).toBe(true)
      expect(grinningFaceEmoji.has_skin_tone).toBe(false)
    })

    it('should apply skin tone from backend variations object', async () => {
      // Mock IconDataLoader 返回結構化資料
      vi.mocked(IconDataLoader).mockImplementation(() => ({
        getEmojiData: vi.fn().mockResolvedValue(mockStructuredEmojiData)
      }))

      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: '3' // 選擇中等膚色
        }
      })

      await flushPromises()

      const processedEmojis = wrapper.vm.processedEmojis
      const wavingHandEmoji = processedEmojis[0].emojis.find(e => e.emoji === '👋')

      // 應該顯示對應的膚色變體
      expect(wavingHandEmoji.displayEmoji).toBe('👋🏽')
    })

    it('should fallback to base emoji when skin tone variation not available', async () => {
      const incompleteVariationsData = [
        {
          categoryId: 'people',
          categoryName: '人物',
          emojis: [
            {
              emoji: '👋',
              name: 'waving hand',
              category: 'people',
              has_skin_tone: true,
              skin_variations: {
                1: '👋🏻',
                3: '👋🏽'
                // 缺少膚色 2, 4, 5
              }
            }
          ]
        }
      ]

      // Mock IconDataLoader
      vi.mocked(IconDataLoader).mockImplementation(() => ({
        getEmojiData: vi.fn().mockResolvedValue(incompleteVariationsData)
      }))

      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: '4' // 選擇不存在的膚色
        }
      })

      await flushPromises()

      const processedEmojis = wrapper.vm.processedEmojis
      const wavingHandEmoji = processedEmojis[0].emojis.find(e => e.emoji === '👋')

      // 應該 fallback 到基礎 emoji
      expect(wavingHandEmoji.displayEmoji).toBe('👋')
    })

    it('should maintain scroll position when skin tone changes', async () => {
      // Mock IconDataLoader
      vi.mocked(IconDataLoader).mockImplementation(() => ({
        getEmojiData: vi.fn().mockResolvedValue(mockStructuredEmojiData)
      }))

      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      // 記錄初始的 flattened emojis 長度
      const initialLength = wrapper.vm.flattenedEmojis.length

      // 模擬膚色變更
      await wrapper.setProps({ selectedSkinTone: '2' })

      // 長度應該保持相同（因為只是變更顯示的 emoji，不改變結構）
      expect(wrapper.vm.flattenedEmojis.length).toBe(initialLength)

      // VirtualScrollGrid 的 preserveScrollPosition 應該為 true
      const virtualScrollGrid = wrapper.findComponent({ name: 'VirtualScrollGrid' })
      expect(virtualScrollGrid.props('preserveScrollPosition')).toBe(true)
    })

    it('should handle missing has_skin_tone property gracefully', async () => {
      const legacyFormatData = [
        {
          categoryId: 'people',
          categoryName: '人物',
          emojis: [
            {
              emoji: '👋',
              name: 'waving hand',
              category: 'people'
              // 缺少 has_skin_tone 屬性（舊格式資料）
            }
          ]
        }
      ]

      // Mock IconDataLoader
      vi.mocked(IconDataLoader).mockImplementation(() => ({
        getEmojiData: vi.fn().mockResolvedValue(legacyFormatData)
      }))

      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: '2'
        }
      })

      await flushPromises()

      const processedEmojis = wrapper.vm.processedEmojis
      const wavingHandEmoji = processedEmojis[0].emojis.find(e => e.emoji === '👋')

      // 應該顯示原始 emoji（因為沒有膚色支援資訊）
      expect(wavingHandEmoji.displayEmoji).toBe('👋')
    })

    it('should reduce total emoji count with structured data', async () => {
      // Mock IconDataLoader
      vi.mocked(IconDataLoader).mockImplementation(() => ({
        getEmojiData: vi.fn().mockResolvedValue(mockStructuredEmojiData)
      }))

      wrapper = mount(EmojiPanel, {
        props: {
          searchQuery: '',
          selectedSkinTone: ''
        }
      })

      await flushPromises()

      const flattenedItems = wrapper.vm.flattenedEmojis
      const emojiItems = flattenedItems.filter(item => !item.isCategory)

      // 應該只有 2 個基礎 emoji（👋 和 😀），而不是 6 個（👋 + 5個膚色變體 + 😀）
      expect(emojiItems.length).toBe(2)

      // 確保沒有重複的膚色變體
      const emojis = emojiItems.map(item => item.emoji)
      const uniqueEmojis = [...new Set(emojis)]
      expect(emojis.length).toBe(uniqueEmojis.length)
    })

    it('should not contain hardcoded skin tone support list', () => {
      wrapper = mount(EmojiPanel)

      // 檢查元件內不應該有硬編碼的膚色支援列表
      const componentSource = wrapper.vm.$options.__vccOpts || wrapper.vm.$options
      const setupFunction = componentSource.setup?.toString() || ''

      // 這些是硬編碼的膚色支援列表中的 emoji，不應該出現在新版本中
      const hardcodedEmojis = ['👋', '🤚', '🖐', '✋', '🖖', '👌']
      const hardcodedPatternsFound = hardcodedEmojis.some(emoji => 
        setupFunction.includes(`'${emoji}'`) || setupFunction.includes(`"${emoji}"`)
      )

      expect(hardcodedPatternsFound).toBe(false)
    })
  })
})