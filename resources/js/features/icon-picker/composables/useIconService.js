import axios from 'axios'
import { useAsyncState } from './useAsyncState.js'

/**
 * 圖標服務 Composable - 適配新的扁平化 API 格式
 * 
 * 提供統一的圖標資料載入介面，包含 emoji 和圖標庫資料
 */
export function useIconService() {
  const apiBaseUrl = '/api/config/icon'
  
  // 記憶體快取
  const cache = new Map()
  const cacheExpiry = new Map()
  const defaultCacheDuration = 24 * 60 * 60 * 1000 // 24小時

  /**
   * 載入 Emoji 資料
   * 
   * @returns {Promise<Array>} 處理後的 emoji 分類陣列
   */
  const loadEmojiData = async () => {
    const cacheKey = 'emojis'
    
    // 檢查快取
    if (hasValidCache(cacheKey)) {
      return getCache(cacheKey)
    }

    try {
      const response = await axios.get(`${apiBaseUrl}/emoji`)
      
      // 驗證回應資料
      validateResponseData(response.data)
      
      // 處理資料格式
      const processedData = processEmojiData(response.data)
      
      // 儲存到快取
      setCache(cacheKey, processedData)
      
      return processedData
    } catch (error) {
      throw handleError('Failed to load emoji data', error)
    }
  }

  /**
   * 載入 HeroIcons 資料 - 新的扁平化格式
   */
  const loadHeroIcons = async () => {
    const cacheKey = 'heroicons'
    
    if (hasValidCache(cacheKey)) {
      return getCache(cacheKey)
    }

    try {
      const response = await axios.get(`${apiBaseUrl}/heroicons`)
      validateResponseData(response.data)
      
      setCache(cacheKey, response.data)
      return response.data
    } catch (error) {
      throw handleError('Failed to load HeroIcons', error)
    }
  }

  /**
   * 載入 Bootstrap Icons 資料 - 新的扁平化格式
   */
  const loadBootstrapIcons = async () => {
    const cacheKey = 'bootstrap-icons'
    
    if (hasValidCache(cacheKey)) {
      return getCache(cacheKey)
    }

    try {
      const response = await axios.get(`${apiBaseUrl}/bootstrap-icons`)
      validateResponseData(response.data)
      
      setCache(cacheKey, response.data)
      return response.data
    } catch (error) {
      throw handleError('Failed to load Bootstrap Icons', error)
    }
  }

  /**
   * 載入圖標庫資料 - 簡化版本，直接合併扁平化資料
   * 
   * @returns {Promise<Object>} 合併的圖標庫資料
   */
  const loadIconLibraryData = async () => {
    const cacheKey = 'icon-library-data'
    
    // 檢查快取
    if (hasValidCache(cacheKey)) {
      return getCache(cacheKey)
    }

    try {
      // 並行載入兩種圖標資料
      const [heroIconsData, bootstrapIconsData] = await Promise.all([
        loadHeroIcons(),
        loadBootstrapIcons()
      ])
      
      // 合併資料 - 新的簡化合併邏輯
      const mergedData = mergeIconLibraryData(heroIconsData, bootstrapIconsData)
      
      // 儲存到快取
      setCache(cacheKey, mergedData)
      
      return mergedData
    } catch (error) {
      throw handleError('Failed to load icon library data', error)
    }
  }

  /**
   * 合併圖標庫資料 - 簡化版本，適配扁平化 API
   */
  const mergeIconLibraryData = (heroIconsData, bootstrapIconsData) => {
    const result = {
      data: {
        heroicons: [],
        bootstrap: {}
      },
      meta: {
        total: 0,
        type: 'mixed'
      }
    }
    
    // 處理 HeroIcons 資料 - 扁平化結構
    if (heroIconsData?.data) {
      // 新的 API 格式可能是物件（按分類）或陣列
      let heroIconsList = []
      
      if (Array.isArray(heroIconsData.data)) {
        heroIconsList = heroIconsData.data
      } else {
        // 如果是物件，展開所有分類
        heroIconsList = Object.values(heroIconsData.data).flat()
      }
      
      result.data.heroicons = heroIconsList.map(icon => ({
        ...icon,
        type: 'heroicons',
        source: 'heroicons'
      }))
      result.meta.total += heroIconsList.length
    }
    
    // 處理 Bootstrap Icons 資料 - 扁平化結構
    if (bootstrapIconsData?.data && typeof bootstrapIconsData.data === 'object') {
      let bootstrapTotal = 0
      
      // 直接使用 API 返回的分類結構
      Object.entries(bootstrapIconsData.data).forEach(([categoryName, categoryIcons]) => {
        if (Array.isArray(categoryIcons)) {
          result.data.bootstrap[categoryName] = categoryIcons.map(icon => ({
            ...icon,
            type: 'bootstrap-icons',
            source: 'bootstrap-icons'
          }))
          bootstrapTotal += categoryIcons.length
        }
      })
      
      result.meta.total += bootstrapTotal
    }
    
    return result
  }

  /**
   * 取得快取狀態
   */
  const getCacheStatus = () => {
    const now = Date.now()
    
    return {
      emoji: {
        cached: cache.has('emojis'),
        expired: cacheExpiry.has('emojis') && now > cacheExpiry.get('emojis'),
        size: cache.has('emojis') ? JSON.stringify(cache.get('emojis')).length : 0
      },
      iconLibrary: {
        cached: cache.has('icon-library-data'),
        expired: cacheExpiry.has('icon-library-data') && now > cacheExpiry.get('icon-library-data'),
        size: cache.has('icon-library-data') ? JSON.stringify(cache.get('icon-library-data')).length : 0
      }
    }
  }

  /**
   * 清除快取
   */
  const clearCache = (type = null) => {
    if (type === 'emoji') {
      cache.delete('emojis')
      cacheExpiry.delete('emojis')
    } else if (type === 'iconLibrary') {
      cache.delete('icon-library-data')
      cache.delete('heroicons')
      cache.delete('bootstrap-icons')
      cacheExpiry.delete('icon-library-data')
      cacheExpiry.delete('heroicons')
      cacheExpiry.delete('bootstrap-icons')
    } else {
      // 清除所有快取
      cache.clear()
      cacheExpiry.clear()
    }
  }

  // ===== 私有輔助函數 =====

  /**
   * 處理 Emoji 資料格式 - 適配新的扁平化 API 格式
   */
  const processEmojiData = (rawData) => {
    // 如果資料已經是陣列格式，直接返回
    if (Array.isArray(rawData)) {
      return rawData
    }
    
    // 如果是物件，嘗試提取陣列
    if (rawData && typeof rawData === 'object') {
      // 新的扁平化 API 格式：{ data: { categoryId: [...] } }
      if (rawData.data && typeof rawData.data === 'object') {
        return processNewEmojiFormat(rawData.data)
      }
      
      // 處理舊的 API 格式：{ categories: { categoryId: { subgroups: { subgroupId: { emojis: [...] } } } } }
      if (rawData.categories && typeof rawData.categories === 'object') {
        return processCategoriesFormat(rawData.categories)
      }
      
      // 檢查其他常見的資料結構
      if (rawData.emojis && Array.isArray(rawData.emojis)) {
        return rawData.emojis
      }
      if (rawData.items && Array.isArray(rawData.items)) {
        return rawData.items
      }
    }
    
    // 如果無法識別格式，返回空陣列
    console.warn('Unknown emoji data format, returning empty array')
    return []
  }

  /**
   * 處理新的扁平化 emoji 格式
   */
  const processNewEmojiFormat = (data) => {
    const result = []
    
    // 分類名稱對應表
    const categoryNameMap = {
      'smileys_emotion': '表情符號與人物',
      'people_body': '人物與身體',
      'animals_nature': '動物與自然',
      'food_drink': '食物與飲料',
      'travel_places': '旅遊與地點',
      'activities': '活動',
      'objects': '物品',
      'symbols': '符號',
      'flags': '旗幟'
    }
    
    Object.entries(data).forEach(([categoryId, emojis]) => {
      if (Array.isArray(emojis) && emojis.length > 0) {
        // API 端已經處理過濾，直接使用
        result.push({
          categoryId,
          categoryName: categoryNameMap[categoryId] || categoryId.replace(/_/g, ' '),
          emojis: emojis
        })
      }
    })
    
    return result
  }

  /**
   * 處理 categories 格式的 emoji 資料
   */
  const processCategoriesFormat = (categories) => {
    const result = []
    
    // 分類名稱對應表
    const categoryNameMap = {
      'smileys_emotion': '表情符號與人物',
      'people_body': '人物與身體',
      'animals_nature': '動物與自然',
      'food_drink': '食物與飲料',
      'travel_places': '旅遊與地點',
      'activities': '活動',
      'objects': '物品',
      'symbols': '符號',
      'flags': '旗幟'
    }
    
    Object.entries(categories).forEach(([categoryId, category]) => {
      if (!category.subgroups || typeof category.subgroups !== 'object') {
        return
      }
      
      // 收集該分類下所有子群組的 emoji
      const allEmojis = []
      
      Object.entries(category.subgroups).forEach(([subgroupId, subgroup]) => {
        if (Array.isArray(subgroup.emojis)) {
          allEmojis.push(...subgroup.emojis)
        }
      })
      
      // API 端已經處理過濾，直接使用
      const cleanedEmojis = allEmojis
      
      // 只有當有 emoji 時才新增分類
      if (cleanedEmojis.length > 0) {
        result.push({
          categoryId,
          categoryName: categoryNameMap[categoryId] || categoryId.replace(/_/g, ' '),
          emojis: cleanedEmojis
        })
      }
    })
    
    return result
  }


  /**
   * 快取相關輔助函數
   */
  const setCache = (key, data, duration = defaultCacheDuration) => {
    cache.set(key, data)
    cacheExpiry.set(key, Date.now() + duration)
  }

  const getCache = (key) => {
    return cache.get(key)
  }

  const hasValidCache = (key) => {
    if (!cache.has(key) || !cacheExpiry.has(key)) {
      return false
    }
    
    // 檢查是否過期
    const expiry = cacheExpiry.get(key)
    if (Date.now() > expiry) {
      // 清除過期快取
      cache.delete(key)
      cacheExpiry.delete(key)
      return false
    }
    
    return true
  }

  /**
   * 驗證回應資料
   */
  const validateResponseData = (data) => {
    if (data === null || data === undefined) {
      throw new Error('Invalid response data received')
    }
    
    // 檢查是否為物件且非字串
    if (typeof data !== 'object' || typeof data === 'string') {
      throw new Error('Invalid response data received')
    }
  }

  /**
   * 統一錯誤處理
   */
  const handleError = (message, originalError) => {
    const error = new Error(`${message}: ${originalError.message}`)
    error.originalError = originalError
    
    if (originalError.response) {
      error.status = originalError.response.status
      error.statusText = originalError.response.statusText
      error.data = originalError.response.data
    }
    
    return error
  }

  return {
    // 主要功能
    loadEmojiData,
    loadIconLibraryData,
    loadHeroIcons,          // 新增：公開這些方法供測試使用
    loadBootstrapIcons,     // 新增：公開這些方法供測試使用
    
    // 快取管理
    clearCache,
    getCacheStatus
  }
}