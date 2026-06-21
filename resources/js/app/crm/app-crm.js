import '../../public-path.js'; // MUST be first: sets webpack publicPath
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

try { Vue.use(PrimeVue); } catch(e) { console.warn('[init] PrimeVue:', e.message); }
try { Vue.use(Vue3Transitions); } catch(e) { console.warn('[init] Vue3Transitions:', e.message); }
try { Vue.use(i18n); } catch(e) { console.warn('[init] i18n:', e.message); }
try {
    Vue.use(Vue3Toastify, { autoClose: 2000, style: { opacity: "1", userSelect: "initial" } });
} catch(e) { console.warn('[init] Vue3Toastify:', e.message); }

Vue.config.globalProperties.$showToast = showToast;

Promise.all([routerPromise, storePromise, NprogressContainerPromise, MultiselectPromise])
    .then(([router, store, NprogressContainer, Multiselect]) => {
        Vue.mixin(errorHandlerMixin);
        Vue.component("NprogressContainer", NprogressContainer.default);
        Vue.component("Multiselect", Multiselect.default);
        Vue.use(VueLazyload);
        Vue.use(router.default);
        Vue.use(store.default);
        Vue.mount("#app");
    })
    .catch(err => {
        console.error('[Vue mount failed]', err);
    });
