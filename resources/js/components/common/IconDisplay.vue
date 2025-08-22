<template>
  <div
    :class="[
      'inline-flex items-center justify-center rounded-full select-none',
      sizeClasses,
      'overflow-hidden flex-shrink-0',
      'border-[0.8px] border-gray-300/80'
    ]"
    :style="containerStyles"
    :title="title"
  >
    <!-- 圖片顯示 -->
    <img
      v-if="iconData?.type === 'image' && imageUrl"
      :src="imageUrl"
      :alt="title"
      class="w-full h-full object-cover"
      @error="onImageError"
    />
    
    <!-- 文字顯示 -->
    <span
      v-else-if="iconData?.type === 'text'"
      :class="textSizeClasses"
      :style="textStyles"
      class="font-semibold leading-none"
    >
      {{ iconData.text }}
    </span>
    
    <!-- Emoji 顯示 -->
    <span
      v-else-if="iconData?.type === 'emoji'"
      :class="textSizeClasses"
      :style="emojiStyles"
      class="leading-none"
    >
      {{ iconData.emoji }}
    </span>
    
    <!-- Hero Icon 顯示 -->
    <component
      v-else-if="(iconData?.type === 'hero_icon' || iconData?.type === 'heroicons') && heroIconComponent"
      :is="heroIconComponent"
      :class="iconSizeClasses"
      :style="iconStyles"
    />
    
    <!-- Bootstrap Icon 顯示 -->
    <i
      v-else-if="iconData?.type === 'bootstrap_icon' || iconData?.type === 'bootstrap-icons'"
      :class="[
        iconData.icon,
        iconSizeClasses
      ]"
      :style="iconStyles"
    ></i>
    
    <!-- 預設圖標（當沒有有效數據時） -->
    <i
      v-else
      :class="['bi-person-fill', iconSizeClasses]"
      style="color: #9ca3af;"
    ></i>
  </div>
</template>

<script>
import { computed, ref, onMounted, watch } from 'vue'
import { getIconDisplayConfig } from '@/config/iconDisplayConfig.js'

