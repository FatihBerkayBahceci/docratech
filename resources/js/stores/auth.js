import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authService } from '@/services/api';

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null);
  const token = ref(null);
  const refreshToken = ref(null);
  const permissions = ref([]);
  const roles = ref([]);
  const isAuthenticated = ref(false);
  const loading = ref(false);
  const toasts = ref([]);

  // Getters
  const hasPermission = computed(() => {
    return (permission) => {
      if (!permission) return false;
      
      // If user is admin, they have all permissions
      if (isAdmin.value) {
        console.log(`🔒 Admin access granted for: "${permission}"`);
        return true;
      }
      
      // Convert permissions to array if it's a Proxy
      const permissionsArray = Array.isArray(permissions.value) ? permissions.value : Array.from(permissions.value || []);
      
      // Check for wildcard permission
      if (permissionsArray.includes('*')) return true;
      
      // Check for exact permission match
      const hasExact = permissionsArray.includes(permission);
      
      // Debug logging in development
      if (process.env.NODE_ENV === 'development') {
        console.log(`🔒 Permission check: "${permission}"`, {
          hasExact,
          isAdmin: isAdmin.value,
          userPermissions: permissionsArray,
          userRoles: Array.from(roles.value || []),
          permissionsCount: permissionsArray.length,
          rolesCount: roles.value?.length || 0
        });
      }
      
      return hasExact;
    };
  });

  const hasRole = computed(() => {
    return (role) => {
      if (!roles.value || !roles.value.length) return false;
      // Convert to array if it's a Proxy to ensure proper includes() method
      const rolesArray = Array.isArray(roles.value) ? roles.value : Array.from(roles.value);
      return rolesArray.includes(role);
    };
  });

  const isAdmin = computed(() => {
    // Handle different role name formats
    return hasRole.value('admin') || 
           hasRole.value('super-admin') || 
           hasRole.value('Super Admin') ||
           hasRole.value('super_admin');
  });

  const canAccess = computed(() => {
    return (requiredPermissions = []) => {
      if (!requiredPermissions.length) return true;
      if (isAdmin.value) return true;
      return requiredPermissions.some(permission => hasPermission.value(permission));
    };
  });

  // Actions
  const login = async (credentials) => {
    loading.value = true;
    try {
      const response = await authService.login(credentials);
      // Suspended user: just return the response, do not redirect here
      if (response.status === 'suspended') {
        loading.value = false;
        return response;
      }
      // Set authentication data
      user.value = response.user;
      token.value = response.token || response.access_token;
      refreshToken.value = response.refresh_token;
      permissions.value = response.permissions || [];
      roles.value = response.roles || [];
      isAuthenticated.value = true;
      
      // Save to localStorage
      saveToStorage();
      
      // Show success message
      showToast({
        type: 'success',
        title: 'Welcome back!',
        message: 'You have successfully signed in.'
      });
      
      return response;
    } catch (error) {
      console.error('Login error:', error);
      throw error;
    } finally {
      loading.value = false;
    }
  };

  const register = async (userData) => {
    loading.value = true;
    try {
      const response = await authService.register(userData);
      
      // Set authentication data
      user.value = response.user;
      token.value = response.access_token;
      refreshToken.value = response.refresh_token;
      permissions.value = response.permissions || [];
      roles.value = response.roles || [];
      isAuthenticated.value = true;
      
      // Save to localStorage
      saveToStorage();
      
      // Show success message
      showToast({
        type: 'success',
        title: 'Account created!',
        message: 'Your account has been created successfully.'
      });
      
      return response;
    } catch (error) {
      console.error('Register error:', error);
      throw error;
    } finally {
      loading.value = false;
    }
  };

  const logout = async () => {
    try {
      if (token.value) {
        await authService.logout();
      }
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      // Clear authentication data
      user.value = null;
      token.value = null;
      refreshToken.value = null;
      permissions.value = [];
      roles.value = [];
      isAuthenticated.value = false;
      
      // Clear localStorage
      clearStorage();
      
      // Show success message
      showToast({
        type: 'info',
        title: 'Signed out',
        message: 'You have been successfully signed out.'
      });
    }
  };

  const refreshTokenAction = async () => {
    console.log('>>> refreshTokenAction çağrıldı');
    if (!refreshToken.value) {
      console.error('>>> refreshTokenAction: No refresh token available');
      throw new Error('No refresh token available');
    }
    try {
      const response = await authService.refresh(refreshToken.value);
      console.log('>>> refreshTokenAction başarılı', response);
      // Update tokens
      token.value = response.token || response.access_token;
      if (response.refresh_token) {
        refreshToken.value = response.refresh_token;
      }
      if (response.user) {
        user.value = response.user;
      }
      if (response.permissions) {
        permissions.value = response.permissions;
      }
      if (response.roles) {
        roles.value = response.roles;
      }
      saveToStorage();
      return response;
    } catch (error) {
      console.error('>>> refreshTokenAction hata:', error);
      await logout();
      throw error;
    }
  };

  const forgotPassword = async (email) => {
    loading.value = true;
    try {
      const response = await authService.forgotPassword(email);
      
      showToast({
        type: 'success',
        title: 'Reset link sent',
        message: 'Password reset link has been sent to your email.'
      });
      
      return response;
    } catch (error) {
      console.error('Forgot password error:', error);
      throw error;
    } finally {
      loading.value = false;
    }
  };

  const resetPassword = async (data) => {
    loading.value = true;
    try {
      const response = await authService.resetPassword(data);
      
      showToast({
        type: 'success',
        title: 'Password reset',
        message: 'Your password has been reset successfully.'
      });
      
      return response;
    } catch (error) {
      console.error('Reset password error:', error);
      throw error;
    } finally {
      loading.value = false;
    }
  };

  const getProfile = async () => {
    try {
      const response = await authService.getProfile();
      user.value = response.user;
      permissions.value = response.permissions || [];
      roles.value = response.roles || [];
      
      // Save to localStorage
      saveToStorage();
      
      return response;
    } catch (error) {
      console.error('Get profile error:', error);
      throw error;
    }
  };

  const updateProfile = async (data) => {
    loading.value = true;
    try {
      const response = await authService.updateProfile(data);
      user.value = response.user;
      
      // Save to localStorage
      saveToStorage();
      
      showToast({
        type: 'success',
        title: 'Profile updated',
        message: 'Your profile has been updated successfully.'
      });
      
      return response;
    } catch (error) {
      console.error('Update profile error:', error);
      throw error;
    } finally {
      loading.value = false;
    }
  };

  // Storage management
  const saveToStorage = () => {
    if (typeof window !== 'undefined') {
      const authData = {
        user: user.value,
        token: token.value,
        refreshToken: refreshToken.value,
        permissions: permissions.value,
        roles: roles.value,
        timestamp: Date.now()
      };
      
      localStorage.setItem('auth_user', JSON.stringify(user.value));
      localStorage.setItem('auth_token', token.value);
      localStorage.setItem('auth_refresh_token', refreshToken.value);
      localStorage.setItem('auth_permissions', JSON.stringify(permissions.value));
      localStorage.setItem('auth_roles', JSON.stringify(roles.value));
      localStorage.setItem('auth_timestamp', authData.timestamp.toString());
    }
  };

  const loadFromStorage = () => {
    if (typeof window !== 'undefined') {
      const storedUser = localStorage.getItem('auth_user');
      const storedToken = localStorage.getItem('auth_token');
      const storedRefreshToken = localStorage.getItem('auth_refresh_token');
      const storedPermissions = localStorage.getItem('auth_permissions');
      const storedRoles = localStorage.getItem('auth_roles');
      const storedTimestamp = localStorage.getItem('auth_timestamp');
      
      // Check if auth data exists and is not too old (7 days max)
      if (storedUser && storedToken && storedTimestamp) {
        const timestamp = parseInt(storedTimestamp);
        const maxAge = 7 * 24 * 60 * 60 * 1000; // 7 days in milliseconds
        const isExpired = Date.now() - timestamp > maxAge;
        
        if (!isExpired) {
          user.value = JSON.parse(storedUser);
          token.value = storedToken;
          refreshToken.value = storedRefreshToken;
          permissions.value = storedPermissions ? JSON.parse(storedPermissions) : [];
          roles.value = storedRoles ? JSON.parse(storedRoles) : [];
          isAuthenticated.value = true;
        } else {
          // Clear expired auth data
          console.log('Auth data expired, clearing storage');
          clearStorage();
        }
      }
    }
  };

  const clearStorage = () => {
    if (typeof window !== 'undefined') {
      localStorage.removeItem('auth_user');
      localStorage.removeItem('auth_token');
      localStorage.removeItem('auth_refresh_token');
      localStorage.removeItem('auth_permissions');
      localStorage.removeItem('auth_roles');
      localStorage.removeItem('auth_timestamp');
    }
  };

  // Toast management
  const showToast = (toast) => {
    const id = Date.now();
    const newToast = {
      id,
      ...toast,
      timestamp: new Date()
    };
    
    toasts.value.push(newToast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
      removeToast(id);
    }, 5000);
  };

  const removeToast = (id) => {
    const index = toasts.value.findIndex(toast => toast.id === id);
    if (index > -1) {
      toasts.value.splice(index, 1);
    }
  };

  const clearToasts = () => {
    toasts.value = [];
  };

  // Initialize store
  const initialize = () => {
    console.log('>>> AuthStore initialize başladı');
    loadFromStorage();
    console.log('>>> AuthStore storage sonrası:', { token: token.value, refreshToken: refreshToken.value, user: user.value });
    if (token.value && refreshToken.value) {
      isAuthenticated.value = true;
      console.log('>>> AuthStore isAuthenticated TRUE');
    } else {
      isAuthenticated.value = false;
      console.log('>>> AuthStore isAuthenticated FALSE');
    }
  };

  return {
    // State
    user,
    token,
    refreshToken,
    permissions,
    roles,
    isAuthenticated,
    loading,
    toasts,
    
    // Getters
    hasPermission,
    hasRole,
    isAdmin,
    canAccess,
    
    // Actions
    login,
    register,
    logout,
    refreshToken: refreshTokenAction,
    forgotPassword,
    resetPassword,
    getProfile,
    updateProfile,
    
    // Storage
    saveToStorage,
    loadFromStorage,
    clearStorage,
    
    // Toast
    showToast,
    removeToast,
    clearToasts,
    
    // Initialize
    initialize
  };
}); 