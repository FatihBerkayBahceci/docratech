import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import Suspended from './views/Suspended.vue'

// Import views
import Dashboard from './views/Dashboard.vue'
import Login from './views/auth/Login.vue'
import ComponentShowcase from './views/ComponentShowcase.vue'

// Lazy-loaded components for better performance
const Users = () => import('./views/Users.vue');
const Roles = () => import('./views/Roles.vue');

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: {
      title: 'Login - DocraTech Admin Panel',
      requiresGuest: true,
      layout: 'auth'
    }
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('./views/auth/Register.vue'),
    meta: {
      title: 'Register - DocraTech',
      requiresGuest: true,
      layout: 'auth'
    }
  },
  {
    path: '/forgot-password',
    name: 'forgot-password',
    component: () => import('./views/auth/ForgotPassword.vue'),
    meta: {
      title: 'Forgot Password - DocraTech',
      requiresGuest: true,
      layout: 'auth'
    }
  },
  {
    path: '/reset-password',
    name: 'reset-password',
    component: () => import('./views/auth/ResetPassword.vue'),
    meta: {
      title: 'Reset Password - DocraTech',
      requiresGuest: true,
      layout: 'auth'
    }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: {
      title: 'Dashboard - DocraTech',
      requiresAuth: true,
      layout: 'app'
    }
  },
  {
    path: '/users',
    name: 'users',
    component: Users,
    meta: {
      title: 'User Management - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['users.view']
    }
  },
  {
    path: '/users/create',
    name: 'users.create',
    component: () => import('./views/Users/Create.vue'),
    meta: {
      title: 'Create User - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['users.create']
    }
  },
  {
    path: '/users/:id',
    name: 'users.show',
    component: () => import('./views/Users/Show.vue'),
    meta: {
      title: 'User Details - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['users.view']
    }
  },
  {
    path: '/users/:id/edit',
    name: 'users.edit',
    component: () => import('./views/Users/Edit.vue'),
    meta: {
      title: 'Edit User - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['users.edit']
    }
  },
  {
    path: '/roles',
    name: 'roles',
    component: Roles,
    meta: {
      title: 'Role Management - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['roles.view']
    }
  },
  {
    path: '/roles/create',
    name: 'roles.create',
    component: () => import('./views/Roles/Create.vue'),
    meta: {
      title: 'Create Role - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['roles.create']
    }
  },
  {
    path: '/roles/:id',
    name: 'roles.show',
    component: () => import('./views/Roles/Show.vue'),
    meta: {
      title: 'Role Details - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['roles.view']
    }
  },
  {
    path: '/roles/:id/edit',
    name: 'roles.edit',
    component: () => import('./views/Roles/Edit.vue'),
    meta: {
      title: 'Edit Role - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['roles.edit']
    }
  },
  {
    path: '/permissions',
    name: 'permissions',
    component: () => import('./views/EnterprisePermissions.vue'),
    meta: {
      title: 'Permission Management - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['permissions.view']
    }
  },
  {
    path: '/permissions/modules/:moduleName',
    name: 'permissions.module',
    component: () => import('./views/ModuleDetail.vue'),
    meta: {
      title: 'Module Detail - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['permissions.view']
    }
  },
  {
    path: '/permissions/create',
    name: 'permissions.create',
    component: () => import('./views/Permissions/Create.vue'),
    meta: {
      title: 'Create Permission - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['permissions.create']
    }
  },
  {
    path: '/permissions/:id',
    name: 'permissions.show',
    component: () => import('./views/Permissions/Show.vue'),
    meta: {
      title: 'Permission Details - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['permissions.view']
    }
  },
  {
    path: '/permissions/:id/edit',
    name: 'permissions.edit',
    component: () => import('./views/Permissions/Edit.vue'),
    meta: {
      title: 'Edit Permission - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['permissions.edit']
    }
  },
  {
    path: '/components',
    name: 'components',
    component: ComponentShowcase,
    meta: {
      title: 'Component Showcase - DocraTech',
      requiresAuth: true,
      layout: 'app'
    }
  },
  {
    path: '/test',
    name: 'test',
    component: () => import('./views/Dashboard.vue'), // Reuse dashboard component for testing
    meta: {
      title: 'Test Page - DocraTech',
      requiresAuth: true,
      layout: 'app'
      // No permissions required - for testing navigation
    }
  },
  {
    path: '/profile',
    name: 'profile',
    component: () => import('./views/Profile.vue'),
    meta: {
      title: 'Profile - DocraTech',
      requiresAuth: true,
      layout: 'app'
    }
  },
  {
    path: '/settings',
    name: 'settings',
    component: () => import('./views/Settings.vue'),
    meta: {
      title: 'Settings - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['settings.view']
    }
  },
  {
    path: '/activity',
    name: 'activity',
    component: () => import('./views/Activity.vue'),
    meta: {
      title: 'Activity Log - DocraTech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['activity.view']
    }
  },
  {
    path: '/logs',
    name: 'logs',
    component: () => import('./views/Logs.vue'),
    meta: {
      title: 'System Logs - DocRatech',
      requiresAuth: true,
      layout: 'app',
      permissions: ['logs.view']
    }
  },
  {
    path: '/notifications',
    name: 'notifications',
    component: () => import('./views/Notifications.vue'),
    meta: {
      title: 'Notifications - DocRatech',
      requiresAuth: true,
      layout: 'app'
    }
  },
  {
    path: '/support',
    name: 'support',
    component: () => import('./views/Support.vue'),
    meta: {
      title: 'Support - DocRatech',
      requiresAuth: true,
      layout: 'app'
    }
  },
  {
    path: '/suspended',
    name: 'suspended',
    component: Suspended,
    meta: {
      title: 'Account Suspended - DocraTech',
      requiresAuth: false,
      layout: 'none',
      public: true
    }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('./views/NotFound.vue'),
    meta: {
      title: 'Page Not Found - DocRatech',
      layout: 'app'
    }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0 };
    }
  }
});

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  
  // Update page title
  if (to.meta.title) {
    document.title = to.meta.title;
  }
  
  // Check if user is authenticated
  const isAuthenticated = authStore.isAuthenticated;
  
  // Suspended user: force redirect to Suspended page
  if (isAuthenticated && authStore.user?.status === 'suspended' && to.name !== 'suspended') {
    next({ name: 'suspended' });
    return;
  }
  
  // Handle guest-only routes (login, register, etc.)
  if (to.meta.requiresGuest) {
    if (isAuthenticated) {
      next({ name: 'dashboard' });
      return;
    }
    next();
    return;
  }
  
  // Handle protected routes
  if (to.meta.requiresAuth) {
    if (!isAuthenticated) {
      // If we have a refresh token, try to refresh
      if (authStore.refreshToken) {
        try {
          console.log('Attempting token refresh for protected route');
          await authStore.refreshToken();
          if (authStore.isAuthenticated) {
            console.log('Token refresh successful, proceeding to route');
            next();
            return;
          }
        } catch (error) {
          console.error('Token refresh failed:', error);
          // Clear any remaining auth data
          await authStore.logout();
        }
      }
      // Redirect to login
      console.log('Authentication required, redirecting to login');
      next({
        name: 'login',
        query: { redirect: to.fullPath }
      });
      return;
    }
    
    // Check permissions
    if (to.meta.permissions) {
      console.log('🔒 Checking permissions for route:', to.name);
      console.log('📋 Required permissions:', to.meta.permissions);
      console.log('👤 User permissions:', authStore.permissions);
      console.log('🎭 User roles:', authStore.roles);
      console.log('👑 Is admin:', authStore.isAdmin);
      
      const hasPermission = to.meta.permissions.some(permission =>
        authStore.hasPermission(permission)
      );
      
      console.log('✅ Permission check result:', hasPermission);
      
      if (!hasPermission) {
        console.warn(`❌ Access denied to ${to.path}. User lacks required permissions: ${to.meta.permissions.join(', ')}`);
        
        // Show user-friendly message about missing permissions
        if (authStore.showToast) {
          authStore.showToast({
            type: 'warning',
            title: 'Access Denied',
            message: `You don't have permission to access ${to.meta.title || to.name}. Contact your administrator if you need access.`
          });
        }
        
        next({ name: 'dashboard' });
        return;
      }
      
      console.log(`✅ Access granted to ${to.path}`);
    }
  }
  
  next();
});

// After navigation
router.afterEach((to, from) => {
  // Track page views
  if (to.name && to.name !== from.name) {
    // Analytics tracking could go here
    console.log(`🎯 Successfully navigated from ${from.name || 'unknown'} to: ${to.name}`);
    
    // Log route info in development
    if (process.env.NODE_ENV === 'development') {
      console.log('📍 Route details:', {
        path: to.path,
        name: to.name,
        meta: to.meta,
        params: to.params,
        query: to.query
      });
    }
  }
});

export default router; 