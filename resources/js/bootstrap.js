import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = localStorage.getItem('token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

window.toast = {
    success: (message) => { if (!import.meta?.env?.PROD) console.log('Success:', message); },
    error: (message) => { if (!import.meta?.env?.PROD) console.error('Error:', message); },
    warning: (message) => { if (!import.meta?.env?.PROD) console.warn('Warning:', message); },
    info: (message) => { if (!import.meta?.env?.PROD) console.info('Info:', message); },
};