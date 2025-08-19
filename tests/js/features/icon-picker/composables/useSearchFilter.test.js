import { describe, it, expect, vi, beforeEach } from 'vitest'
import { ref, nextTick } from 'vue'
import { useSearchFilter } from '@/features/icon-picker/composables/useSearchFilter.js'

describe('useSearchFilter', () => {
  let mockData

  beforeEach(() => {
    mockData = ref([
      {
        categoryId: 'smileys',
        categoryName: '表情符號',
        emojis: [
          { emoji: '😀', name: 'grinning face', keywords: ['happy', 'smile'] },
          { emoji: '😃', name: 'grinning face with big eyes', keywords: ['happy', 'joy'] },
          { emoji: '😢', name: 'crying face', keywords: ['sad', 'tear'] }
        ]
      },
      {
        categoryId: 'animals',
        categoryName: '動物',
        emojis: [
          { emoji: '🐶', name: 'dog face', keywords: ['animal', 'pet'] },
          { emoji: '🐱', name: 'cat face', keywords: ['animal', 'pet'] },
          { emoji: '🦁', name: 'lion', keywords: ['animal', 'wild'] }
        ]
      }
    ])
  })

  describe('基本功能', () => {
    it('應該初始化預設狀態', () => {
      const { searchQuery, filteredData } = useSearchFilter(mockData)
      
      expect(searchQuery.value).toBe('')
      expect(filteredData.value).toEqual(mockData.value)
    })

    it('應該提供搜尋統計', () => {
      const { searchStats } = useSearchFilter(mockData)
      
      expect(searchStats.value.totalItems).toBe(6) // 3 + 3 emojis
      expect(searchStats.value.totalCategories).toBe(2)
      expect(searchStats.value.filteredItems).toBe(6)
      expect(searchStats.value.filteredCategories).toBe(2)
    })
  })

  describe('搜尋功能', () => {
    it('應該根據名稱搜尋', async () => {
      const { searchQuery, filteredData } = useSearchFilter(mockData)
      
      searchQuery.value = 'grinning'
      await nextTick()
      
      expect(filteredData.value).toHaveLength(1)
      expect(filteredData.value[0].emojis).toHaveLength(2)
      expect(filteredData.value[0].emojis.every(emoji => 
        emoji.name.includes('grinning')
      )).toBe(true)
    })

    it('應該根據關鍵字搜尋', async () => {
      const { searchQuery, filteredData } = useSearchFilter(mockData)
      
      searchQuery.value = 'happy'
      await nextTick()
      
      expect(filteredData.value).toHaveLength(1)
      expect(filteredData.value[0].emojis).toHaveLength(2)
      expect(filteredData.value[0].emojis.every(emoji => 
        emoji.keywords.includes('happy')
      )).toBe(true)
    })

    it('應該根據 emoji 本身搜尋', async () => {
      const { searchQuery, filteredData } = useSearchFilter(mockData)
      
      searchQuery.value = '🐶'
      await nextTick()
      
      expect(filteredData.value).toHaveLength(1)
      expect(filteredData.value[0].emojis).toHaveLength(1)
      expect(filteredData.value[0].emojis[0].emoji).toBe('🐶')
    })

    it('應該支援大小寫不敏感搜尋', async () => {
      const { searchQuery, filteredData } = useSearchFilter(mockData)
      
      searchQuery.value = 'HAPPY'
      await nextTick()
      
      expect(filteredData.value).toHaveLength(1)
      expect(filteredData.value[0].emojis).toHaveLength(2)
    })

    it('應該過濾空的分類', async () => {
      const { searchQuery, filteredData } = useSearchFilter(mockData)
      
      searchQuery.value = 'nonexistent'
      await nextTick()
      
      expect(filteredData.value).toHaveLength(0)
    })

    it('應該正確處理空白搜尋', async () => {
      const { searchQuery, filteredData } = useSearchFilter(mockData)
      
      searchQuery.value = '   '
      await nextTick()
      
      expect(filteredData.value).toEqual(mockData.value)
    })
  })

  describe('自訂搜尋邏輯', () => {
    it('應該支援自訂搜尋函數', async () => {
      const customSearchFn = (item, query) => {
        return item.name.startsWith(query.toLowerCase())
      }

      const { searchQuery, filteredData } = useSearchFilter(mockData, {
        searchFunction: customSearchFn
      })
      
      searchQuery.value = 'grin'
      await nextTick()
      
      expect(filteredData.value).toHaveLength(1)
      expect(filteredData.value[0].emojis).toHaveLength(2)
    })

    it('應該支援自訂資料結構', async () => {
      const iconData = ref([
        {
          categoryName: 'General',
          icons: [
            { name: 'home', component: 'HomeIcon', tags: ['house', 'main'] },
            { name: 'user', component: 'UserIcon', tags: ['person', 'account'] }
          ]
        }
      ])

      const customSearchFn = (item, query) => {
        const lowerQuery = query.toLowerCase()
        return item.name.includes(lowerQuery) || 
               item.tags.some(tag => tag.includes(lowerQuery))
      }

      const { searchQuery, filteredData } = useSearchFilter(iconData, {
        itemsKey: 'icons',
        searchFunction: customSearchFn
      })
      
      searchQuery.value = 'house'
      await nextTick()
      
      expect(filteredData.value).toHaveLength(1)
      expect(filteredData.value[0].icons).toHaveLength(1)
      expect(filteredData.value[0].icons[0].name).toBe('home')
    })
  })

  describe('搜尋統計', () => {
    it('應該正確計算搜尋統計', async () => {
      const { searchQuery, searchStats } = useSearchFilter(mockData)
      
      // 初始狀態
      expect(searchStats.value.totalItems).toBe(6)
      expect(searchStats.value.totalCategories).toBe(2)
      expect(searchStats.value.filteredItems).toBe(6)
      expect(searchStats.value.filteredCategories).toBe(2)
      
      // 搜尋後
      searchQuery.value = 'animal'
      await nextTick()
      
      expect(searchStats.value.totalItems).toBe(6) // 總數不變
      expect(searchStats.value.totalCategories).toBe(2) // 總數不變
      expect(searchStats.value.filteredItems).toBe(3) // 只有動物分類的3個
      expect(searchStats.value.filteredCategories).toBe(1) // 只有1個分類匹配
    })

    it('應該支援無結果的統計', async () => {
      const { searchQuery, searchStats } = useSearchFilter(mockData)
      
      searchQuery.value = 'xyz'
      await nextTick()
      
      expect(searchStats.value.filteredItems).toBe(0)
      expect(searchStats.value.filteredCategories).toBe(0)
    })
  })

  describe('清除搜尋', () => {
    it('應該提供清除搜尋功能', async () => {
      const { searchQuery, filteredData, clearSearch } = useSearchFilter(mockData)
      
      searchQuery.value = 'test'
      await nextTick()
      
      clearSearch()
      
      expect(searchQuery.value).toBe('')
      expect(filteredData.value).toEqual(mockData.value)
    })
  })

  describe('響應式更新', () => {
    it('應該響應原始資料變化', async () => {
      const { filteredData, searchStats } = useSearchFilter(mockData)
      
      // 新增資料
      mockData.value.push({
        categoryId: 'food',
        categoryName: '食物',
        emojis: [
          { emoji: '🍎', name: 'apple', keywords: ['fruit'] }
        ]
      })
      
      await nextTick()
      
      expect(filteredData.value).toHaveLength(3)
      expect(searchStats.value.totalItems).toBe(7)
      expect(searchStats.value.totalCategories).toBe(3)
    })

    it('應該在搜尋狀態下響應資料變化', async () => {
      const { searchQuery, filteredData } = useSearchFilter(mockData)
      
      searchQuery.value = 'fruit'
      await nextTick()
      expect(filteredData.value).toHaveLength(0)
      
      // 新增包含 'fruit' 關鍵字的資料
      mockData.value[0].emojis.push({
        emoji: '🍎', 
        name: 'apple', 
        keywords: ['fruit']
      })
      
      await nextTick()
      
      expect(filteredData.value).toHaveLength(1)
      expect(filteredData.value[0].emojis).toHaveLength(1)
      expect(filteredData.value[0].emojis[0].emoji).toBe('🍎')
    })
  })

  describe('效能最佳化', () => {
    it('應該防抖搜尋查詢', async () => {
      const { searchQuery, filteredData } = useSearchFilter(mockData, {
        debounce: 100
      })
      
      // 快速連續變更
      searchQuery.value = 'h'
      searchQuery.value = 'ha'
      searchQuery.value = 'hap'
      searchQuery.value = 'happ'
      searchQuery.value = 'happy'
      
      // 立即檢查，應該還是原始資料
      expect(filteredData.value).toEqual(mockData.value)
      
      // 等待防抖時間
      await new Promise(resolve => setTimeout(resolve, 150))
      
      expect(filteredData.value).toHaveLength(1)
      expect(filteredData.value[0].emojis).toHaveLength(2)
    })
  })

  describe('邊界情況', () => {
    it('應該處理空資料', () => {
      const emptyData = ref([])
      const { searchQuery, filteredData, searchStats } = useSearchFilter(emptyData)
      
      expect(filteredData.value).toEqual([])
      expect(searchStats.value.totalItems).toBe(0)
      
      searchQuery.value = 'test'
      expect(filteredData.value).toEqual([])
    })

    it('應該處理無效的資料結構', () => {
      const invalidData = ref([
        { categoryName: 'Invalid', emojis: null },
        { categoryName: 'Another', emojis: undefined },
        { categoryName: 'Valid', emojis: [{ emoji: '😀', name: 'test' }] }
      ])
      
      const { filteredData, searchStats } = useSearchFilter(invalidData)
      
      // 無效的分類會被轉換為空陣列，在沒有搜尋時仍會顯示
      expect(filteredData.value).toHaveLength(3) // 所有分類都會顯示
      expect(searchStats.value.totalItems).toBe(1) // 但只有1個有效項目
      
      // 但無效分類的項目陣列會是空的
      expect(filteredData.value[0].emojis).toEqual([])
      expect(filteredData.value[1].emojis).toEqual([])
      expect(filteredData.value[2].emojis).toHaveLength(1)
    })

    it('應該處理項目缺少必要屬性', async () => {
      const incompleteData = ref([
        {
          categoryName: 'Incomplete',
          emojis: [
            { emoji: '😀' }, // 缺少 name
            { name: 'test' }, // 缺少 emoji
            { emoji: '😃', name: 'complete', keywords: ['happy'] }
          ]
        }
      ])
      
      const { searchQuery, filteredData } = useSearchFilter(incompleteData)
      
      searchQuery.value = 'complete'
      await nextTick()
      
      expect(filteredData.value).toHaveLength(1)
      expect(filteredData.value[0].emojis).toHaveLength(1)
      expect(filteredData.value[0].emojis[0].name).toBe('complete')
    })
  })
})