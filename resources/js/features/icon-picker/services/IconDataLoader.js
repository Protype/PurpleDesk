import { IconService } from '@/services/IconService.js'
import { useIconVariants } from '../composables/useIconVariants.js'

/**
 * IconDataLoader - 統一的圖標資料載入服務
 * 
 * 提供統一的介面來載入不同類型的圖標資料，包含快取機制和錯誤處理
 */
export class IconDataLoader {
  /**
   * 建立 IconDataLoader 實例
   */
  constructor() {
    this.cache = new Map()
    this.cacheExpiry = new Map()
    this.iconService = new IconService()
    this.iconVariants = useIconVariants()
    this.defaultCacheDuration = 24 * 60 * 60 * 1000 // 24小時
  }

  /**
   * 取得 Emoji 資料
   * 
   * @returns {Promise<Array>} Emoji 資料陣列
   * @throws {Error} 載入失敗時拋出錯誤
   */
  async getEmojiData() {
    const cacheKey = 'emoji-data'
    
    // 檢查快取
    if (this._hasValidCache(cacheKey)) {
      return this._getCache(cacheKey)
    }

    try {
      // 從 API 載入 emoji 資料
      const rawData = await this.iconService.fetchEmojis()
      
      // 驗證資料格式
      this._validateData(rawData)
      
      // 轉換為統一格式（如果需要）
      const processedData = this._processEmojiData(rawData)
      
      // 儲存到快取
      this._setCache(cacheKey, processedData)
      
      return processedData
    } catch (error) {
      throw this._handleError('Failed to load emoji data', error)
    }
  }

  /**
   * 取得圖標庫資料 (HeroIcon + Bootstrap Icon 合併)
   * 
   * @param {string} [style] - 指定圖標樣式 ('outline' | 'solid')，預設使用當前樣式
   * @returns {Promise<Object>} 合併的圖標資料物件
   * @throws {Error} 載入失敗時拋出錯誤
   */
  async getIconLibraryData(style = null) {
    const targetStyle = style || this.iconVariants.currentIconStyle.value
    const cacheKey = `icon-library-data-${targetStyle}`
    
    // 檢查快取
    if (this._hasValidCache(cacheKey)) {
      return this._getCache(cacheKey)
    }

    try {
      // 並行載入圖標資料和變體資訊
      const [heroIconsData, bootstrapIconsData, heroVariants, bootstrapVariants] = await Promise.all([
        this._loadHeroIcons(targetStyle),
        this._loadBootstrapIcons(targetStyle),
        this._loadHeroIconVariants(),
        this._loadBootstrapIconVariants()
      ])
      
      // 合併資料
      const mergedData = this._mergeIconLibraryData(
        heroIconsData, 
        bootstrapIconsData, 
        heroVariants, 
        bootstrapVariants,
        targetStyle
      )
      
      // 儲存到快取
      this._setCache(cacheKey, mergedData)
      
      return mergedData
    } catch (error) {
      throw this._handleError('Failed to load icon library data', error)
    }
  }

  /**
   * 清除快取
   * 
   * @param {string} [key] - 要清除的快取鍵，如果不指定則清除所有快取
   */
  clearCache(key = null) {
    if (key) {
      this.cache.delete(key)
      this.cacheExpiry.delete(key)
      
      // 同時清除 IconService 的快取
      if (key === 'emoji-data') {
        this.iconService.clearCache('emojis')
      } else if (key.startsWith('icon-library-data')) {
        this.iconService.clearCache('heroicons')
        this.iconService.clearCache('bootstrap_icons')
        this.iconService.clearCache('heroicons_variants')
        this.iconService.clearCache('bootstrap_icons_variants')
      }
    } else {
      this.cache.clear()
      this.cacheExpiry.clear()
      this.iconService.clearCache()
    }
  }

  // ===== 私有方法 =====

  /**
   * 載入 HeroIcons 資料
   * 
   * @private
   * @param {string} [style] - 指定圖標樣式
   * @returns {Promise<Object>} HeroIcons 資料物件
   */
  async _loadHeroIcons(style = null) {
    try {
      if (style) {
        // 載入特定樣式的圖標
        return await this.iconService.fetchHeroIconsByStyle(style)
      } else {
        // 載入所有圖標
        return await this.iconService.fetchHeroIcons()
      }
    } catch (error) {
      console.warn('Failed to load HeroIcons:', error.message)
      return { data: [], meta: { total: 0 } }
    }
  }

  /**
   * 載入 Bootstrap Icons 資料
   * 
   * @private
   * @param {string} [style] - 指定圖標樣式
   * @returns {Promise<Object>} Bootstrap Icons 資料物件
   */
  async _loadBootstrapIcons(style = null) {
    try {
      if (style) {
        // 載入特定樣式的圖標
        return await this.iconService.fetchBootstrapIconsByStyle(style)
      } else {
        // 載入所有圖標
        return await this.iconService.fetchBootstrapIcons()
      }
    } catch (error) {
      console.warn('Failed to load Bootstrap Icons:', error.message)
      return { data: {}, meta: { total: 0 } }
    }
  }

  /**
   * 載入 HeroIcons 變體資訊
   * 
   * @private
   * @returns {Promise<Object>} HeroIcons 變體資訊
   */
  async _loadHeroIconVariants() {
    try {
      return await this.iconService.fetchHeroIconVariants()
    } catch (error) {
      console.warn('Failed to load HeroIcons variants:', error.message)
      return { data: {} }
    }
  }

