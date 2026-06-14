import { createApp } from "vue";
import App from "./App.vue";
import VueLazyload from "vue-lazyload";
import { errorHandlerMixin } from "../../inc/responseError.js";
import Vue3Transitions from "vue3-transitions";
import Vue3Toastify, { toast } from "vue3-toastify";
import i18n from '../../plugins/i18n.js';
import { showToast } from "../../inc/toastr.js";
import "vue3-toastify/dist/index.css";
import axios from "axios";
import PrimeVue from "primevue/config";

// ============ DEBUG: FIRST THING THAT RUNS ============
// This runs BEFORE anything else - if this doesn't show, app-crm.js is not loading!
try {
    const __d = document.createElement('div');
    __d.id = '__app_crm_debug';
    __d.style.cssText = 'position:fixed;top:0;left:0;right:0;z-index:2147483647;background:#6b21a8;color:white;padding:4px 8px;font-size:11px;font-weight:bold;font-family:monospace;pointer-events:none;';
    __d.textContent = '🟣 app-crm.js LOADED @ ' + new Date().toLocaleTimeString();
    document.body.appendChild(__d);
    window.__crmDbg = __d;
} catch(e) {}
// ============ END DEBUG ============

const Vue = createApp(App);
const routerPromise = import("./router.js");
const storePromise = import("./store.js");
const NprogressContainerPromise = import("vue-nprogress/src/NprogressContainer.vue");
const MultiselectPromise = import("vue-multiselect");

// Konfigurasi Axios
axios.defaults.baseURL = "/app/";
axios.defaults.withCredentials = true;
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
Vue.config.globalProperties.$axios = axios;

try { Vue.use(PrimeVue); } catch(e) { console.error('[init] PrimeVue failed:', e.message); }
try { Vue.use(Vue3Transitions); } catch(e) { console.error('[init] Vue3Transitions failed:', e.message); }
try { Vue.use(i18n); } catch(e) { console.error('[init] i18n failed:', e.message); }
try {
    Vue.use(Vue3Toastify, { autoClose: 2000, style: { opacity: "1", userSelect: "initial" } });
} catch(e) { console.error('[init] Vue3Toastify failed:', e.message); }

Vue.config.globalProperties.$showToast = showToast;

if (window.__crmDbg) {
    window.__crmDbg.style.background = '#1d4ed8';
    window.__crmDbg.textContent = '🔵 app-crm.js: plugins done, waiting for Promise.all @ ' + new Date().toLocaleTimeString();
}

Promise.all([routerPromise, storePromise, NprogressContainerPromise, MultiselectPromise])
    .then(([router, store, NprogressContainer, Multiselect]) => {
        if (window.__crmDbg) {
            window.__crmDbg.style.background = '#15803d';
            window.__crmDbg.textContent = '🟢 Promise.all resolved, mounting Vue @ ' + new Date().toLocaleTimeString();
        }
        Vue.mixin(errorHandlerMixin);
        Vue.component("NprogressContainer", NprogressContainer.default);
        Vue.component("Multiselect", Multiselect.default);
        Vue.use(VueLazyload);
        Vue.use(router.default);
        Vue.use(store.default);
        Vue.mount("#app");
        if (window.__crmDbg) {
            window.__crmDbg.style.background = '#15803d';
            window.__crmDbg.textContent = '✅ Vue MOUNTED on #app @ ' + new Date().toLocaleTimeString();
        }
    })
    .catch(err => {
        if (window.__crmDbg) {
            window.__crmDbg.style.background = '#dc2626';
            window.__crmDbg.textContent = '❌ Promise.all FAILED: ' + (err?.message || String(err));
        }
        console.error('[Promise.all FAILED]', err);
    });
