

  /**
   *
   * Vue
   *
   */
  import Vue from 'vue'


  /**
   *
   * Import resource
   *
   */
  import '@/assets/style/main.scss';
  import 'font-awesome/css/font-awesome.css';


  /**
   *
   * Bootstrap files
   *
   */
  import app from '@/app.vue';
  import './registerServiceWorker';
  import router from '@/router';
  import store from '@/store';


  /**
   *
   * Vue global & config & plugin
   *
   */
  Vue.config.productionTip = false;


  /**
   *
   * Instance
   *
   */
  new Vue({
    router,
    store,
    render: h => h(app)
  }).$mount('#app')
