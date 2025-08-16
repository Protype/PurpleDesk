<?php

/**
 * Heroicon Configuration Index
 * 基於 Heroicons v1 圖標庫
 */

return [
    'categories' => [
        'navigation' => [
            'id' => 'navigation',
            'name' => 'Navigation',
            'icon' => 'arrow-right',
            'priority' => 'immediate',
            'file' => 'navigation.php',
        ],
        'communication' => [
            'id' => 'communication',
            'name' => 'Communication',
            'icon' => 'chat',
            'priority' => 'high',
            'file' => 'communication.php',
        ],
        'interface' => [
            'id' => 'interface',
            'name' => 'Interface',
            'icon' => 'adjustments',
            'priority' => 'high',
            'file' => 'interface.php',
        ],
        'document' => [
            'id' => 'document',
            'name' => 'Document',
            'icon' => 'document',
            'priority' => 'medium',
            'file' => 'document.php',
        ],
        'device' => [
            'id' => 'device',
            'name' => 'Device',
            'icon' => 'desktop-computer',
            'priority' => 'medium',
            'file' => 'device.php',
        ],
        'finance' => [
            'id' => 'finance',
            'name' => 'Finance',
            'icon' => 'currency-dollar',
            'priority' => 'low',
            'file' => 'finance.php',
        ],
        'media' => [
            'id' => 'media',
            'name' => 'Media',
            'icon' => 'photograph',
            'priority' => 'low',
            'file' => 'media.php',
        ],
        'misc' => [
            'id' => 'misc',
            'name' => 'Miscellaneous',
            'icon' => 'sparkles',
            'priority' => 'low',
            'file' => 'misc.php',
        ],
    ],
    'stats' => [
        'total_icons' => 230,
        'total_categories' => 8,
        'styles' => ['outline', 'solid'],
    ],
];