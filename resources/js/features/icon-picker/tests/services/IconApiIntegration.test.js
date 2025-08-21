import { describe, it, expect, beforeEach, vi } from 'vitest'
import { useIconService } from '../../composables/useIconService.js'

// Mock axios for API calls
vi.mock('axios', () => ({
  default: {
    get: vi.fn()
  }
}))

describe('Icon API Integration Tests', () => {
  let iconService
  let mockAxios

  beforeEach(async () => {
    // Reset mocks
    vi.clearAllMocks()
    
    // Get axios mock
    const axios = await import('axios')
    mockAxios = axios.default
    
    // Create icon service instance
    iconService = useIconService()
  })

  describe('Bootstrap Icons API 新格式測試', () => {
    it('應該正確載入扁平化的 Bootstrap Icons 資料', async () => {
      // 模擬新的 API 回應格式
      const mockApiResponse = {
        data: {
          data: {
            general: [
              {
                name: 'Activity',
                value: 'bi-activity',
                type: 'bootstrap-icons',
                keywords: ['activity'],
                category: 'general',
                has_variants: false
              },
              {
                name: 'Alarm',
                value: 'bi-alarm',
                type: 'bootstrap-icons', 
                keywords: ['alarm', 'clock'],
                category: 'general',
                has_variants: true
              },
              {
                name: 'Alarm Fill',
                value: 'bi-alarm-fill',
                type: 'bootstrap-icons',
                keywords: ['alarm', 'clock'],
                category: 'general', 
                has_variants: true
              }
            ]
          },
          meta: {
            total: 3,
            type: 'bootstrap-icons'
          }
        }
      }

      mockAxios.get.mockResolvedValueOnce(mockApiResponse)

      const result = await iconService.loadBootstrapIcons()

      // 驗證 API 調用
      expect(mockAxios.get).toHaveBeenCalledWith('/api/config/icon/bootstrap-icons')

      // 驗證回傳資料結構
      expect(result).toHaveProperty('data')
      expect(result).toHaveProperty('meta')
      expect(result.meta.total).toBe(3)

      // 驗證扁平化資料結構
      const generalIcons = result.data.general
      expect(Array.isArray(generalIcons)).toBe(true)
      expect(generalIcons).toHaveLength(3)

      // 驗證圖標資料格式
      generalIcons.forEach(icon => {
        expect(icon).toHaveProperty('name')
        expect(icon).toHaveProperty('value')
        expect(icon).toHaveProperty('type', 'bootstrap-icons')
        expect(icon).toHaveProperty('category', 'general')
        expect(icon).toHaveProperty('has_variants')
        expect(Array.isArray(icon.keywords)).toBe(true)
      })
    })

    it('應該正確識別有變體的圖標', async () => {
      const mockApiResponse = {
        data: {
          data: {
            general: [
              {
                name: 'Alarm',
                value: 'bi-alarm',
                type: 'bootstrap-icons',
                category: 'general',
                has_variants: true
              },
              {
                name: 'Alarm Fill', 
                value: 'bi-alarm-fill',
                type: 'bootstrap-icons',
                category: 'general',
                has_variants: true
              }
            ]
          },
          meta: { total: 2 }
        }
      }

      mockAxios.get.mockResolvedValueOnce(mockApiResponse)
      const result = await iconService.loadBootstrapIcons()

      const icons = result.data.general
      const outlineIcon = icons.find(icon => icon.value === 'bi-alarm')
      const fillIcon = icons.find(icon => icon.value === 'bi-alarm-fill')

      expect(outlineIcon.has_variants).toBe(true)
      expect(fillIcon.has_variants).toBe(true)

      // 驗證可以透過名稱區分變體
      expect(outlineIcon.name).toBe('Alarm')
      expect(fillIcon.name).toBe('Alarm Fill')
    })
  })

  describe('HeroIcons API 新格式測試', () => {
    it('應該正確載入扁平化的 HeroIcons 資料', async () => {
      const mockApiResponse = {
        data: {
          data: {
            general: [
              {
                id: 'academic-cap-outline',
                name: 'Academic Cap',
                value: 'AcademicCapIcon',
                type: 'heroicons',
                keywords: ['education', 'graduation'],
                category: 'general',
                has_variants: true,
                variant_type: 'outline'
              },
              {
                id: 'academic-cap-solid',
                name: 'Academic Cap',
                value: 'AcademicCapIcon', 
                type: 'heroicons',
                keywords: ['education', 'graduation'],
                category: 'general',
                has_variants: true,
                variant_type: 'solid'
              }
            ]
          },
          meta: {
            total: 2,
            type: 'heroicons'
          }
        }
      }

      mockAxios.get.mockResolvedValueOnce(mockApiResponse)

      const result = await iconService.loadHeroIcons()

      // 驗證 API 調用
      expect(mockAxios.get).toHaveBeenCalledWith('/api/config/icon/heroicons')

      // 驗證資料結構
      expect(result).toHaveProperty('data')
      expect(result).toHaveProperty('meta')

      // 驗證扁平化資料
      const generalIcons = result.data.general
      expect(Array.isArray(generalIcons)).toBe(true)
      expect(generalIcons).toHaveLength(2)

      // 驗證圖標資料格式
      generalIcons.forEach(icon => {
        expect(icon).toHaveProperty('id')
        expect(icon).toHaveProperty('name')
        expect(icon).toHaveProperty('value')
        expect(icon).toHaveProperty('type', 'heroicons')
        expect(icon).toHaveProperty('variant_type')
        expect(['outline', 'solid']).toContain(icon.variant_type)
      })
    })

    it('應該正確區分 outline 和 solid 變體', async () => {
      const mockApiResponse = {
        data: {
          data: {
            general: [
              {
                id: 'home-outline',
                name: 'Home',
                value: 'HomeIcon',
                type: 'heroicons',
                variant_type: 'outline'
              },
              {
                id: 'home-solid',
                name: 'Home',
                value: 'HomeIcon',
                type: 'heroicons', 
                variant_type: 'solid'
              }
            ]
          },
          meta: { total: 2 }
        }
      }

      mockAxios.get.mockResolvedValueOnce(mockApiResponse)
      const result = await iconService.loadHeroIcons()

      const icons = result.data.general
      const outlineIcon = icons.find(icon => icon.variant_type === 'outline')
      const solidIcon = icons.find(icon => icon.variant_type === 'solid')

      expect(outlineIcon).toBeTruthy()
      expect(solidIcon).toBeTruthy()
      expect(outlineIcon.name).toBe('Home')
      expect(solidIcon.name).toBe('Home')
      expect(outlineIcon.value).toBe('HomeIcon')
      expect(solidIcon.value).toBe('HomeIcon')
    })
  })

  describe('圖標庫資料合併測試', () => {
    it('應該正確合併 HeroIcons 和 Bootstrap Icons', async () => {
      // Mock HeroIcons API
      const mockHeroResponse = {
        data: {
          data: { 
            general: [{
              name: 'Home',
              value: 'HomeIcon',
              type: 'heroicons',
              variant_type: 'outline'
            }]
          },
          meta: { total: 1 }
        }
      }

      // Mock Bootstrap Icons API
      const mockBootstrapResponse = {
        data: {
          data: {
            general: [{
              name: 'House',
              value: 'bi-house', 
              type: 'bootstrap-icons',
              category: 'general'
            }]
          },
          meta: { total: 1 }
        }
      }

      // Mock categories API (if needed)
      const mockCategoriesResponse = {
        data: { data: {} }
      }

      mockAxios.get
        .mockResolvedValueOnce(mockHeroResponse) // HeroIcons
        .mockResolvedValueOnce(mockBootstrapResponse) // Bootstrap Icons  
        .mockResolvedValueOnce(mockCategoriesResponse) // Categories

      const result = await iconService.loadIconLibraryData()

      // 驗證合併結果包含兩種類型的圖標
      expect(result.data).toHaveProperty('heroicons')
      expect(result.data).toHaveProperty('bootstrap')

      // 驗證 HeroIcons 資料
      expect(Array.isArray(result.data.heroicons)).toBe(true)
      expect(result.data.heroicons[0]).toMatchObject({
        name: 'Home',
        value: 'HomeIcon',
        type: 'heroicons'
      })

      // 驗證 Bootstrap Icons 資料
      expect(result.data.bootstrap).toHaveProperty('general')
      expect(Array.isArray(result.data.bootstrap.general)).toBe(true)
      expect(result.data.bootstrap.general[0]).toMatchObject({
        name: 'House', 
        value: 'bi-house',
        type: 'bootstrap-icons'
      })
    })
  })

  describe('快取機制測試', () => {
    it('應該快取載入的資料', async () => {
      // 清除快取確保測試隔離
      iconService.clearCache('iconLibrary')
      
      const mockResponse = {
        data: {
          data: { general: [] },
          meta: { total: 0 }
        }
      }

      mockAxios.get.mockResolvedValue(mockResponse)

      // 第一次調用
      await iconService.loadBootstrapIcons()
      const firstCallCount = mockAxios.get.mock.calls.length

      // 第二次調用應該使用快取
      await iconService.loadBootstrapIcons()
      expect(mockAxios.get.mock.calls.length).toBe(firstCallCount) // 沒有增加
    })

    it('清除快取後應該重新載入', async () => {
      // 清除快取確保測試隔離
      iconService.clearCache('iconLibrary')
      
      const mockResponse = {
        data: {
          data: { general: [] },
          meta: { total: 0 }
        }
      }

      mockAxios.get.mockResolvedValue(mockResponse)

      // 第一次調用
      await iconService.loadBootstrapIcons()
      const firstCallCount = mockAxios.get.mock.calls.length

      // 清除快取
      iconService.clearCache('iconLibrary')

      // 再次調用應該重新載入
      await iconService.loadBootstrapIcons()
      expect(mockAxios.get.mock.calls.length).toBe(firstCallCount + 1)
    })
  })
})