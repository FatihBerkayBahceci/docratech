import axios from 'axios';

// Use relative API path for Ubuntu development
axios.defaults.baseURL = '/api';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Create axios instance for Ubuntu development
const api = axios.create({
  baseURL: '/api',
  withCredentials: true,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
  },
});

// Configure interceptors for Ubuntu environment
api.interceptors.request.use(config => {
  if (config.url.startsWith('/sanctum/csrf-cookie')) {
    config.baseURL = '';
  }
  return config;
});

// Token'ı localStorage'dan yükle
const token = localStorage.getItem('token');
if (token) {
  api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Response interceptor - 401 hatası durumunda logout
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('isAuthenticated');
      localStorage.removeItem('user');
      localStorage.removeItem('token');
      delete api.defaults.headers.common['Authorization'];
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// Global olarak window ve app'a ata
window.axios = api;
export default api;
