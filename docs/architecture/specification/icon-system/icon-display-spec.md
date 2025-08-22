# IconDisplay 元件規範

**文件版本**: 1.0  
**建立日期**: 2025-08-22  
**維護者**: 開發團隊

## 概述

IconDisplay 是 PurpleDesk 的核心顯示元件，負責統一渲染所有類型的圖標。支援 5 種圖標類型，具備響應式尺寸調整、動態載入和優化的顯示效果。

## 元件架構

### 主要元件
- **IconDisplay.vue** - 正式版本，整合 iconDisplayConfig
- **TestIconDisplay.vue** - 測試版本，支援動態配置調整

### 配置檔案
- **iconDisplayConfig.js** - 尺寸和顯示配置
- **heroiconsLoader.js** - Hero Icons 動態載入器

## IconData 支援格式

### 完整結構定義
```javascript
{
  type: 'text' | 'emoji' | 'hero_icon' | 'bootstrap_icon' | 'image',
  
  // type: 'text' 專用欄位
  text: string,           // 1-2 個字符
  textColor: string,      // 文字顏色
  
  // type: 'emoji' 專用欄位
  emoji: string,          // Unicode Emoji 字符
  
  // type: 'hero_icon' 專用欄位
  icon: string,           // 圖標名稱（如 'UserIcon'）
  style: 'outline' | 'solid',
  iconColor: string,      // 圖標顏色
  
  // type: 'bootstrap_icon' 專用欄位
  icon: string,           // 圖標名稱（如 'person'）
  style: 'outline' | 'fill',
  iconColor: string,      // 圖標顏色
  
  // type: 'image' 專用欄位
  path: string,           // 本地圖片路徑（相對於 /storage/）
  url: string,            // 外部圖片 URL
  
  // 通用欄位
  backgroundColor: string // 背景顏色
}
```

## Props 定義

### IconDisplay 元件
```javascript
const props = defineProps({
  iconData: {
    type: Object,
    default: null,
    description: '圖標數據物件，必須符合 IconData 格式'
  },
  size: {
    type: String,
    default: 'md',
    validator: value => ['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4', '5', '6', '8', '10', '12'].includes(value),
    description: '圖標顯示尺寸'
  },
  title: {
    type: String,
    default: '',
    description: 'hover 提示文字'
  },
  backgroundColor: {
    type: String,
    default: null,
    description: '強制覆蓋背景顏色（優先於 iconData.backgroundColor）'
  }
})
```

### TestIconDisplay 元件
```javascript
// 繼承 IconDisplay 所有 props，額外增加：
testConfig: {
  type: Object,
  default: null,
  description: '測試用動態配置，僅在開發工具中使用'
}
```

## 尺寸系統

IconDisplay 元件使用三層尺寸定義系統，提供精確的尺寸控制：

### 1. 容器尺寸 (sizeClasses)
定義圖標容器的外框尺寸，使用 Tailwind CSS 類別：

```javascript
const sizeMap = {
  // Tailwind 數字尺寸
  '4': 'w-4 h-4',     // 16px × 16px
  '5': 'w-5 h-5',     // 20px × 20px
  '6': 'w-6 h-6',     // 24px × 24px
  '8': 'w-8 h-8',     // 32px × 32px
  '10': 'w-10 h-10',  // 40px × 40px
  '12': 'w-12 h-12',  // 48px × 48px
  
  // Tailwind 語義尺寸
  'xs': 'w-6 h-6',    // 24px × 24px
  'sm': 'w-8 h-8',    // 32px × 32px
  'md': 'w-10 h-10',  // 40px × 40px (預設基準)
  'lg': 'w-12 h-12',  // 48px × 48px
  'xl': 'w-16 h-16',  // 64px × 64px
  '2xl': 'w-20 h-20', // 80px × 80px
  '3xl': 'w-24 h-24'  // 96px × 96px
}
```

### 2. 文字尺寸類別 (textSizeClasses)
用於 text 和 emoji 類型的 Tailwind 文字尺寸：

