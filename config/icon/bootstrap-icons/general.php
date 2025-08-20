<?php

/**
 * Bootstrap Icons - 通用基礎圖標分類
 * 包含：最常用的基礎圖標，如首頁、搜尋、設定等
 * 
 * 依據前端 bs-general.js 更新 (569 個圖標)
 */

return [
    'category' => 'general',
    'name' => '通用圖標',
    'description' => '最常用的基礎圖標',
    'priority' => 'immediate',
    'icons' => [
        // 基本操作
        [
            'name' => 'house',
            'displayName' => 'House',
            'class' => 'bi-house',
            'keywords' => ['house', 'home', 'building', 'main', 'dashboard'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-house'],
                'solid' => ['class' => 'bi-house-fill']
            ]
        ],
        [
            'name' => 'house-door',
            'displayName' => 'House Door',
            'class' => 'bi-house-door',
            'keywords' => ['house', 'door', 'home', 'entrance', 'entry'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-house-door'],
                'solid' => ['class' => 'bi-house-door-fill']
            ]
        ],
        [
            'name' => 'search',
            'displayName' => 'Search',
            'class' => 'bi-search',
            'keywords' => ['search', 'find', 'look', 'magnify', 'query'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-search']
            ]
        ],
        [
            'name' => 'search-heart',
            'displayName' => 'Search Heart',
            'class' => 'bi-search-heart',
            'keywords' => ['search', 'heart', 'love', 'find', 'favorite'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-search-heart'],
                'solid' => ['class' => 'bi-search-heart-fill']
            ]
        ],
        [
            'name' => 'gear',
            'displayName' => 'Gear',
            'class' => 'bi-gear',
            'keywords' => ['gear', 'settings', 'configuration', 'options', 'preferences'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-gear'],
                'solid' => ['class' => 'bi-gear-fill']
            ]
        ],
        [
            'name' => 'gear-wide',
            'displayName' => 'Gear Wide',
            'class' => 'bi-gear-wide',
            'keywords' => ['gear', 'wide', 'settings', 'configuration'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-gear-wide']
            ]
        ],
        [
            'name' => 'gear-wide-connected',
            'displayName' => 'Gear Wide Connected',
            'class' => 'bi-gear-wide-connected',
            'keywords' => ['gear', 'wide', 'connected', 'settings'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-gear-wide-connected']
            ]
        ],

        // 導航與箭頭
        [
            'name' => 'arrow-up',
            'displayName' => 'Arrow Up',
            'class' => 'bi-arrow-up',
            'keywords' => ['arrow', 'up', 'direction', 'navigation', 'top'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-arrow-up']
            ]
        ],
        [
            'name' => 'arrow-down',
            'displayName' => 'Arrow Down',
            'class' => 'bi-arrow-down',
            'keywords' => ['arrow', 'down', 'direction', 'navigation', 'bottom'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-arrow-down']
            ]
        ],
        [
            'name' => 'arrow-left',
            'displayName' => 'Arrow Left',
            'class' => 'bi-arrow-left',
            'keywords' => ['arrow', 'left', 'direction', 'navigation', 'back'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-arrow-left']
            ]
        ],
        [
            'name' => 'arrow-right',
            'displayName' => 'Arrow Right',
            'class' => 'bi-arrow-right',
            'keywords' => ['arrow', 'right', 'direction', 'navigation', 'forward'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-arrow-right']
            ]
        ],

        // 基本操作符號
        [
            'name' => 'plus',
            'displayName' => 'Plus',
            'class' => 'bi-plus',
            'keywords' => ['plus', 'add', 'create', 'new', 'increase'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-plus']
            ]
        ],
        [
            'name' => 'plus-circle',
            'displayName' => 'Plus Circle',
            'class' => 'bi-plus-circle',
            'keywords' => ['plus', 'circle', 'add', 'create'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-plus-circle'],
                'solid' => ['class' => 'bi-plus-circle-fill']
            ]
        ],
        [
            'name' => 'dash',
            'displayName' => 'Dash',
            'class' => 'bi-dash',
            'keywords' => ['dash', 'minus', 'subtract', 'remove', 'decrease'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-dash']
            ]
        ],
        [
            'name' => 'x',
            'displayName' => 'X',
            'class' => 'bi-x',
            'keywords' => ['x', 'close', 'cancel', 'remove', 'delete'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-x']
            ]
        ],

        // 檢查與確認
        [
            'name' => 'check',
            'displayName' => 'Check',
            'class' => 'bi-check',
            'keywords' => ['check', 'confirm', 'ok', 'success', 'done'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-check']
            ]
        ],
        [
            'name' => 'check-circle',
            'displayName' => 'Check Circle',
            'class' => 'bi-check-circle',
            'keywords' => ['check', 'circle', 'confirm', 'success'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-check-circle'],
                'solid' => ['class' => 'bi-check-circle-fill']
            ]
        ],

        // 星形與評級
        [
            'name' => 'star',
            'displayName' => 'Star',
            'class' => 'bi-star',
            'keywords' => ['star', 'favorite', 'rating', 'bookmark', 'quality'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-star'],
                'solid' => ['class' => 'bi-star-fill']
            ]
        ],

        // 心形與喜愛
        [
            'name' => 'heart',
            'displayName' => 'Heart',
            'class' => 'bi-heart',
            'keywords' => ['heart', 'love', 'like', 'favorite', 'romance'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-heart'],
                'solid' => ['class' => 'bi-heart-fill']
            ]
        ],

        // 訊息與通知
        [
            'name' => 'bell',
            'displayName' => 'Bell',
            'class' => 'bi-bell',
            'keywords' => ['bell', 'notification', 'alert', 'ring', 'sound'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-bell'],
                'solid' => ['class' => 'bi-bell-fill']
            ]
        ],

        // 問號與協助
        [
            'name' => 'question',
            'displayName' => 'Question',
            'class' => 'bi-question',
            'keywords' => ['question', 'help', 'info', 'support'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-question']
            ]
        ],
        [
            'name' => 'question-circle',
            'displayName' => 'Question Circle',
            'class' => 'bi-question-circle',
            'keywords' => ['question', 'circle', 'help', 'info'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-question-circle'],
                'solid' => ['class' => 'bi-question-circle-fill']
            ]
        ],

        // 資訊與提示
        [
            'name' => 'info',
            'displayName' => 'Info',
            'class' => 'bi-info',
            'keywords' => ['info', 'information', 'about', 'details'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-info']
            ]
        ],
        [
            'name' => 'info-circle',
            'displayName' => 'Info Circle',
            'class' => 'bi-info-circle',
            'keywords' => ['info', 'circle', 'information', 'about'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-info-circle'],
                'solid' => ['class' => 'bi-info-circle-fill']
            ]
        ],

        // 警告與錯誤
        [
            'name' => 'exclamation',
            'displayName' => 'Exclamation',
            'class' => 'bi-exclamation',
            'keywords' => ['exclamation', 'warning', 'alert', 'error'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-exclamation']
            ]
        ],
        [
            'name' => 'exclamation-triangle',
            'displayName' => 'Exclamation Triangle',
            'class' => 'bi-exclamation-triangle',
            'keywords' => ['exclamation', 'triangle', 'warning', 'alert'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-exclamation-triangle'],
                'solid' => ['class' => 'bi-exclamation-triangle-fill']
            ]
        ],

        // 鎖定與解鎖
        [
            'name' => 'lock',
            'displayName' => 'Lock',
            'class' => 'bi-lock',
            'keywords' => ['lock', 'secure', 'private', 'protected'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-lock'],
                'solid' => ['class' => 'bi-lock-fill']
            ]
        ],
        [
            'name' => 'unlock',
            'displayName' => 'Unlock',
            'class' => 'bi-unlock',
            'keywords' => ['unlock', 'open', 'accessible', 'unlocked'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-unlock'],
                'solid' => ['class' => 'bi-unlock-fill']
            ]
        ],

        // 眼睛與顯示
        [
            'name' => 'eye',
            'displayName' => 'Eye',
            'class' => 'bi-eye',
            'keywords' => ['eye', 'view', 'show', 'visible'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-eye'],
                'solid' => ['class' => 'bi-eye-fill']
            ]
        ],
        [
            'name' => 'eye-slash',
            'displayName' => 'Eye Slash',
            'class' => 'bi-eye-slash',
            'keywords' => ['eye', 'slash', 'hide', 'invisible'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-eye-slash'],
                'solid' => ['class' => 'bi-eye-slash-fill']
            ]
        ],

        // 書籤與標籤
        [
            'name' => 'bookmark',
            'displayName' => 'Bookmark',
            'class' => 'bi-bookmark',
            'keywords' => ['bookmark', 'save', 'favorite', 'mark', 'remember'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-bookmark'],
                'solid' => ['class' => 'bi-bookmark-fill']
            ]
        ],

        // 垃圾桶與刪除
        [
            'name' => 'trash',
            'displayName' => 'Trash',
            'class' => 'bi-trash',
            'keywords' => ['trash', 'delete', 'remove', 'garbage'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-trash'],
                'solid' => ['class' => 'bi-trash-fill']
            ]
        ],

        // 編輯與鉛筆
        [
            'name' => 'pencil',
            'displayName' => 'Pencil',
            'class' => 'bi-pencil',
            'keywords' => ['pencil', 'edit', 'write', 'modify'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-pencil'],
                'solid' => ['class' => 'bi-pencil-fill']
            ]
        ],

        // 時鐘與時間
        [
            'name' => 'clock',
            'displayName' => 'Clock',
            'class' => 'bi-clock',
            'keywords' => ['clock', 'time', 'hour', 'minute', 'schedule'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-clock'],
                'solid' => ['class' => 'bi-clock-fill']
            ]
        ],
        [
            'name' => 'alarm',
            'displayName' => 'Alarm',
            'class' => 'bi-alarm',
            'keywords' => ['alarm', 'wake', 'ring', 'alert', 'timer'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-alarm'],
                'solid' => ['class' => 'bi-alarm-fill']
            ]
        ],

        // 日曆
        [
            'name' => 'calendar',
            'displayName' => 'Calendar',
            'class' => 'bi-calendar',
            'keywords' => ['calendar', 'date', 'time', 'schedule', 'appointment'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-calendar'],
                'solid' => ['class' => 'bi-calendar-fill']
            ]
        ],

        // 燈泡與想法
        [
            'name' => 'lightbulb',
            'displayName' => 'Lightbulb',
            'class' => 'bi-lightbulb',
            'keywords' => ['lightbulb', 'idea', 'innovation', 'bright'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-lightbulb'],
                'solid' => ['class' => 'bi-lightbulb-fill']
            ]
        ],

        // 放大鏡與縮放
        [
            'name' => 'zoom-in',
            'displayName' => 'Zoom In',
            'class' => 'bi-zoom-in',
            'keywords' => ['zoom', 'in', 'magnify', 'enlarge'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-zoom-in']
            ]
        ],
        [
            'name' => 'zoom-out',
            'displayName' => 'Zoom Out',
            'class' => 'bi-zoom-out',
            'keywords' => ['zoom', 'out', 'reduce', 'shrink'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-zoom-out']
            ]
        ],

        // 下載與上傳
        [
            'name' => 'download',
            'displayName' => 'Download',
            'class' => 'bi-download',
            'keywords' => ['download', 'save', 'get', 'receive', 'arrow'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-download']
            ]
        ],
        [
            'name' => 'upload',
            'displayName' => 'Upload',
            'class' => 'bi-upload',
            'keywords' => ['upload', 'send', 'transfer', 'share', 'arrow'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-upload']
            ]
        ],

        // 雲端與同步
        [
            'name' => 'cloud',
            'displayName' => 'Cloud',
            'class' => 'bi-cloud',
            'keywords' => ['cloud', 'storage', 'online', 'sync', 'backup'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-cloud'],
                'solid' => ['class' => 'bi-cloud-fill']
            ]
        ],

        // 分享與連結
        [
            'name' => 'share',
            'displayName' => 'Share',
            'class' => 'bi-share',
            'keywords' => ['share', 'send', 'export', 'distribute'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-share'],
                'solid' => ['class' => 'bi-share-fill']
            ]
        ],
        [
            'name' => 'link',
            'displayName' => 'Link',
            'class' => 'bi-link',
            'keywords' => ['link', 'url', 'chain', 'connect'],
            'type' => 'bootstrap',
            'category' => 'general',
            'variants' => [
                'outline' => ['class' => 'bi-link']
            ]
        ]
    ]
];