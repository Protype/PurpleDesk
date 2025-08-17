import { IconService } from '../../../resources/js/services/IconService.js'

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
   * @returns {Promise<Array>} 合併的圖標資料陣列
   * @throws {Error} 載入失敗時拋出錯誤
   */
  async getIconLibraryData() {
    const cacheKey = 'icon-library-data'
    
    // 檢查快取
    if (this._hasValidCache(cacheKey)) {
      return this._getCache(cacheKey)
    }

    try {
      // 並行載入兩種圖標資料
      const [heroIconsData, bootstrapIconsData] = await Promise.all([
        this._loadHeroIcons(),
        this._loadBootstrapIcons()
      ])
      
      // 合併資料
      const mergedData = this._mergeIconLibraryData(heroIconsData, bootstrapIconsData)
      
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
   * @returns {Promise<Array>} HeroIcons 資料
   */
  async _loadHeroIcons() {
    try {
      // 從前端檔案載入 HeroIcons
      const heroIconsModule = await import('../../../resources/js/utils/heroicons/allHeroicons.js')
      const heroIcons = heroIconsModule.default || heroIconsModule
      return Array.isArray(heroIcons) ? heroIcons : []
    } catch (error) {
      console.warn('Failed to load HeroIcons:', error.message)
      return []
    }
  }

  /**
   * 載入 Bootstrap Icons 資料
   * 
   * @private
   * @returns {Promise<Array>} Bootstrap Icons 資料
   */
  async _loadBootstrapIcons() {
    try {
      // 從前端檔案載入 Bootstrap Icons
      const bootstrapIconsModule = await import('../../../resources/js/utils/icons/index.js')
      const bootstrapIconsIndex = bootstrapIconsModule.default || bootstrapIconsModule
      
      // 載入所有圖標
      await bootstrapIconsIndex.loadAllIcons()
      const icons = bootstrapIconsIndex.getAllLoadedIcons()
      return Array.isArray(icons) ? icons : []
    } catch (error) {
      console.warn('Failed to load Bootstrap Icons:', error.message)
      return []
    }
  }

  /**
   * 合併 HeroIcons 和 Bootstrap Icons 資料
   * 
   * @private
   * @param {Array} heroIcons - HeroIcons 資料
   * @param {Array} bootstrapIcons - Bootstrap Icons 資料
   * @returns {Array} 合併後的資料
   */
  _mergeIconLibraryData(heroIcons, bootstrapIcons) {
    const result = []
    
    // 添加 HeroIcons
    if (Array.isArray(heroIcons)) {
      heroIcons.forEach(icon => {
        result.push({
          ...icon,
          type: 'heroicons',
          source: 'heroicons'
        })
      })
    }
    
    // 添加 Bootstrap Icons
    if (Array.isArray(bootstrapIcons)) {
      bootstrapIcons.forEach(icon => {
        result.push({
          ...icon,
          type: 'bootstrap',
          source: 'bootstrap-icons'
        })
      })
    }
    
    return result
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