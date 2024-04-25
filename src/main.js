import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import { createWebHashHistory, createRouter } from 'vue-router'
import { createPinia } from 'pinia'

import StoreSettings from './components/StoreSettings.vue'
import HubSettings from './components/HubSettings.vue'
import TabNavigation from './components/TabNavigation.vue'
import OrderList from './components/OrderList.vue'

const routes = [
    { path: '/', components: { default: OrderList } },
    { path: '/settings', components: { default: HubSettings, tab: TabNavigation } },
    { path: '/store-settings', components: { default: StoreSettings, tab: TabNavigation } },
]

const router = createRouter({
    history: createWebHashHistory(),
    routes,
})
const pinia = createPinia()
const app = createApp(App)
app.use(router)
app.use(pinia)
app.mount('#mho-hub-order-app')
