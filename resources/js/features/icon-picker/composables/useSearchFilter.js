import { ref, computed, watch } from 'vue'

/**
 * 搜尋過濾 Composable
 * 
 * 提供通用的搜尋過濾功能，支援自訂搜尋邏輯和防抖
 * 適用於 emoji、圖標等分類資料的搜尋過濾
 */
export function useSearchFilter(data, options = {}) {
  const {
    // 自訂項目陣列的 key 名稱（預設為 'emojis'）
    itemsKey = 'emojis',
    
    // 自訂搜尋函數
    searchFunction = null,
    
    // 防抖延遲時間（毫秒）
    debounce = 0,
    
    // 初始搜尋查詢
    initialQuery = ''
  } = options

  // 搜尋狀態
  const searchQuery = ref(initialQuery)
  const debouncedQuery = ref(initialQuery)
  
  // 防抖處理
  let debounceTimer = null
  
  if (debounce > 0) {
    watch(searchQuery, (newQuery) => {
      if (debounceTimer) {
        clearTimeout(debounceTimer)
      }
      
      debounceTimer = setTimeout(() => {
        debouncedQuery.value = newQuery
      }, debounce)
    })
  } else {
    // 沒有防抖時，直接同步
    watch(searchQuery, (newQuery) => {
      debouncedQuery.value = newQuery
    })
  }

  // 預設搜尋函數
  const defaultSearchFunction = (item, query) => {
    const lowerQuery = query.toLowerCase()
    
    // 搜尋名稱
    if (item.name && item.name.toLowerCase().includes(lowerQuery)) {
      return true
    }
    
    // 搜尋關鍵字
    if (item.keywords && Array.isArray(item.keywords)) {
      if (item.keywords.some(keyword => 
        keyword.toLowerCase().includes(lowerQuery)
      )) {
        return true
      }
    }
    
    // 搜尋 emoji 本身
    if (item.emoji && item.emoji.includes(query)) {
      return true
    }
    
    // 搜尋 component 名稱（用於圖標）
    if (item.component && item.component.toLowerCase().includes(lowerQuery)) {
      return true
    }
    
    // 搜尋 class 名稱（用於圖標）
    if (item.class && item.class.toLowerCase().includes(lowerQuery)) {
      return true
    }
    
    // 搜尋 tags（用於圖標）
    if (item.tags && Array.isArray(item.tags)) {
      if (item.tags.some(tag => 
        tag.toLowerCase().includes(lowerQuery)
      )) {
        return true
      }
    }
    
    return false
  }

  // 使用的搜尋函數
  const activeSearchFunction = searchFunction || defaultSearchFunction

  // 過濾後的資料
  const filteredData = computed(() => {
    const query = debouncedQuery.value.trim()
    const originalData = data.value || []
    
    // 先處理資料結構，確保每個分類都有有效的項目陣列
    const normalizedData = originalData.map(category => {
      // 確保分類有有效的項目陣列
      if (!category[itemsKey] || !Array.isArray(category[itemsKey])) {
        return {
          ...category,
          [itemsKey]: []
        }
      }
      return category
    })
    
    // 如果沒有搜尋查詢，返回標準化後的資料
    if (!query) {
      return normalizedData
    }
    
    // 過濾資料
    return normalizedData.map(category => {
      // 過濾該分類下的項目
      const filteredItems = category[itemsKey].filter(item => {
        try {
          return activeSearchFunction(item, query)
        } catch (error) {
          console.warn('Search function error:', error)
          return false
        }
      })
      
      return {
        ...category,
        [itemsKey]: filteredItems
      }
    }).filter(category => {
      // 只保留有結果的分類
      return category[itemsKey] && category[itemsKey].length > 0
    })
  })

  // 搜尋統計
  const searchStats = computed(() => {
    const originalData = data.value || []
    const filtered = filteredData.value || []
    
    // 計算原始資料統計
    const totalCategories = originalData.length
    const totalItems = originalData.reduce((total, category) => {
      const items = category[itemsKey]
      return total + (Array.isArray(items) ? items.length : 0)
    }, 0)
    
    // 計算過濾後統計
    const filteredCategories = filtered.length
    const filteredItems = filtered.reduce((total, category) => {
      const items = category[itemsKey]
      return total + (Array.isArray(items) ? items.length : 0)
    }, 0)
    
    return {
      totalItems,
      totalCategories,
      filteredItems,
      filteredCategories,
      hasResults: filteredItems > 0,
      isFiltered: debouncedQuery.value.trim() !== ''
    }
  })

  // 清除搜尋
  const clearSearch = () => {
    searchQuery.value = ''
    
    // 如果有防抖，立即清除防抖查詢
    if (debounceTimer) {
      clearTimeout(debounceTimer)
    }
    debouncedQuery.value = ''
  }

  // 設定搜尋查詢
  const setSearchQuery = (query) => {
    searchQuery.value = query || ''
  }

  // 檢查是否有搜尋結果
  const hasSearchResults = computed(() => {
    return searchStats.value.hasResults
  })

  // 檢查是否正在搜尋
  const isSearching = computed(() => {
    return searchStats.value.isFiltered
  })

  return {
    // 狀態
    searchQuery,
    filteredData,
    searchStats,
    hasSearchResults,
    isSearching,
    
    // 方法
    clearSearch,
    setSearchQuery
  }
}