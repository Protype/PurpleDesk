// Bootstrap Icons 漸進式載入管理系統
// 提供分類載入、搜尋索引、快取機制

// 載入優先級定義
export const loadingPriority = {
  immediate: ['general'],        // 立即載入
  high: ['ui', 'communications'], // 高優先級
  medium: ['files', 'media', 'people'], // 中優先級  
  low: ['alphanumeric', 'others'] // 低優先級
}

// 分類資訊映射
export const categoryMap = {
  general: { name: '通用圖標', description: '最常用的基礎圖標', priority: 'immediate' },
  ui: { name: 'UI 介面', description: '使用者介面元件與控制項', priority: 'high' },
  communications: { name: '通訊溝通', description: '郵件、電話、聊天等通訊圖標', priority: 'high' },
  files: { name: '檔案文件', description: '檔案、資料夾、文件類型相關圖標', priority: 'medium' },
  media: { name: '媒體播放', description: '播放、音量、錄製等媒體控制圖標', priority: 'medium' },
  people: { name: '人物相關', description: '人員、群組、身體部位相關圖標', priority: 'medium' },
  alphanumeric: { name: '數字字母', description: '數字、字母、文字排版相關圖標', priority: 'low' },
  others: { name: '其他圖標', description: '商業、地理、工具、天氣等其他圖標', priority: 'low' }
}

// 圖標快取
const iconCache = new Map()
const loadingPromises = new Map()
const searchIndex = new Map()

// 動態載入分類
async function loadCategory(categoryId) {
  if (iconCache.has(categoryId)) {
    return iconCache.get(categoryId)
  }
  
  if (loadingPromises.has(categoryId)) {
    return loadingPromises.get(categoryId)
  }
  
  const loadPromise = (async () => {
    try {
      let icons
      
      switch (categoryId) {
        case 'general':
          const generalModule = await import('./bs-general.js')
          icons = generalModule.generalIcons
          break
        case 'ui':
          const uiModule = await import('./bs-ui.js')
          icons = uiModule.uiIcons
          break
        case 'communications':
          const commModule = await import('./bs-communications.js')
          icons = commModule.communicationsIcons
          break
        case 'files':
          const filesModule = await import('./bs-files.js')
          icons = filesModule.filesIcons
          break
        case 'media':
          const mediaModule = await import('./bs-media.js')
          icons = mediaModule.mediaIcons
          break
        case 'people':
          const peopleModule = await import('./bs-people.js')
          icons = peopleModule.peopleIcons
          break
        case 'alphanumeric':
          const alphaModule = await import('./bs-alphanumeric.js')
          icons = alphaModule.alphanumericIcons
          break
        case 'others':
          const othersModule = await import('./bs-others.js')
          icons = othersModule.othersIcons
          break
        default:
          throw new Error(`Unknown category: ${categoryId}`)
      }
      
      // 確保 icons 是陣列
      if (!Array.isArray(icons)) {
        console.error(`Category ${categoryId} returned:`, icons)
        throw new Error(`Category ${categoryId} did not return an array`)
      }
      
      iconCache.set(categoryId, icons)
      
      // 建立搜尋索引
      icons.forEach(icon => {
        const searchKey = icon.name.toLowerCase()
        if (!searchIndex.has(searchKey)) {
          searchIndex.set(searchKey, [])
        }
        searchIndex.get(searchKey).push({ ...icon, category: categoryId })
        
        // 建立關鍵字索引
        const keywords = icon.name.toLowerCase().split(/\s+/)
        keywords.forEach(keyword => {
          if (!searchIndex.has(keyword)) {
            searchIndex.set(keyword, [])
          }
          searchIndex.get(keyword).push({ ...icon, category: categoryId })
        })
      })
      
      loadingPromises.delete(categoryId)
      return icons
    } catch (error) {
      loadingPromises.delete(categoryId)
      console.error(`Failed to load category ${categoryId}:`, error)
      throw error
    }
  })()
  
  loadingPromises.set(categoryId, loadPromise)
  return loadPromise
}

// 載入所有圖標（按優先級順序）
export async function loadAllIcons() {
  const allCategories = Object.keys(categoryMap)
  
  // 按優先級順序載入
  for (const priority of ['immediate', 'high', 'medium', 'low']) {
    const categories = allCategories.filter(cat => categoryMap[cat].priority === priority)
    
    // 並行載入同優先級的分類
    const loadPromises = categories.map(cat => loadCategory(cat))
    await Promise.all(loadPromises)
    
    // 在載入高優先級後給瀏覽器一些時間處理
    if (priority === 'immediate' || priority === 'high') {
      await new Promise(resolve => setTimeout(resolve, 50))
    }
  }
}

