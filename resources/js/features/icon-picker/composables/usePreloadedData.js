import { ref, computed, provide, inject } from 'vue'
import { useIconService } from './useIconService.js'
import { useAsyncState } from './useAsyncState.js'

/**
 * 預載入資料管理 Composable
 * 
 * 在 IconPicker 層級預載入所有資料，避免子元件重複載入
 * 使用 provide/inject 模式將資料共享給子元件
 */

// Injection keys
export const PRELOADED_EMOJI_DATA_KEY = Symbol('preloaded-emoji-data')
export const PRELOADED_ICON_DATA_KEY = Symbol('preloaded-icon-data')

/**
 * 提供預載入資料（在 IconPicker 中使用）
 */
export function usePreloadedDataProvider() {
  const iconService = useIconService()
  
  // Emoji 資料預載入
  const emojiState = useAsyncState({
    immediate: true,
    asyncFn: iconService.loadEmojiData,
    onError: (error) => {
      console.error('Failed to preload emoji data:', error)
      return error
    }
  })
  
  // 圖標庫資料預載入
  const iconState = useAsyncState({
    immediate: true,
    asyncFn: () => iconService.loadIconLibraryData(),
    onError: (error) => {
      console.error('Failed to preload icon library data:', error)
      return error
    }
  })
  
  // 整體載入狀態
  const isLoading = computed(() => {
    return emojiState.loading.value || iconState.loading.value
  })
  
  // 整體錯誤狀態
  const hasError = computed(() => {
    return !!emojiState.error.value || !!iconState.error.value
  })
  
  const errorMessage = computed(() => {
    const errors = []
    if (emojiState.error.value) errors.push(`Emoji: ${emojiState.error.value.message}`)
    if (iconState.error.value) errors.push(`Icons: ${iconState.error.value.message}`)
    return errors.join(', ') || null
  })
  
  // Emoji 資料提供者
  const emojiDataProvider = {
    data: emojiState.data,
    loading: emojiState.loading,
    error: emojiState.error,
    reload: emojiState.execute
  }
  
  // 圖標資料提供者
  const iconDataProvider = {
    data: iconState.data,
    loading: iconState.loading,
    error: iconState.error,
    reload: iconState.execute
  }
  
  // 提供資料給子元件
  provide(PRELOADED_EMOJI_DATA_KEY, emojiDataProvider)
  provide(PRELOADED_ICON_DATA_KEY, iconDataProvider)
  
  return {
    // 整體狀態
    isLoading,
    hasError,
    errorMessage,
    
    // 個別資料狀態
    emojiData: emojiState.data,
    iconData: iconState.data,
    
    // 載入狀態
    emojiLoading: emojiState.loading,
    iconLoading: iconState.loading,
    
    // 錯誤狀態
    emojiError: emojiState.error,
    iconError: iconState.error,
    
    // 重載方法
    reloadEmoji: emojiState.execute,
    reloadIcons: iconState.execute,
    
    // 服務層
    iconService
  }
}

/**
 * 取得預載入的 Emoji 資料（在子元件中使用）
 */
export function usePreloadedEmojiData() {
  const emojiProvider = inject(PRELOADED_EMOJI_DATA_KEY)
  
  if (!emojiProvider) {
    throw new Error('usePreloadedEmojiData must be used within a component that provides preloaded emoji data')
  }
  
  return emojiProvider
}

/**
 * 取得預載入的圖標資料（在子元件中使用）
 */
export function usePreloadedIconData() {
  const iconProvider = inject(PRELOADED_ICON_DATA_KEY)
  
  if (!iconProvider) {
    throw new Error('usePreloadedIconData must be used within a component that provides preloaded icon data')
  }
  
  return iconProvider
}

/**
 * 統一的預載入資料消費者（在子元件中使用）
 */
export function usePreloadedData() {
  return {
    emoji: usePreloadedEmojiData(),
    icon: usePreloadedIconData()
  }
}