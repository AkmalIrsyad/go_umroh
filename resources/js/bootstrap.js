import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;
window.axios.defaults.baseURL = window.location.origin;

// Interceptor: attach Bearer token if present
window.axios.interceptors.request.use((config) => {
    const token = localStorage.getItem('go_umrah_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Interceptor: redirect to login on 401, to /suspended on 403 account_suspended
window.axios.interceptors.response.use(
    (r) => r,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('go_umrah_token');
            localStorage.removeItem('go_umrah_user');
            window.location.href = '/login';
        }
        if (
            error.response?.status === 403 &&
            error.response?.data?.error === 'account_suspended'
        ) {
            window.location.href = '/suspended';
        }
        return Promise.reject(error);
    }
);