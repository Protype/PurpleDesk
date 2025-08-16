/**
 * Heroicons API Manager
 * 管理 Heroicons 的 API 載入、快取、搜尋
 */

import axios from 'axios'

class HeroiconApiManager {
  constructor() {
    this.initialized = false
    this.categories = {}
    this.allIcons = []
    this.searchIndex = new Map()
    this.cache = {
      data: null,
      timestamp: null,
      ttl: 24 * 60 * 60 * 1000 // 24 小時快取
    }
    this.loadingStatus = {
      total: 0,
      loaded: 0,
      loading: false,
      progress: 0
    }
  }

  /**
   * 初始化管理器
   */
  async initialize() {
    if (this.initialized) return

    try {
      // 檢查快取
      const cached = this.getFromCache()
      if (cached) {
        this.processData(cached)
        this.initialized = true
        return
      }

      // 從 API 載入資料
      await this.loadFromApi()
      this.initialized = true
    } catch (error) {
      console.error('❌ HeroiconApiManager: 初始化失敗', error)
      throw error
    }
  }

  /**
   * 從 API 載入資料
   */
  async loadFromApi() {
    this.loadingStatus.loading = true
    
    try {
      const response = await axios.get('/api/config/icon/heroicon')
      const data = response.data
      
      // 儲存到快取
      this.saveToCache(data)
      
      // 處理資料
      this.processData(data)
      
      this.loadingStatus.loading = false
      this.loadingStatus.progress = 100
      
      return data
    } catch (error) {
      this.loadingStatus.loading = false
      console.error('載入 Heroicon API 失敗:', error)
      throw error
    }
  }

  /**
   * 處理資料
   */
  processData(data) {
    this.categories = data.categories || {}
    this.allIcons = []
    this.searchIndex.clear()
    
    // 建立搜尋索引
    Object.entries(this.categories).forEach(([categoryId, category]) => {
      Object.entries(category.subgroups || {}).forEach(([subgroupId, subgroup]) => {
        subgroup.icons.forEach(icon => {
          const iconData = {
            ...icon,
            category: categoryId,
            categoryName: category.name,
            subgroup: subgroupId,
            subgroupName: subgroup.name
          }
          
          this.allIcons.push(iconData)
          
          // 建立搜尋索引
          const searchTerms = [
            icon.name.toLowerCase(),
            icon.icon.toLowerCase(),
            categoryId.toLowerCase(),
            subgroupId.toLowerCase()
          ]
          
          searchTerms.forEach(term => {
            if (!this.searchIndex.has(term)) {
              this.searchIndex.set(term, [])
            }
            this.searchIndex.get(term).push(iconData)
          })
        })
      })
    })
    
    this.loadingStatus.total = this.allIcons.length
    this.loadingStatus.loaded = this.allIcons.length
  }

  /**
   * 搜尋 Heroicons
   */
  async searchHeroicons(query) {
    if (!this.initialized) {
      await this.initialize()
    }
    
    if (!query || query.trim().length === 0) {
      return []
    }
    
    const searchTerm = query.toLowerCase().trim()
    const results = new Set()
    
    // 精確匹配
    if (this.searchIndex.has(searchTerm)) {
      this.searchIndex.get(searchTerm).forEach(icon => results.add(icon))
    }
    
    // 部分匹配
    this.allIcons.forEach(icon => {
      if (icon.name.toLowerCase().includes(searchTerm) || 
          icon.icon.toLowerCase().includes(searchTerm)) {
        results.add(icon)
      }
    })
    
    return Array.from(results)
  }

  /**
   * 根據分類取得 Heroicons
   */
  async getHeroiconsByCategory(categoryId) {
    if (!this.initialized) {
      await this.initialize()
    }
    
    const category = this.categories[categoryId]
    if (!category) {
      return []
    }
    
    const icons = []
    Object.values(category.subgroups || {}).forEach(subgroup => {
      icons.push(...subgroup.icons)
    })
    
    return icons
  }

  /**
   * 取得所有已載入的 Heroicons
   */
  getAllLoadedHeroicons() {
    return this.allIcons
  }


  /**
   * 取得載入狀態
   */
  getHeroiconLoadingStatus() {
    return { ...this.loadingStatus }
  }

  /**
   * 取得分類資訊
   */
  getCategoriesInfo() {
    return Object.entries(this.categories).map(([id, category]) => {
      let count = 0
      Object.values(category.subgroups || {}).forEach(subgroup => {
        count += subgroup.icons.length
      })
      
      return {
        id,
        name: category.name,
        icon: category.icon,
        priority: category.priority,
        count
      }
    })
  }

  /**
   * 取得記憶體統計
   */
  getMemoryStats() {
    return {
      loadedCategories: Object.keys(this.categories).length,
      totalIcons: this.allIcons.length,
      searchIndexSize: this.searchIndex.size,
      estimatedMemoryKB: Math.round((JSON.stringify(this.categories).length + 
                                    JSON.stringify(this.allIcons).length) / 1024)
    }
  }

  /**
   * 從快取取得資料
   */
  getFromCache() {
    const cached = localStorage.getItem('heroicons_cache')
    if (!cached) return null
    
    try {
      const { data, timestamp } = JSON.parse(cached)
      const now = Date.now()
      
      // 檢查快取是否過期
      if (now - timestamp > this.cache.ttl) {
        localStorage.removeItem('heroicons_cache')
        return null
      }
      
      return data
    } catch (error) {
      console.error('讀取 Heroicons 快取失敗:', error)
      localStorage.removeItem('heroicons_cache')
      return null
    }
  }

  /**
   * 儲存到快取
   */
  saveToCache(data) {
    try {
      const cacheData = {
        data,
        timestamp: Date.now()
      }
      localStorage.setItem('heroicons_cache', JSON.stringify(cacheData))
    } catch (error) {
      console.error('儲存 Heroicons 快取失敗:', error)
    }
  }

  /**
   * 清除快取
   */
  clearHeroiconCache() {
    localStorage.removeItem('heroicons_cache')
    this.categories = {}
    this.allIcons = []
    this.searchIndex.clear()
    this.initialized = false
  }

  /**
   * 根據優先級載入 Heroicons
   */
  async loadHeroiconsByPriority() {
    // Heroicons 通常一次載入完成，所以這個方法只是為了相容性
    if (!this.initialized) {
      await this.initialize()
    }
  }

  /**
   * 預載入熱門分類
   */
  async preloadHeroiconCategories() {
    // Heroicons 通常一次載入完成，所以這個方法只是為了相容性
    if (!this.initialized) {
      await this.initialize()
    }
  }

  /**
   * 載入所有 Heroicons
   */
  async loadAllHeroicons() {
    if (!this.initialized) {
      await this.initialize()
    }
    return this.allIcons
  }
}

// 創建單例實例
const heroiconApiManager = new HeroiconApiManager()

export default heroiconApiManager