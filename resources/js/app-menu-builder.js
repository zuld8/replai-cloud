import { createApp } from "vue";
import Vue3Toastify from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import axios from "axios";
import { showToast } from "./inc/toastr.js";
import MenuBuilder from "./pages/MenuOtomatis/MenuBuilder.vue";

axios.defaults.baseURL = "/app/";
axios.defaults.withCredentials = true;
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

if (document.getElementById("app")) {
    const app = createApp({ components: { MenuBuilder } });
    app.use(Vue3Toastify, { autoClose: 3000, style: { opacity: "1", userSelect: "initial" } });
    app.config.globalProperties.$axios = axios;
    app.config.globalProperties.$showToast = showToast;
    app.mount("#app");
}
