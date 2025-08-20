<?php

/**
 * Bootstrap Icons - UI 介面元件分類
 * 包含：按鈕、選單、視窗、表格等 UI 控制項圖標
 * 
 * 依據前端 bs-ui.js 更新 (450 個圖標)
 */

return [
    'category' => 'ui',
    'name' => 'UI 介面',
    'description' => '使用者介面元件與控制項',
    'priority' => 'high',
    'icons' => [
        // 選單與導航
        [
            'name' => 'list',
            'displayName' => 'List',
            'class' => 'bi-list',
            'keywords' => ['list', 'menu', 'hamburger', 'navigation', 'items'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-list']
            ]
        ],
        [
            'name' => 'justify',
            'displayName' => 'Justify',
            'class' => 'bi-justify',
            'keywords' => ['justify', 'align', 'text', 'layout', 'spacing'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-justify']
            ]
        ],
        [
            'name' => 'justify-left',
            'displayName' => 'Justify Left',
            'class' => 'bi-justify-left',
            'keywords' => ['justify', 'left', 'align', 'text'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-justify-left']
            ]
        ],
        [
            'name' => 'justify-right',
            'displayName' => 'Justify Right',
            'class' => 'bi-justify-right',
            'keywords' => ['justify', 'right', 'align', 'text'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-justify-right']
            ]
        ],
        [
            'name' => 'menu-button',
            'displayName' => 'Menu Button',
            'class' => 'bi-menu-button',
            'keywords' => ['menu', 'button', 'hamburger', 'navigation'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-menu-button'],
                'solid' => ['class' => 'bi-menu-button-fill']
            ]
        ],
        [
            'name' => 'menu-button-wide',
            'displayName' => 'Menu Button Wide',
            'class' => 'bi-menu-button-wide',
            'keywords' => ['menu', 'button', 'wide', 'navigation'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-menu-button-wide'],
                'solid' => ['class' => 'bi-menu-button-wide-fill']
            ]
        ],

        // 表格與網格
        [
            'name' => 'table',
            'displayName' => 'Table',
            'class' => 'bi-table',
            'keywords' => ['table', 'grid', 'data', 'layout', 'structure'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-table']
            ]
        ],
        [
            'name' => 'grid',
            'displayName' => 'Grid',
            'class' => 'bi-grid',
            'keywords' => ['grid', 'layout', 'structure', 'cells', 'matrix'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-grid'],
                'solid' => ['class' => 'bi-grid-fill']
            ]
        ],
        [
            'name' => 'grid-1x2',
            'displayName' => 'Grid 1x2',
            'class' => 'bi-grid-1x2',
            'keywords' => ['grid', '1x2', 'layout', 'two', 'cells'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-grid-1x2'],
                'solid' => ['class' => 'bi-grid-1x2-fill']
            ]
        ],
        [
            'name' => 'grid-3x2',
            'displayName' => 'Grid 3x2',
            'class' => 'bi-grid-3x2',
            'keywords' => ['grid', '3x2', 'layout', 'six', 'cells'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-grid-3x2']
            ]
        ],
        [
            'name' => 'grid-3x2-gap',
            'displayName' => 'Grid 3x2 Gap',
            'class' => 'bi-grid-3x2-gap',
            'keywords' => ['grid', '3x2', 'gap', 'spacing'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-grid-3x2-gap'],
                'solid' => ['class' => 'bi-grid-3x2-gap-fill']
            ]
        ],
        [
            'name' => 'grid-3x3',
            'displayName' => 'Grid 3x3',
            'class' => 'bi-grid-3x3',
            'keywords' => ['grid', '3x3', 'layout', 'nine', 'cells'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-grid-3x3']
            ]
        ],
        [
            'name' => 'grid-3x3-gap',
            'displayName' => 'Grid 3x3 Gap',
            'class' => 'bi-grid-3x3-gap',
            'keywords' => ['grid', '3x3', 'gap', 'spacing'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-grid-3x3-gap'],
                'solid' => ['class' => 'bi-grid-3x3-gap-fill']
            ]
        ],

        // 視窗與對話框
        [
            'name' => 'window',
            'displayName' => 'Window',
            'class' => 'bi-window',
            'keywords' => ['window', 'dialog', 'popup', 'interface', 'frame'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-window']
            ]
        ],
        [
            'name' => 'window-desktop',
            'displayName' => 'Window Desktop',
            'class' => 'bi-window-desktop',
            'keywords' => ['window', 'desktop', 'computer', 'screen'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-window-desktop']
            ]
        ],
        [
            'name' => 'window-dock',
            'displayName' => 'Window Dock',
            'class' => 'bi-window-dock',
            'keywords' => ['window', 'dock', 'panel', 'attach'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-window-dock']
            ]
        ],
        [
            'name' => 'window-fullscreen',
            'displayName' => 'Window Fullscreen',
            'class' => 'bi-window-fullscreen',
            'keywords' => ['window', 'fullscreen', 'maximize', 'expand'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-window-fullscreen']
            ]
        ],
        [
            'name' => 'window-split',
            'displayName' => 'Window Split',
            'class' => 'bi-window-split',
            'keywords' => ['window', 'split', 'divide', 'pane'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-window-split']
            ]
        ],

        // 滑桿與進度條
        [
            'name' => 'sliders',
            'displayName' => 'Sliders',
            'class' => 'bi-sliders',
            'keywords' => ['sliders', 'controls', 'adjustments', 'settings'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-sliders']
            ]
        ],
        [
            'name' => 'sliders2',
            'displayName' => 'Sliders 2',
            'class' => 'bi-sliders2',
            'keywords' => ['sliders', 'controls', 'vertical', 'adjustments'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-sliders2']
            ]
        ],
        [
            'name' => 'sliders2-vertical',
            'displayName' => 'Sliders 2 Vertical',
            'class' => 'bi-sliders2-vertical',
            'keywords' => ['sliders', 'vertical', 'controls'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-sliders2-vertical']
            ]
        ],

        // 按鈕與控制項
        [
            'name' => 'toggle-off',
            'displayName' => 'Toggle Off',
            'class' => 'bi-toggle-off',
            'keywords' => ['toggle', 'off', 'switch', 'button', 'control'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-toggle-off']
            ]
        ],
        [
            'name' => 'toggle-on',
            'displayName' => 'Toggle On',
            'class' => 'bi-toggle-on',
            'keywords' => ['toggle', 'on', 'switch', 'button', 'control'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-toggle-on']
            ]
        ],
        [
            'name' => 'toggle2-off',
            'displayName' => 'Toggle2 Off',
            'class' => 'bi-toggle2-off',
            'keywords' => ['toggle', 'off', 'switch', 'alternative'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-toggle2-off']
            ]
        ],
        [
            'name' => 'toggle2-on',
            'displayName' => 'Toggle2 On',
            'class' => 'bi-toggle2-on',
            'keywords' => ['toggle', 'on', 'switch', 'alternative'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-toggle2-on']
            ]
        ],
        [
            'name' => 'toggles',
            'displayName' => 'Toggles',
            'class' => 'bi-toggles',
            'keywords' => ['toggles', 'multiple', 'switches'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-toggles']
            ]
        ],
        [
            'name' => 'toggles2',
            'displayName' => 'Toggles 2',
            'class' => 'bi-toggles2',
            'keywords' => ['toggles', 'multiple', 'switches', 'alternative'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-toggles2']
            ]
        ],

        // 尺寸調整
        [
            'name' => 'arrows-angle-contract',
            'displayName' => 'Arrows Angle Contract',
            'class' => 'bi-arrows-angle-contract',
            'keywords' => ['arrows', 'angle', 'contract', 'minimize'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-arrows-angle-contract']
            ]
        ],
        [
            'name' => 'arrows-angle-expand',
            'displayName' => 'Arrows Angle Expand',
            'class' => 'bi-arrows-angle-expand',
            'keywords' => ['arrows', 'angle', 'expand', 'maximize'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-arrows-angle-expand']
            ]
        ],
        [
            'name' => 'arrows-collapse',
            'displayName' => 'Arrows Collapse',
            'class' => 'bi-arrows-collapse',
            'keywords' => ['arrows', 'collapse', 'contract', 'shrink'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-arrows-collapse']
            ]
        ],
        [
            'name' => 'arrows-expand',
            'displayName' => 'Arrows Expand',
            'class' => 'bi-arrows-expand',
            'keywords' => ['arrows', 'expand', 'enlarge', 'grow'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-arrows-expand']
            ]
        ],
        [
            'name' => 'arrows-fullscreen',
            'displayName' => 'Arrows Fullscreen',
            'class' => 'bi-arrows-fullscreen',
            'keywords' => ['arrows', 'fullscreen', 'expand', 'resize'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-arrows-fullscreen']
            ]
        ],
        [
            'name' => 'arrows-move',
            'displayName' => 'Arrows Move',
            'class' => 'bi-arrows-move',
            'keywords' => ['arrows', 'move', 'drag', 'position'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-arrows-move']
            ]
        ],
        [
            'name' => 'fullscreen',
            'displayName' => 'Fullscreen',
            'class' => 'bi-fullscreen',
            'keywords' => ['fullscreen', 'expand', 'maximize', 'enlarge'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-fullscreen']
            ]
        ],
        [
            'name' => 'fullscreen-exit',
            'displayName' => 'Fullscreen Exit',
            'class' => 'bi-fullscreen-exit',
            'keywords' => ['fullscreen', 'exit', 'minimize', 'restore'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-fullscreen-exit']
            ]
        ],

        // 佈局與對齊
        [
            'name' => 'layout-sidebar',
            'displayName' => 'Layout Sidebar',
            'class' => 'bi-layout-sidebar',
            'keywords' => ['layout', 'sidebar', 'panel', 'structure'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-layout-sidebar']
            ]
        ],
        [
            'name' => 'layout-sidebar-inset',
            'displayName' => 'Layout Sidebar Inset',
            'class' => 'bi-layout-sidebar-inset',
            'keywords' => ['layout', 'sidebar', 'inset', 'indented'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-layout-sidebar-inset']
            ]
        ],
        [
            'name' => 'layout-sidebar-reverse',
            'displayName' => 'Layout Sidebar Reverse',
            'class' => 'bi-layout-sidebar-reverse',
            'keywords' => ['layout', 'sidebar', 'reverse', 'right'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-layout-sidebar-reverse']
            ]
        ],
        [
            'name' => 'layout-split',
            'displayName' => 'Layout Split',
            'class' => 'bi-layout-split',
            'keywords' => ['layout', 'split', 'divide', 'panel'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-layout-split']
            ]
        ],
        [
            'name' => 'layout-text-sidebar',
            'displayName' => 'Layout Text Sidebar',
            'class' => 'bi-layout-text-sidebar',
            'keywords' => ['layout', 'text', 'sidebar', 'content'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-layout-text-sidebar']
            ]
        ],
        [
            'name' => 'layout-text-window',
            'displayName' => 'Layout Text Window',
            'class' => 'bi-layout-text-window',
            'keywords' => ['layout', 'text', 'window', 'content'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-layout-text-window']
            ]
        ],
        [
            'name' => 'layout-three-columns',
            'displayName' => 'Layout Three Columns',
            'class' => 'bi-layout-three-columns',
            'keywords' => ['layout', 'three', 'columns', 'structure'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-layout-three-columns']
            ]
        ],

        // 展開收合
        [
            'name' => 'chevron-compact-down',
            'displayName' => 'Chevron Compact Down',
            'class' => 'bi-chevron-compact-down',
            'keywords' => ['chevron', 'compact', 'down', 'small'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-chevron-compact-down']
            ]
        ],
        [
            'name' => 'chevron-compact-left',
            'displayName' => 'Chevron Compact Left',
            'class' => 'bi-chevron-compact-left',
            'keywords' => ['chevron', 'compact', 'left', 'small'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-chevron-compact-left']
            ]
        ],
        [
            'name' => 'chevron-compact-right',
            'displayName' => 'Chevron Compact Right',
            'class' => 'bi-chevron-compact-right',
            'keywords' => ['chevron', 'compact', 'right', 'small'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-chevron-compact-right']
            ]
        ],
        [
            'name' => 'chevron-compact-up',
            'displayName' => 'Chevron Compact Up',
            'class' => 'bi-chevron-compact-up',
            'keywords' => ['chevron', 'compact', 'up', 'small'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-chevron-compact-up']
            ]
        ],
        [
            'name' => 'chevron-down',
            'displayName' => 'Chevron Down',
            'class' => 'bi-chevron-down',
            'keywords' => ['chevron', 'down', 'expand', 'collapse', 'dropdown'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-chevron-down']
            ]
        ],
        [
            'name' => 'chevron-up',
            'displayName' => 'Chevron Up',
            'class' => 'bi-chevron-up',
            'keywords' => ['chevron', 'up', 'expand', 'collapse', 'dropdown'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-chevron-up']
            ]
        ],
        [
            'name' => 'chevron-left',
            'displayName' => 'Chevron Left',
            'class' => 'bi-chevron-left',
            'keywords' => ['chevron', 'left', 'navigation', 'back', 'previous'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-chevron-left']
            ]
        ],
        [
            'name' => 'chevron-right',
            'displayName' => 'Chevron Right',
            'class' => 'bi-chevron-right',
            'keywords' => ['chevron', 'right', 'navigation', 'forward', 'next'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-chevron-right']
            ]
        ],
        [
            'name' => 'chevron-double-down',
            'displayName' => 'Chevron Double Down',
            'class' => 'bi-chevron-double-down',
            'keywords' => ['chevron', 'double', 'down', 'expand'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-chevron-double-down']
            ]
        ],
        [
            'name' => 'chevron-double-up',
            'displayName' => 'Chevron Double Up',
            'class' => 'bi-chevron-double-up',
            'keywords' => ['chevron', 'double', 'up', 'collapse'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-chevron-double-up']
            ]
        ],
        [
            'name' => 'chevron-double-left',
            'displayName' => 'Chevron Double Left',
            'class' => 'bi-chevron-double-left',
            'keywords' => ['chevron', 'double', 'left', 'navigation'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-chevron-double-left']
            ]
        ],
        [
            'name' => 'chevron-double-right',
            'displayName' => 'Chevron Double Right',
            'class' => 'bi-chevron-double-right',
            'keywords' => ['chevron', 'double', 'right', 'navigation'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-chevron-double-right']
            ]
        ],

        // 拖拽與操作
        [
            'name' => 'grip-horizontal',
            'displayName' => 'Grip Horizontal',
            'class' => 'bi-grip-horizontal',
            'keywords' => ['grip', 'horizontal', 'drag', 'handle', 'resize'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-grip-horizontal']
            ]
        ],
        [
            'name' => 'grip-vertical',
            'displayName' => 'Grip Vertical',
            'class' => 'bi-grip-vertical',
            'keywords' => ['grip', 'vertical', 'drag', 'handle', 'resize'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-grip-vertical']
            ]
        ],

        // 載入與處理
        [
            'name' => 'hourglass',
            'displayName' => 'Hourglass',
            'class' => 'bi-hourglass',
            'keywords' => ['hourglass', 'loading', 'wait', 'time', 'progress'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-hourglass']
            ]
        ],
        [
            'name' => 'hourglass-bottom',
            'displayName' => 'Hourglass Bottom',
            'class' => 'bi-hourglass-bottom',
            'keywords' => ['hourglass', 'bottom', 'loading', 'progress'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-hourglass-bottom']
            ]
        ],
        [
            'name' => 'hourglass-split',
            'displayName' => 'Hourglass Split',
            'class' => 'bi-hourglass-split',
            'keywords' => ['hourglass', 'split', 'loading', 'progress'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-hourglass-split']
            ]
        ],
        [
            'name' => 'hourglass-top',
            'displayName' => 'Hourglass Top',
            'class' => 'bi-hourglass-top',
            'keywords' => ['hourglass', 'top', 'loading', 'progress'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-hourglass-top']
            ]
        ],

        // 分隔線
        [
            'name' => 'hr',
            'displayName' => 'HR',
            'class' => 'bi-hr',
            'keywords' => ['hr', 'line', 'separator', 'divider', 'horizontal'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-hr']
            ]
        ],

        // 輸入與表單控制項
        [
            'name' => 'input-cursor',
            'displayName' => 'Input Cursor',
            'class' => 'bi-input-cursor',
            'keywords' => ['input', 'cursor', 'text', 'field', 'form'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-input-cursor']
            ]
        ],
        [
            'name' => 'input-cursor-text',
            'displayName' => 'Input Cursor Text',
            'class' => 'bi-input-cursor-text',
            'keywords' => ['input', 'cursor', 'text', 'field'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-input-cursor-text']
            ]
        ],
        [
            'name' => 'textarea',
            'displayName' => 'Textarea',
            'class' => 'bi-textarea',
            'keywords' => ['textarea', 'input', 'text', 'field', 'multiline'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-textarea']
            ]
        ],
        [
            'name' => 'textarea-resize',
            'displayName' => 'Textarea Resize',
            'class' => 'bi-textarea-resize',
            'keywords' => ['textarea', 'resize', 'drag', 'corner'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-textarea-resize']
            ]
        ],
        [
            'name' => 'textarea-t',
            'displayName' => 'Textarea T',
            'class' => 'bi-textarea-t',
            'keywords' => ['textarea', 't', 'text', 'editor'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-textarea-t']
            ]
        ],

        // 選擇器
        [
            'name' => 'ui-checks',
            'displayName' => 'UI Checks',
            'class' => 'bi-ui-checks',
            'keywords' => ['ui', 'checks', 'checkbox', 'selection', 'multiple'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-ui-checks']
            ]
        ],
        [
            'name' => 'ui-checks-grid',
            'displayName' => 'UI Checks Grid',
            'class' => 'bi-ui-checks-grid',
            'keywords' => ['ui', 'checks', 'grid', 'checkbox'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-ui-checks-grid']
            ]
        ],
        [
            'name' => 'ui-radios',
            'displayName' => 'UI Radios',
            'class' => 'bi-ui-radios',
            'keywords' => ['ui', 'radios', 'radio', 'selection', 'single'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-ui-radios']
            ]
        ],
        [
            'name' => 'ui-radios-grid',
            'displayName' => 'UI Radios Grid',
            'class' => 'bi-ui-radios-grid',
            'keywords' => ['ui', 'radios', 'grid', 'radio'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-ui-radios-grid']
            ]
        ],

        // 更多選單類型
        [
            'name' => 'menu-app',
            'displayName' => 'Menu App',
            'class' => 'bi-menu-app',
            'keywords' => ['menu', 'app', 'application', 'navigation'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-menu-app'],
                'solid' => ['class' => 'bi-menu-app-fill']
            ]
        ],
        [
            'name' => 'menu-down',
            'displayName' => 'Menu Down',
            'class' => 'bi-menu-down',
            'keywords' => ['menu', 'down', 'dropdown', 'navigation'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-menu-down']
            ]
        ],
        [
            'name' => 'menu-up',
            'displayName' => 'Menu Up',
            'class' => 'bi-menu-up',
            'keywords' => ['menu', 'up', 'dropup', 'navigation'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-menu-up']
            ]
        ],

        // 卡片與面板
        [
            'name' => 'card-checklist',
            'displayName' => 'Card Checklist',
            'class' => 'bi-card-checklist',
            'keywords' => ['card', 'checklist', 'todo', 'list', 'tasks'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-card-checklist']
            ]
        ],
        [
            'name' => 'card-heading',
            'displayName' => 'Card Heading',
            'class' => 'bi-card-heading',
            'keywords' => ['card', 'heading', 'title', 'header'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-card-heading']
            ]
        ],
        [
            'name' => 'card-image',
            'displayName' => 'Card Image',
            'class' => 'bi-card-image',
            'keywords' => ['card', 'image', 'picture', 'photo'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-card-image']
            ]
        ],
        [
            'name' => 'card-list',
            'displayName' => 'Card List',
            'class' => 'bi-card-list',
            'keywords' => ['card', 'list', 'items', 'content'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-card-list']
            ]
        ],
        [
            'name' => 'card-text',
            'displayName' => 'Card Text',
            'class' => 'bi-card-text',
            'keywords' => ['card', 'text', 'content', 'description'],
            'type' => 'bootstrap',
            'category' => 'ui',
            'variants' => [
                'outline' => ['class' => 'bi-card-text']
            ]
        ]
    ]
];