import './bootstrap';
import {createApp, markRaw} from 'vue/dist/vue.esm-bundler';
import {createRouter, createWebHistory, useRoute} from 'vue-router';
import { createPinia } from 'pinia'
import routes from './router';

const router = createRouter(
    {
        history: createWebHistory(),
        routes: [
            ...routes,
          ],
        linkActiveClass: 'active'
    }
)
const pinia = createPinia()
pinia.use(({ store }) => {
    store.router = markRaw(router);
    store.route = markRaw(useRoute());
});

const app = createApp({});
app.use(router);
app.use(pinia);
app.mount('#app');