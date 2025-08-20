#!/usr/bin/env python3
"""
統計舊版 icon picker 中的 Bootstrap Icons 總數
"""

import subprocess
import re
from pathlib import Path

def count_icons_in_old_bootstrap_files():
    """統計舊版 Bootstrap Icons 檔案中的圖標數量"""
    old_commit = "ce703a8"
    
    # 舊版 Bootstrap Icons 相關檔案
    bootstrap_files = [
        'resources/js/utils/allBootstrapIcons.js',
        'resources/js/utils/allBootstrapIconsComplete.js'
    ]
    
    total_icons = 0
    file_details = []
    
    for file_path in bootstrap_files:
        try:
            result = subprocess.run(
                ['git', 'show', f'{old_commit}:{file_path}'],
                capture_output=True, text=True,
                cwd='/Library/Workspace/protype/project/purple-desk'
            )
            
            if result.returncode == 0:
                content = result.stdout
                
                # 計算 { name: 'xxx', class: 'bi-xxx' } 格式的圖標
                pattern = r"{\s*name:\s*['\"]([^'\"]*)['\"],\s*class:\s*['\"]([^'\"]*)['\"]"
                matches = re.findall(pattern, content)
                count = len(matches)
                
                if count > 0:
                    file_details.append({
                        'file': file_path,
                        'count': count,
                        'sample_icons': [match[1] for match in matches[:5]]  # 取前5個作為樣本
                    })
                    total_icons += count
                    
        except Exception as e:
            print(f"處理檔案 {file_path} 時發生錯誤: {e}")
    
    return total_icons, file_details

def count_current_systems():
    """統計目前系統的數量"""
    base_path = Path('/Library/Workspace/protype/project/purple-desk')
    
    # 統計 PHP 配置檔案
    php_total = 0
    categories = ['general', 'ui', 'communications', 'files', 'others', 'people', 'media', 'alphanumeric']
    
    for category in categories:
        php_file = base_path / 'config/icon/bootstrap-icons' / f'{category}.php'
        try:
            with open(php_file, 'r', encoding='utf-8') as f:
                content = f.read()
            matches = re.findall(r"'name'\s*=>\s*'[^']*'", content)
            php_total += len(matches)
        except:
            pass
    
    # 統計前端 JS 檔案
    js_total = 0
    for category in categories:
        js_file = base_path / 'resources/js/utils/icons' / f'bs-{category}.js'
        try:
            with open(js_file, 'r', encoding='utf-8') as f:
                content = f.read()
            pattern = r"{\s*name:\s*['\"]([^'\"]*)['\"],\s*class:\s*['\"]([^'\"]*)['\"]"
            matches = re.findall(pattern, content)
            js_total += len(matches)
        except:
            pass
    
    return php_total, js_total

def main():
    print("=== 舊版 vs 新版 Bootstrap Icons 完整統計 ===\n")
    
    # 統計舊版
    old_total, old_files = count_icons_in_old_bootstrap_files()
    
    # 統計新版
    new_php_total, new_js_total = count_current_systems()
    
    print("📊 完整數量對比：")
    print("=" * 70)
    print(f"{'系統版本':20} | {'數量':>8} | {'檔案結構'}")
    print("=" * 70)
    print(f"{'舊版 Icon Picker':20} | {old_total:>8,} | allBootstrapIcons.js + Complete.js")
    print(f"{'新版 PHP Config':20} | {new_php_total:>8,} | 8個分類 PHP 檔案")
    print(f"{'新版前端 JS':20} | {new_js_total:>8,} | 8個 bs-*.js 分類檔案")
    
    print(f"\n🔍 舊版檔案詳細資訊：")
    if old_files:
        for file_info in old_files:
            print(f"📁 {file_info['file']}")
            print(f"   └── 圖標數量: {file_info['count']:,} 個")
            print(f"   └── 範例: {', '.join(file_info['sample_icons'])}")
    
    print(f"\n📈 升級對比：")
    if old_total > 0:
        php_growth = (new_php_total - old_total) / old_total * 100
        js_growth = (new_js_total - old_total) / old_total * 100
        print(f"• 舊版 → 新版 PHP: {old_total:,} → {new_php_total:,} ({php_growth:+.1f}%)")
        print(f"• 舊版 → 新版 JS:  {old_total:,} → {new_js_total:,} ({js_growth:+.1f}%)")
        print(f"• PHP 淨增加: {new_php_total - old_total:+,} 個圖標")
        print(f"• JS 淨增加:  {new_js_total - old_total:+,} 個圖標")
    else:
        print(f"• 新版系統全新建立")
        print(f"• PHP 配置: {new_php_total:,} 個圖標")
        print(f"• JS 前端: {new_js_total:,} 個圖標")
    
    print(f"\n✅ 系統升級總結：")
    print(f"• 從單一大檔案 → 分類化管理系統")
    print(f"• 實現前後端完全同步")
    print(f"• 支援圖標變體管理 (outline/solid)")
    print(f"• 新增關鍵字搜尋支援")
    print(f"• 提供自動化同步工具")

if __name__ == '__main__':
    main()