```javascript
const textSizeMap = {
  'xs': 'text-xs',    // 12px
  'sm': 'text-sm',    // 14px
  'md': 'text-base',  // 16px
  'lg': 'text-lg',    // 18px
  'xl': 'text-xl',    // 20px
  '2xl': 'text-2xl',  // 24px
  '3xl': 'text-3xl',  // 30px
  '4': 'text-xs',     // 12px
  '5': 'text-xs',     // 12px
  '6': 'text-xs',     // 12px
  '8': 'text-sm',     // 14px
  '10': 'text-base',  // 16px
  '12': 'text-lg'     // 18px
}
```

### 3. 圖標尺寸類別 (iconSizeClasses)
用於 bootstrap_icon 類型的基礎 CSS 類別：

```javascript
const iconSizeMap = {
  'xs': 'text-xs',    // 12px (基礎參照)
  'sm': 'text-sm',    // 14px
  'md': 'text-base',  // 16px
  'lg': 'text-lg',    // 18px
  'xl': 'text-xl',    // 20px
  '2xl': 'text-2xl',  // 24px
  '3xl': 'text-3xl',  // 30px
  '4': 'text-xs',     // 12px
  '5': 'text-xs',     // 12px
  '6': 'text-xs',     // 12px
  '8': 'text-sm',     // 14px
  '10': 'text-base',  // 16px
  '12': 'text-lg'     // 18px
}
```

### 4. 動態尺寸配置 (iconDisplayConfig.js)
精確的 rem 值配置，透過內聯樣式覆蓋 CSS 類別：

## 三層尺寸系統運作原理

### 尺寸覆蓋優先級
```text
1. iconDisplayConfig.js (最高優先級)
   ↓ 透過內聯 style 覆蓋
2. textSizeClasses / iconSizeClasses (中等優先級)
   ↓ 透過 Tailwind CSS 類別
3. 瀏覽器預設 (最低優先級)
```

### 實際運作範例
以 `size="md"` 的 CJK 文字為例：

```javascript
// 1. 容器尺寸：w-10 h-10 (40px × 40px)
sizeClasses = 'w-10 h-10'

// 2. 基礎文字類別：text-base (16px)
textSizeClasses = 'text-base'

// 3. 精確配置覆蓋：0.9rem (約 14.4px)
textStyles = { fontSize: '0.9rem', marginTop: '0.05em' }

// 最終渲染：14.4px 文字在 40px 容器中
```

## 詳細尺寸參照表

### 完整尺寸對照 (容器 → 內容)

| 尺寸 | 容器 CSS | CJK文字 (rem) | 拉丁文字 (rem) | Emoji (rem) | Hero Icons (rem) | Bootstrap Icons (rem) |
|------|----------|---------------|----------------|-------------|------------------|-------------------|
| 4    | w-4 h-4  | 0.3           | 0.4            | 0.4         | 0.5              | 0.5               |
| 5    | w-5 h-5  | 0.4           | 0.5            | 0.5         | 0.6              | 0.6               |
| 6    | w-6 h-6  | 0.5           | 0.6            | 0.8         | 0.8              | 0.8               |
| xs   | w-6 h-6  | 0.5           | 0.6            | 0.8         | 0.8              | 0.8               |
| 8    | w-8 h-8  | 0.7           | 0.9            | 1.1         | 1.1              | 1.0               |
| sm   | w-8 h-8  | 0.7           | 0.9            | 1.1         | 1.1              | 1.0               |
| 10   | w-10 h-10| 0.9           | 1.1            | 1.3         | 1.4              | 1.3               |
| md   | w-10 h-10| 0.9           | 1.1            | 1.3         | 1.4              | 1.3               |
| 12   | w-12 h-12| 1.1           | 1.4            | 1.7         | 1.8              | 1.8               |
| lg   | w-12 h-12| 1.1           | 1.4            | 1.7         | 1.8              | 1.8               |
| xl   | w-16 h-16| 1.5           | 2.0            | 2.4         | 2.3              | 2.2               |
| 2xl  | w-20 h-20| 1.9           | 2.4            | 2.9         | 2.8              | 2.6               |
| 3xl  | w-24 h-24| 2.2           | 3.0            | 3.6         | 3.6              | 3.4               |

*註：所有數值來源於 `resources/js/config/iconDisplayConfig.js` 和 `resources/js/components/common/IconDisplay.vue`*

## 內容尺寸配置

