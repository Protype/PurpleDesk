import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'
import { nextTick } from 'vue'
import { useIconService } from '@/features/icon-picker/composables/useIconService.js'

// Mock axios
vi.mock('axios', () => ({
  default: {
    get: vi.fn()
  }
}))

describe('useIconService', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    // 清除 localStorage
    if (typeof localStorage !== 'undefined') {
      localStorage.clear()
    }
  })

  afterEach(() => {
    vi.clearAllMocks()
  })

  describe('基本功能', () => {
    it('應該提供 emoji 載入功能', () => {
      const { loadEmojiData } = useIconService()
      
      expect(typeof loadEmojiData).toBe('function')
    })
    
    it('應該提供圖標庫載入功能', () => {
      const { loadIconLibraryData } = useIconService()
      
      expect(typeof loadIconLibraryData).toBe('function')
    })
    
    it('應該提供快取管理功能', () => {
      const { clearCache, getCacheStatus } = useIconService()
      
      expect(typeof clearCache).toBe('function')
      expect(typeof getCacheStatus).toBe('function')
    })
  })

  describe('Emoji 資料載入', () => {
    it('應該成功載入 emoji 資料', async () => {
      const mockEmojiData = {
        categories: {
          'smileys_emotion': {
            subgroups: {
              'face_smiling': {
                emojis: [
                  { emoji: '😀', name: 'grinning face' },
                  { emoji: '😃', name: 'grinning face with big eyes' }
                ]
              }
            }
          }
        }
      }

      const { loadEmojiData } = useIconService()
      
      // Mock successful API response
      const axios = await import('axios')
      axios.default.get.mockResolvedValue({ data: mockEmojiData })

      const result = await loadEmojiData()

      expect(axios.default.get).toHaveBeenCalledWith('/api/config/icon/emoji')
      expect(result).toBeDefined()
      expect(Array.isArray(result)).toBe(true)
    })

    it('應該處理 emoji 資料載入錯誤', async () => {
      const mockError = new Error('API Error')
      
      const { loadEmojiData } = useIconService()
      
      const axios = await import('axios')
      axios.default.get.mockRejectedValue(mockError)

      await expect(loadEmojiData()).rejects.toThrow('Failed to load emoji data: API Error')
    })

    it('應該使用快取避免重複請求', async () => {
      const mockEmojiData = {
        categories: {
          'smileys_emotion': {
            subgroups: {
              'face_smiling': {
                emojis: [{ emoji: '😀', name: 'grinning face' }]
              }
            }
          }
        }
      }

      const { loadEmojiData } = useIconService()
      
      const axios = await import('axios')
      axios.default.get.mockResolvedValue({ data: mockEmojiData })

      // 第一次請求
      await loadEmojiData()
      expect(axios.default.get).toHaveBeenCalledTimes(1)

      // 第二次請求應該使用快取
      await loadEmojiData()
      expect(axios.default.get).toHaveBeenCalledTimes(1)
    })
  })

  describe('圖標庫資料載入', () => {
    it('應該成功載入圖標庫資料', async () => {
      const mockHeroIconsData = {
        data: [
          { name: 'home', component: 'HomeIcon' },
          { name: 'user', component: 'UserIcon' }
        ]
      }

      const mockVariantsData = {
        data: { mapping: { outline: {}, solid: {} } }
      }

      const { loadIconLibraryData } = useIconService()
      
      const axios = await import('axios')
      axios.default.get
        .mockResolvedValueOnce({ data: mockHeroIconsData })
        .mockResolvedValueOnce({ data: mockVariantsData })

      const result = await loadIconLibraryData('outline')

      expect(result).toBeDefined()
      expect(result.data).toBeDefined()
      expect(result.data.heroicons).toBeDefined()
      expect(result.meta).toBeDefined()
    })

    it('應該處理不同的圖標樣式', async () => {
      const { loadIconLibraryData } = useIconService()
      
      const axios = await import('axios')
      axios.default.get.mockResolvedValue({ data: { data: [] } })

      await loadIconLibraryData('solid')
      expect(axios.default.get).toHaveBeenCalledWith('/api/config/icon/heroicons/style/solid')

      await loadIconLibraryData('outline')
      expect(axios.default.get).toHaveBeenCalledWith('/api/config/icon/heroicons/style/outline')
    })

    it('應該正確合併多個圖標庫資料', async () => {
      const mockHeroIconsData = {
        data: [{ name: 'home', component: 'HomeIcon' }],
        meta: { total: 1 }
      }

      const mockBootstrapIconsData = {
        data: {
          'general': [{ name: 'house', class: 'bi-house' }]
        },
        meta: { total: 1 }
      }

      const mockVariantsData = { data: {} }

      const { loadIconLibraryData } = useIconService()
      
      const axios = await import('axios')
      axios.default.get
        .mockImplementationOnce(() => Promise.resolve({ data: mockHeroIconsData }))
        .mockImplementationOnce(() => Promise.resolve({ data: mockBootstrapIconsData }))
        .mockImplementationOnce(() => Promise.resolve({ data: mockVariantsData }))
        .mockImplementationOnce(() => Promise.resolve({ data: mockVariantsData }))

      const result = await loadIconLibraryData()

      expect(result.data.heroicons).toHaveLength(1)
      expect(result.data.bootstrap).toBeDefined()
      expect(result.meta.total).toBe(2)
    })
  })

  describe('快取管理', () => {
    it('應該提供快取狀態資訊', () => {
      const { getCacheStatus } = useIconService()
      
      const status = getCacheStatus()
      
      expect(status).toBeDefined()
      expect(typeof status.emoji).toBe('object')
      expect(typeof status.iconLibrary).toBe('object')
    })

    it('應該能清除特定類型快取', async () => {
      const mockData = { data: [] }
      const { loadEmojiData, clearCache, getCacheStatus } = useIconService()
      
      const axios = await import('axios')
      axios.default.get.mockResolvedValue({ data: mockData })

      // 載入資料建立快取
      await loadEmojiData()
      let status = getCacheStatus()
      expect(status.emoji.cached).toBe(true)

      // 清除 emoji 快取
      clearCache('emoji')
      status = getCacheStatus()
      expect(status.emoji.cached).toBe(false)
    })

    it('應該能清除所有快取', async () => {
      const mockEmojiData = { data: [] }
      const mockIconData = { data: [] }
      
      const { loadEmojiData, loadIconLibraryData, clearCache, getCacheStatus } = useIconService()
      
      const axios = await import('axios')
      axios.default.get.mockResolvedValue({ data: mockIconData })

      // 載入資料建立快取
      await loadEmojiData()
      await loadIconLibraryData()

      let status = getCacheStatus()
      expect(status.emoji.cached || status.iconLibrary.cached).toBe(true)

      // 清除所有快取
      clearCache()
      status = getCacheStatus()
      expect(status.emoji.cached).toBe(false)
      expect(status.iconLibrary.cached).toBe(false)
    })
  })

  describe('錯誤處理', () => {
    it('應該正確格式化 API 錯誤', async () => {
      const mockError = {
        message: 'Network Error',
        response: {
          status: 500,
          statusText: 'Internal Server Error',
          data: { error: 'Database connection failed' }
        }
      }

      const { loadEmojiData } = useIconService()
      
      const axios = await import('axios')
      axios.default.get.mockRejectedValue(mockError)

      try {
        await loadEmojiData()
      } catch (error) {
        expect(error.message).toContain('Failed to load emoji data')
        expect(error.status).toBe(500)
        expect(error.statusText).toBe('Internal Server Error')
        expect(error.data).toEqual({ error: 'Database connection failed' })
      }
    })

    it('應該處理無效的回應資料', async () => {
      const { loadEmojiData } = useIconService()
      
      const axios = await import('axios')
      axios.default.get.mockResolvedValue({ data: null })

      await expect(loadEmojiData()).rejects.toThrow('Invalid response data')
    })
  })

  describe('資料處理', () => {
    it('應該正確處理 categories 格式的 emoji 資料', async () => {
      const mockEmojiData = {
        categories: {
          'smileys_emotion': {
            subgroups: {
              'face_smiling': {
                emojis: [
                  { emoji: '😀', name: 'grinning face' },
                  { emoji: '😃', name: 'grinning face with big eyes' }
                ]
              }
            }
          },
          'people_body': {
            subgroups: {
              'person': {
                emojis: [
                  { emoji: '👶', name: 'baby' }
                ]
              }
            }
          }
        }
      }

      const { loadEmojiData } = useIconService()
      
      const axios = await import('axios')
      axios.default.get.mockResolvedValue({ data: mockEmojiData })

      const result = await loadEmojiData()

      expect(Array.isArray(result)).toBe(true)
      expect(result).toHaveLength(2) // 兩個分類
      expect(result[0].categoryName).toBe('表情符號與人物')
      expect(result[0].emojis).toHaveLength(2)
      expect(result[1].categoryName).toBe('人物與身體')
      expect(result[1].emojis).toHaveLength(1)
    })

    it('應該過濾重複和複合 emoji', async () => {
      const mockEmojiData = {
        categories: {
          'smileys_emotion': {
            subgroups: {
              'face_smiling': {
                emojis: [
                  { emoji: '😀', name: 'grinning face' },
                  { emoji: '😀', name: 'grinning face' }, // 重複
                  { emoji: '👨‍💻', name: 'man technologist' }, // ZWJ 序列，應被過濾
                  { emoji: '😀🏻', name: 'grinning face: light skin tone' } // 膚色變體，應被過濾
                ]
              }
            }
          }
        }
      }

      const { loadEmojiData } = useIconService()
      
      const axios = await import('axios')
      axios.default.get.mockResolvedValue({ data: mockEmojiData })

      const result = await loadEmojiData()

      expect(result[0].emojis).toHaveLength(1) // 只保留基礎版本
      expect(result[0].emojis[0].emoji).toBe('😀')
    })
  })
})