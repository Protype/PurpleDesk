import { describe, it, expect, beforeEach, vi } from 'vitest'
import { IconDataLoader } from '../../services/IconDataLoader.js'

// Mock axios
vi.mock('axios', () => ({
  default: {
    get: vi.fn()
  }
}))

// Mock IconService
vi.mock('@/services/IconService.js', () => ({
  IconService: vi.fn().mockImplementation(() => ({
    fetchEmojis: vi.fn(),
    clearCache: vi.fn()
  }))
}))

describe('IconDataLoader', () => {
  let loader
  let mockIconService

  beforeEach(() => {
    vi.clearAllMocks()
    loader = new IconDataLoader()
    mockIconService = {
      fetchEmojis: vi.fn(),
      clearCache: vi.fn()
    }
    loader.iconService = mockIconService
  })

  describe('基礎功能', () => {
    it('可以建立 IconDataLoader 實例', () => {
      expect(loader).toBeInstanceOf(IconDataLoader)
      expect(loader.cache).toBeInstanceOf(Map)
    })

    it('有正確的初始狀態', () => {
      expect(loader.cache.size).toBe(0)
      expect(loader.cacheExpiry).toBeInstanceOf(Map)
    })
  })

  describe('快取機制', () => {
    it('可以設定和取得快取', () => {
      const testData = { test: 'data' }
      loader._setCache('test', testData)
      
      expect(loader._getCache('test')).toEqual(testData)
    })

    it('可以檢查快取是否存在', () => {
      const testData = { test: 'data' }
      loader._setCache('test', testData)
      
      expect(loader._hasValidCache('test')).toBe(true)
      expect(loader._hasValidCache('nonexistent')).toBe(false)
    })

    it('過期的快取應該被視為無效', () => {
      const testData = { test: 'data' }
      loader._setCache('test', testData, 0) // 立即過期
      
      // 等待一毫秒確保過期
      setTimeout(() => {
        expect(loader._hasValidCache('test')).toBe(false)
      }, 1)
    })

    it('可以清除所有快取', () => {
      loader._setCache('test1', { data: 1 })
      loader._setCache('test2', { data: 2 })
      
      expect(loader.cache.size).toBe(2)
      
      loader.clearCache()
      
      expect(loader.cache.size).toBe(0)
      expect(loader.cacheExpiry.size).toBe(0)
    })

    it('可以清除特定快取', () => {
      loader._setCache('test1', { data: 1 })
      loader._setCache('test2', { data: 2 })
      
      loader.clearCache('test1')
      
      expect(loader.cache.has('test1')).toBe(false)
      expect(loader.cache.has('test2')).toBe(true)
    })
  })

  describe('錯誤處理', () => {
    it('可以處理基本錯誤', () => {
      const originalError = new Error('Test error')
      const handledError = loader._handleError('Failed to test', originalError)
      
      expect(handledError).toBeInstanceOf(Error)
      expect(handledError.message).toBe('Failed to test: Test error')
      expect(handledError.originalError).toBe(originalError)
    })

    it('可以處理 HTTP 錯誤回應', () => {
      const originalError = new Error('Network error')
      originalError.response = {
        status: 404,
        statusText: 'Not Found',
        data: { message: 'Resource not found' }
      }
      
      const handledError = loader._handleError('API failed', originalError)
      
      expect(handledError.status).toBe(404)
      expect(handledError.statusText).toBe('Not Found')
      expect(handledError.data).toEqual({ message: 'Resource not found' })
    })
  })

  describe('資料格式驗證', () => {
    it('應該接受有效的物件資料', () => {
      expect(() => loader._validateData({ valid: 'data' })).not.toThrow()
      expect(() => loader._validateData([])).not.toThrow()
    })

    it('應該拒絕無效資料', () => {
      expect(() => loader._validateData(null)).toThrow('Invalid data format')
      expect(() => loader._validateData(undefined)).toThrow('Invalid data format')
      expect(() => loader._validateData('string')).toThrow('Invalid data format')
      expect(() => loader._validateData(123)).toThrow('Invalid data format')
    })
  })

  describe('資料載入介面', () => {
    it('getEmojiData 方法存在', () => {
      expect(typeof loader.getEmojiData).toBe('function')
    })

    it('getIconLibraryData 方法存在', () => {
      expect(typeof loader.getIconLibraryData).toBe('function')
    })
  })
})