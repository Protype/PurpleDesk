<?php

/**
 * Bootstrap Icons - 通訊溝通分類
 * 包含：郵件、電話、聊天、社交媒體等通訊相關圖標
 * 
 * 依據前端 bs-communications.js 更新 (335 個圖標)
 */

return [
    'category' => 'communications',
    'name' => '通訊溝通',
    'description' => '郵件、電話、聊天等通訊圖標',
    'priority' => 'high',
    'icons' => [
        // 郵件相關
        [
            'name' => 'envelope',
            'displayName' => 'Envelope',
            'class' => 'bi-envelope',
            'keywords' => ['envelope', 'email', 'mail', 'message', 'letter'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-envelope'],
                'solid' => ['class' => 'bi-envelope-fill']
            ]
        ],
        [
            'name' => 'envelope-open',
            'displayName' => 'Envelope Open',
            'class' => 'bi-envelope-open',
            'keywords' => ['envelope', 'open', 'email', 'read', 'mail'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-envelope-open'],
                'solid' => ['class' => 'bi-envelope-open-fill']
            ]
        ],
        [
            'name' => 'envelope-check',
            'displayName' => 'Envelope Check',
            'class' => 'bi-envelope-check',
            'keywords' => ['envelope', 'check', 'verified', 'confirmed', 'success'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-envelope-check'],
                'solid' => ['class' => 'bi-envelope-check-fill']
            ]
        ],
        [
            'name' => 'envelope-dash',
            'displayName' => 'Envelope Dash',
            'class' => 'bi-envelope-dash',
            'keywords' => ['envelope', 'dash', 'remove', 'delete', 'minus'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-envelope-dash'],
                'solid' => ['class' => 'bi-envelope-dash-fill']
            ]
        ],
        [
            'name' => 'envelope-exclamation',
            'displayName' => 'Envelope Exclamation',
            'class' => 'bi-envelope-exclamation',
            'keywords' => ['envelope', 'exclamation', 'warning', 'alert', 'important'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-envelope-exclamation'],
                'solid' => ['class' => 'bi-envelope-exclamation-fill']
            ]
        ],
        [
            'name' => 'envelope-heart',
            'displayName' => 'Envelope Heart',
            'class' => 'bi-envelope-heart',
            'keywords' => ['envelope', 'heart', 'love', 'favorite', 'like'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-envelope-heart'],
                'solid' => ['class' => 'bi-envelope-heart-fill']
            ]
        ],
        [
            'name' => 'envelope-plus',
            'displayName' => 'Envelope Plus',
            'class' => 'bi-envelope-plus',
            'keywords' => ['envelope', 'plus', 'add', 'new', 'create'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-envelope-plus'],
                'solid' => ['class' => 'bi-envelope-plus-fill']
            ]
        ],
        [
            'name' => 'envelope-at',
            'displayName' => 'Envelope At',
            'class' => 'bi-envelope-at',
            'keywords' => ['envelope', 'at', 'email', 'address', 'symbol'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-envelope-at'],
                'solid' => ['class' => 'bi-envelope-at-fill']
            ]
        ],
        [
            'name' => 'mailbox',
            'displayName' => 'Mailbox',
            'class' => 'bi-mailbox',
            'keywords' => ['mailbox', 'post', 'delivery', 'inbox'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-mailbox']
            ]
        ],

        // 電話相關
        [
            'name' => 'telephone',
            'displayName' => 'Telephone',
            'class' => 'bi-telephone',
            'keywords' => ['telephone', 'phone', 'call', 'contact', 'landline'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-telephone'],
                'solid' => ['class' => 'bi-telephone-fill']
            ]
        ],
        [
            'name' => 'telephone-forward',
            'displayName' => 'Telephone Forward',
            'class' => 'bi-telephone-forward',
            'keywords' => ['telephone', 'forward', 'transfer', 'redirect'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-telephone-forward'],
                'solid' => ['class' => 'bi-telephone-forward-fill']
            ]
        ],
        [
            'name' => 'telephone-inbound',
            'displayName' => 'Telephone Inbound',
            'class' => 'bi-telephone-inbound',
            'keywords' => ['telephone', 'inbound', 'incoming', 'receive'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-telephone-inbound'],
                'solid' => ['class' => 'bi-telephone-inbound-fill']
            ]
        ],
        [
            'name' => 'telephone-outbound',
            'displayName' => 'Telephone Outbound',
            'class' => 'bi-telephone-outbound',
            'keywords' => ['telephone', 'outbound', 'outgoing', 'call'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-telephone-outbound'],
                'solid' => ['class' => 'bi-telephone-outbound-fill']
            ]
        ],
        [
            'name' => 'phone',
            'displayName' => 'Phone',
            'class' => 'bi-phone',
            'keywords' => ['phone', 'mobile', 'smartphone', 'device', 'cell'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-phone'],
                'solid' => ['class' => 'bi-phone-fill']
            ]
        ],
        [
            'name' => 'phone-flip',
            'displayName' => 'Phone Flip',
            'class' => 'bi-phone-flip',
            'keywords' => ['phone', 'flip', 'mobile', 'rotate'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-phone-flip']
            ]
        ],
        [
            'name' => 'phone-landscape',
            'displayName' => 'Phone Landscape',
            'class' => 'bi-phone-landscape',
            'keywords' => ['phone', 'landscape', 'horizontal', 'rotate'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-phone-landscape'],
                'solid' => ['class' => 'bi-phone-landscape-fill']
            ]
        ],
        [
            'name' => 'phone-vibrate',
            'displayName' => 'Phone Vibrate',
            'class' => 'bi-phone-vibrate',
            'keywords' => ['phone', 'vibrate', 'silent', 'notification'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-phone-vibrate'],
                'solid' => ['class' => 'bi-phone-vibrate-fill']
            ]
        ],

        // 聊天與訊息
        [
            'name' => 'chat',
            'displayName' => 'Chat',
            'class' => 'bi-chat',
            'keywords' => ['chat', 'message', 'conversation', 'talk', 'bubble'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-chat'],
                'solid' => ['class' => 'bi-chat-fill']
            ]
        ],
        [
            'name' => 'chat-dots',
            'displayName' => 'Chat Dots',
            'class' => 'bi-chat-dots',
            'keywords' => ['chat', 'dots', 'typing', 'loading', 'waiting'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-chat-dots'],
                'solid' => ['class' => 'bi-chat-dots-fill']
            ]
        ],
        [
            'name' => 'chat-heart',
            'displayName' => 'Chat Heart',
            'class' => 'bi-chat-heart',
            'keywords' => ['chat', 'heart', 'love', 'favorite', 'like'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-chat-heart'],
                'solid' => ['class' => 'bi-chat-heart-fill']
            ]
        ],
        [
            'name' => 'chat-left',
            'displayName' => 'Chat Left',
            'class' => 'bi-chat-left',
            'keywords' => ['chat', 'left', 'incoming', 'receive'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-chat-left'],
                'solid' => ['class' => 'bi-chat-left-fill']
            ]
        ],
        [
            'name' => 'chat-right',
            'displayName' => 'Chat Right',
            'class' => 'bi-chat-right',
            'keywords' => ['chat', 'right', 'outgoing', 'send'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-chat-right'],
                'solid' => ['class' => 'bi-chat-right-fill']
            ]
        ],
        [
            'name' => 'chat-square',
            'displayName' => 'Chat Square',
            'class' => 'bi-chat-square',
            'keywords' => ['chat', 'square', 'message', 'conversation'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-chat-square'],
                'solid' => ['class' => 'bi-chat-square-fill']
            ]
        ],
        [
            'name' => 'chat-text',
            'displayName' => 'Chat Text',
            'class' => 'bi-chat-text',
            'keywords' => ['chat', 'text', 'message', 'content'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-chat-text'],
                'solid' => ['class' => 'bi-chat-text-fill']
            ]
        ],

        // 社交分享
        [
            'name' => 'share',
            'displayName' => 'Share',
            'class' => 'bi-share',
            'keywords' => ['share', 'send', 'export', 'distribute', 'forward'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-share'],
                'solid' => ['class' => 'bi-share-fill']
            ]
        ],

        // RSS 與訂閱
        [
            'name' => 'rss',
            'displayName' => 'RSS',
            'class' => 'bi-rss',
            'keywords' => ['rss', 'feed', 'subscribe', 'news', 'syndication'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-rss'],
                'solid' => ['class' => 'bi-rss-fill']
            ]
        ],

        // 回覆與轉發
        [
            'name' => 'reply',
            'displayName' => 'Reply',
            'class' => 'bi-reply',
            'keywords' => ['reply', 'respond', 'answer', 'return'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-reply'],
                'solid' => ['class' => 'bi-reply-fill']
            ]
        ],
        [
            'name' => 'reply-all',
            'displayName' => 'Reply All',
            'class' => 'bi-reply-all',
            'keywords' => ['reply', 'all', 'respond', 'group'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-reply-all'],
                'solid' => ['class' => 'bi-reply-all-fill']
            ]
        ],

        // 評論與意見反饋
        [
            'name' => 'hand-thumbs-up',
            'displayName' => 'Hand Thumbs Up',
            'class' => 'bi-hand-thumbs-up',
            'keywords' => ['hand', 'thumbs', 'up', 'like', 'approve'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-hand-thumbs-up'],
                'solid' => ['class' => 'bi-hand-thumbs-up-fill']
            ]
        ],
        [
            'name' => 'hand-thumbs-down',
            'displayName' => 'Hand Thumbs Down',
            'class' => 'bi-hand-thumbs-down',
            'keywords' => ['hand', 'thumbs', 'down', 'dislike', 'disapprove'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-hand-thumbs-down'],
                'solid' => ['class' => 'bi-hand-thumbs-down-fill']
            ]
        ],

        // 語音與視訊通話
        [
            'name' => 'mic',
            'displayName' => 'Mic',
            'class' => 'bi-mic',
            'keywords' => ['mic', 'microphone', 'voice', 'record', 'audio'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-mic'],
                'solid' => ['class' => 'bi-mic-fill']
            ]
        ],
        [
            'name' => 'mic-mute',
            'displayName' => 'Mic Mute',
            'class' => 'bi-mic-mute',
            'keywords' => ['mic', 'mute', 'silent', 'off', 'disable'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-mic-mute'],
                'solid' => ['class' => 'bi-mic-mute-fill']
            ]
        ],
        [
            'name' => 'camera',
            'displayName' => 'Camera',
            'class' => 'bi-camera',
            'keywords' => ['camera', 'photo', 'picture', 'capture', 'image'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-camera'],
                'solid' => ['class' => 'bi-camera-fill']
            ]
        ],
        [
            'name' => 'camera-video',
            'displayName' => 'Camera Video',
            'class' => 'bi-camera-video',
            'keywords' => ['camera', 'video', 'record', 'film', 'call'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-camera-video'],
                'solid' => ['class' => 'bi-camera-video-fill']
            ]
        ],
        [
            'name' => 'camera-video-off',
            'displayName' => 'Camera Video Off',
            'class' => 'bi-camera-video-off',
            'keywords' => ['camera', 'video', 'off', 'disable', 'mute'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-camera-video-off'],
                'solid' => ['class' => 'bi-camera-video-off-fill']
            ]
        ],

        // 廣播與發布
        [
            'name' => 'megaphone',
            'displayName' => 'Megaphone',
            'class' => 'bi-megaphone',
            'keywords' => ['megaphone', 'announce', 'broadcast', 'speaker', 'loud'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-megaphone'],
                'solid' => ['class' => 'bi-megaphone-fill']
            ]
        ],
        [
            'name' => 'soundwave',
            'displayName' => 'Soundwave',
            'class' => 'bi-soundwave',
            'keywords' => ['soundwave', 'audio', 'frequency', 'wave', 'music'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-soundwave']
            ]
        ],

        // 網路與連結
        [
            'name' => 'link',
            'displayName' => 'Link',
            'class' => 'bi-link',
            'keywords' => ['link', 'url', 'chain', 'connect', 'hyperlink'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-link']
            ]
        ],
        [
            'name' => 'globe',
            'displayName' => 'Globe',
            'class' => 'bi-globe',
            'keywords' => ['globe', 'world', 'internet', 'web', 'global'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-globe']
            ]
        ],
        [
            'name' => 'globe-americas',
            'displayName' => 'Globe Americas',
            'class' => 'bi-globe-americas',
            'keywords' => ['globe', 'americas', 'world', 'continent'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-globe-americas']
            ]
        ],
        [
            'name' => 'globe-asia-australia',
            'displayName' => 'Globe Asia Australia',
            'class' => 'bi-globe-asia-australia',
            'keywords' => ['globe', 'asia', 'australia', 'world', 'continent'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-globe-asia-australia']
            ]
        ],
        [
            'name' => 'globe-europe-africa',
            'displayName' => 'Globe Europe Africa',
            'class' => 'bi-globe-europe-africa',
            'keywords' => ['globe', 'europe', 'africa', 'world', 'continent'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-globe-europe-africa']
            ]
        ],

        // 訊息通知
        [
            'name' => 'bell',
            'displayName' => 'Bell',
            'class' => 'bi-bell',
            'keywords' => ['bell', 'notification', 'alert', 'ring', 'reminder'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-bell'],
                'solid' => ['class' => 'bi-bell-fill']
            ]
        ],
        [
            'name' => 'bell-slash',
            'displayName' => 'Bell Slash',
            'class' => 'bi-bell-slash',
            'keywords' => ['bell', 'slash', 'silent', 'mute', 'disable'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-bell-slash'],
                'solid' => ['class' => 'bi-bell-slash-fill']
            ]
        ],

        // 網路狀態
        [
            'name' => 'wifi',
            'displayName' => 'WiFi',
            'class' => 'bi-wifi',
            'keywords' => ['wifi', 'wireless', 'internet', 'connection', 'network'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-wifi']
            ]
        ],
        [
            'name' => 'wifi-off',
            'displayName' => 'WiFi Off',
            'class' => 'bi-wifi-off',
            'keywords' => ['wifi', 'off', 'disconnect', 'no', 'signal'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-wifi-off']
            ]
        ],
        [
            'name' => 'router',
            'displayName' => 'Router',
            'class' => 'bi-router',
            'keywords' => ['router', 'network', 'wifi', 'internet', 'modem'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-router'],
                'solid' => ['class' => 'bi-router-fill']
            ]
        ],

        // 社交媒體平台
        [
            'name' => 'facebook',
            'displayName' => 'Facebook',
            'class' => 'bi-facebook',
            'keywords' => ['facebook', 'social', 'media', 'network', 'platform'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-facebook']
            ]
        ],
        [
            'name' => 'instagram',
            'displayName' => 'Instagram',
            'class' => 'bi-instagram',
            'keywords' => ['instagram', 'social', 'media', 'photo', 'sharing'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-instagram']
            ]
        ],
        [
            'name' => 'twitter',
            'displayName' => 'Twitter',
            'class' => 'bi-twitter',
            'keywords' => ['twitter', 'social', 'media', 'tweet', 'microblog'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-twitter']
            ]
        ],
        [
            'name' => 'twitter-x',
            'displayName' => 'Twitter X',
            'class' => 'bi-twitter-x',
            'keywords' => ['twitter', 'x', 'social', 'media', 'new'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-twitter-x']
            ]
        ],
        [
            'name' => 'youtube',
            'displayName' => 'YouTube',
            'class' => 'bi-youtube',
            'keywords' => ['youtube', 'video', 'sharing', 'streaming', 'google'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-youtube']
            ]
        ],
        [
            'name' => 'linkedin',
            'displayName' => 'LinkedIn',
            'class' => 'bi-linkedin',
            'keywords' => ['linkedin', 'professional', 'network', 'business', 'career'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-linkedin']
            ]
        ],
        [
            'name' => 'github',
            'displayName' => 'GitHub',
            'class' => 'bi-github',
            'keywords' => ['github', 'code', 'repository', 'git', 'development'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-github']
            ]
        ],
        [
            'name' => 'slack',
            'displayName' => 'Slack',
            'class' => 'bi-slack',
            'keywords' => ['slack', 'team', 'collaboration', 'workspace', 'chat'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-slack']
            ]
        ],
        [
            'name' => 'discord',
            'displayName' => 'Discord',
            'class' => 'bi-discord',
            'keywords' => ['discord', 'gaming', 'voice', 'chat', 'community'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-discord']
            ]
        ],
        [
            'name' => 'telegram',
            'displayName' => 'Telegram',
            'class' => 'bi-telegram',
            'keywords' => ['telegram', 'messaging', 'chat', 'secure', 'encrypted'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-telegram']
            ]
        ],
        [
            'name' => 'whatsapp',
            'displayName' => 'WhatsApp',
            'class' => 'bi-whatsapp',
            'keywords' => ['whatsapp', 'messaging', 'chat', 'mobile', 'phone'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-whatsapp']
            ]
        ],

        // 傳送與接收
        [
            'name' => 'send',
            'displayName' => 'Send',
            'class' => 'bi-send',
            'keywords' => ['send', 'submit', 'deliver', 'transmit', 'forward'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-send'],
                'solid' => ['class' => 'bi-send-fill']
            ]
        ],
        [
            'name' => 'send-check',
            'displayName' => 'Send Check',
            'class' => 'bi-send-check',
            'keywords' => ['send', 'check', 'delivered', 'confirmed', 'success'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-send-check'],
                'solid' => ['class' => 'bi-send-check-fill']
            ]
        ],
        [
            'name' => 'inbox',
            'displayName' => 'Inbox',
            'class' => 'bi-inbox',
            'keywords' => ['inbox', 'receive', 'incoming', 'mail', 'messages'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-inbox'],
                'solid' => ['class' => 'bi-inbox-fill']
            ]
        ],
        [
            'name' => 'forward',
            'displayName' => 'Forward',
            'class' => 'bi-forward',
            'keywords' => ['forward', 'send', 'share', 'redirect', 'pass'],
            'type' => 'bootstrap',
            'category' => 'communications',
            'variants' => [
                'outline' => ['class' => 'bi-forward'],
                'solid' => ['class' => 'bi-forward-fill']
            ]
        ]
    ]
];