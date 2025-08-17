import { describe, it, expect, beforeEach, vi } from 'vitest'
import { IconDataLoader } from '../../services/IconDataLoader.js'

describe('IconDataLoader - Icon Library Basic Tests', () => {
  let loader

  beforeEach(() => {
    loader = new IconDataLoader()
  })

  describe('基本合併功能', () => {
    it('_mergeIconLibraryData 可以正確合併兩種圖標', () => {
      const heroIcons = [
        { name: 'Home', component: 'HomeIcon', category: 'general' },
        { name: 'User', component: 'UserIcon', category: 'people' }
      ]
      
      const bootstrapIcons = [
        { name: 'House', class: 'bi-house', category: 'general' },
        { name: 'Person', class: 'bi-person', category: 'people' }
      ]
      
      const result = loader._mergeIconLibraryData(heroIcons, bootstrapIcons)
      
      // 驗證合併結果
      expect(Array.isArray(result)).toBe(true)
      expect(result.length).toBe(4)
      
      // 驗證 HeroIcons 格式
      const heroIconResults = result.filter(icon => icon.type === 'heroicons')
      expect(heroIconResults).toHaveLength(2)
      heroIconResults.forEach(icon => {
        expect(icon).toHaveProperty('name')
        expect(icon).toHaveProperty('component')
        expect(icon).toHaveProperty('type', 'heroicons')
        expect(icon).toHaveProperty('source', 'heroicons')
      })
      
      // 驗證 Bootstrap Icons 格式
      const bootstrapIconResults = result.filter(icon => icon.type === 'bootstrap')
      expect(bootstrapIconResults).toHaveLength(2)
      bootstrapIconResults.forEach(icon => {
        expect(icon).toHaveProperty('name')
        expect(icon).toHaveProperty('class')
        expect(icon).toHaveProperty('type', 'bootstrap')
        expect(icon).toHaveProperty('source', 'bootstrap-icons')
      })
    })

    it('可以處理空的 HeroIcons 陣列', () => {
      const heroIcons = []
      const bootstrapIcons = [
        { name: 'House', class: 'bi-house', category: 'general' }
      ]
      
      const result = loader._mergeIconLibraryData(heroIcons, bootstrapIcons)
      
      expect(result).toHaveLength(1)
      expect(result[0].type).toBe('bootstrap')
    })

    it('可以處理空的 Bootstrap Icons 陣列', () => {
      const heroIcons = [
        { name: 'Home', component: 'HomeIcon', category: 'general' }
      ]
      const bootstrapIcons = []
      
      const result = loader._mergeIconLibraryData(heroIcons, bootstrapIcons)
      
      expect(result).toHaveLength(1)
      expect(result[0].type).toBe('heroicons')
    })

    it('可以處理非陣列輸入', () => {
      const result1 = loader._mergeIconLibraryData(null, [])
      expect(result1).toEqual([])
      
      const result2 = loader._mergeIconLibraryData([], null)
      expect(result2).toEqual([])
      
      const result3 = loader._mergeIconLibraryData('invalid', { invalid: 'data' })
      expect(result3).toEqual([])
    })

    it('保持原有圖標的所有屬性', () => {
      const heroIcons = [
        { 
          name: 'Home', 
          component: 'HomeIcon', 
          category: 'general',
          description: 'Home icon',
          keywords: ['home', 'house']
        }
      ]
      
      const bootstrapIcons = [
        { 
          name: 'House', 
          class: 'bi-house', 
          category: 'general',
          tags: ['building', 'home']
        }
      ]
      
      const result = loader._mergeIconLibraryData(heroIcons, bootstrapIcons)
      
      // 檢查 HeroIcon 保持所有原有屬性
      const heroIcon = result.find(icon => icon.component === 'HomeIcon')
      expect(heroIcon).toMatchObject({
        name: 'Home',
        component: 'HomeIcon',
        category: 'general',
        description: 'Home icon',
        keywords: ['home', 'house'],
        type: 'heroicons',
        source: 'heroicons'
      })
      
      // 檢查 Bootstrap Icon 保持所有原有屬性
      const bootstrapIcon = result.find(icon => icon.class === 'bi-house')
      expect(bootstrapIcon).toMatchObject({
        name: 'House',
        class: 'bi-house',
        category: 'general',
        tags: ['building', 'home'],
        type: 'bootstrap',
        source: 'bootstrap-icons'
      })
    })
  })

  describe('快取機制測試', () => {
    it('可以正確設定和取得快取', () => {
      const testData = { test: 'data' }
      loader._setCache('test-key', testData)
      
      expect(loader._getCache('test-key')).toEqual(testData)
      expect(loader._hasValidCache('test-key')).toBe(true)
    })

    it('可以設定快取過期時間', () => {
      const testData = { test: 'data' }
      loader._setCache('test-key', testData, 100) // 100ms 過期
      
      expect(loader._hasValidCache('test-key')).toBe(true)
      
      // 等待過期
      setTimeout(() => {
        expect(loader._hasValidCache('test-key')).toBe(false)
      }, 150)
    })

    it('可以清除特定快取', () => {
      loader._setCache('key1', { data: 1 })
      loader._setCache('key2', { data: 2 })
      
      expect(loader._hasValidCache('key1')).toBe(true)
      expect(loader._hasValidCache('key2')).toBe(true)
      
      loader.clearCache('key1')
      
      expect(loader._hasValidCache('key1')).toBe(false)
      expect(loader._hasValidCache('key2')).toBe(true)
    })
  })

  describe('錯誤處理測試', () => {
    it('可以正確處理基本錯誤', () => {
      const originalError = new Error('Test error')
      const handledError = loader._handleError('Operation failed', originalError)
      
      expect(handledError).toBeInstanceOf(Error)
      expect(handledError.message).toBe('Operation failed: Test error')
      expect(handledError.originalError).toBe(originalError)
    })

    it('可以處理帶有 HTTP 狀態的錯誤', () => {
      const originalError = new Error('API error')
      originalError.response = {
        status: 404,
        statusText: 'Not Found',
        data: { message: 'Resource not found' }
      }
      
      const handledError = loader._handleError('API request failed', originalError)
      
      expect(handledError.status).toBe(404)
      expect(handledError.statusText).toBe('Not Found')
      expect(handledError.data).toEqual({ message: 'Resource not found' })
    })
  })
})