  /**
   * 載入 Bootstrap Icons 變體資訊
   * 
   * @private
   * @returns {Promise<Object>} Bootstrap Icons 變體資訊
   */
  async _loadBootstrapIconVariants() {
    try {
      return await this.iconService.fetchBootstrapIconVariants()
    } catch (error) {
      console.warn('Failed to load Bootstrap Icons variants:', error.message)
      return { data: {} }
    }
  }

  /**
   * 合併 HeroIcons 和 Bootstrap Icons 資料
   * 
   * @private
   * @param {Object} heroIconsData - HeroIcons 資料物件
   * @param {Object} bootstrapIconsData - Bootstrap Icons 資料物件
   * @param {Object} heroVariants - HeroIcons 變體資訊
   * @param {Object} bootstrapVariants - Bootstrap Icons 變體資訊
   * @param {string} currentStyle - 當前選擇的樣式
   * @returns {Object} 合併後的資料物件
   */
  _mergeIconLibraryData(heroIconsData, bootstrapIconsData, heroVariants, bootstrapVariants, currentStyle) {
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
        variantInfo: this._buildHeroIconVariantInfo(icon, heroVariants?.data)
      }))
      result.meta.total += heroIconsData.data.length
    }
    
    // 處理 Bootstrap Icons 資料
    if (bootstrapIconsData?.data && typeof bootstrapIconsData.data === 'object') {
      let bootstrapTotal = 0
      
      Object.entries(bootstrapIconsData.data).forEach(([categoryName, categoryIcons]) => {
        if (Array.isArray(categoryIcons)) {
          result.data.bootstrap[categoryName] = categoryIcons.map(icon => ({
            ...icon,
            type: 'bootstrap',
            source: 'bootstrap-icons',
            currentStyle: currentStyle,
            hasVariants: icon.variants && Object.keys(icon.variants).length > 0,
            variantInfo: this._buildBootstrapIconVariantInfo(icon, bootstrapVariants?.data)
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
   * 
   * @private
   * @param {Object} icon - 圖標物件
   * @param {Object} variantData - 變體資料
   * @returns {Object} 變體資訊物件
   */
  _buildHeroIconVariantInfo(icon, variantData) {
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
   * 
   * @private
   * @param {Object} icon - 圖標物件
   * @param {Object} variantData - 變體資料
   * @returns {Object} 變體資訊物件
   */
  _buildBootstrapIconVariantInfo(icon, variantData) {
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
   * 
   * @private
   * @param {any} rawData - 原始資料
   * @returns {Array} 處理後的資料
   */
  _processEmojiData(rawData) {
    // 如果資料已經是陣列格式，直接返回
    if (Array.isArray(rawData)) {
      return rawData
    }
    
    // 如果是物件，嘗試提取陣列
    if (rawData && typeof rawData === 'object') {
      // 處理新的 API 格式：{ categories: { categoryId: { subgroups: { subgroupId: { emojis: [...] } } } } }
      if (rawData.categories && typeof rawData.categories === 'object') {
        return this._processCategoriesFormat(rawData.categories)
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
   * 
   * @private
   * @param {Object} categories - 分類資料物件
   * @returns {Array} 轉換後的陣列格式
   */
  _processCategoriesFormat(categories) {
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
      const cleanedEmojis = this._filterAndCleanEmojis(allEmojis)
      
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
   * 
   * @private
   * @param {Array} emojis - 原始 emoji 陣列
   * @returns {Array} 清理後的 emoji 陣列
   */
  _filterAndCleanEmojis(emojis) {
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
   * 設定快取
   * 
   * @private
   * @param {string} key - 快取鍵
   * @param {any} data - 要快取的資料
   * @param {number} [duration] - 快取持續時間（毫秒）
   */
  _setCache(key, data, duration = this.defaultCacheDuration) {
    this.cache.set(key, data)
    this.cacheExpiry.set(key, Date.now() + duration)
  }

  /**
   * 取得快取
   * 
   * @private
   * @param {string} key - 快取鍵
   * @returns {any} 快取的資料
   */
  _getCache(key) {
    return this.cache.get(key)
  }

  /**
   * 檢查快取是否有效
   * 
   * @private
   * @param {string} key - 快取鍵
   * @returns {boolean} 快取是否有效且未過期
   */
  _hasValidCache(key) {
    if (!this.cache.has(key) || !this.cacheExpiry.has(key)) {
      return false
    }
    
    // 檢查是否過期
    const expiry = this.cacheExpiry.get(key)
    if (Date.now() > expiry) {
      // 清除過期快取
      this.cache.delete(key)
      this.cacheExpiry.delete(key)
      return false
    }
    
    return true
  }

  /**
   * 驗證資料格式
   * 
   * @private
   * @param {any} data - 要驗證的資料
   * @throws {Error} 資料格式無效時拋出錯誤
   */
  _validateData(data) {
    if (data === null || data === undefined) {
      throw new Error('Invalid data format: data is null or undefined')
    }
    
    if (typeof data !== 'object') {
      throw new Error('Invalid data format: data must be an object or array')
    }
  }

  /**
   * 統一處理錯誤
   * 
   * @private
   * @param {string} message - 錯誤訊息前綴
   * @param {Error} originalError - 原始錯誤
   * @returns {Error} 格式化的錯誤
   */
  _handleError(message, originalError) {
    const error = new Error(`${message}: ${originalError.message}`)
    error.originalError = originalError
    
    // 複製 HTTP 相關屬性
    if (originalError.response) {
      error.status = originalError.response.status
      error.statusText = originalError.response.statusText
      error.data = originalError.response.data
    }
    
    if (originalError.status) {
      error.status = originalError.status
    }
    
    if (originalError.statusText) {
      error.statusText = originalError.statusText
    }
    
    if (originalError.data) {
      error.data = originalError.data
    }
    
    return error
  }
}