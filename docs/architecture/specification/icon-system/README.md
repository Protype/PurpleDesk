# 圖標系統規範文件

**最後更新**: 2025-08-22  
**維護者**: 開發團隊

## 文件結構

本目錄包含 PurpleDesk 圖標系統的完整技術規範，拆分為以下專門文件：

### 📋 規範文件清單

1. **[color-spec.md](./color-spec.md)**
   - 顏色系統完整規範
   - 預設調色盤（16 色）+ 淡色系調色盤（16 色）
   - 顏色選擇原則和自動計算邏輯
   - 前後端驗證規則

2. **[api-spec.md](./api-spec.md)**
   - 所有圖標 API 端點規格
   - 統一 data/meta 回傳格式
   - 錯誤處理和快取策略
   - 效能指標和資料來源

3. **[icon-picker-spec.md](./icon-picker-spec.md)**
   - IconPicker/IconDisplay 元件規範
   - IconData 物件完整結構
   - 前後端資料流和驗證
   - 模組化架構和測試要求

## 🎯 5 種圖標類型

| 類型 | 識別碼 | 描述 | 主要用途 |
|------|--------|------|----------|
| 文字圖標 | `text` | 1-2 個字符，支援中英文 | 個人頭像、團隊簡稱 |
| Emoji | `emoji` | Unicode 標準，支援膚色 | 狀態標示、分類標籤 |
| Hero Icons | `hero_icon` | outline/solid 樣式 | 組織標誌、功能圖標 |
| Bootstrap Icons | `bootstrap_icon` | 2,252 個圖標，8 分類 | 團隊標誌、UI 元件 |
| 上傳圖片 | `image` | JPG/PNG/GIF，2MB 限制 | 自訂頭像、品牌標誌 |

## 🛠️ 技術整合

### 後端實作
- **控制器**: `EmojiController`, `HeroIconController`, `BootstrapIconController`
- **服務層**: `EmojiService`, `HeroIconService`, `BootstrapIconService`  
- **Helper**: `IconDataHelper`, `IconResetHelper`
- **配置**: `config/icon/` 目錄下的所有配置檔案

### 前端實作
- **主元件**: `IconPicker.vue`, `IconDisplay.vue`
- **服務**: `IconDataLoader.js`, `IconService.js`
- **工具**: `resources/js/utils/` 下的圖標處理工具
- **模組**: `resources/js/features/icon-picker/` (重構中)

## 📊 關鍵指標

- **圖標總數**: 6,033 個
- **Emoji 相容性**: 過濾 57 個不相容項目
- **記憶體使用**: ~1MB
- **API 回應時間**: <500ms (首次), <50ms (快取)
- **測試覆蓋率**: 目標 >80%

## 🔄 開發流程

1. **閱讀規範**: 先閱讀相關的 spec 文件
2. **遵循 TDD**: 測試先行開發
3. **模組化開發**: 使用 features/ 結構
4. **API 整合**: 遵循統一的 data/meta 格式
5. **驗證測試**: 前後端驗證規則一致

## 🚀 快速開始

### 查看 API 規格
```bash
# 查看所有可用的圖標 API
curl http://localhost:8000/api/emojis/categories
curl http://localhost:8000/api/heroicons/categories  
curl http://localhost:8000/api/bootstrap-icons/categories
```

### 前端整合
```vue
<script setup>
import { IconPicker, IconDisplay } from '@/components/common'

const iconData = ref({})

const handleIconSelect = (selectedIcon) => {
  iconData.value = selectedIcon
}
</script>

<template>
  <IconPicker 
    v-model="iconData"
    @icon-selected="handleIconSelect"
  />
  
  <IconDisplay 
    :icon-data="iconData"
    size="md"
  />
</template>
```

---

如需詳細的實作細節，請參考各個專門的規範文件。