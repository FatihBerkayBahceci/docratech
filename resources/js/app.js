import './bootstrap';
import { createApp } from 'vue';
import axios from 'axios';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import router from './router';
import App from './App.vue';
import { createPinia } from 'pinia';

// Global UI Components (atomic design)
import AppButton from './components/button/AppButton.vue';
import AppCard from './components/card/AppCard.vue';
import AppModal from './components/modal/AppDialog.vue';
import Spinner from './components/loading/LoadingSpinner.vue';
import Icon from './components/visual/Icon.vue';
import Avatar from './components/media/Avatar.vue';
import StatusBadge from './components/badge/StatusBadge.vue';
import ToastContainer from './components/toast/ToastContainer.vue';
import ThemeProvider from './components/theme/ThemeProvider.vue';

// Import global stores
import { useAuthStore } from './stores/auth';
import { useDashboardStore } from './stores/dashboard';
import { useUsersStore } from './stores/users';
import { useRolesStore } from './stores/roles';
import { usePermissionsStore } from './stores/permissions';

// Set axios defaults (using bootstrap.js configuration)
// Note: bootstrap.js already configures axios with proper baseURL and interceptors

// Create Vue app
const app = createApp(App);
const pinia = createPinia();

// Global properties
app.config.globalProperties.$axios = axios;

// Global error handler
app.config.errorHandler = (err, vm, info) => {
  console.error('Global Vue Error:', err);
  console.error('Component:', vm);
  console.error('Error Info:', info);
  
  // You can add error reporting service here
  // Example: Sentry.captureException(err);
};

// Global warn handler
app.config.warnHandler = (msg, vm, trace) => {
  console.warn('Global Vue Warning:', msg);
  console.warn('Component:', vm);
  console.warn('Trace:', trace);
};

// Register global UI components
app.component('AppButton', AppButton);
app.component('AppCard', AppCard);
app.component('AppModal', AppModal);
app.component('Spinner', Spinner);
app.component('Icon', Icon);
app.component('Avatar', Avatar);
app.component('StatusBadge', StatusBadge);
app.component('ToastContainer', ToastContainer);
app.component('ThemeProvider', ThemeProvider);

// Global filters
app.config.globalProperties.$filters = {
  formatDate: (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  },
  formatDateTime: (date) => {
    if (!date) return '';
    return new Date(date).toLocaleString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  },
  formatCurrency: (amount, currency = 'USD') => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: currency
    }).format(amount);
  },
  truncate: (text, length = 50) => {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
  }
};

// Use plugins
app.use(pinia);
app.use(router);

// Initialize stores after pinia is installed
const authStore = useAuthStore();
const dashboardStore = useDashboardStore();
const usersStore = useUsersStore();
const rolesStore = useRolesStore();
const permissionsStore = usePermissionsStore();

// Global store access
app.config.globalProperties.$stores = {
  auth: authStore,
  dashboard: dashboardStore,
  users: usersStore,
  roles: rolesStore,
  permissions: permissionsStore
};

// Mount the app
app.mount('#app');

// Development helpers
if (import.meta.env.DEV) {
  // Make stores available globally for debugging
  window.$stores = {
    auth: authStore,
    dashboard: dashboardStore,
    users: usersStore,
    roles: rolesStore,
    permissions: permissionsStore
  };
  
  console.log('🚀 DocRatech Medical Website Platform initialized');
  console.log('📦 Available stores:', Object.keys(window.$stores));
}
