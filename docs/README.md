# PurpleDesk 文件索引

**專案**：PurpleDesk - 中小型團隊綜合專案管理系統  
**更新日期**：2025-08-22  
**維護者**：開發團隊

## 📁 文件結構總覽

```
docs/
├── README.md                    # 本文件 - 文件索引
├── TDD.md                      # 測試驅動開發指導原則
├── TEA.md                      # Tea CLI 工具使用說明
├── architecture/               # 系統架構文件
│   ├── brownfield-architecture.md    # 既有系統架構分析
│   ├── coding-standards.md           # 程式碼規範
│   ├── source-tree.md               # 原始碼結構說明
│   ├── tech-stack.md                # 技術堆疊文件
│   └── specification/               # 詳細規格文件
│       └── icon-system/             # 圖標系統規格
│           ├── README.md            # 圖標系統文件索引
│           ├── api-spec.md          # API 規格
│           ├── color-spec.md        # 顏色系統規格
│           ├── icon-display-spec.md # IconDisplay 元件規格
│           └── icon-picker-spec.md  # IconPicker 元件規格
├── prd/                        # 產品需求文件
│   └── refactor/               # 重構相關 PRD
│       ├── ICON-PICKER-EPICS.md           # Epic 定義
│       ├── ICON-PICKER-STORIES.md         # User Story
│       ├── ICON-PICKER-TEST-PLAN.md       # 測試計畫
│       ├── ICON-PICKER-TEST-PAGE-GUIDE.md # 測試頁面指南
│       ├── ICON-PICKER-HANDOVER.md        # 交接文件
│       ├── ICON-PICKER-EP-014-BREAKDOWN.md # Epic 014 分解
│       ├── ICON-PICKER-INTEGRATION-WORKFLOW.md # 整合工作流程
│       └── ICON-PICKER-BROWNFIELD-PRD.md  # 既有系統 PRD
└── stories/                    # Story 實作文件
    ├── ST-014-4-FIX.md        # Story 014-4 修正
    └── ST-014-5-TDD.md        # Story 014-5 TDD 實作
```

## 🏗️ 架構文件

### 系統架構
- **[技術堆疊](architecture/tech-stack.md)** - Laravel + Vue.js 完整技術選型
- **[既有架構分析](architecture/brownfield-architecture.md)** - 現有系統分析
- **[原始碼結構](architecture/source-tree.md)** - 檔案組織和模組劃分
- **[程式碼規範](architecture/coding-standards.md)** - 開發標準和最佳實務

### 規格文件
- **[圖標系統規格](architecture/specification/icon-system/README.md)** - 完整的圖標系統技術規格

## 📋 產品文件

### 開發方法論
- **[TDD 指導原則](TDD.md)** - Kent Beck TDD 和 Tidy First 方法論
- **[Tea CLI 工具](TEA.md)** - 版本控制輔助工具說明

### 產品需求 (PRD)
- **[IconPicker 重構 PRD](prd/refactor/ICON-PICKER-BROWNFIELD-PRD.md)** - 主要產品需求文件
- **[Epic 定義](prd/refactor/ICON-PICKER-EPICS.md)** - 重構 Epic 規劃
- **[User Stories](prd/refactor/ICON-PICKER-STORIES.md)** - 詳細使用者故事
- **[測試計畫](prd/refactor/ICON-PICKER-TEST-PLAN.md)** - 完整測試策略
- **[整合工作流程](prd/refactor/ICON-PICKER-INTEGRATION-WORKFLOW.md)** - 開發流程

### Story 實作
- **[ST-014-4 修正](stories/ST-014-4-FIX.md)** - Bootstrap Icons 修正
- **[ST-014-5 TDD](stories/ST-014-5-TDD.md)** - TDD 實作流程

## 🎯 快速導航

### 👩‍💻 對於開發者
1. **新手入門**：[技術堆疊](architecture/tech-stack.md) → [程式碼規範](architecture/coding-standards.md)
2. **TDD 開發**：[TDD 指導原則](TDD.md) → [測試計畫](prd/refactor/ICON-PICKER-TEST-PLAN.md)
3. **圖標系統**：[圖標系統規格](architecture/specification/icon-system/README.md)

### 🏢 對於產品經理
1. **產品概述**：[IconPicker 重構 PRD](prd/refactor/ICON-PICKER-BROWNFIELD-PRD.md)
2. **開發規劃**：[Epic 定義](prd/refactor/ICON-PICKER-EPICS.md) → [User Stories](prd/refactor/ICON-PICKER-STORIES.md)
3. **交接流程**：[交接文件](prd/refactor/ICON-PICKER-HANDOVER.md)

### 🔧 對於架構師
1. **系統架構**：[既有架構分析](architecture/brownfield-architecture.md) → [原始碼結構](architecture/source-tree.md)
2. **技術規格**：[圖標系統規格](architecture/specification/icon-system/README.md)

## 📝 文件維護

### 文件類型說明
- **架構文件** - 系統設計和技術決策記錄
- **規格文件** - 詳細的實作規範和 API 定義  
- **PRD 文件** - 產品需求和業務邏輯
- **Story 文件** - 具體實作過程和解決方案

### 更新原則
- 重大架構變更必須更新相關架構文件
- 新功能開發需要對應的規格文件
- PRD 變更需要同步更新實作文件
- 所有文件使用中文（zh-tw）撰寫

## 🔗 相關連結

- **專案倉庫**：[GitHub Repository](https://github.com/Protype/PurpleDesk)
- **開發工具**：Claude Code (claude.ai/code)
- **技術支援**：參考 [CLAUDE.md](../CLAUDE.md) 開發指導