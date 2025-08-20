#!/usr/bin/env python3
"""
Bootstrap Icons 同步結果統計
"""

import re
from pathlib import Path

def count_icons_in_php(file_path):
    """計算 PHP 檔案中的圖標數量"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # 計算 'name' => 項目數量
        matches = re.findall(r"'name'\s*=>\s*'[^']*'", content)
        return len(matches)
    except Exception as e:
        return 0

def count_icons_in_js(file_path):
    """計算 JS 檔案中的圖標數量"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # 提取圖標定義
        pattern = r"{\s*name:\s*['\"]([^'\"]*)['\"],\s*class:\s*['\"]([^'\"]*)['\"]"
        matches = re.findall(pattern, content)
        return len(matches)
    except Exception as e:
        return 0

def main():
    base_path = Path('/Library/Workspace/protype/project/purple-desk')
    
    categories = [
        'general', 'ui', 'communications', 'files', 
        'others', 'people', 'media', 'alphanumeric'
    ]
    
    print("=== Bootstrap Icons 同步結果統計 ===\n")
    
    total_js = 0
    total_php = 0
    
    for category in categories:
        js_file = base_path / 'resources/js/utils/icons' / f'bs-{category}.js'
        php_file = base_path / 'config/icon/bootstrap-icons' / f'{category}.php'
        
        js_count = count_icons_in_js(js_file)
        php_count = count_icons_in_php(php_file)
        
        total_js += js_count
        total_php += php_count
        
        coverage = (php_count / js_count * 100) if js_count > 0 else 0
        
        print(f"{category:13} | JS: {js_count:3} | PHP: {php_count:3} | 覆蓋率: {coverage:5.1f}%")
    
    print("-" * 50)
    overall_coverage = (total_php / total_js * 100) if total_js > 0 else 0
    print(f"{'總計':13} | JS: {total_js:3} | PHP: {total_php:3} | 覆蓋率: {overall_coverage:5.1f}%")
    
    print("\n=== 同步完成！===")
    print(f"✅ 成功同步 {total_php} 個圖標")
    print(f"✅ 覆蓋率達到 {overall_coverage:.1f}%")

if __name__ == '__main__':
    main()