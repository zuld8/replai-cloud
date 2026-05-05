// resources/js/vue-page.js
import { createApp } from "vue";
import Vue3Toastify, { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import axios from "axios";
import Vue3Transitions from "vue3-transitions"; // Tambahkan ini, sepertinya lupa diimpor
import { showToast } from "./inc/toastr.js";
import WhatsappTemplate from "./pages/Templates/WhatsappTemplate.vue";

// Konfigurasi axios
axios.defaults.baseURL = "/app/";
axios.defaults.withCredentials = true;
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Mount Vue hanya untuk elemen dengan ID 'app'
if (document.getElementById("app")) {
    const app = createApp({
        components: { WhatsappTemplate },
    });

    // Tambahkan plugin global
    app.use(Vue3Toastify, {
        autoClose: 2000,
        style: {
            opacity: "1",
            userSelect: "initial",
        },
    });

    app.use(Vue3Transitions);

    // Tambahkan axios dan fungsi toast sebagai properti global
    app.config.globalProperties.$axios = axios;
    app.config.globalProperties.$showToast = showToast;

    // Mount aplikasi Vue
    app.mount("#app");
}