#### CJK 文字 (中日韓)
```javascript
textCJKBySize: {
  '4': { fontSize: '0.3rem' },
  '5': { fontSize: '0.4rem' },
  '6': { fontSize: '0.5rem' },
  '8': { fontSize: '0.7rem' },
  '10': { fontSize: '0.9rem' },
  '12': { fontSize: '1.1rem' },
  'xs': { fontSize: '0.5rem' },
  'sm': { fontSize: '0.7rem' },
  'md': { fontSize: '0.9rem' },  // 基準尺寸
  'lg': { fontSize: '1.1rem' },
  'xl': { fontSize: '1.5rem' },
  '2xl': { fontSize: '1.9rem' },
  '3xl': { fontSize: '2.2rem' }
}
```

#### 拉丁文字
```javascript
textLatinBySize: {
  '4': { fontSize: '0.4rem' },
  '5': { fontSize: '0.5rem' },
  '6': { fontSize: '0.6rem' },
  '8': { fontSize: '0.9rem' },
  '10': { fontSize: '1.1rem' },
  '12': { fontSize: '1.4rem' },
  'xs': { fontSize: '0.6rem' },
  'sm': { fontSize: '0.9rem' },
  'md': { fontSize: '1.1rem' },  // 基準尺寸
  'lg': { fontSize: '1.4rem' },
  'xl': { fontSize: '2rem' },
  '2xl': { fontSize: '2.4rem' },
  '3xl': { fontSize: '3rem' }
}
```

#### Emoji
```javascript
emojiBySize: {
  '4': { fontSize: '0.4rem' },
  '5': { fontSize: '0.5rem' },
  '6': { fontSize: '0.8rem' },
  '8': { fontSize: '1.1rem' },
  '10': { fontSize: '1.3rem' },
  '12': { fontSize: '1.7rem' },
  'xs': { fontSize: '0.8rem' },
  'sm': { fontSize: '1.1rem' },
  'md': { fontSize: '1.3rem' },  // 基準尺寸
  'lg': { fontSize: '1.7rem' },
  'xl': { fontSize: '2.4rem' },
  '2xl': { fontSize: '2.9rem' },
  '3xl': { fontSize: '3.6rem' }
}
```

#### Hero Icons
```javascript
heroIconBySize: {
  '4': { size: '0.5rem' },
  '5': { size: '0.6rem' },
  '6': { size: '0.8rem' },
  '8': { size: '1.1rem' },
  '10': { size: '1.4rem' },
  '12': { size: '1.8rem' },
  'xs': { size: '0.8rem' },
  'sm': { size: '1.1rem' },
  'md': { size: '1.4rem' },  // 基準尺寸
  'lg': { size: '1.8rem' },
  'xl': { size: '2.3rem' },
  '2xl': { size: '2.8rem' },
  '3xl': { size: '3.6rem' }
}
```

#### Bootstrap Icons
```javascript
iconBySize: {
  '4': { size: '0.5rem' },
  '5': { size: '0.6rem' },
  '6': { size: '0.8rem' },
  '8': { size: '1rem' },
  '10': { size: '1.3rem' },
  '12': { size: '1.8rem' },
  'xs': { size: '0.8rem' },
  'sm': { size: '1rem' },
  'md': { size: '1.3rem' },  // 基準尺寸
  'lg': { size: '1.8rem' },
  'xl': { size: '2.2rem' },
  '2xl': { size: '2.6rem' },
  '3xl': { size: '3.4rem' }
}
```

## 渲染邏輯

### 1. 圖片顯示 (type: 'image')
```vue
<img
  v-if="iconData?.type === 'image' && imageUrl"
  :src="imageUrl"
  :alt="title"
  class="w-full h-full object-cover"
  @error="onImageError"
/>
```

**特性**:
- 自動縮放填滿容器
- 圖片載入失敗處理
- 支援本地路徑和外部 URL

### 2. 文字顯示 (type: 'text')
```vue
<span
  v-else-if="iconData?.type === 'text'"
  :class="textSizeClasses"
  :style="textStyles"
  class="font-semibold leading-none"
>
  {{ iconData.text }}
</span>
```

**特性**:
- 自動 CJK/拉丁文字偵測
- 不同語系的專用尺寸配置
- 最多 2 個字符限制

### 3. Emoji 顯示 (type: 'emoji')
```vue
<span
  v-else-if="iconData?.type === 'emoji'"
  :class="textSizeClasses"
  :style="emojiStyles"
  class="leading-none"
>
  {{ iconData.emoji }}
</span>
```

