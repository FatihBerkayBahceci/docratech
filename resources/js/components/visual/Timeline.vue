<!-- Developer: DocraTech Team - Fatih Berkay Bahceci -->
<template>
  <div class="timeline-container">
    <div v-if="!items || !items.length" class="text-center py-8 text-gray-500">
      No timeline items to display
    </div>
    <div v-else class="timeline">
      <div 
        v-for="(item, index) in items" 
        :key="item.id || index"
        class="timeline-item"
        :class="{ 'timeline-item-last': index === items.length - 1 }"
      >
        <!-- Timeline marker -->
        <div class="timeline-marker">
          <div 
            class="timeline-dot"
            :class="getMarkerClass(item.type || item.status)"
          >
            <svg v-if="item.icon" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
              <path :d="getIconPath(item.icon)"/>
            </svg>
          </div>
          <div 
            v-if="index !== items.length - 1"
            class="timeline-line"
          ></div>
        </div>
        
        <!-- Timeline content -->
        <div class="timeline-content">
          <div class="timeline-card">
            <div class="timeline-header">
              <h4 class="timeline-title">{{ item.title || item.name }}</h4>
              <span class="timeline-time">{{ formatTime(item.timestamp || item.created_at) }}</span>
            </div>
            
            <p v-if="item.description" class="timeline-description">
              {{ item.description }}
            </p>
            
            <!-- Additional metadata -->
            <div v-if="item.metadata || item.user" class="timeline-metadata">
              <span v-if="item.user" class="timeline-user">
                {{ item.user.name || item.user }}
              </span>
              <span v-if="item.ip_address" class="timeline-ip">
                {{ item.ip_address }}
              </span>
              <span v-if="item.action" class="timeline-action">
                {{ item.action }}
              </span>
            </div>
            
            <!-- Tags -->
            <div v-if="item.tags && item.tags.length" class="timeline-tags">
              <span 
                v-for="tag in item.tags" 
                :key="tag"
                class="timeline-tag"
              >
                {{ tag }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  items: {
    type: Array,
    default: () => []
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'compact', 'detailed'].includes(value)
  }
})

const getMarkerClass = (type) => {
  const classes = {
    success: 'bg-green-500',
    warning: 'bg-yellow-500',
    error: 'bg-red-500',
    info: 'bg-blue-500',
    login: 'bg-green-500',
    logout: 'bg-gray-500',
    create: 'bg-blue-500',
    update: 'bg-yellow-500',
    delete: 'bg-red-500',
    active: 'bg-green-500',
    inactive: 'bg-gray-500',
    pending: 'bg-yellow-500',
    default: 'bg-gray-400'
  }
  return classes[type] || classes.default
}

const getIconPath = (iconName) => {
  const icons = {
    check: 'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z',
    warning: 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z',
    error: 'M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z',
    info: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z',
    user: 'M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z',
    login: 'M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 11-2 0V4H5v12h10v-2a1 1 0 112 0v3a1 1 0 01-1 1H4a1 1 0 01-1-1V3z',
    logout: 'M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a2 2 0 00-2-2H5a1 1 0 000 2z'
  }
  return icons[iconName] || icons.info
}

const formatTime = (timestamp) => {
  if (!timestamp) return ''
  
  const date = new Date(timestamp)
  const now = new Date()
  const diff = now - date
  
  // Less than 1 minute
  if (diff < 60000) {
    return 'Just now'
  }
  
  // Less than 1 hour
  if (diff < 3600000) {
    const minutes = Math.floor(diff / 60000)
    return `${minutes}m ago`
  }
  
  // Less than 1 day
  if (diff < 86400000) {
    const hours = Math.floor(diff / 3600000)
    return `${hours}h ago`
  }
  
  // Less than 1 week
  if (diff < 604800000) {
    const days = Math.floor(diff / 86400000)
    return `${days}d ago`
  }
  
  // More than 1 week
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
  })
}
</script>

<style scoped>
.timeline {
  position: relative;
}

.timeline-item {
  display: flex;
  margin-bottom: 1.5rem;
}

.timeline-item-last {
  margin-bottom: 0;
}

.timeline-marker {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-right: 1rem;
  position: relative;
}

.timeline-dot {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid white;
  box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
  z-index: 1;
}

.timeline-line {
  width: 2px;
  flex: 1;
  background: #e5e7eb;
  margin-top: 0.5rem;
  min-height: 2rem;
}

.timeline-content {
  flex: 1;
  min-width: 0;
}

.timeline-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1rem;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease;
}

.timeline-card:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border-color: #d1d5db;
}

.timeline-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.5rem;
}

.timeline-title {
  font-weight: 600;
  color: #111827;
  margin: 0;
  flex: 1;
}

.timeline-time {
  font-size: 0.75rem;
  color: #6b7280;
  white-space: nowrap;
  margin-left: 1rem;
}

.timeline-description {
  color: #4b5563;
  margin-bottom: 0.75rem;
  line-height: 1.5;
}

.timeline-metadata {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
  font-size: 0.75rem;
}

.timeline-user,
.timeline-ip,
.timeline-action {
  background: #f3f4f6;
  color: #374151;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
}

.timeline-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 0.25rem;
}

.timeline-tag {
  background: #dbeafe;
  color: #1e40af;
  font-size: 0.625rem;
  padding: 0.125rem 0.375rem;
  border-radius: 0.25rem;
  font-weight: 500;
}

/* Responsive design */
@media (max-width: 640px) {
  .timeline-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .timeline-time {
    margin-left: 0;
    margin-top: 0.25rem;
  }
  
  .timeline-metadata {
    font-size: 0.625rem;
  }
}
</style>
