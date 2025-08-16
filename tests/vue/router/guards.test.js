import { describe, it, expect, vi, beforeEach } from 'vitest';
import { createRouter, createWebHistory } from 'vue-router';
import { createPinia, setActivePinia } from 'pinia';
import { useAuthStore } from '../../../resources/js/stores/auth.js';

// Mock stores/auth
vi.mock('../../../resources/js/stores/auth', () => ({
  useAuthStore: vi.fn()
}));

describe('路由守衛', () => {
  let router;
  let mockAuthStore;
  let navigateToSpy;

  beforeEach(() => {
    // 建立新的 Pinia 實例
    setActivePinia(createPinia());

    // 建立 mock AuthStore
    mockAuthStore = {
      isInitialized: false,
      isLoggedIn: false,
      user: null,
      initializeAuth: vi.fn()
    };

    // Mock useAuthStore 回傳我們的 mock store
    useAuthStore.mockReturnValue(mockAuthStore);

    // 建立測試路由
    const routes = [
      {
        path: '/login',
        name: 'login',
        component: { template: '<div>Login</div>' },
        meta: { requiresGuest: true }
      },
      {
        path: '/register',
        name: 'register',
        component: { template: '<div>Register</div>' },
        meta: { requiresGuest: true }
      },
      {
        path: '/dashboard',
        name: 'dashboard',
        component: { template: '<div>Dashboard</div>' },
        meta: { requiresAuth: true }
      },
      {
        path: '/profile',
        name: 'profile',
        component: { template: '<div>Profile</div>' },
        meta: { requiresAuth: true }
      },
      {
        path: '/admin',
        name: 'admin',
        component: { template: '<div>Admin</div>' },
        meta: { requiresAuth: true, requiresAdmin: true }
      },
      {
        path: '/dev-tool',
        name: 'dev-tool',
        component: { template: '<div>Dev Tool</div>' },
        meta: { requiresAuth: true, devOnly: true }
      },
      {
        path: '/',
        redirect: '/login'
      }
    ];

    router = createRouter({
      history: createWebHistory(),
      routes
    });

    // 動態載入並應用路由守衛
    // 這裡我們需要手動複製 router.js 中的 beforeEach 邏輯
    router.beforeEach(async (to, from, next) => {
      const authStore = useAuthStore();
      
      // 等待認證初始化完成
      if (!authStore.isInitialized) {
        await authStore.initializeAuth();
      }
      
      // 需要認證的頁面
      if (to.meta.requiresAuth && !authStore.isLoggedIn) {
        next('/login');
        return;
      }
      
      // 需要管理員權限的頁面
      if (to.meta.requiresAdmin) {
        const user = authStore.user;
        if (!user || !user.is_admin) {
          next('/dashboard');
          return;
        }
      }
      
      // 開發工具頁面只在開發模式下可訪問
      if (to.meta.devOnly && import.meta.env.PROD) {
        next('/dashboard');
        return;
      }
      
      // 已登入用戶不能訪問登入/註冊頁面
      if (to.meta.requiresGuest && authStore.isLoggedIn) {
        next('/dashboard');
        return;
      }
      
      next();
    });

    // Spy on router.push 來追蹤導航
    navigateToSpy = vi.spyOn(router, 'push');

    vi.clearAllMocks();
  });

  describe('認證初始化', () => {
    it('未初始化時應該先呼叫 initializeAuth', async () => {
      mockAuthStore.isInitialized = false;
      mockAuthStore.isLoggedIn = false;
      mockAuthStore.initializeAuth.mockResolvedValue();

      await router.push('/dashboard');

      expect(mockAuthStore.initializeAuth).toHaveBeenCalled();
    });

    it('已初始化時不應該重複呼叫 initializeAuth', async () => {
      mockAuthStore.isInitialized = true;
      mockAuthStore.isLoggedIn = true;

      await router.push('/dashboard');

      expect(mockAuthStore.initializeAuth).not.toHaveBeenCalled();
    });
  });

  describe('需要認證的路由', () => {
    beforeEach(() => {
      mockAuthStore.isInitialized = true;
    });

    it('未登入時存取受保護頁面應該重導向至登入頁', async () => {
      mockAuthStore.isLoggedIn = false;

      await router.push('/dashboard');

      expect(router.currentRoute.value.path).toBe('/login');
    });

    it('未登入時存取個人資料頁面應該重導向至登入頁', async () => {
      mockAuthStore.isLoggedIn = false;

      await router.push('/profile');

      expect(router.currentRoute.value.path).toBe('/login');
    });

    it('已登入時可以正常存取受保護頁面', async () => {
      mockAuthStore.isLoggedIn = true;

      await router.push('/dashboard');

      expect(router.currentRoute.value.path).toBe('/dashboard');
    });
  });

  describe('管理員權限路由', () => {
    beforeEach(() => {
      mockAuthStore.isInitialized = true;
      mockAuthStore.isLoggedIn = true;
    });

    it('非管理員用戶存取管理頁面應該重導向至 dashboard', async () => {
      mockAuthStore.user = { id: 1, is_admin: false };

      await router.push('/admin');

      expect(router.currentRoute.value.path).toBe('/dashboard');
    });

    it('沒有用戶資訊時存取管理頁面應該重導向至 dashboard', async () => {
      mockAuthStore.user = null;

      await router.push('/admin');

      expect(router.currentRoute.value.path).toBe('/dashboard');
    });

    it('管理員用戶可以正常存取管理頁面', async () => {
      mockAuthStore.user = { id: 1, is_admin: true };

      await router.push('/admin');

      expect(router.currentRoute.value.path).toBe('/admin');
    });
  });

  describe('開發工具路由', () => {
    beforeEach(() => {
      mockAuthStore.isInitialized = true;
      mockAuthStore.isLoggedIn = true;
    });

    it('生產環境下應該重導向至 dashboard', async () => {
      // Mock 生產環境
      vi.stubEnv('PROD', true);

      await router.push('/dev-tool');

      expect(router.currentRoute.value.path).toBe('/dashboard');
      
      vi.unstubAllEnvs();
    });

    it('開發環境下可以正常存取', async () => {
      // Mock 開發環境
      vi.stubEnv('PROD', false);

      await router.push('/dev-tool');

      expect(router.currentRoute.value.path).toBe('/dev-tool');
      
      vi.unstubAllEnvs();
    });
  });

  describe('訪客專用路由', () => {
    beforeEach(() => {
      mockAuthStore.isInitialized = true;
    });

    it('已登入用戶存取登入頁面應該重導向至 dashboard', async () => {
      mockAuthStore.isLoggedIn = true;

      await router.push('/login');

      expect(router.currentRoute.value.path).toBe('/dashboard');
    });

    it('已登入用戶存取註冊頁面應該重導向至 dashboard', async () => {
      mockAuthStore.isLoggedIn = true;

      await router.push('/register');

      expect(router.currentRoute.value.path).toBe('/dashboard');
    });

    it('未登入用戶可以正常存取登入頁面', async () => {
      mockAuthStore.isLoggedIn = false;

      await router.push('/login');

      expect(router.currentRoute.value.path).toBe('/login');
    });

    it('未登入用戶可以正常存取註冊頁面', async () => {
      mockAuthStore.isLoggedIn = false;

      await router.push('/register');

      expect(router.currentRoute.value.path).toBe('/register');
    });
  });

  describe('複雜情境測試', () => {
    it('完整登入流程：未登入 → 登入 → 存取受保護頁面', async () => {
      // 1. 初始狀態：未登入
      mockAuthStore.isInitialized = true;
      mockAuthStore.isLoggedIn = false;

      // 2. 嘗試存取受保護頁面，應該重導向至登入頁
      await router.push('/dashboard');
      expect(router.currentRoute.value.path).toBe('/login');

      // 3. 模擬登入成功
      mockAuthStore.isLoggedIn = true;
      mockAuthStore.user = { id: 1, is_admin: false };

      // 4. 再次嘗試存取受保護頁面，應該成功
      await router.push('/dashboard');
      expect(router.currentRoute.value.path).toBe('/dashboard');
    });

    it('管理員登入流程：登入 → 存取一般頁面 → 存取管理頁面', async () => {
      mockAuthStore.isInitialized = true;
      mockAuthStore.isLoggedIn = true;
      mockAuthStore.user = { id: 1, is_admin: true };

      // 1. 存取一般受保護頁面
      await router.push('/dashboard');
      expect(router.currentRoute.value.path).toBe('/dashboard');

      // 2. 存取管理頁面
      await router.push('/admin');
      expect(router.currentRoute.value.path).toBe('/admin');
    });

    it('權限降級情境：管理員 → 一般用戶', async () => {
      mockAuthStore.isInitialized = true;
      mockAuthStore.isLoggedIn = true;

      // 1. 初始為管理員，可以存取管理頁面
      mockAuthStore.user = { id: 1, is_admin: true };
      await router.push('/admin');
      expect(router.currentRoute.value.path).toBe('/admin');

      // 2. 權限被移除，從其他頁面再次嘗試存取管理頁面
      mockAuthStore.user = { id: 1, is_admin: false };
      // 先導航到其他頁面，再嘗試存取管理頁面
      await router.push('/dashboard');
      await router.push('/admin');
      expect(router.currentRoute.value.path).toBe('/dashboard');
    });

    it('登出流程：已登入 → 登出 → 嘗試存取受保護頁面', async () => {
      mockAuthStore.isInitialized = true;

      // 1. 初始已登入狀態
      mockAuthStore.isLoggedIn = true;
      mockAuthStore.user = { id: 1 };

      // 2. 存取受保護頁面成功
      await router.push('/dashboard');
      expect(router.currentRoute.value.path).toBe('/dashboard');

      // 3. 模擬登出
      mockAuthStore.isLoggedIn = false;
      mockAuthStore.user = null;

      // 4. 再次嘗試存取受保護頁面，應該重導向至登入頁
      await router.push('/profile');
      expect(router.currentRoute.value.path).toBe('/login');
    });
  });
});