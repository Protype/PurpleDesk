import { test, expect } from '@playwright/test';

test.describe('Icon Picker 面板定位測試', () => {
  test.beforeEach(async ({ page }) => {
    // 訪問測試頁面並切換到新版 IconPicker
    await page.goto('/test/icon-picker?iconpicker=new');
    
    // 等待頁面載入完成
    await page.waitForLoadState('networkidle');
  });

  test('應該正確計算面板位置並避免被螢幕邊界裁切', async ({ page }) => {
    // 點擊觸發按鈕打開 Icon Picker
    const triggerButton = page.locator('button:has-text("🎨 開啟 Icon Picker")');
    await triggerButton.click();

    // 等待面板出現
    const iconPanel = page.locator('.icon-picker .fixed');
    await expect(iconPanel).toBeVisible();

    // 檢查面板位置
    const panelBox = await iconPanel.boundingBox();
    const viewportSize = page.viewportSize();

    // 驗證面板不會超出螢幕邊界
    expect(panelBox.x).toBeGreaterThanOrEqual(0);
    expect(panelBox.y).toBeGreaterThanOrEqual(0);
    expect(panelBox.x + panelBox.width).toBeLessThanOrEqual(viewportSize.width);
    expect(panelBox.y + panelBox.height).toBeLessThanOrEqual(viewportSize.height);

    // 截圖以供視覺檢查
    await page.screenshot({ 
      path: 'test-results/icon-picker-position-desktop.png',
      fullPage: true 
    });
  });

  test('在小螢幕上應該正確調整面板位置', async ({ page }) => {
    // 設定小螢幕尺寸
    await page.setViewportSize({ width: 800, height: 600 });

    // 等待響應式調整
    await page.waitForTimeout(100);

    // 點擊觸發按鈕
    const triggerButton = page.locator('button:has-text("🎨 開啟 Icon Picker")');
    await triggerButton.click();

    // 等待面板出現
    const iconPanel = page.locator('.icon-picker .fixed');
    await expect(iconPanel).toBeVisible();

    // 檢查面板位置
    const panelBox = await iconPanel.boundingBox();
    const viewportSize = page.viewportSize();

    // 驗證面板適應小螢幕
    expect(panelBox.x).toBeGreaterThanOrEqual(0);
    expect(panelBox.y).toBeGreaterThanOrEqual(0);
    expect(panelBox.x + panelBox.width).toBeLessThanOrEqual(viewportSize.width);
    expect(panelBox.y + panelBox.height).toBeLessThanOrEqual(viewportSize.height);

    // 截圖對比
    await page.screenshot({ 
      path: 'test-results/icon-picker-position-small.png',
      fullPage: true 
    });
  });

  test('在手機螢幕上應該正確顯示', async ({ page }) => {
    // 設定手機螢幕尺寸
    await page.setViewportSize({ width: 375, height: 667 }); // iPhone 6/7/8

    // 等待響應式調整
    await page.waitForTimeout(100);

    // 點擊觸發按鈕
    const triggerButton = page.locator('button:has-text("🎨 開啟 Icon Picker")');
    await triggerButton.click();

    // 等待面板出現
    const iconPanel = page.locator('.icon-picker .fixed');
    await expect(iconPanel).toBeVisible();

    // 檢查面板是否完全可見
    const panelBox = await iconPanel.boundingBox();
    const viewportSize = page.viewportSize();

    // 在手機螢幕上，面板應該不超出邊界
    expect(panelBox.x).toBeGreaterThanOrEqual(0);
    expect(panelBox.y).toBeGreaterThanOrEqual(0);
    expect(panelBox.x + panelBox.width).toBeLessThanOrEqual(viewportSize.width);

    // 截圖對比
    await page.screenshot({ 
      path: 'test-results/icon-picker-position-mobile.png',
      fullPage: true 
    });
  });

  test('TextIconPanel 功能應該正常運作', async ({ page }) => {
    // 打開 Icon Picker
    const triggerButton = page.locator('button:has-text("🎨 開啟 Icon Picker")');
    await triggerButton.click();

    // 等待面板出現並確認在文字標籤頁
    const iconPanel = page.locator('.icon-picker .fixed');
    await expect(iconPanel).toBeVisible();

    // 點擊文字標籤（應該已經是預設選中）- 使用更精確的選擇器
    const textTab = iconPanel.locator('button:has-text("文字")').first();
    await textTab.click();

    // 檢查 TextIconPanel 是否顯示
    const textPanel = iconPanel.locator('.text-icon-panel');
    await expect(textPanel).toBeVisible();

    // 測試文字輸入
    const textInput = textPanel.locator('input[type="text"]');
    await textInput.fill('ABC');

    // 檢查預覽區域
    const preview = textPanel.locator('div').filter({ hasText: 'ABC' }).first();
    await expect(preview).toBeVisible();

    // 檢查應用按鈕是否啟用
    const applyButton = textPanel.locator('button:has-text("套用文字")');
    await expect(applyButton).toBeEnabled();

    // 截圖記錄
    await page.screenshot({ 
      path: 'test-results/text-icon-panel-function.png',
      fullPage: true 
    });
  });

  test('其他標籤頁應該顯示開發中狀態', async ({ page }) => {
    // 打開 Icon Picker
    const triggerButton = page.locator('button:has-text("🎨 開啟 Icon Picker")');
    await triggerButton.click();

    const iconPanel = page.locator('.icon-picker .fixed');
    await expect(iconPanel).toBeVisible();

    // 測試 Emoji 標籤頁 - 使用更精確的選擇器
    const emojiTab = iconPanel.locator('button:has-text("Emoji")');
    await emojiTab.click();

    // 應該顯示開發中狀態
    const developmentStatus = iconPanel.locator('text=開發中');
    await expect(developmentStatus).toBeVisible();

    // 測試 Icons 標籤頁
    const iconsTab = iconPanel.locator('button:has-text("Icons")');
    await iconsTab.click();
    await expect(iconPanel.locator('text=開發中')).toBeVisible();

    // 測試 Upload 標籤頁
    const uploadTab = iconPanel.locator('button:has-text("Upload")');
    await uploadTab.click();
    await expect(iconPanel.locator('text=開發中')).toBeVisible();

    // 截圖記錄
    await page.screenshot({ 
      path: 'test-results/development-status.png',
      fullPage: true 
    });
  });

  test('點擊外部應該關閉面板', async ({ page }) => {
    // 打開 Icon Picker
    const triggerButton = page.locator('button:has-text("🎨 開啟 Icon Picker")');
    await triggerButton.click();

    // 確認面板已打開
    const iconPanel = page.locator('.icon-picker .fixed');
    await expect(iconPanel).toBeVisible();

    // 點擊外部關閉面板
    await page.click('body', { position: { x: 50, y: 50 } });

    // 確認面板已關閉
    await expect(iconPanel).not.toBeVisible();
  });

  test('窗口大小改變時面板位置應該調整', async ({ page }) => {
    // 打開 Icon Picker
    const triggerButton = page.locator('button:has-text("🎨 開啟 Icon Picker")');
    await triggerButton.click();

    // 記錄初始位置
    const iconPanel = page.locator('.icon-picker .fixed');
    await expect(iconPanel).toBeVisible();
    const initialBox = await iconPanel.boundingBox();

    // 改變窗口大小
    await page.setViewportSize({ width: 1200, height: 900 });
    await page.waitForTimeout(100);

    // 檢查位置是否仍然合理
    const newBox = await iconPanel.boundingBox();
    const newViewportSize = page.viewportSize();

    expect(newBox.x).toBeGreaterThanOrEqual(0);
    expect(newBox.y).toBeGreaterThanOrEqual(0);
    expect(newBox.x + newBox.width).toBeLessThanOrEqual(newViewportSize.width);
    expect(newBox.y + newBox.height).toBeLessThanOrEqual(newViewportSize.height);

    // 截圖對比
    await page.screenshot({ 
      path: 'test-results/responsive-adjustment.png',
      fullPage: true 
    });
  });
});