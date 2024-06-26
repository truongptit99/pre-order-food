import './bootstrap';
import { createApp } from 'vue';
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/reset.css';
import App from './components/App.vue';

createApp(App)
    .use(Antd)
    .mount('#app');
