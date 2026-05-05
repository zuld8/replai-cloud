import { toast } from "vue3-toastify";

// Fungsi helper global untuk menampilkan toast
export const showToast = (message, type = "info", duration = 3000) => {
    toast(message, {
        type: type,
        autoClose: duration,
    });
};
