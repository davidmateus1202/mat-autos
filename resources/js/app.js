import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import router from './router';
import App from './App.vue';

document.documentElement.classList.remove('dark');
document.documentElement.style.colorScheme = 'light';

try {
    window.localStorage.removeItem('venta-carros-theme');
} catch {
    // Ignore storage cleanup errors.
}

const app = createApp(App);

app.use(createPinia());
app.use(router);
app.use(Toast, {
    transition: "Vue-Toastification__bounce",
    maxToasts: 20,
    newestOnTop: true
});

app.mount('#app');