// 漸進式載入（按需載入）
export async function loadIconsByPriority() {
  // 立即載入最重要的分類
  await Promise.all(loadingPriority.immediate.map(cat => loadCategory(cat)))
  
  // 延遲載入其他分類
  requestIdleCallback(async () => {
    await Promise.all(loadingPriority.high.map(cat => loadCategory(cat)))
    
    requestIdleCallback(async () => {
      await Promise.all(loadingPriority.medium.map(cat => loadCategory(cat)))
      
      requestIdleCallback(async () => {
        await Promise.all(loadingPriority.low.map(cat => loadCategory(cat)))
      })
    })
  })
}

// 取得分類圖標
export async function getIconsByCategory(categoryId) {
  return await loadCategory(categoryId)
}

// 取得所有已載入的圖標
export function getAllLoadedIcons() {
  const allIcons = []
  for (const [categoryId, icons] of iconCache) {
    allIcons.push(...icons.map(icon => ({ ...icon, category: categoryId })))
  }
  return allIcons
}

// 搜尋圖標
export async function searchIcons(query) {
  if (!query || query.trim().length === 0) {
    return []
  }
  
  // 確保所有分類都已載入以進行搜尋
  if (iconCache.size < Object.keys(categoryMap).length) {
    await loadAllIcons()
  }
  
  const searchTerm = query.toLowerCase().trim()
  const results = new Set()
  
  // 精確匹配
  if (searchIndex.has(searchTerm)) {
    searchIndex.get(searchTerm).forEach(icon => results.add(JSON.stringify(icon)))
  }
  
  // 部分匹配
  for (const [key, icons] of searchIndex) {
    if (key.includes(searchTerm)) {
      icons.forEach(icon => results.add(JSON.stringify(icon)))
    }
  }
  
  return Array.from(results).map(json => JSON.parse(json))
}

// 檢查分類是否已載入
export function isCategoryLoaded(categoryId) {
  return iconCache.has(categoryId)
}

// 取得載入狀態
export function getLoadingStatus() {
  const totalCategories = Object.keys(categoryMap).length
  const loadedCategories = iconCache.size
  const loadingCategories = loadingPromises.size
  
  return {
    total: totalCategories,
    loaded: loadedCategories,
    loading: loadingCategories,
    progress: Math.round((loadedCategories / totalCategories) * 100)
  }
}

// 預載入熱門分類
export async function preloadCategories() {
  const popular = ['general', 'ui', 'communications']
  
  try {
    const results = await Promise.allSettled(popular.map(cat => loadCategory(cat)))
    
    // 檢查哪些分類載入失敗
    results.forEach((result, index) => {
      if (result.status === 'rejected') {
        console.error(`Failed to preload category ${popular[index]}:`, result.reason)
      }
    })
    
    // 只返回成功載入的結果
    return results
      .filter(r => r.status === 'fulfilled')
      .map(r => r.value)
  } catch (error) {
    console.error('Failed to preload popular categories:', error)
    return []
  }
}

// 清除快取
export function clearCache() {
  iconCache.clear()
  loadingPromises.clear()
  searchIndex.clear()
}

// 取得記憶體使用統計
export function getMemoryStats() {
  let totalIcons = 0
  for (const icons of iconCache.values()) {
    totalIcons += icons.length
  }
  
  return {
    loadedCategories: iconCache.size,
    totalIcons,
    searchIndexSize: searchIndex.size,
    estimatedMemoryKB: Math.round((totalIcons * 100 + searchIndex.size * 50) / 1024)
  }
}

// 自動初始化
let autoInitialized = false

export function autoInitialize() {
  if (autoInitialized) return
  autoInitialized = true
  
  // 當 DOM 準備好時開始漸進式載入
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadIconsByPriority)
  } else {
    loadIconsByPriority()
  }
}

// 如果在瀏覽器環境中，自動初始化
if (typeof window !== 'undefined') {
  autoInitialize()
}

// 預設匯出主要功能
export default {
  loadAllIcons,
  loadIconsByPriority,
  getIconsByCategory,
  getAllLoadedIcons,
  searchIcons,
  isCategoryLoaded,
  getLoadingStatus,
  preloadCategories,
  clearCache,
  getMemoryStats,
  categoryMap,
  loadingPriority
}