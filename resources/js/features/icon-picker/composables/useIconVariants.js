import { ref, computed, reactive } from 'vue'

/**
 * 圖標變體管理 Composable
 * 
 * 提供統一的變體選擇和管理介面，支援：
 * - HeroIcons: outline/solid 樣式切換
 * - Bootstrap Icons: outline/solid (fill) 樣式切換
 * - Emoji: 膚色變體切換（未來可擴展）
 */
export function useIconVariants() {
  // 當前選擇的變體狀態
  const currentVariants = reactive({
    iconStyle: 'outline',  // 圖標樣式變體
    skinTone: 0           // 膚色變體
  })
  
  // 變體映射快取
  const variantMappings = reactive({
    heroicons: null,
    bootstrap: null
  })
  
  // 載入狀態
  const isLoading = ref(false)
  const error = ref(null)
  
  /**
   * 載入變體映射資訊
   * 
   * @param {string} iconLibrary - 圖標庫類型 ('heroicons' | 'bootstrap')
   * @returns {Promise<Object>}
   */
  const loadVariantMapping = async (iconLibrary) => {
    if (variantMappings[iconLibrary]) {
      return variantMappings[iconLibrary]
    }
    
    isLoading.value = true
    error.value = null
    
    try {
      const response = await fetch(`/api/config/icon/${iconLibrary}/variants`)
      
      if (!response.ok) {
        throw new Error(`Failed to load ${iconLibrary} variants: ${response.statusText}`)
      }
      
      const data = await response.json()
      variantMappings[iconLibrary] = data.data
      
      return data.data
    } catch (err) {
      error.value = err.message
      console.error(`Error loading ${iconLibrary} variants:`, err)
      throw err
    } finally {
      isLoading.value = false
    }
  }
  
  /**
   * 設定圖標樣式變體
   * 
   * @param {string} style - 樣式名稱 ('outline' | 'solid')
   */
  const setIconStyle = (style) => {
    const validStyles = ['outline', 'solid']
    
    if (!validStyles.includes(style)) {
      console.warn(`Invalid icon style: ${style}. Valid styles are: ${validStyles.join(', ')}`)
      return
    }
    
    currentVariants.iconStyle = style
  }
  
  /**
   * 設定膚色變體
   * 
   * @param {number} tone - 膚色編號 (0-5)
   */
  const setSkinTone = (tone) => {
    const validTones = [0, 1, 2, 3, 4, 5]
    
    if (!validTones.includes(tone)) {
      console.warn(`Invalid skin tone: ${tone}. Valid tones are: ${validTones.join(', ')}`)
      return
    }
    
    currentVariants.skinTone = tone
  }
  
  /**
   * 根據當前樣式過濾圖標
   * 
   * @param {Array} icons - 圖標陣列
   * @param {string} iconLibrary - 圖標庫類型
   * @returns {Array} 過濾後的圖標陣列
   */
  const filterIconsByStyle = (icons, iconLibrary) => {
    if (!icons || !Array.isArray(icons)) {
      return []
    }
    
    const style = currentVariants.iconStyle
    
    if (iconLibrary === 'heroicons') {
      // HeroIcons: 所有圖標都支援兩種樣式
      return icons
    } else if (iconLibrary === 'bootstrap') {
      // Bootstrap Icons: 根據樣式過濾
      return icons.filter(icon => {
        const isFillIcon = icon.class && icon.class.includes('-fill')
        
        if (style === 'outline') {
          return !isFillIcon
        } else if (style === 'solid') {
          if (isFillIcon) {
            return true
          } else {
            // 檢查是否有對應的 -fill 版本
            return !hasFilledVariant(icons, icon.class)
          }
        }
        
        return true
      })
    }
    
    return icons
  }
  
  /**
   * 檢查圖標是否有填充變體
   * 
   * @param {Array} icons - 圖標陣列
   * @param {string} baseClassName - 基礎 class 名稱
   * @returns {boolean}
   */
  const hasFilledVariant = (icons, baseClassName) => {
    const filledClassName = baseClassName + '-fill'
    return icons.some(icon => icon.class === filledClassName)
  }
  
  /**
   * 取得圖標的特定樣式版本
   * 
   * @param {Object} icon - 圖標物件
   * @param {string} iconLibrary - 圖標庫類型
   * @param {string} [style] - 指定樣式，預設使用當前樣式
   * @returns {Object} 轉換後的圖標物件
   */
  const getIconVariant = (icon, iconLibrary, style = null) => {
    const targetStyle = style || currentVariants.iconStyle
    
    if (iconLibrary === 'heroicons') {
      return {
        ...icon,
        currentStyle: targetStyle,
        component: icon.component,
        path: targetStyle === 'solid' ? '@heroicons/vue/solid' : '@heroicons/vue/outline'
      }
    } else if (iconLibrary === 'bootstrap') {
      const baseClassName = icon.class.replace(/-fill$/, '')
      let className = baseClassName
      
      if (targetStyle === 'solid') {
        // 如果原本就是 -fill 圖標或者需要轉換為 solid
        className = icon.class.includes('-fill') ? icon.class : baseClassName + '-fill'
      }
      
      return {
        ...icon,
        currentStyle: targetStyle,
        class: className,
        base: baseClassName
      }
    }
    
    return icon
  }
  
  /**
   * 批量轉換圖標為特定樣式
   * 
   * @param {Array} icons - 圖標陣列
   * @param {string} iconLibrary - 圖標庫類型
   * @param {string} [style] - 指定樣式，預設使用當前樣式
   * @returns {Array} 轉換後的圖標陣列
   */
  const transformIconsToStyle = (icons, iconLibrary, style = null) => {
    if (!icons || !Array.isArray(icons)) {
      return []
    }
    
    return icons.map(icon => getIconVariant(icon, iconLibrary, style))
  }
  
  /**
   * 重置所有變體為預設值
   */
  const resetVariants = () => {
    currentVariants.iconStyle = 'outline'
    currentVariants.skinTone = 0
  }
  
  /**
   * 取得變體選項清單
   * 
   * @param {string} variantType - 變體類型 ('iconStyle' | 'skinTone')
   * @returns {Array} 變體選項陣列
   */
  const getVariantOptions = (variantType) => {
    if (variantType === 'iconStyle') {
      return [
        { value: 'outline', label: 'Outline', description: '線條樣式' },
        { value: 'solid', label: 'Solid', description: '實心樣式' }
      ]
    } else if (variantType === 'skinTone') {
      return [
        { value: 0, label: '預設', emoji: '👋', color: '#FFC83D' },
        { value: 1, label: '淺膚色', emoji: '👋🏻', color: '#F7DECE' },
        { value: 2, label: '中淺膚色', emoji: '👋🏼', color: '#F3D2A2' },
        { value: 3, label: '中膚色', emoji: '👋🏽', color: '#D5AB88' },
        { value: 4, label: '中深膚色', emoji: '👋🏾', color: '#AF7E57' },
        { value: 5, label: '深膚色', emoji: '👋🏿', color: '#7C533E' }
      ]
    }
    
    return []
  }
  
  // 計算屬性
  const currentIconStyle = computed(() => currentVariants.iconStyle)
  const currentSkinTone = computed(() => currentVariants.skinTone)
  
  const iconStyleOptions = computed(() => getVariantOptions('iconStyle'))
  const skinToneOptions = computed(() => getVariantOptions('skinTone'))
  
  const hasError = computed(() => !!error.value)
  const isReady = computed(() => !isLoading.value && !error.value)
  
  return {
    // 狀態
    currentVariants,
    variantMappings,
    isLoading,
    error,
    
    // 計算屬性
    currentIconStyle,
    currentSkinTone,
    iconStyleOptions,
    skinToneOptions,
    hasError,
    isReady,
    
    // 方法
    loadVariantMapping,
    setIconStyle,
    setSkinTone,
    filterIconsByStyle,
    getIconVariant,
    transformIconsToStyle,
    resetVariants,
    getVariantOptions
  }
}

/**
 * 全域圖標變體管理實例
 * 
 * 可在多個元件間共享變體狀態
 */
export const globalIconVariants = useIconVariants()