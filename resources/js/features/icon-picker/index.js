/**
 * IconPicker Feature Entry Point
 * 
 * 提供標準的元件匯出介面
 */

// 主要組件匯出
export { default } from './IconPicker.vue'
export { default as IconPicker } from './IconPicker.vue'

// 代理組件（用於版本切換）
export { default as IconPickerProxy } from './demo/IconPickerProxy.vue'

// 子面板組件（可選，用於進階使用）
export { default as EmojiPanel } from './components/EmojiPanel.vue'
export { default as TextIconPanel } from './components/TextIconPanel.vue'
export { default as IconPickerSearch } from './components/IconPickerSearch.vue'

// 共用組件
export { default as VirtualScrollGrid } from './components/shared/VirtualScrollGrid.vue'

// 服務層
export { IconDataLoader } from './services/IconDataLoader.js'

// 開發工具（開發環境）
if (import.meta.env.DEV) {
  export { default as IconPickerDevTool } from './demo/IconPickerDevTool.vue'
}