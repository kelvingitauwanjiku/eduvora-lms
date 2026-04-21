import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = localStorage.getItem('token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

window.toast = {
    success: (message) => console.log('Success:', message),
    error: (message) => console.error('Error:', message),
    warning: (message) => console.warn('Warning:', message),
    info: (message) => console.info('Info:', message),
};