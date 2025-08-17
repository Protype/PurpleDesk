import './bootstrap';
import { createApp } from 'vue';
import IconPickerTestPage from './components/test/IconPickerTestPage.vue';

// Bootstrap Icons CSS
import 'bootstrap-icons/font/bootstrap-icons.css';

const app = createApp();

// 註冊測試頁面元件
app.component('icon-picker-test-page', IconPickerTestPage);

app.mount('#app');