export default {
  name: 'IconDisplay',
  props: {
    /**
     * 圖標數據物件
     * {
     *   type: 'image' | 'text' | 'emoji' | 'hero_icon' | 'bootstrap_icon',
     *   // 依據類型有不同的屬性
     * }
     */
    iconData: {
      type: Object,
      default: null
    },
    
    /**
     * 圖標大小
     */
    size: {
      type: String,
      default: 'md',
      validator: value => ['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4', '5', '6', '8', '10', '12'].includes(value)
    },
    
    /**
     * 顯示標題（hover 提示）
     */
    title: {
      type: String,
      default: ''
    },
    
    /**
     * 強制設定背景顏色
     */
    backgroundColor: {
      type: String,
      default: null
    },
    
  },
  
  setup(props) {
    const imageError = ref(false)
    const heroIconComponent = ref(null)
    
    // 大小相關的 CSS 類別
    const sizeClasses = computed(() => {
      const sizeMap = {
        'xs': 'w-6 h-6',
        'sm': 'w-8 h-8',
        'md': 'w-10 h-10',
        'lg': 'w-12 h-12',
        'xl': 'w-16 h-16',
        '2xl': 'w-20 h-20',
        '3xl': 'w-24 h-24',
        '4': 'w-4 h-4',
        '5': 'w-5 h-5',
        '6': 'w-6 h-6',
        '8': 'w-8 h-8',
        '10': 'w-10 h-10',
        '12': 'w-12 h-12'
      }
      return sizeMap[props.size] || sizeMap.md
    })
    
    // 文字大小類別
    const textSizeClasses = computed(() => {
      const textSizeMap = {
        'xs': 'text-xs',
        'sm': 'text-sm', 
        'md': 'text-base',
        'lg': 'text-lg',
        'xl': 'text-xl',
        '2xl': 'text-2xl',
        '3xl': 'text-3xl',
        '4': 'text-xs',
        '5': 'text-xs',
        '6': 'text-xs',
        '8': 'text-sm',
        '10': 'text-base',
        '12': 'text-lg'
      }
      return textSizeMap[props.size] || textSizeMap.md
    })
    
    // 圖標大小類別
    const iconSizeClasses = computed(() => {
      const iconSizeMap = {
        'xs': 'text-xs',
        'sm': 'text-sm',
        'md': 'text-base', 
        'lg': 'text-lg',
        'xl': 'text-xl',
        '2xl': 'text-2xl',
        '3xl': 'text-3xl',
        '4': 'text-xs',
        '5': 'text-xs',
        '6': 'text-xs',
        '8': 'text-sm',
        '10': 'text-base',
        '12': 'text-lg'
      }
      return iconSizeMap[props.size] || iconSizeMap.md
    })
    
    // 容器樣式
    const containerStyles = computed(() => {
      const styles = {}
      
      // 背景顏色（優先級：props > iconData）
      const bgColor = props.backgroundColor || props.iconData?.backgroundColor
      if (bgColor) {
        styles.backgroundColor = bgColor
      } else {
        // 預設淡灰色背景
        styles.backgroundColor = '#f3f4f6'
      }
      
      return styles
    })
    
    // 文字樣式
    const textStyles = computed(() => {
      const styles = {}

      if (props.iconData?.textColor) {
        styles.color = props.iconData.textColor
      }
      
      // 應用文字配置
      if (props.iconData?.type === 'text' && props.iconData?.text) {
        const config = getIconDisplayConfig(props.size, 'text', props.iconData.text)
        
        if (config.fontSize) {
          styles.fontSize = config.fontSize
        }
        if (config.marginTop) {
          styles.marginTop = config.marginTop
        }
      }
      
      return styles
    })
    
    // 圖標樣式
    const iconStyles = computed(() => {
      const styles = {}
      if (props.iconData?.iconColor) {
        styles.color = props.iconData.iconColor
      }
      
      // 應用圖標配置
      if (props.iconData?.type === 'hero_icon' || props.iconData?.type === 'heroicons') {
        const config = getIconDisplayConfig(props.size, 'hero_icon')
        if (config.size) {
          styles.width = config.size
          styles.height = config.size
        }
      } else if (props.iconData?.type === 'bootstrap_icon' || props.iconData?.type === 'bootstrap-icons') {
        const config = getIconDisplayConfig(props.size, 'bootstrap_icon')
        if (config.size) {
          styles.fontSize = config.size
        }
      }
      
      return styles
    })
    
    // Emoji 樣式
    const emojiStyles = computed(() => {
      const styles = {}
      
      // 應用 Emoji 配置
      if (props.iconData?.type === 'emoji') {
        const config = getIconDisplayConfig(props.size, 'emoji')
        
        if (config.fontSize) {
          styles.fontSize = config.fontSize
        }
        if (config.marginTop) {
          styles.marginTop = config.marginTop
        }
      }
      
      return styles
    })
    
    // 圖片 URL
    const imageUrl = computed(() => {
      if (imageError.value) return null
      
      if (props.iconData?.type === 'image') {
        if (props.iconData.path) {
          // 本地圖片路徑
          return `/storage/${props.iconData.path}`
        }
        if (props.iconData.url) {
          // 外部圖片 URL
          return props.iconData.url
        }
      }
      
      return null
    })
    
    // 處理圖片載入失敗
    const onImageError = () => {
      imageError.value = true
    }
    
    // 載入 Hero Icon (使用專用 loader)
    const loadHeroIcon = async () => {
      const isHeroIcon = props.iconData?.type === 'hero_icon' || props.iconData?.type === 'heroicons'
      const iconName = props.iconData?.icon
      
      if (!isHeroIcon || !iconName) {
        heroIconComponent.value = null
        return
      }
      
      try {
        const variant = props.iconData.variant === 'solid' ? 'solid' : 'outline'
        
        // 使用專用的 heroicons loader
        const { loadHeroicon } = await import('@/utils/heroicons/heroiconsLoader.js')
        const component = await loadHeroicon(iconName, variant)
        
        heroIconComponent.value = component
      } catch (error) {
        console.error('Failed to load hero icon:', iconName, error)
        heroIconComponent.value = null
      }
    }
    
    // 監聽 iconData 變化，重新載入 hero icon
    watch(() => props.iconData, () => {
      loadHeroIcon()
    }, { immediate: true })
    
    onMounted(() => {
      loadHeroIcon()
    })
    
    return {
      imageError,
      heroIconComponent,
      sizeClasses,
      textSizeClasses,
      iconSizeClasses,
      containerStyles,
      textStyles,
      iconStyles,
      emojiStyles,
      imageUrl,
      onImageError,
    }
  }
}
</script>

<style scoped>
/* 確保頭像容器保持正圓形 */
.rounded-full {
  aspect-ratio: 1 / 1;
}
</style>