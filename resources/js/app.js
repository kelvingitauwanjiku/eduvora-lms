import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';

import '../css/app.css';

const app = createApp(App);

app.use(createPinia());
app.use(router);

app.config.errorHandler = (err, instance, info) => {
    if (!import.meta?.env?.PROD) {
        console.error('Vue Error:', err);
        console.error('Info:', info);
    }
};

app.mount('#app');