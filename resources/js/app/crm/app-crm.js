import { createApp } from "vue";
import App from "./App.vue";
import VueLazyload from "vue-lazyload";
import { errorHandlerMixin } from "../../inc/responseError.js";
const Vue = createApp(App);
import Vue3Transitions from "vue3-transitions";
import Vue3Toastify, { toast } from "vue3-toastify";
const routerPromise = import("./router.js");
const storePromise = import("./store.js");
import i18n from '../../plugins/i18n.js';
const NprogressContainerPromise = import(
    "vue-nprogress/src/NprogressContainer.vue"
);
const MultiselectPromise = import("vue-multiselect");
import { showToast } from "../../inc/toastr.js"; // Import fungsi toast
import "vue3-toastify/dist/index.css";
import axios from "axios";
import PrimeVue from "primevue/config";
Vue.use(PrimeVue);

// Konfigurasi Axios
axios.defaults.baseURL = "/app/";
axios.defaults.withCredentials = true;
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Tambahkan axios ke properti global Vue
Vue.config.globalProperties.$axios = axios;


Vue.use(Vue3Transitions);
Vue.use(i18n);
Vue.use(Vue3Toastify, {
    autoClose: 2000,
    style: {
        opacity: "1",
        userSelect: "initial",
    },
});

// Buat fungsi toast global
Vue.config.globalProperties.$showToast = showToast;

// DEBUG: Alert on body before Promise.all
const __entryDbg = document.createElement('div');
__entryDbg.style.cssText = 'position:fixed;top:0;left:0;right:0;z-index:2147483647;background:blue;color:white;padding:4px;font-size:11px;font-weight:bold;';
__entryDbg.textContent = 'ENTRY: Promise.all starting @ ' + new Date().toLocaleTimeString();
document.body.appendChild(__entryDbg);

Promise.all([routerPromise, storePromise,NprogressContainerPromise, MultiselectPromise]).then(([router, store,NprogressContainer, Multiselect]) => {
    __entryDbg.style.background = 'green';
    __entryDbg.textContent = 'ENTRY: Promise.all RESOLVED, mounting Vue @ ' + new Date().toLocaleTimeString();
    Vue.mixin(errorHandlerMixin);
    Vue.component("NprogressContainer", NprogressContainer.default);
    Vue.component("Multiselect", Multiselect.default);
    Vue.use(VueLazyload);
    Vue.use(router.default);
    Vue.use(store.default);
    Vue.mount("#app");
    __entryDbg.textContent = 'ENTRY: Vue MOUNTED @ ' + new Date().toLocaleTimeString();
}).catch(err => {
    __entryDbg.style.background = 'red';
    __entryDbg.textContent = 'ENTRY: Promise.all FAILED! ' + (err?.message || String(err));
    console.error('[ENTRY FAIL]', err);
});
