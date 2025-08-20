#!/usr/bin/env python3
"""
Bootstrap Icons 轉換工具
將前端 JavaScript 檔案轉換為後端 PHP 配置檔案
"""

import re
import os
import json
from pathlib import Path

def parse_js_icons(js_content):
    """解析 JavaScript 檔案中的圖標定義"""
    icons = []
    
    # 提取圖標定義
    pattern = r"{\s*name:\s*['\"]([^'\"]*)['\"],\s*class:\s*['\"]([^'\"]*)['\"]"
    matches = re.findall(pattern, js_content)
    
    for name, css_class in matches:
        # 生成 display name 和 icon name
        display_name = name
        icon_name = css_class.replace('bi-', '').replace('-fill', '')
        
        # 生成關鍵字
        keywords = generate_keywords(icon_name, display_name)
        
        # 檢查是否有 fill 變體
        variants = {'outline': {'class': css_class}}
        if '-fill' in css_class:
            base_class = css_class.replace('-fill', '')
            variants = {
                'outline': {'class': base_class},
                'solid': {'class': css_class}
            }
        elif not css_class.endswith('-fill'):
            # 檢查是否存在對應的 fill 版本
            fill_class = css_class + '-fill'
            if any(fill_class in match[1] for match in matches):
                variants['solid'] = {'class': fill_class}
        
        icons.append({
            'name': icon_name,
            'displayName': display_name,
            'class': css_class,
            'keywords': keywords,
            'variants': variants
        })
    
    return icons

def generate_keywords(icon_name, display_name):
    """生成關鍵字"""
    keywords = []
    
    # 基於圖標名稱生成關鍵字
    parts = icon_name.replace('-', ' ').split()
    keywords.extend(parts)
    
    # 基於顯示名稱生成關鍵字  
    display_parts = display_name.lower().replace('-', ' ').split()
    keywords.extend(display_parts)
    
    # 移除重複並返回
    return list(set(keywords))

def generate_php_config(category, icons, description, priority='medium'):
    """生成 PHP 配置檔案內容"""
    
    php_content = f'''<?php

/**
 * Bootstrap Icons - {category} 分類
 * {description}
 * 
 * 完整同步前端 bs-{category.lower()}.js (共 {len(icons)} 個圖標)
 */

return [
    'category' => '{category.lower()}',
    'name' => '{category}',
    'description' => '{description}',
    'priority' => '{priority}',
    'icons' => [
'''
    
    for icon in icons:
        variants_str = format_variants(icon['variants'])
        keywords_str = format_keywords(icon['keywords'])
        
        php_content += f'''        [
            'name' => '{icon['name']}',
            'displayName' => '{icon['displayName']}',
            'class' => '{icon['class']}',
            'keywords' => {keywords_str},
            'type' => 'bootstrap',
            'category' => '{category.lower()}',
            'variants' => {variants_str}
        ],
'''
    
    php_content += '''    ]
];
'''
    
    return php_content

def format_variants(variants):
    """格式化變體配置"""
    variant_lines = []
    for variant_type, variant_data in variants.items():
        variant_lines.append(f"                '{variant_type}' => ['class' => '{variant_data['class']}']")
    return "[\n" + ",\n".join(variant_lines) + "\n            ]"

def format_keywords(keywords):
    """格式化關鍵字陣列"""
    keyword_strings = [f"'{kw}'" for kw in keywords[:5]]  # 限制關鍵字數量
    return "[" + ", ".join(keyword_strings) + "]"

def main():
    """主程式"""
    categories = {
        'general': {
            'description': '最常用的基礎圖標',
            'priority': 'immediate'
        },
        'ui': {
            'description': '使用者介面元件與控制項', 
            'priority': 'high'
        },
        'communications': {
            'description': '郵件、電話、聊天等通訊圖標',
            'priority': 'high'
        },
        'files': {
            'description': '檔案、資料夾、文件類型相關圖標',
            'priority': 'medium'
        },
        'others': {
            'description': '商業、地理、工具、天氣等其他圖標',
            'priority': 'low'
        },
        'people': {
            'description': '人員、群組、身體部位相關圖標',
            'priority': 'medium'
        },
        'media': {
            'description': '播放、音量、錄製等媒體控制圖標',
            'priority': 'medium'
        },
        'alphanumeric': {
            'description': '數字、字母、文字排版相關圖標',
            'priority': 'low'
        }
    }
    
    base_path = Path('/Library/Workspace/protype/project/purple-desk')
    
    for category, config in categories.items():
        print(f"處理 {category} 分類...")
        
        # 讀取前端 JS 檔案
        js_file = base_path / 'resources/js/utils/icons' / f'bs-{category}.js'
        if not js_file.exists():
            print(f"警告: {js_file} 不存在")
            continue
            
        with open(js_file, 'r', encoding='utf-8') as f:
            js_content = f.read()
        
        # 解析圖標
        icons = parse_js_icons(js_content)
        print(f"找到 {len(icons)} 個圖標")
        
        # 生成 PHP 配置
        php_content = generate_php_config(
            category.title(), 
            icons, 
            config['description'],
            config['priority']
        )
        
        # 寫入 PHP 檔案
        php_file = base_path / 'config/icon/bootstrap-icons' / f'{category}.php'
        with open(php_file, 'w', encoding='utf-8') as f:
            f.write(php_content)
        
        print(f"已生成 {php_file}")

if __name__ == '__main__':
    main()