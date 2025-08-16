import { mount, shallowMount } from '@vue/test-utils';
import { createPinia } from 'pinia';
import { createRouter, createWebHistory } from 'vue-router';
import { vi } from 'vitest';

/**
 * 建立測試用的 Pinia store
 */
export function createTestPinia() {
  return createPinia();
}

/**
 * 建立測試用的 Vue Router
 */
export function createTestRouter(routes = []) {
  const defaultRoutes = [
    { path: '/', name: 'home', component: { template: '<div>Home</div>' } },
    { path: '/login', name: 'login', component: { template: '<div>Login</div>' } }
  ];
  
  return createRouter({
    history: createWebHistory(),
    routes: [...defaultRoutes, ...routes]
  });
}

/**
 * 建立完整的組件掛載選項
 */
export function createMountOptions(options = {}) {
  const pinia = createTestPinia();
  const router = createTestRouter(options.routes);
  
  return {
    global: {
      plugins: [pinia, router],
      stubs: options.stubs || {},
      mocks: options.mocks || {}
    },
    ...options
  };
}

/**
 * 等待 Vue 的下一次 tick 和所有 Promise
 */
export async function flushPromises() {
  return new Promise(resolve => setTimeout(resolve, 0));
}

/**
 * 模擬 API 回應
 */
export function mockApiResponse(data, status = 200) {
  return Promise.resolve({
    status,
    data,
    headers: {},
    config: {},
    statusText: status === 200 ? 'OK' : 'Error'
  });
}

/**
 * 模擬 API 錯誤
 */
export function mockApiError(message = 'API Error', status = 500) {
  const error = new Error(message);
  error.response = {
    status,
    data: { message },
    headers: {},
    config: {},
    statusText: 'Error'
  };
  return Promise.reject(error);
}

/**
 * 模擬認證 API 回應
 */
export function mockAuthAPI() {
  return {
    // 成功登入回應
    loginSuccess: (user = null, token = 'test-token') => {
      const defaultUser = {
        id: 1,
        account: 'testuser',
        email: 'test@example.com',
        full_name: '測試用戶',
        display_name: '測試用戶',
        is_admin: false,
        organizations: [],
        teams: []
      };
      
      return mockApiResponse({
        user: user || defaultUser,
        token
      });
    },

    // 登入失敗回應
    loginError: (message = '帳號或密碼錯誤') => {
      return mockApiError(message, 401);
    },

    // 註冊成功回應
    registerSuccess: (user = null, token = 'test-token') => {
      const defaultUser = {
        id: 1,
        account: 'newuser',
        email: 'new@example.com',
        full_name: '新用戶',
        display_name: '新用戶',
        is_admin: false,
        organizations: [],
        teams: []
      };
      
      return mockApiResponse({
        user: user || defaultUser,
        token
      }, 201);
    },

    // 註冊失敗回應
    registerError: (message = '註冊失敗') => {
      return mockApiError(message, 422);
    },

    // 取得用戶資訊成功回應
    userInfoSuccess: (user = null) => {
      const defaultUser = {
        id: 1,
        account: 'testuser',
        email: 'test@example.com',
        full_name: '測試用戶',
        display_name: '測試用戶',
        is_admin: false,
        organizations: [],
        teams: []
      };
      
      return mockApiResponse({
        user: user || defaultUser
      });
    },

    // 取得用戶資訊失敗回應（通常是 token 過期）
    userInfoError: (message = 'Unauthorized') => {
      return mockApiError(message, 401);
    },

    // 登出成功回應
    logoutSuccess: () => {
      return mockApiResponse({
        message: '登出成功'
      });
    },

    // 登出失敗回應
    logoutError: (message = '登出失敗') => {
      return mockApiError(message, 500);
    }
  };
}

/**
 * 設定認證狀態的輔助函數
 */
export function mockAuthState(authStore, state = {}) {
  const defaultState = {
    user: null,
    token: null,
    isAuthenticated: false,
    isInitialized: true
  };

  const finalState = { ...defaultState, ...state };

  Object.keys(finalState).forEach(key => {
    authStore[key] = finalState[key];
  });

  return authStore;
}

/**
 * 等待認證初始化完成
 */
export async function waitForAuth(authStore, maxWait = 1000) {
  const startTime = Date.now();
  
  while (!authStore.isInitialized && (Date.now() - startTime) < maxWait) {
    await new Promise(resolve => setTimeout(resolve, 10));
  }
  
  if (!authStore.isInitialized) {
    throw new Error('認證初始化超時');
  }
}

/**
 * 建立 localStorage mock
 */
export function createLocalStorageMock() {
  const store = new Map();
  
  return {
    getItem: vi.fn((key) => store.get(key) || null),
    setItem: vi.fn((key, value) => store.set(key, value)),
    removeItem: vi.fn((key) => store.delete(key)),
    clear: vi.fn(() => store.clear()),
    length: store.size,
    key: vi.fn((index) => [...store.keys()][index] || null)
  };
}

/**
 * 設定 axios mock 回應
 */
export function setupAxiosMocks(axios, mocks = {}) {
  const defaultMocks = {
    post: vi.fn(),
    get: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
    patch: vi.fn()
  };

  const finalMocks = { ...defaultMocks, ...mocks };

  Object.keys(finalMocks).forEach(method => {
    axios[method] = finalMocks[method];
  });

  // 重置 defaults
  axios.defaults = {
    headers: {
      common: {}
    }
  };

  return axios;
}

/**
 * 驗證表單驗證錯誤
 */
export function expectValidationError(wrapper, fieldName, errorMessage) {
  const errorElement = wrapper.find(`[data-testid="${fieldName}-error"]`);
  expect(errorElement.exists()).toBe(true);
  expect(errorElement.text()).toContain(errorMessage);
}

/**
 * 驗證成功訊息
 */
export function expectSuccessMessage(wrapper, message) {
  const successElement = wrapper.find('[data-testid="success-message"]');
  expect(successElement.exists()).toBe(true);
  expect(successElement.text()).toContain(message);
}

/**
 * 驗證錯誤訊息
 */
export function expectErrorMessage(wrapper, message) {
  const errorElement = wrapper.find('[data-testid="error-message"]');
  expect(errorElement.exists()).toBe(true);
  expect(errorElement.text()).toContain(message);
}