**特性**:
- 支援複雜 Emoji（含膚色修飾）
- 專用的尺寸配置
- 特殊的 marginTop 調整 (0.1em)

### 4. Hero Icons 顯示 (type: 'hero_icon')
```vue
<component
  v-else-if="iconData?.type === 'hero_icon' && heroIconComponent"
  :is="heroIconComponent"
  :class="iconSizeClasses"
  :style="iconStyles"
/>
```

**特性**:
- 動態元件載入
- 支援 outline/solid 兩種樣式
- 完整的 230 個圖標支援

### 5. Bootstrap Icons 顯示 (type: 'bootstrap_icon')
```vue
<i
  v-else-if="iconData?.type === 'bootstrap_icon'"
  :class="[
    `bi-${iconData.icon}${iconData.style === 'fill' ? '-fill' : ''}`,
    iconSizeClasses
  ]"
  :style="iconStyles"
></i>
```

**特性**:
- CSS 類別動態組合
- 支援 outline/fill 兩種樣式
- 基於 fontSize 的尺寸控制

### 6. 預設圖標 (fallback)
```vue
<i
  v-else
  :class="['bi-person-fill', iconSizeClasses]"
  style="color: #9ca3af;"
></i>
```

**特性**:
- 無效數據時的回退顯示
- 使用 Bootstrap Icons 的 person-fill
- 固定灰色 (#9ca3af)

## 樣式計算邏輯

### 容器樣式 (containerStyles)
```javascript
const containerStyles = computed(() => {
  const styles = {}
  
  // 背景顏色優先級：props.backgroundColor > iconData.backgroundColor > 預設
  const bgColor = props.backgroundColor || props.iconData?.backgroundColor
  if (bgColor) {
    styles.backgroundColor = bgColor
  } else {
    styles.backgroundColor = '#f3f4f6' // 預設淡灰色
  }
  
  return styles
})
```

### 文字樣式 (textStyles)
```javascript
const textStyles = computed(() => {
  const styles = {}
  
  // 文字顏色
  if (props.iconData?.textColor) {
    styles.color = props.iconData.textColor
  }
  
  // 應用配置檔案的尺寸設定
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
```

### 圖標樣式 (iconStyles)
```javascript
const iconStyles = computed(() => {
  const styles = {}
  
  // 圖標顏色
  if (props.iconData?.iconColor) {
    styles.color = props.iconData.iconColor
  }
  
  // Hero Icons: 使用 width/height
  if (props.iconData?.type === 'hero_icon') {
    const config = getIconDisplayConfig(props.size, 'hero_icon')
    if (config.size) {
      styles.width = config.size
      styles.height = config.size
    }
  }
  // Bootstrap Icons: 使用 fontSize  
  else if (props.iconData?.type === 'bootstrap_icon') {
    const config = getIconDisplayConfig(props.size, 'bootstrap_icon')
    if (config.size) {
      styles.fontSize = config.size
    }
  }
  
  return styles
})
```

### Emoji 樣式 (emojiStyles)
```javascript
const emojiStyles = computed(() => {
  const styles = {}
  
  if (props.iconData?.type === 'emoji') {
    const config = getIconDisplayConfig(props.size, 'emoji')
    
    if (config.fontSize) {
      styles.fontSize = config.fontSize
    }
    if (config.marginTop) {
      styles.marginTop = config.marginTop // 固定 0.1em
    }
  }
  
  return styles
})
```

## Hero Icons 動態載入

### 載入機制
```javascript
const loadHeroIcon = async () => {
  const isHeroIcon = props.iconData?.type === 'hero_icon'
  const iconName = props.iconData?.icon
  
  if (!isHeroIcon || !iconName) {
    heroIconComponent.value = null
    return
  }
  
  try {
    const variant = props.iconData.style === 'solid' ? 'solid' : 'outline'
    
    // 使用專用 heroicons loader
    const { loadHeroicon } = await import('@/utils/heroicons/heroiconsLoader.js')
    const component = await loadHeroicon(iconName, variant)
    
    heroIconComponent.value = component
  } catch (error) {
    console.error('Failed to load hero icon:', iconName, error)
    heroIconComponent.value = null
  }
}
```

### 支援的圖標
- **Outline**: 230 個完整圖標
- **Solid**: 230 個完整圖標
- **載入器**: heroiconsLoaders 靜態映射
- **回退機制**: 載入失敗時顯示 null

## 圖片處理

### 路徑解析
```javascript
const imageUrl = computed(() => {
  if (imageError.value) return null
  
  if (props.iconData?.type === 'image') {
    if (props.iconData.path) {
      // 本地圖片：自動加上 /storage/ 前綴
      return `/storage/${props.iconData.path}`
    }
    if (props.iconData.url) {
      // 外部圖片：直接使用 URL
      return props.iconData.url
    }
  }
  
  return null
})
```

### 錯誤處理
```javascript
const onImageError = () => {
  imageError.value = true // 隱藏圖片，顯示預設圖標
}
```

## CSS 樣式設計

### 基礎容器樣式
```css
.inline-flex items-center justify-center rounded-full select-none
.overflow-hidden flex-shrink-0
.border-[0.8px] border-gray-300/80
```

### 響應式設計
```css
/* 確保容器保持正圓形 */
.rounded-full {
  aspect-ratio: 1 / 1;
}
```

### 字型設定
- **文字**: `font-semibold leading-none`
- **Emoji**: `leading-none`
- **圖標**: 繼承容器樣式

## 配置檔案整合

### getIconDisplayConfig 函式
```javascript
import { getIconDisplayConfig } from '@/config/iconDisplayConfig.js'

// 使用範例
const config = getIconDisplayConfig(size, contentType, text)
// 返回: { fontSize?, size?, marginTop? }
```

### CJK 文字偵測
```javascript
import { isCJKText } from '@/config/iconDisplayConfig.js'

const textType = isCJKText(text) ? 'textCJK' : 'textLatin'
```

## 使用場景

### Avatar 頭像顯示
```vue
<IconDisplay
  :iconData="userIconData"
  :size="'md'"
  :title="user.name"
/>
```

### Logo 品牌顯示
```vue
<IconDisplay
  :iconData="organizationIconData"
  :size="'lg'"
  :title="organization.name"
/>
```

### 功能圖標
```vue
<IconDisplay
  :iconData="buttonIconData"
  :size="'6'"
/>
```

### 強制背景顏色
```vue
<IconDisplay
  :iconData="iconData"
  :size="'md'"
  :backgroundColor="'#6366f1'"
/>
```

## 測試和開發工具

### TestIconDisplay 元件
- 繼承所有 IconDisplay 功能
- 支援 `testConfig` 動態配置
- 用於 IconSizeTool 開發工具

### IconSizeTool 頁面
- 視覺化調整所有尺寸配置
- 即時預覽效果
- 匯出配置功能
- 批量同步調整

### 測試範例數據
```javascript
// 由 IconDataHelper::generateTestIconData() 提供
const testData = {
  user_default: { type: 'text', text: '張小', backgroundColor: '#6366f1', textColor: '#ffffff' },
  organization_default: { type: 'hero_icon', icon: 'OfficeBuildingIcon', style: 'outline' },
  team_default: { type: 'bootstrap_icon', icon: 'people', style: 'fill' },
  // ... 更多測試案例
}
```

## 效能優化

### 動態載入策略
- **Hero Icons**: 按需動態載入，避免 bundle 體積過大
- **Bootstrap Icons**: CSS 靜態載入，快速渲染
- **圖片**: 錯誤處理和回退機制

### 記憶體管理
- **元件快取**: Hero Icons 元件在載入後快取
- **配置快取**: iconDisplayConfig 一次載入，多次使用
- **圖片處理**: 載入失敗時自動清理

### 渲染優化
- **Computed 屬性**: 樣式計算使用 computed 避免重複計算
- **條件渲染**: v-if 避免不必要的 DOM 節點
- **CSS 最佳化**: 使用 Tailwind utility classes

## 後端整合

### PHP Helper 類別
```php
// app/Helpers/IconDataHelper.php
class IconDataHelper {
  // 生成預設圖標配置
  public static function generateUserIconDefault($fullName)
  public static function generateOrganizationIconDefault()
  public static function generateTeamIconDefault()
  
  // 顏色驗證和處理
  public static function getAllowedBackgroundColors()
  public static function isAllowedBackgroundColor($color)
  public static function generateRandomColor($lightColor = true)
  
  // 數據解析和編碼
  public static function parseIconData($iconData)
  public static function encodeIconData($iconData)
}
```

## 錯誤處理和回退機制

### 圖標載入失敗
1. **Hero Icons**: 載入失敗時設為 null，不顯示圖標
2. **圖片**: 載入失敗時隱藏 img 標籤，顯示預設圖標
3. **無效數據**: 顯示預設 bi-person-fill 圖標

### 數據驗證
```javascript
// 前端驗證邏輯
const isValidIconData = (iconData) => {
  if (!iconData || typeof iconData !== 'object') return false
  
  const allowedTypes = ['text', 'emoji', 'hero_icon', 'bootstrap_icon', 'image']
  if (!allowedTypes.includes(iconData.type)) return false
  
  // 類型特定驗證
  switch (iconData.type) {
    case 'text':
      return iconData.text && iconData.text.length <= 2
    case 'emoji':
      return !!iconData.emoji
    case 'hero_icon':
    case 'bootstrap_icon':
      return !!(iconData.icon && iconData.style)
    case 'image':
      return !!(iconData.path || iconData.url)
    default:
      return false
  }
}
```

## 無障礙設計

### ARIA 支援
```vue
<div
  role="img"
  :aria-label="title || `${iconData?.type} icon`"
  :title="title"
>
  <!-- 圖標內容 -->
</div>
```

### 鍵盤導航
- **焦點管理**: 可選擇性的 tabindex 設定
- **語義化**: 適當的 role 和 aria-label
- **對比度**: 遵循 WCAG AA 標準

## API 整合

### 數據來源
- **使用者頭像**: User model 的 avatar_data 欄位
- **組織 Logo**: Organization model 的 logo_data 欄位  
- **團隊圖標**: Team model 的 icon_data 欄位

### 數據格式
- **儲存**: JSON 字串格式
- **解析**: IconDataHelper::parseIconData()
- **編碼**: IconDataHelper::encodeIconData()

## 測試要求

### 單元測試
```javascript
describe('IconDisplay', () => {
  it('應該正確渲染文字圖標', () => {
    const iconData = { type: 'text', text: '王', textColor: '#374151' }
    // 測試邏輯...
  })
  
  it('應該正確載入 Hero Icons', async () => {
    const iconData = { type: 'hero_icon', icon: 'UserIcon', style: 'outline' }
    // 測試邏輯...
  })
  
  it('應該處理圖片載入失敗', () => {
    const iconData = { type: 'image', path: 'invalid/path.jpg' }
    // 測試邏輯...
  })
})
```

### 視覺回歸測試
- **尺寸一致性**: 確保所有尺寸比例正確
- **顏色準確性**: 驗證顏色計算邏輯
- **跨瀏覽器**: 測試不同瀏覽器的渲染效果

## 相關檔案

### 前端檔案
- `resources/js/components/common/IconDisplay.vue`
- `resources/js/components/dev-tool/TestIconDisplay.vue`
- `resources/js/config/iconDisplayConfig.js`
- `resources/js/utils/heroicons/heroiconsLoader.js`
- `resources/js/pages/dev-tool/IconSizeTool.vue`

### 後端檔案
- `app/Helpers/IconDataHelper.php`
- `config/icon/colors.php`

### 測試檔案
- `tests/vue/components/IconDisplay.test.js`
- `tests/Feature/IconDataHelperTest.php`

## 最佳實務

### 使用建議
1. **尺寸選擇**: 根據使用場景選擇合適尺寸
   - Avatar: md, lg
   - Button Icon: 4, 5, 6
   - Logo: lg, xl
   
2. **顏色搭配**: 遵循色彩規範
   - 淡背景配深色文字/圖標
   - 深背景配白色文字/圖標
   
3. **效能考慮**: 
   - 避免頻繁更換 Hero Icons
   - 使用 backgroundColor prop 覆蓋預設值
   - 圖片路徑使用本地儲存優於外部 URL

### 注意事項
1. **Hero Icons 命名**: 必須包含 'Icon' 後綴 (如: 'UserIcon')
2. **Bootstrap Icons**: 不需要 'bi-' 前綴和 '-fill' 後綴
3. **圖片路徑**: 自動加上 `/storage/` 前綴，請勿重複添加
4. **文字限制**: text 類型最多 2 個字符，超出會被截斷