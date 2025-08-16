import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import { createMountOptions } from '../utils/testHelpers.js';
import Dashboard from '../../../resources/js/pages/Dashboard.vue';

// 模擬 AppNavbar 元件
const mockAppNavbar = {
  name: 'AppNavbar',
  template: '<nav data-testid="app-navbar">MockedNavbar</nav>'
};

// 模擬 Hero Icons
const mockDocumentTextIcon = {
  name: 'DocumentTextIcon',
  template: '<svg data-testid="document-text-icon"></svg>'
};

const mockClipboardCheckIcon = {
  name: 'ClipboardCheckIcon',
  template: '<svg data-testid="clipboard-check-icon"></svg>'
};

describe('Dashboard', () => {
  let wrapper;

  beforeEach(() => {
    const mountOptions = createMountOptions({
      global: {
        components: {
          AppNavbar: mockAppNavbar,
          DocumentTextIcon: mockDocumentTextIcon,
          ClipboardCheckIcon: mockClipboardCheckIcon
        }
      }
    });

    wrapper = mount(Dashboard, mountOptions);
  });

  describe('元件渲染', () => {
    it('應該渲染 AppNavbar 元件', () => {
      expect(wrapper.find('[data-testid="app-navbar"]').exists()).toBe(true);
    });

    it('應該顯示歡迎標題', () => {
      expect(wrapper.find('h1').text()).toBe('歡迎使用 PurpleDesk');
    });

    it('應該顯示副標題', () => {
      expect(wrapper.text()).toContain('您的專案管理系統已準備就緒');
    });

    it('應該使用正確的背景色', () => {
      expect(wrapper.find('.bg-gray-50').exists()).toBe(true);
    });
  });

  describe('功能卡片', () => {
    it('應該顯示三個功能卡片', () => {
      const cards = wrapper.findAll('.bg-white.shadow');
      expect(cards).toHaveLength(3);
    });

    it('應該顯示專案管理卡片', () => {
      expect(wrapper.text()).toContain('專案管理');
      expect(wrapper.find('[data-testid="document-text-icon"]').exists()).toBe(true);
    });

    it('應該顯示任務追蹤卡片', () => {
      expect(wrapper.text()).toContain('任務追蹤');
      // 此卡片也使用 DocumentTextIcon
      const documentIcons = wrapper.findAll('[data-testid="document-text-icon"]');
      expect(documentIcons.length).toBeGreaterThanOrEqual(1);
    });

    it('應該顯示團隊協作卡片', () => {
      expect(wrapper.text()).toContain('團隊協作');
      expect(wrapper.find('.bi-people-fill').exists()).toBe(true);
    });

    it('每個卡片都應該顯示即將推出狀態', () => {
      const comingSoonTexts = wrapper.findAll('.text-gray-500');
      const comingSoonCount = comingSoonTexts.filter(
        element => element.text() === '即將推出'
      ).length;
      expect(comingSoonCount).toBe(3);
    });
  });

  describe('樣式與佈局', () => {
    it('應該使用響應式網格佈局', () => {
      expect(wrapper.find('.grid-cols-1').exists()).toBe(true);
      expect(wrapper.find('.md\\:grid-cols-3').exists()).toBe(true);
    });

    it('應該有適當的間距', () => {
      expect(wrapper.find('.gap-6').exists()).toBe(true);
      expect(wrapper.find('.max-w-3xl').exists()).toBe(true);
    });

    it('卡片應該有圓角和內邊距', () => {
      const cards = wrapper.findAll('.bg-white.shadow');
      cards.forEach(card => {
        expect(card.classes()).toContain('rounded-lg');
        expect(card.classes()).toContain('p-6');
      });
    });

    it('應該有虛線邊框裝飾', () => {
      expect(wrapper.find('.border-dashed').exists()).toBe(true);
      expect(wrapper.find('.border-gray-200').exists()).toBe(true);
    });
  });

  describe('圖標顯示', () => {
    it('應該在中央顯示主要圖標', () => {
      expect(wrapper.find('.bg-primary-100').exists()).toBe(true);
      expect(wrapper.find('.rounded-full').exists()).toBe(true);
    });

    it('應該正確顯示 ClipboardCheckIcon', () => {
      expect(wrapper.find('[data-testid="clipboard-check-icon"]').exists()).toBe(true);
    });

    it('應該顯示 Bootstrap 圖標', () => {
      expect(wrapper.find('.bi-people-fill').exists()).toBe(true);
      expect(wrapper.find('.text-3xl').exists()).toBe(true);
    });
  });

  describe('內容組織', () => {
    it('應該有正確的頁面標題層級', () => {
      const h1 = wrapper.find('h1');
      expect(h1.exists()).toBe(true);
      expect(h1.classes()).toContain('text-2xl');
      expect(h1.classes()).toContain('font-bold');
    });

    it('應該有正確的卡片標題層級', () => {
      const cardTitles = wrapper.findAll('h3');
      expect(cardTitles).toHaveLength(3);
      cardTitles.forEach(title => {
        expect(title.classes()).toContain('font-medium');
      });
    });

    it('應該使用語意化的 main 標籤', () => {
      expect(wrapper.find('main').exists()).toBe(true);
      expect(wrapper.find('main').classes()).toContain('max-w-7xl');
    });
  });
});