// Developer: DocraTech Team | Fatih Berkay Bahceci
// Language: American English (US)
// License: MIT
// Project: DocraTech Medical Website Platform
import { defineStore } from 'pinia'
export const useMenuStore = defineStore('menu', {
  state: () => ({
    items: [
      { label: 'Dashboard', to: '/dashboard', icon: 'home' },
      { label: 'Users', to: '/users', icon: 'users' },
      { label: 'Roles', to: '/roles', icon: 'shield' },
      { label: 'Permissions', to: '/permissions', icon: 'key' },
      { label: 'Support', to: '/support', icon: 'help-circle' },
      { label: 'Notifications', to: '/notifications', icon: 'bell' },
      { label: 'Logs', to: '/logs', icon: 'file-text' },
      { label: 'Activity', to: '/activity', icon: 'activity' },
      { label: 'Settings', to: '/settings', icon: 'settings' },
      { label: 'Profile', to: '/profile', icon: 'user' },
      // Advanced/Admin Components
      { label: 'Roles Manager', to: '/admin/roles-manager', icon: 'users-cog' },
      { label: 'Notification Center', to: '/advanced/notification-center', icon: 'bell' },
      { label: 'Data Grid', to: '/advanced/data-grid', icon: 'grid' },
      { label: 'Kanban Board', to: '/advanced/kanban-board', icon: 'columns' },
      { label: 'Gantt Chart', to: '/advanced/gantt-chart', icon: 'bar-chart' },
      { label: 'File Upload', to: '/advanced/file-upload', icon: 'upload' },
      { label: 'Command Palette', to: '/advanced/command-palette', icon: 'command' }
    ]
  })
}) 