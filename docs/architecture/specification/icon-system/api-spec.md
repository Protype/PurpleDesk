# 圖標系統 API 規格

**文件版本**: 1.0  
**建立日期**: 2025-08-22  
**維護者**: 開發團隊

## API 端點總覽

### Emoji API
| 端點 | 方法 | 描述 |
|------|------|------|
| `/api/emojis` | GET | 取得所有 emoji 資料 |
| `/api/emojis/categories` | GET | 取得 emoji 分類列表 |
| `/api/emojis/category/{category}` | GET | 取得指定分類的 emoji |

### HeroIcons API
| 端點 | 方法 | 描述 |
|------|------|------|
| `/api/heroicons` | GET | 取得所有 HeroIcons 資料 |
| `/api/heroicons/categories` | GET | 取得 HeroIcons 分類列表 |
| `/api/heroicons/category/{category}` | GET | 取得指定分類的 HeroIcons |

### Bootstrap Icons API
| 端點 | 方法 | 描述 |
|------|------|------|
| `/api/bootstrap-icons` | GET | 取得所有 Bootstrap Icons 資料 |
| `/api/bootstrap-icons/categories` | GET | 取得 Bootstrap Icons 分類列表 |
| `/api/bootstrap-icons/category/{category}` | GET | 取得指定分類的 Bootstrap Icons |

## 統一回傳格式

### 標準 API 回應結構

```json
{
  "data": {
    "category_id": [
      {
        "id": "unique_identifier",
        "name": "display_name",
        "emoji|value": "icon_content",
        "type": "emoji|heroicons|bootstrap-icons",
        "keywords": ["keyword1", "keyword2"],
        "category": "category_id",
        "has_skin_tone|has_variants": boolean,
        "skin_variations|variant_type": "optional_data"
      }
    ]
  },
  "meta": {
    "total": 數量,
    "type": "emoji|heroicons|bootstrap-icons",
    "categories": {
      "category_id": {
        "name": "分類名稱",
        "description": "分類描述"
      }
    }
  }
}
```

## 詳細 API 規格

### 1. Emoji API

#### GET /api/emojis

**回應範例**:
```json
{
  "data": {
    "smileys_emotion": [
      {
        "id": "1f600",
        "name": "grinning face",
        "emoji": "😀",
        "type": "emoji",
        "keywords": ["grinning", "face", "smile"],
        "category": "smileys_emotion",
        "has_skin_tone": false
      },
      {
        "id": "1f44d",
        "name": "thumbs up",
        "emoji": "👍",
        "type": "emoji", 
        "keywords": ["thumbs", "up", "like"],
        "category": "people_body",
        "has_skin_tone": true,
        "skin_variations": {
          "light": "👍🏻",
          "medium_light": "👍🏼",
          "medium": "👍🏽",
          "medium_dark": "👍🏾",
          "dark": "👍🏿"
        }
      }
    ]
  },
  "meta": {
    "total": 3781,
    "type": "emoji",
    "categories": {
      "smileys_emotion": {
        "name": "Smileys & Emotion",
        "description": "表情符號與情感表達"
      },
      "people_body": {
        "name": "People & Body", 
        "description": "人物與身體部位相關表情"
      }
    }
  }
}
```

### 2. HeroIcons API

#### GET /api/heroicons

**回應範例**:
```json
{
  "data": {
    "general": [
      {
        "id": "academic-cap-outline",
        "name": "Academic Cap",
        "value": "AcademicCapIcon",
        "type": "heroicons",
        "keywords": ["academic", "cap", "education"],
        "category": "general",
        "has_variants": true,
        "variant_type": "outline"
      },
      {
        "id": "academic-cap-solid", 
        "name": "Academic Cap",
        "value": "AcademicCapIcon",
        "type": "heroicons",
        "keywords": ["academic", "cap", "education"],
        "category": "general",
        "has_variants": true,
        "variant_type": "solid"
      }
    ]
  },
  "meta": {
    "total": 588,
    "type": "heroicons",
    "categories": {
      "general": {
        "name": "General",
        "description": "一般用途圖標"
      }
    }
  }
}
```

### 3. Bootstrap Icons API

#### GET /api/bootstrap-icons

**回應範例**:
```json
{
  "data": {
    "general": [
      {
        "id": "house-outline",
        "name": "House",
        "value": "bi-house",
        "type": "bootstrap-icons",
        "keywords": ["house", "home", "building"],
        "category": "general", 
        "has_variants": true,
        "variant_type": "outline"
      },
      {
        "id": "house-fill",
        "name": "House",
        "value": "bi-house-fill",
        "type": "bootstrap-icons",
        "keywords": ["house", "home", "building"],
        "category": "general",
        "has_variants": true,
        "variant_type": "fill"
      }
    ]
  },
  "meta": {
    "total": 2252,
    "type": "bootstrap-icons",
    "categories": {
      "general": {
        "name": "General",
        "description": "一般通用圖標"
      },
      "ui": {
        "name": "UI Elements", 
        "description": "使用者介面元件圖標"
      }
    }
  }
}
```

## 錯誤處理

### 標準錯誤回應
```json
{
  "error": "錯誤訊息",
  "code": "ERROR_CODE"
}
```

### 常見錯誤碼
| HTTP 狀態 | 錯誤碼 | 描述 |
|----------|--------|------|
| 400 | INVALID_CATEGORY | 無效的分類 ID |
| 404 | CATEGORY_NOT_FOUND | 分類不存在 |
| 500 | CONFIG_LOAD_FAILED | 設定檔案載入失敗 |
| 500 | CACHE_ERROR | 快取操作失敗 |

## 快取策略

### 快取設定
- **快取時間**: 24 小時 (86400 秒)
- **快取驅動**: Redis (生產環境) / Array (開發環境)

### 快取鍵值
| API 類型 | 快取鍵 | 描述 |
|----------|--------|------|
| Emoji | `all_emojis_v2` | 所有 emoji 資料 |
| HeroIcons | `heroicons_data_v2` | 所有 HeroIcons 資料 |
| Bootstrap Icons | `bootstrap_icons_data_v2` | 所有 Bootstrap Icons 資料 |

### 快取管理
```php
// 清除特定快取
EmojiService::clearCache();
HeroIconService::clearCache();
BootstrapIconService::clearCache();

// 清除所有圖標快取
php artisan cache:forget all_emojis_v2
php artisan cache:forget heroicons_data_v2
php artisan cache:forget bootstrap_icons_data_v2
```

## 資料來源配置

### 後端配置檔案
- **Emoji**: `config/icon/emoji/*.php`
- **HeroIcons**: `config/icon/heroicons.php`
- **Bootstrap Icons**: `config/icon/bootstrap-icons/*.php`

### 前端資料載入
- **服務層**: `IconDataLoader.js` 統一載入所有類型
- **快取機制**: 本地記憶體快取 + API 快取
- **載入策略**: 按需載入，支援漸進式載入

## 效能指標

### API 回應時間
- **首次載入**: < 500ms (含資料處理)
- **快取命中**: < 50ms
- **資料大小**: 
  - Emoji: ~800KB
  - HeroIcons: ~200KB
  - Bootstrap Icons: ~600KB

### 記憶體使用
- **總記憶體佔用**: ~1MB
- **快取有效期**: 24 小時
- **併發支援**: 支援高併發讀取