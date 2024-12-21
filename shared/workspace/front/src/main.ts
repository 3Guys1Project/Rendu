import {createApp} from 'vue';
import './assets/index.css';
import App from '@/App.vue';
import router from '@/lib/routes.ts';

const app = createApp(App);
app.use(router);
app.mount('#app');

