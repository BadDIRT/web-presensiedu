import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.cocToast = function (message, type = 'info') {
    const existing = document.querySelector('.coc-toast');
    if (existing) existing.remove();
    const toast = document.createElement('div');
    toast.className = `coc-toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.transition = 'opacity 0.4s ease';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 400);
    }, 2500);
};