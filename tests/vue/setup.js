import { config } from '@vue/test-utils';

// 清除全域配置以避免衝突
config.global.plugins = [];

// 模擬 Laravel Echo 如果需要
global.Echo = {
  channel: () => ({
    listen: () => {},
    stopListening: () => {}
  }),
  private: () => ({
    listen: () => {},
    stopListening: () => {}
  })
};

// 模擬 window.route 如果專案使用 Ziggy
global.route = (name, params) => {
  const routes = {
    'login': '/login',
    'home': '/',
    'admin.dashboard': '/admin'
  };
  return routes[name] || '/';
};