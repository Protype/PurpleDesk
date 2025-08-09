import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './components/App.vue';
import { useAuthStore } from './stores/auth';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

// 初始化認證狀態
const authStore = useAuthStore();

// 等待認證初始化完成後再掛載應用
authStore.initializeAuth().then(() => {
  app.mount('#app');
});
