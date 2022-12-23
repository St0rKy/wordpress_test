import Menu from "./components/Menu";
import {router} from "./router";
import { createApp } from 'vue';

/**
 * Initialize the appllication
 * @type {App<Element>}
 */
const app = createApp(Menu, );

/**
 * On Ready (Wait for everything to load)
 */
function onReady() {
    /**
     * Add Router and mount
     */
    app.use(router);
    app.mount('#pa-vue-app');
}

document.addEventListener('DOMContentLoaded', onReady);