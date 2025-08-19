import axios from 'axios'
import { useAsyncState } from './useAsyncState.js'

/**
 * 圖標服務 Composable
 * 
 * 整合並簡化了原有的 IconService 和 IconDataLoader 功能
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
   * 載入圖標庫資料
   * 
   * @param {string} [style='outline'] - 圖標樣式
   * @returns {Promise<Object>} 合併的圖標庫資料
   */
  const loadIconLibraryData = async (style = 'outline') => {
    const cacheKey = `icon-library-${style}`
    
    // 檢查快取
    if (hasValidCache(cacheKey)) {
      return getCache(cacheKey)
    }

    try {
      // 並行載入圖標資料和變體資訊
      const [heroIconsData, bootstrapIconsData, bootstrapCategories, heroVariants, bootstrapVariants] = await Promise.all([
        loadHeroIcons(),
        loadBootstrapIcons(),
        loadBootstrapIconCategories(),
        loadHeroIconVariants(),
        loadBootstrapIconVariants()
      ])
      
      // 合併資料
      const mergedData = mergeIconLibraryData(
        heroIconsData, 
        bootstrapIconsData, 
        bootstrapCategories,
        heroVariants, 
        bootstrapVariants,
        style
      )
      
      // 儲存到快取
      setCache(cacheKey, mergedData)
      
      return mergedData
    } catch (error) {
      throw handleError('Failed to load icon library data', error)
    }
  }

  /**
   * 取得快取狀態
   * 
   * @returns {Object} 快取狀態資訊
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
        cached: Array.from(cache.keys()).some(key => key.startsWith('icon-library-')),
        expired: false, // 簡化實現
        size: Array.from(cache.keys())
          .filter(key => key.startsWith('icon-library-'))
          .reduce((total, key) => total + JSON.stringify(cache.get(key)).length, 0)
      }
    }
  }

  /**
   * 清除快取
   * 
   * @param {string} [type] - 快取類型 ('emoji' | 'iconLibrary') 或留空清除全部
   */
  const clearCache = (type = null) => {
    if (type === 'emoji') {
      cache.delete('emojis')
      cacheExpiry.delete('emojis')
    } else if (type === 'iconLibrary') {
      // 清除所有圖標庫快取
      Array.from(cache.keys())
        .filter(key => key.startsWith('icon-library-'))
        .forEach(key => {
          cache.delete(key)
          cacheExpiry.delete(key)
        })
    } else {
      // 清除所有快取
      cache.clear()
      cacheExpiry.clear()
    }
  }

  // ===== 私有輔助函數 =====

  /**
   * 載入 HeroIcons 資料
   */
  const loadHeroIcons = async () => {
    try {
      const response = await axios.get(`${apiBaseUrl}/heroicons`)
      validateResponseData(response.data)
      return response.data
    } catch (error) {
      console.warn('Failed to load HeroIcons:', error.message)
      return { data: [], meta: { total: 0 } }
    }
  }

  /**
   * 載入 Bootstrap Icons 資料
   */
  const loadBootstrapIcons = async () => {
    try {
      const response = await axios.get(`${apiBaseUrl}/bootstrap-icons`)
      validateResponseData(response.data)
      return response.data
    } catch (error) {
      console.warn('Failed to load Bootstrap Icons:', error.message)
      return { data: {}, meta: { total: 0 } }
    }
  }

  /**
   * 載入 Bootstrap Icons 分類資訊
   */
  const loadBootstrapIconCategories = async () => {
    try {
      const response = await axios.get(`${apiBaseUrl}/bootstrap-icons/categories`)
      validateResponseData(response.data)
      return response.data
    } catch (error) {
      console.warn('Failed to load Bootstrap Icons categories:', error.message)
      return { data: {} }
    }
  }

  /**
   * 載入 HeroIcons 變體資訊
   */
  const loadHeroIconVariants = async () => {
    try {
      const response = await axios.get(`${apiBaseUrl}/heroicons/variants`)
      validateResponseData(response.data)
      return response.data
    } catch (error) {
      console.warn('Failed to load HeroIcons variants:', error.message)
      return { data: {} }
    }
  }

  /**
   * 載入 Bootstrap Icons 變體資訊
   */
  const loadBootstrapIconVariants = async () => {
    try {
      const response = await axios.get(`${apiBaseUrl}/bootstrap-icons/variants`)
      validateResponseData(response.data)
      return response.data
    } catch (error) {
      console.warn('Failed to load Bootstrap Icons variants:', error.message)
      return { data: {} }
    }
  }

  /**
   * 合併圖標庫資料
   */
  const mergeIconLibraryData = (heroIconsData, bootstrapIconsData, bootstrapCategories, heroVariants, bootstrapVariants, currentStyle) => {
    const result = {
      data: {
        heroicons: [],
        bootstrap: {}
      },
      meta: {
        total: 0,
        currentStyle: currentStyle,
        variants: {
          heroicons: heroVariants?.data || {},
          bootstrap: bootstrapVariants?.data || {}
        },
        libraries: {
          heroicons: heroIconsData?.meta || {},
          bootstrap: bootstrapIconsData?.meta || {}
        },
        categories: {
          bootstrap: bootstrapCategories?.data || {}
        }
      }
    }
    
    // 處理 HeroIcons 資料
    if (heroIconsData?.data && Array.isArray(heroIconsData.data)) {
      result.data.heroicons = heroIconsData.data.map(icon => ({
        ...icon,
        type: 'heroicons',
        source: 'heroicons',
        currentStyle: currentStyle,
        hasVariants: true,
        variantInfo: buildHeroIconVariantInfo(icon, heroVariants?.data)
      }))
      result.meta.total += heroIconsData.data.length
    }
    
    // 處理 Bootstrap Icons 資料 - 按分類優先級排序
    if (bootstrapIconsData?.data && typeof bootstrapIconsData.data === 'object') {
      let bootstrapTotal = 0
      const categories = bootstrapCategories?.data || {}
      
      // 定義優先級排序
      const priorityOrder = { immediate: 1, high: 2, medium: 3, low: 4 }
      
      // 按優先級排序分類
      const sortedCategories = Object.entries(bootstrapIconsData.data)
        .sort(([catA], [catB]) => {
          const priorityA = categories[catA]?.priority || 'low'
          const priorityB = categories[catB]?.priority || 'low'
          return priorityOrder[priorityA] - priorityOrder[priorityB]
        })
      
      // 按排序後的順序處理分類
      sortedCategories.forEach(([categoryName, categoryIcons]) => {
        if (Array.isArray(categoryIcons)) {
          result.data.bootstrap[categoryName] = categoryIcons.map(icon => ({
            ...icon,
            type: 'bootstrap',
            source: 'bootstrap-icons',
            currentStyle: currentStyle,
            hasVariants: icon.variants && Object.keys(icon.variants).length > 0,
            variantInfo: buildBootstrapIconVariantInfo(icon, bootstrapVariants?.data),
            categoryInfo: categories[categoryName] || {}
          }))
          bootstrapTotal += categoryIcons.length
        }
      })
      
      result.meta.total += bootstrapTotal
    }
    
    return result
  }

  /**
   * 建立 HeroIcon 變體資訊
   */
  const buildHeroIconVariantInfo = (icon, variantData) => {
    if (!variantData || !variantData.mapping) {
      return {
        available: ['outline', 'solid'],
        current: icon.currentStyle || 'outline',
        mapping: {}
      }
    }
    
    return {
      available: Object.keys(variantData.mapping),
      current: icon.currentStyle || 'outline',
      mapping: variantData.mapping
    }
  }

  /**
   * 建立 Bootstrap Icon 變體資訊
   */
  const buildBootstrapIconVariantInfo = (icon, variantData) => {
    if (!variantData || !variantData.mapping) {
      return {
        available: icon.variants ? Object.keys(icon.variants) : ['outline'],
        current: icon.currentStyle || 'outline',
        mapping: icon.variants || {}
      }
    }
    
    return {
      available: icon.variants ? Object.keys(icon.variants) : Object.keys(variantData.mapping),
      current: icon.currentStyle || 'outline',
      mapping: icon.variants || variantData.mapping
    }
  }

  /**
   * 處理 Emoji 資料格式
   */
  const processEmojiData = (rawData) => {
    // 如果資料已經是陣列格式，直接返回
    if (Array.isArray(rawData)) {
      return rawData
    }
    
    // 如果是物件，嘗試提取陣列
    if (rawData && typeof rawData === 'object') {
      // 處理新的 API 格式：{ categories: { categoryId: { subgroups: { subgroupId: { emojis: [...] } } } } }
      if (rawData.categories && typeof rawData.categories === 'object') {
        return processCategoriesFormat(rawData.categories)
      }
      
      // 檢查常見的資料結構
      if (rawData.data && Array.isArray(rawData.data)) {
        return rawData.data
      }
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
      
      // 過濾和清理 emoji
      const cleanedEmojis = filterAndCleanEmojis(allEmojis)
      
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
   * 過濾和清理 emoji，移除複合 emoji 和膚色變體
   */
  const filterAndCleanEmojis = (emojis) => {
    const seen = new Set()
    const result = []
    
    emojis.forEach(emojiData => {
      if (!emojiData.emoji) return
      
      // 移除膚色修飾符和變化選擇器
      const baseEmoji = emojiData.emoji
        .replace(/[\u{1F3FB}-\u{1F3FF}]/gu, '') // 移除膚色修飾符
        .replace(/\uFE0F/g, '') // 移除變化選擇器
        .replace(/\u200D.*$/g, '') // 移除 ZWJ 序列（複合 emoji）
        .trim()
      
      // 跳過空字符串或已經處理過的基礎 emoji
      if (!baseEmoji || seen.has(baseEmoji)) {
        return
      }
      
      // 跳過複合 emoji（包含 ZWJ 的 emoji）
      if (emojiData.emoji.includes('\u200D')) {
        return
      }
      
      // 跳過膚色變體（保留基礎版本）
      if (/[\u{1F3FB}-\u{1F3FF}]/u.test(emojiData.emoji)) {
        return
      }
      
      seen.add(baseEmoji)
      result.push({
        ...emojiData,
        emoji: baseEmoji
      })
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
    
    // 快取管理
    clearCache,
    getCacheStatus
  }
}