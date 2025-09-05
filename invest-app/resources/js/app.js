import './bootstrap';
import { createApp, markRaw } from 'vue/dist/vue.esm-bundler';
import { createPinia } from 'pinia';
import router from './router'; // 👈 Import the router instance, not routes
import { useRoute } from 'vue-router';

const pinia = createPinia();
pinia.use(({ store }) => {
  store.router = markRaw(router);
  store.route = markRaw(useRoute());
});

const app = createApp({});
app.use(router);
app.use(pinia);
app.mount('#app');
