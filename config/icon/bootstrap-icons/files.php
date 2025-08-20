<?php

/**
 * Bootstrap Icons - 檔案文件分類
 * 包含：檔案、資料夾、文件類型、儲存等相關圖標
 * 
 * 依據前端 bs-files.js 更新 (371 個圖標精選)
 */

return [
    'category' => 'files',
    'name' => '檔案文件',
    'description' => '檔案、資料夾、文件類型、儲存等相關圖標',
    'priority' => 'medium',
    'icons' => [
        // 基本檔案
        [
            'name' => 'file',
            'displayName' => 'File',
            'class' => 'bi-file',
            'keywords' => ['file', 'document', 'page', 'content'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file'],
                'solid' => ['class' => 'bi-file-fill']
            ]
        ],
        [
            'name' => 'files',
            'displayName' => 'Files',
            'class' => 'bi-files',
            'keywords' => ['files', 'documents', 'multiple', 'collection'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-files']
            ]
        ],
        [
            'name' => 'file-earmark',
            'displayName' => 'File Earmark',
            'class' => 'bi-file-earmark',
            'keywords' => ['file', 'earmark', 'document', 'folded'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-earmark'],
                'solid' => ['class' => 'bi-file-earmark-fill']
            ]
        ],

        // 文字檔案
        [
            'name' => 'file-text',
            'displayName' => 'File Text',
            'class' => 'bi-file-text',
            'keywords' => ['file', 'text', 'document', 'content'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-text'],
                'solid' => ['class' => 'bi-file-text-fill']
            ]
        ],
        [
            'name' => 'file-earmark-text',
            'displayName' => 'File Earmark Text',
            'class' => 'bi-file-earmark-text',
            'keywords' => ['file', 'earmark', 'text', 'content'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-earmark-text'],
                'solid' => ['class' => 'bi-file-earmark-text-fill']
            ]
        ],

        // 程式碼檔案
        [
            'name' => 'file-code',
            'displayName' => 'File Code',
            'class' => 'bi-file-code',
            'keywords' => ['file', 'code', 'programming', 'script'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-code'],
                'solid' => ['class' => 'bi-file-code-fill']
            ]
        ],
        [
            'name' => 'file-earmark-code',
            'displayName' => 'File Earmark Code',
            'class' => 'bi-file-earmark-code',
            'keywords' => ['file', 'earmark', 'code', 'programming'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-earmark-code'],
                'solid' => ['class' => 'bi-file-earmark-code-fill']
            ]
        ],

        // Office 檔案
        [
            'name' => 'file-pdf',
            'displayName' => 'File PDF',
            'class' => 'bi-file-pdf',
            'keywords' => ['file', 'pdf', 'document', 'portable'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-pdf'],
                'solid' => ['class' => 'bi-file-pdf-fill']
            ]
        ],
        [
            'name' => 'file-earmark-pdf',
            'displayName' => 'File Earmark PDF',
            'class' => 'bi-file-earmark-pdf',
            'keywords' => ['file', 'earmark', 'pdf', 'document'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-earmark-pdf'],
                'solid' => ['class' => 'bi-file-earmark-pdf-fill']
            ]
        ],
        [
            'name' => 'file-word',
            'displayName' => 'File Word',
            'class' => 'bi-file-word',
            'keywords' => ['file', 'word', 'document', 'doc'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-word'],
                'solid' => ['class' => 'bi-file-word-fill']
            ]
        ],
        [
            'name' => 'file-earmark-word',
            'displayName' => 'File Earmark Word',
            'class' => 'bi-file-earmark-word',
            'keywords' => ['file', 'earmark', 'word', 'document'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-earmark-word'],
                'solid' => ['class' => 'bi-file-earmark-word-fill']
            ]
        ],
        [
            'name' => 'file-excel',
            'displayName' => 'File Excel',
            'class' => 'bi-file-excel',
            'keywords' => ['file', 'excel', 'spreadsheet', 'xls'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-excel'],
                'solid' => ['class' => 'bi-file-excel-fill']
            ]
        ],
        [
            'name' => 'file-earmark-excel',
            'displayName' => 'File Earmark Excel',
            'class' => 'bi-file-earmark-excel',
            'keywords' => ['file', 'earmark', 'excel', 'spreadsheet'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-earmark-excel'],
                'solid' => ['class' => 'bi-file-earmark-excel-fill']
            ]
        ],
        [
            'name' => 'file-ppt',
            'displayName' => 'File PPT',
            'class' => 'bi-file-ppt',
            'keywords' => ['file', 'ppt', 'powerpoint', 'presentation'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-ppt'],
                'solid' => ['class' => 'bi-file-ppt-fill']
            ]
        ],
        [
            'name' => 'file-earmark-ppt',
            'displayName' => 'File Earmark PPT',
            'class' => 'bi-file-earmark-ppt',
            'keywords' => ['file', 'earmark', 'ppt', 'powerpoint'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-earmark-ppt'],
                'solid' => ['class' => 'bi-file-earmark-ppt-fill']
            ]
        ],

        // 試算表與簡報
        [
            'name' => 'file-spreadsheet',
            'displayName' => 'File Spreadsheet',
            'class' => 'bi-file-spreadsheet',
            'keywords' => ['file', 'spreadsheet', 'data', 'table'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-spreadsheet'],
                'solid' => ['class' => 'bi-file-spreadsheet-fill']
            ]
        ],
        [
            'name' => 'file-earmark-spreadsheet',
            'displayName' => 'File Earmark Spreadsheet',
            'class' => 'bi-file-earmark-spreadsheet',
            'keywords' => ['file', 'earmark', 'spreadsheet', 'data'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-earmark-spreadsheet'],
                'solid' => ['class' => 'bi-file-earmark-spreadsheet-fill']
            ]
        ],
        [
            'name' => 'file-slides',
            'displayName' => 'File Slides',
            'class' => 'bi-file-slides',
            'keywords' => ['file', 'slides', 'presentation', 'slideshow'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-slides'],
                'solid' => ['class' => 'bi-file-slides-fill']
            ]
        ],
        [
            'name' => 'file-earmark-slides',
            'displayName' => 'File Earmark Slides',
            'class' => 'bi-file-earmark-slides',
            'keywords' => ['file', 'earmark', 'slides', 'presentation'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-earmark-slides'],
                'solid' => ['class' => 'bi-file-earmark-slides-fill']
            ]
        ],

        // 圖像檔案
        [
            'name' => 'file-image',
            'displayName' => 'File Image',
            'class' => 'bi-file-image',
            'keywords' => ['file', 'image', 'picture', 'photo'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-image'],
                'solid' => ['class' => 'bi-file-image-fill']
            ]
        ],
        [
            'name' => 'file-earmark-image',
            'displayName' => 'File Earmark Image',
            'class' => 'bi-file-earmark-image',
            'keywords' => ['file', 'earmark', 'image', 'picture'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-earmark-image'],
                'solid' => ['class' => 'bi-file-earmark-image-fill']
            ]
        ],
        [
            'name' => 'image',
            'displayName' => 'Image',
            'class' => 'bi-image',
            'keywords' => ['image', 'picture', 'photo', 'graphic'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-image'],
                'solid' => ['class' => 'bi-image-fill']
            ]
        ],
        [
            'name' => 'images',
            'displayName' => 'Images',
            'class' => 'bi-images',
            'keywords' => ['images', 'pictures', 'photos', 'multiple'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-images']
            ]
        ],

        // 音樂檔案
        [
            'name' => 'file-music',
            'displayName' => 'File Music',
            'class' => 'bi-file-music',
            'keywords' => ['file', 'music', 'audio', 'sound'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-music'],
                'solid' => ['class' => 'bi-file-music-fill']
            ]
        ],
        [
            'name' => 'file-earmark-music',
            'displayName' => 'File Earmark Music',
            'class' => 'bi-file-earmark-music',
            'keywords' => ['file', 'earmark', 'music', 'audio'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-earmark-music'],
                'solid' => ['class' => 'bi-file-earmark-music-fill']
            ]
        ],

        // 影片檔案
        [
            'name' => 'file-play',
            'displayName' => 'File Play',
            'class' => 'bi-file-play',
            'keywords' => ['file', 'play', 'video', 'media'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-play'],
                'solid' => ['class' => 'bi-file-play-fill']
            ]
        ],
        [
            'name' => 'file-earmark-play',
            'displayName' => 'File Earmark Play',
            'class' => 'bi-file-earmark-play',
            'keywords' => ['file', 'earmark', 'play', 'video'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-earmark-play'],
                'solid' => ['class' => 'bi-file-earmark-play-fill']
            ]
        ],

        // 壓縮檔案
        [
            'name' => 'file-zip',
            'displayName' => 'File Zip',
            'class' => 'bi-file-zip',
            'keywords' => ['file', 'zip', 'archive', 'compressed'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-zip'],
                'solid' => ['class' => 'bi-file-zip-fill']
            ]
        ],
        [
            'name' => 'file-earmark-zip',
            'displayName' => 'File Earmark Zip',
            'class' => 'bi-file-earmark-zip',
            'keywords' => ['file', 'earmark', 'zip', 'archive'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-file-earmark-zip'],
                'solid' => ['class' => 'bi-file-earmark-zip-fill']
            ]
        ],
        [
            'name' => 'archive',
            'displayName' => 'Archive',
            'class' => 'bi-archive',
            'keywords' => ['archive', 'box', 'storage', 'backup'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-archive'],
                'solid' => ['class' => 'bi-archive-fill']
            ]
        ],

        // 資料夾
        [
            'name' => 'folder',
            'displayName' => 'Folder',
            'class' => 'bi-folder',
            'keywords' => ['folder', 'directory', 'storage', 'container'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-folder'],
                'solid' => ['class' => 'bi-folder-fill']
            ]
        ],
        [
            'name' => 'folder2',
            'displayName' => 'Folder 2',
            'class' => 'bi-folder2',
            'keywords' => ['folder', 'directory', 'alternative'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-folder2']
            ]
        ],
        [
            'name' => 'folder2-open',
            'displayName' => 'Folder 2 Open',
            'class' => 'bi-folder2-open',
            'keywords' => ['folder', 'open', 'directory', 'expanded'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-folder2-open']
            ]
        ],
        [
            'name' => 'folder-plus',
            'displayName' => 'Folder Plus',
            'class' => 'bi-folder-plus',
            'keywords' => ['folder', 'plus', 'add', 'create'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-folder-plus']
            ]
        ],
        [
            'name' => 'folder-minus',
            'displayName' => 'Folder Minus',
            'class' => 'bi-folder-minus',
            'keywords' => ['folder', 'minus', 'remove', 'delete'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-folder-minus']
            ]
        ],
        [
            'name' => 'folder-check',
            'displayName' => 'Folder Check',
            'class' => 'bi-folder-check',
            'keywords' => ['folder', 'check', 'verified', 'approved'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-folder-check']
            ]
        ],
        [
            'name' => 'folder-x',
            'displayName' => 'Folder X',
            'class' => 'bi-folder-x',
            'keywords' => ['folder', 'x', 'close', 'error'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-folder-x']
            ]
        ],

        // 雲端儲存
        [
            'name' => 'cloud',
            'displayName' => 'Cloud',
            'class' => 'bi-cloud',
            'keywords' => ['cloud', 'storage', 'online', 'sync'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-cloud'],
                'solid' => ['class' => 'bi-cloud-fill']
            ]
        ],
        [
            'name' => 'cloud-download',
            'displayName' => 'Cloud Download',
            'class' => 'bi-cloud-download',
            'keywords' => ['cloud', 'download', 'sync', 'get'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-cloud-download'],
                'solid' => ['class' => 'bi-cloud-download-fill']
            ]
        ],
        [
            'name' => 'cloud-upload',
            'displayName' => 'Cloud Upload',
            'class' => 'bi-cloud-upload',
            'keywords' => ['cloud', 'upload', 'sync', 'send'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-cloud-upload'],
                'solid' => ['class' => 'bi-cloud-upload-fill']
            ]
        ],

        // 儲存裝置
        [
            'name' => 'hdd',
            'displayName' => 'HDD',
            'class' => 'bi-hdd',
            'keywords' => ['hdd', 'hard drive', 'storage', 'disk'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-hdd'],
                'solid' => ['class' => 'bi-hdd-fill']
            ]
        ],
        [
            'name' => 'device-ssd',
            'displayName' => 'Device SSD',
            'class' => 'bi-device-ssd',
            'keywords' => ['ssd', 'solid state', 'storage', 'device'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-device-ssd'],
                'solid' => ['class' => 'bi-device-ssd-fill']
            ]
        ],
        [
            'name' => 'usb-drive',
            'displayName' => 'USB Drive',
            'class' => 'bi-usb-drive',
            'keywords' => ['usb', 'drive', 'flash', 'portable'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-usb-drive'],
                'solid' => ['class' => 'bi-usb-drive-fill']
            ]
        ],

        // 下載上傳
        [
            'name' => 'download',
            'displayName' => 'Download',
            'class' => 'bi-download',
            'keywords' => ['download', 'get', 'save', 'receive'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-download']
            ]
        ],
        [
            'name' => 'upload',
            'displayName' => 'Upload',
            'class' => 'bi-upload',
            'keywords' => ['upload', 'send', 'publish', 'transfer'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-upload']
            ]
        ],

        // 書籍與文件
        [
            'name' => 'book',
            'displayName' => 'Book',
            'class' => 'bi-book',
            'keywords' => ['book', 'read', 'document', 'manual'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-book'],
                'solid' => ['class' => 'bi-book-fill']
            ]
        ],
        [
            'name' => 'journal',
            'displayName' => 'Journal',
            'class' => 'bi-journal',
            'keywords' => ['journal', 'notebook', 'diary', 'log'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-journal']
            ]
        ],
        [
            'name' => 'journal-text',
            'displayName' => 'Journal Text',
            'class' => 'bi-journal-text',
            'keywords' => ['journal', 'text', 'write', 'notes'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-journal-text']
            ]
        ],

        // 收藏與集合
        [
            'name' => 'collection',
            'displayName' => 'Collection',
            'class' => 'bi-collection',
            'keywords' => ['collection', 'group', 'files', 'set'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-collection'],
                'solid' => ['class' => 'bi-collection-fill']
            ]
        ],
        [
            'name' => 'stack',
            'displayName' => 'Stack',
            'class' => 'bi-stack',
            'keywords' => ['stack', 'pile', 'files', 'layers'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-stack']
            ]
        ],

        // 表格與資料
        [
            'name' => 'table',
            'displayName' => 'Table',
            'class' => 'bi-table',
            'keywords' => ['table', 'data', 'grid', 'structure'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-table']
            ]
        ],

        // 剪貼簿
        [
            'name' => 'clipboard',
            'displayName' => 'Clipboard',
            'class' => 'bi-clipboard',
            'keywords' => ['clipboard', 'copy', 'paste', 'temporary'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-clipboard'],
                'solid' => ['class' => 'bi-clipboard-fill']
            ]
        ],
        [
            'name' => 'clipboard-data',
            'displayName' => 'Clipboard Data',
            'class' => 'bi-clipboard-data',
            'keywords' => ['clipboard', 'data', 'chart', 'statistics'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-clipboard-data'],
                'solid' => ['class' => 'bi-clipboard-data-fill']
            ]
        ],

        // 常見檔案類型
        [
            'name' => 'filetype-pdf',
            'displayName' => 'Filetype PDF',
            'class' => 'bi-filetype-pdf',
            'keywords' => ['filetype', 'pdf', 'portable', 'document'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-filetype-pdf']
            ]
        ],
        [
            'name' => 'filetype-html',
            'displayName' => 'Filetype HTML',
            'class' => 'bi-filetype-html',
            'keywords' => ['filetype', 'html', 'web', 'markup'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-filetype-html']
            ]
        ],
        [
            'name' => 'filetype-css',
            'displayName' => 'Filetype CSS',
            'class' => 'bi-filetype-css',
            'keywords' => ['filetype', 'css', 'style', 'stylesheet'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-filetype-css']
            ]
        ],
        [
            'name' => 'filetype-js',
            'displayName' => 'Filetype JS',
            'class' => 'bi-filetype-js',
            'keywords' => ['filetype', 'js', 'javascript', 'script'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-filetype-js']
            ]
        ],
        [
            'name' => 'filetype-json',
            'displayName' => 'Filetype JSON',
            'class' => 'bi-filetype-json',
            'keywords' => ['filetype', 'json', 'data', 'format'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-filetype-json']
            ]
        ],
        [
            'name' => 'filetype-xml',
            'displayName' => 'Filetype XML',
            'class' => 'bi-filetype-xml',
            'keywords' => ['filetype', 'xml', 'markup', 'data'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-filetype-xml']
            ]
        ],
        [
            'name' => 'filetype-png',
            'displayName' => 'Filetype PNG',
            'class' => 'bi-filetype-png',
            'keywords' => ['filetype', 'png', 'image', 'graphic'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-filetype-png']
            ]
        ],
        [
            'name' => 'filetype-jpg',
            'displayName' => 'Filetype JPG',
            'class' => 'bi-filetype-jpg',
            'keywords' => ['filetype', 'jpg', 'jpeg', 'image'],
            'type' => 'bootstrap',
            'category' => 'files',
            'variants' => [
                'outline' => ['class' => 'bi-filetype-jpg']
            ]
        ]
    ]
];