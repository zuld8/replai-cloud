import { createApp } from "vue";
import Vue3Toastify, { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import axios from "axios";
import Vue3Transitions from "vue3-transitions"; 
import { showToast } from "./inc/toastr.js";
import CreateAutoReply from "./pages/Templates/CreateAutoReply.vue";
 
axios.defaults.baseURL = "/app/";
axios.defaults.withCredentials = true;
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
 
if (document.getElementById("app")) {
    const app = createApp({
        components: { CreateAutoReply },
    });
 
    app.use(Vue3Toastify, {
        autoClose: 2000,
        style: {
            opacity: "1",
            userSelect: "initial",
        },
    });

    app.use(Vue3Transitions);
 
    app.config.globalProperties.$axios = axios;
    app.config.globalProperties.$showToast = showToast;
 
    app.mount("#app");
}
