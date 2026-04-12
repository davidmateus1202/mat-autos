import axios from 'axios';
import { createToastInterface } from "vue-toastification";

const toast = createToastInterface({
    timeout: 3000,
    position: "top-right"
});

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;

window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            // Keep public routes accessible without forcing auth redirect.
            const publicPaths = new Set(['/', '/login']);
            const isPublicPath = publicPaths.has(window.location.pathname);

            if (!isPublicPath && !window.location.pathname.startsWith('/login')) {
                window.location.href = '/login';
            }
        } else if (error.response?.status >= 500) {
            toast.error("Server error. Please try again later.");
        } else if (error.response?.status !== 422) {
            // 422 errors are validation errors, usually handled by the form
            toast.error(error.response?.data?.message || "An error occurred");
        }
        return Promise.reject(error);
    }
);
