<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Emoji 相容性過濾設定
    |--------------------------------------------------------------------------
    |
    | 這個設定檔控制 emoji 的顯示過濾規則，用於移除有顯示問題的 emoji
    |
    */

    /*
    |--------------------------------------------------------------------------
    | 過濾開關
    |--------------------------------------------------------------------------
    |
    | 是否啟用 emoji 過濾功能
    |
    */
    'enabled' => env('EMOJI_FILTER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | 黑名單 Emoji 清單
    |--------------------------------------------------------------------------
    |
    | 基於用戶確認結果的有問題 emoji 清單 (57 個)
    | 這些 emoji 在部分系統上會顯示為黑色方塊或不正確的字符
    |
    | 統計資料:
    | - 總測試: 383 個 emoji  
    | - 實際問題: 57 個 (14.9%)
    | - 預測準確度: 100.0%
    | - 最後更新: 2025-08-21
    |
    */
    'blacklist' => [
        // 國旗相關問題 emoji
        "🇨🇶",
        
        // Unicode 15+ 新增的問題 emoji
        "🫩", // raw meat
        "🫆", // nesting dolls
        "🪾", // leafless tree
        "🫜", // bagel
        "🪉", // harp
        "🪏", // camel
        "🫟", // splashing sweat symbol
        
        // 複合 emoji（包含方向箭頭的人物動作）
        "🚶‍♀️‍➡️", // woman walking facing right
        "🚶‍♂️‍➡️", // man walking facing right
        "🧎‍♀️‍➡️", // woman kneeling facing right
        "🧎‍♂️‍➡️", // man kneeling facing right
        "🏃‍♀️‍➡️", // woman running facing right
        "🏃‍♂️‍➡️", // man running facing right
        
        // 無障礙相關複合 emoji
        "🧑‍🦯‍➡️", // person with white cane facing right
        "👨‍🦯‍➡️", // man with white cane facing right
        "👩‍🦯‍➡️", // woman with white cane facing right
        "🧑‍🦼‍➡️", // person in motorized wheelchair facing right
        "👨‍🦼‍➡️", // man in motorized wheelchair facing right
        "👩‍🦼‍➡️", // woman in motorized wheelchair facing right
        "🧑‍🦽‍➡️", // person in manual wheelchair facing right
        "👨‍🦽‍➡️", // man in manual wheelchair facing right
        "👩‍🦽‍➡️", // woman in manual wheelchair facing right
        
        // 家庭相關複合 emoji
        "🧑‍🧑‍🧒‍🧒", // family: adult, adult, child, child
        "🧑‍🧑‍🧒",    // family: adult, adult, child
        "🧑‍🧒‍🧒",    // family: adult, child, child
        "🧑‍🧒",       // family: adult, child
        
        // 表情符號變體
        "🙂‍↔️", // head shaking horizontally
        "🙂‍↕️", // head shaking vertically
        
        // 簡化的方向性 emoji
        "🚶‍➡️", // person walking facing right
        "🧎‍➡️", // person kneeling facing right
        "🏃‍➡️", // person running facing right
        
        // 物品相關複合 emoji
        "⛓️‍💥", // broken chain
        
        // 動物相關新 emoji
        "🐦‍🔥", // phoenix
        "🐦‍⬛", // black bird
        
        // 食物相關變體
        "🍋‍🟩", // lime
        "🍄‍🟫", // brown mushroom
        
        // Unicode 15+ 表情和手勢
        "🫨", // shaking face
        "🩷", // pink heart
        "🩵", // light blue heart
        "🩶", // grey heart
        "🫷", // leftwards pushing hand
        "🫸", // rightwards pushing hand
        
        // Unicode 15+ 動物
        "🫎", // moose
        "🫏", // donkey
        "🪽", // wing
        "🪿", // goose
        "🪼", // jellyfish
        
        // Unicode 15+ 植物和物品
        "🪻", // hyacinth
        "🫚", // ginger
        "🫛", // pea pod
        "🪭", // folding hand fan
        "🪮", // hair pick
        "🪇", // maracas
        "🪈", // flute
        "🪯", // khanda
        "🛜", // wireless
    ],

    /*
    |--------------------------------------------------------------------------
    | Unicode 版本過濾規則
    |--------------------------------------------------------------------------
    |
    | 根據 Unicode 版本決定過濾行為
    | 'block' = 完全封鎖
    | 'high_risk' = 高風險但不封鎖
    |
    */
    'version_rules' => [
        '15' => 'block',      // Unicode 15.0 有較高問題率
        '16' => 'block',      // Unicode 16.0 太新，支援不佳
        '15.1' => 'block',    // Unicode 15.1 問題較多
    ],

    /*
    |--------------------------------------------------------------------------
    | 風險因子規則
    |--------------------------------------------------------------------------
    |
    | 根據 emoji 的特性標記風險等級
    |
    */
    'factor_rules' => [
        'FLAG_SEQUENCE' => 'high_risk',     // 國旗序列
        'ZWJ_SEQUENCE' => 'medium_risk',    // 零寬連接符序列
        'VARIATION_SELECTOR' => 'low_risk', // 變體選擇器
    ],

    /*
    |--------------------------------------------------------------------------
    | 過濾統計資訊
    |--------------------------------------------------------------------------
    |
    | 用於追蹤過濾效果的統計資訊
    |
    */
    'stats' => [
        'total_tested' => 383,
        'actual_problems' => 57,
        'problem_rate' => 14.9,
        'prediction_accuracy' => 100.0,
        'last_updated' => '2025-08-21T13:43:00.000Z'
    ],

    /*
    |--------------------------------------------------------------------------
    | 過濾行為設定
    |--------------------------------------------------------------------------
    */
    
    // 是否移除複合 emoji（包含 ZWJ 的）
    'filter_compound_emojis' => env('EMOJI_FILTER_COMPOUND', true),
    
    // 是否移除膚色變體（保留基礎版本）
    'filter_skin_tone_variants' => env('EMOJI_FILTER_SKIN_TONES', true),
    
    // 是否移除重複的基礎 emoji
    'filter_duplicates' => env('EMOJI_FILTER_DUPLICATES', true),
    
    // 開發環境是否記錄過濾詳情
    'log_filtering' => env('EMOJI_FILTER_LOG', true),
];