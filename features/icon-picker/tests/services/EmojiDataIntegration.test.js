import { describe, it, expect, beforeEach, vi } from 'vitest'
import { IconDataLoader } from '../../services/IconDataLoader.js'

// Mock IconService
const mockIconService = {
  fetchEmojis: vi.fn(),
  clearCache: vi.fn()
}

vi.mock('../../../../resources/js/services/IconService.js', () => ({
  IconService: vi.fn().mockImplementation(() => mockIconService)
}))

describe('IconDataLoader - Emoji Data Integration', () => {
  let loader

  beforeEach(() => {
    vi.clearAllMocks()
    loader = new IconDataLoader()
    loader.iconService = mockIconService
  })

  describe('getEmojiData API 整合', () => {
    it('可以成功載入 emoji 資料', async () => {
      const mockEmojiData = [
        { emoji: '😊', name: 'smiling face', categoryId: 'smileys_emotion' },
        { emoji: '👍', name: 'thumbs up', categoryId: 'people_body' }
      ]
      
      mockIconService.fetchEmojis.mockResolvedValue(mockEmojiData)
      
      const result = await loader.getEmojiData()
      
      expect(mockIconService.fetchEmojis).toHaveBeenCalledTimes(1)
      expect(result).toEqual(mockEmojiData)
    })

    it('可以處理包裝在物件中的 emoji 資料', async () => {
      const mockWrappedData = {
        data: [
          { emoji: '😊', name: 'smiling face', categoryId: 'smileys_emotion' }
        ]
      }
      
      mockIconService.fetchEmojis.mockResolvedValue(mockWrappedData)
      
      const result = await loader.getEmojiData()
      
      expect(result).toEqual(mockWrappedData.data)
    })

    it('可以處理不同的資料包裝格式', async () => {
      const testCases = [
        // 直接陣列
        [{ emoji: '😊', name: 'smile' }],
        // data 包裝
        { data: [{ emoji: '😊', name: 'smile' }] },
        // emojis 包裝
        { emojis: [{ emoji: '😊', name: 'smile' }] },
        // items 包裝
        { items: [{ emoji: '😊', name: 'smile' }] }
      ]
      
      for (const testData of testCases) {
        mockIconService.fetchEmojis.mockResolvedValue(testData)
        loader.clearCache() // 清除快取確保重新載入
        
        const result = await loader.getEmojiData()
        expect(Array.isArray(result)).toBe(true)
        expect(result.length).toBeGreaterThan(0)
      }
    })

    it('遇到未知格式時返回空陣列', async () => {
      const invalidData = { unknown: 'format' }
      
      mockIconService.fetchEmojis.mockResolvedValue(invalidData)
      
      const result = await loader.getEmojiData()
      
      expect(result).toEqual([])
    })

    it('可以快取 emoji 資料', async () => {
      const mockEmojiData = [
        { emoji: '😊', name: 'smiling face', categoryId: 'smileys_emotion' }
      ]
      
      mockIconService.fetchEmojis.mockResolvedValue(mockEmojiData)
      
      // 第一次呼叫
      const result1 = await loader.getEmojiData()
      expect(mockIconService.fetchEmojis).toHaveBeenCalledTimes(1)
      
      // 第二次呼叫應該使用快取
      const result2 = await loader.getEmojiData()
      expect(mockIconService.fetchEmojis).toHaveBeenCalledTimes(1) // 沒有增加
      
      expect(result1).toEqual(result2)
    })

    it('API 錯誤時可以正確處理', async () => {
      const apiError = new Error('API request failed')
      apiError.response = {
        status: 500,
        statusText: 'Internal Server Error',
        data: { error: 'Server error' }
      }
      
      mockIconService.fetchEmojis.mockRejectedValue(apiError)
      
      await expect(loader.getEmojiData()).rejects.toThrow('Failed to load emoji data: API request failed')
      
      try {
        await loader.getEmojiData()
      } catch (error) {
        expect(error.status).toBe(500)
        expect(error.statusText).toBe('Internal Server Error')
        expect(error.data).toEqual({ error: 'Server error' })
      }
    })

    it('網路錯誤時可以正確處理', async () => {
      const networkError = new Error('Network timeout')
      mockIconService.fetchEmojis.mockRejectedValue(networkError)
      
      await expect(loader.getEmojiData()).rejects.toThrow('Failed to load emoji data: Network timeout')
    })

    it('無效資料時可以正確處理', async () => {
      // 測試 null 資料
      mockIconService.fetchEmojis.mockResolvedValue(null)
      await expect(loader.getEmojiData()).rejects.toThrow('Invalid data format')
      
      // 測試 undefined 資料
      mockIconService.fetchEmojis.mockResolvedValue(undefined)
      await expect(loader.getEmojiData()).rejects.toThrow('Invalid data format')
      
      // 測試字串資料
      mockIconService.fetchEmojis.mockResolvedValue('invalid')
      await expect(loader.getEmojiData()).rejects.toThrow('Invalid data format')
    })

    it('清除快取後會重新載入資料', async () => {
      const mockEmojiData = [
        { emoji: '😊', name: 'smiling face', categoryId: 'smileys_emotion' }
      ]
      
      mockIconService.fetchEmojis.mockResolvedValue(mockEmojiData)
      
      // 第一次載入
      await loader.getEmojiData()
      expect(mockIconService.fetchEmojis).toHaveBeenCalledTimes(1)
      
      // 清除快取
      loader.clearCache('emoji-data')
      
      // 再次載入，應該重新呼叫 API
      await loader.getEmojiData()
      expect(mockIconService.fetchEmojis).toHaveBeenCalledTimes(2)
    })
  })

  describe('資料格式相容性', () => {
    it('保持與現有 emoji 資料格式相容', async () => {
      const standardEmojiFormat = [
        {
          emoji: '😊',
          name: 'smiling face with smiling eyes',
          categoryId: 'smileys_emotion',
          category: 'Smileys & Emotion',
          order: 1
        },
        {
          emoji: '👍',
          name: 'thumbs up',
          categoryId: 'people_body',
          category: 'People & Body',
          order: 2
        }
      ]
      
      mockIconService.fetchEmojis.mockResolvedValue(standardEmojiFormat)
      
      const result = await loader.getEmojiData()
      
      expect(result).toEqual(standardEmojiFormat)
      
      // 驗證每個 emoji 都有必要的屬性
      result.forEach(emoji => {
        expect(emoji).toHaveProperty('emoji')
        expect(emoji).toHaveProperty('name')
        expect(emoji).toHaveProperty('categoryId')
      })
    })
  })
})