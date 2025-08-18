import { describe, it, expect, beforeEach, vi } from 'vitest'
import { IconDataLoader } from '../../services/IconDataLoader.js'

// Mock HeroIcons
const mockHeroIcons = [
  { name: 'Home', component: 'HomeIcon', category: 'general' },
  { name: 'User', component: 'UserIcon', category: 'people' }
]

vi.mock('@/utils/heroicons/allHeroicons.js', () => ({
  default: mockHeroIcons
}))

// Mock Bootstrap Icons
const mockBootstrapIcons = [
  { name: 'House', class: 'bi-house', category: 'general' },
  { name: 'Person', class: 'bi-person', category: 'people' }
]

const mockBootstrapIconsIndex = {
  loadAllIcons: vi.fn().mockResolvedValue(),
  getAllLoadedIcons: vi.fn().mockReturnValue(mockBootstrapIcons)
}

vi.mock('@/utils/icons/index.js', () => ({
  default: mockBootstrapIconsIndex
}))

describe('IconDataLoader - Icon Library Data Integration', () => {
  let loader

  beforeEach(() => {
    vi.clearAllMocks()
    loader = new IconDataLoader()
  })

  describe('getIconLibraryData 合併載入', () => {
    beforeEach(() => {
      // 重設 mock 函數的狀態
      mockBootstrapIconsIndex.loadAllIcons.mockClear()
      mockBootstrapIconsIndex.getAllLoadedIcons.mockClear()
      mockBootstrapIconsIndex.loadAllIcons.mockResolvedValue()
      mockBootstrapIconsIndex.getAllLoadedIcons.mockReturnValue(mockBootstrapIcons)
    })

    it('可以成功載入並合併 HeroIcons 和 Bootstrap Icons', async () => {
      const result = await loader.getIconLibraryData()
      
      expect(mockBootstrapIconsIndex.loadAllIcons).toHaveBeenCalledTimes(1)
      expect(mockBootstrapIconsIndex.getAllLoadedIcons).toHaveBeenCalledTimes(1)
      
      // 驗證合併結果
      expect(Array.isArray(result)).toBe(true)
      expect(result.length).toBe(4) // 2 HeroIcons + 2 Bootstrap Icons
      
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

    it('可以處理空的 HeroIcons 資料', async () => {
      // 為這個測試建立新的 loader 實例
      const newLoader = new IconDataLoader()
      
      // Mock 空的 HeroIcons 只對這個實例
      vi.doMock('@/utils/heroicons/allHeroicons.js', () => ({
        default: []
      }))
      
      const result = await newLoader.getIconLibraryData()
      
      // 應該只有 Bootstrap Icons
      expect(result).toHaveLength(2)
      expect(result.every(icon => icon.type === 'bootstrap')).toBe(true)
    })

    it('可以處理空的 Bootstrap Icons 資料', async () => {
      mockBootstrapIconsIndex.getAllLoadedIcons.mockReturnValue([])
      loader.clearCache() // 確保重新載入
      
      const result = await loader.getIconLibraryData()
      
      // 應該只有 HeroIcons
      expect(result).toHaveLength(2)
      expect(result.every(icon => icon.type === 'heroicons')).toBe(true)
    })

    it('可以處理載入錯誤', async () => {
      mockBootstrapIconsIndex.loadAllIcons.mockRejectedValue(new Error('Load failed'))
      loader.clearCache() // 確保不使用快取
      
      await expect(loader.getIconLibraryData()).rejects.toThrow('Failed to load icon library data: Load failed')
    })

    it('可以快取合併後的資料', async () => {
      // 重設 mock 狀態
      mockBootstrapIconsIndex.loadAllIcons.mockClear()
      mockBootstrapIconsIndex.loadAllIcons.mockResolvedValue()
      
      // 第一次呼叫
      const result1 = await loader.getIconLibraryData()
      expect(mockBootstrapIconsIndex.loadAllIcons).toHaveBeenCalledTimes(1)
      
      // 第二次呼叫應該使用快取
      const result2 = await loader.getIconLibraryData()
      expect(mockBootstrapIconsIndex.loadAllIcons).toHaveBeenCalledTimes(1) // 沒有增加
      
      expect(result1).toEqual(result2)
    })

    it('清除快取後會重新載入資料', async () => {
      // 重設 mock 狀態
      mockBootstrapIconsIndex.loadAllIcons.mockClear()
      mockBootstrapIconsIndex.loadAllIcons.mockResolvedValue()
      
      // 第一次載入
      await loader.getIconLibraryData()
      expect(mockBootstrapIconsIndex.loadAllIcons).toHaveBeenCalledTimes(1)
      
      // 清除快取
      loader.clearCache('icon-library-data')
      
      // 再次載入，應該重新呼叫載入方法
      await loader.getIconLibraryData()
      expect(mockBootstrapIconsIndex.loadAllIcons).toHaveBeenCalledTimes(2)
    })

    it('保持原有圖標的所有屬性', async () => {
      // 重設 mock 狀態
      mockBootstrapIconsIndex.loadAllIcons.mockClear()
      mockBootstrapIconsIndex.loadAllIcons.mockResolvedValue()
      loader.clearCache()
      
      const result = await loader.getIconLibraryData()
      
      // 檢查 HeroIcons 保持原有屬性
      const heroIcon = result.find(icon => icon.component === 'HomeIcon')
      expect(heroIcon).toMatchObject({
        name: 'Home',
        component: 'HomeIcon',
        category: 'general',
        type: 'heroicons',
        source: 'heroicons'
      })
      
      // 檢查 Bootstrap Icons 保持原有屬性
      const bootstrapIcon = result.find(icon => icon.class === 'bi-house')
      expect(bootstrapIcon).toMatchObject({
        name: 'House',
        class: 'bi-house',
        category: 'general',
        type: 'bootstrap',
        source: 'bootstrap-icons'
      })
    })

    it('可以正確區分不同類型的圖標', async () => {
      // 重設 mock 狀態
      mockBootstrapIconsIndex.loadAllIcons.mockClear()
      mockBootstrapIconsIndex.loadAllIcons.mockResolvedValue()
      loader.clearCache()
      
      const result = await loader.getIconLibraryData()
      
      // 驗證類型標記
      const heroIcons = result.filter(icon => icon.type === 'heroicons')
      const bootstrapIcons = result.filter(icon => icon.type === 'bootstrap')
      
      expect(heroIcons.length).toBe(2)
      expect(bootstrapIcons.length).toBe(2)
      
      // HeroIcons 有 component 屬性
      heroIcons.forEach(icon => {
        expect(icon).toHaveProperty('component')
        expect(icon).not.toHaveProperty('class')
      })
      
      // Bootstrap Icons 有 class 屬性
      bootstrapIcons.forEach(icon => {
        expect(icon).toHaveProperty('class')
        expect(icon).not.toHaveProperty('component')
      })
    })
  })

  describe('資料格式相容性', () => {
    it('合併後的資料格式與現有系統相容', async () => {
      // 重設 mock 狀態
      mockBootstrapIconsIndex.loadAllIcons.mockClear()
      mockBootstrapIconsIndex.loadAllIcons.mockResolvedValue()
      loader.clearCache()
      
      const result = await loader.getIconLibraryData()
      
      // 每個圖標都應該有基本屬性
      result.forEach(icon => {
        expect(icon).toHaveProperty('name')
        expect(icon).toHaveProperty('type')
        expect(icon).toHaveProperty('source')
        
        // 根據類型檢查特定屬性
        if (icon.type === 'heroicons') {
          expect(icon).toHaveProperty('component')
        } else if (icon.type === 'bootstrap') {
          expect(icon).toHaveProperty('class')
        }
      })
    })

    it('保持與 VirtualScrollGrid 的相容性', async () => {
      // 重設 mock 狀態
      mockBootstrapIconsIndex.loadAllIcons.mockClear()
      mockBootstrapIconsIndex.loadAllIcons.mockResolvedValue()
      loader.clearCache()
      
      const result = await loader.getIconLibraryData()
      
      // VirtualScrollGrid 需要每個項目都有唯一識別
      result.forEach(icon => {
        expect(icon).toHaveProperty('name')
        
        // 檢查唯一標識符
        const hasUniqueId = icon.component || icon.class || icon.id
        expect(hasUniqueId).toBeTruthy()
      })
    })
  })

  describe('效能測試', () => {
    it('並行載入兩種圖標應該比順序載入快', async () => {
      // 重設 mock 狀態
      mockBootstrapIconsIndex.loadAllIcons.mockClear()
      mockBootstrapIconsIndex.loadAllIcons.mockResolvedValue()
      loader.clearCache()
      
      const startTime = Date.now()
      await loader.getIconLibraryData()
      const endTime = Date.now()
      
      // 這是一個基本的效能檢查，確保沒有明顯的效能問題
      expect(endTime - startTime).toBeLessThan(1000) // 應該在1秒內完成
    })
  })
})