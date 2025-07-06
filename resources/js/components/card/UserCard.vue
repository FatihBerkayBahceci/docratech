<!--
  Project: Enterprise Health UserCard
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div class="user-card" :class="{ loading: loading }" role="region" :aria-label="`User card for ${user.name}`" tabindex="0">
    <div class="user-avatar">
      <img 
        v-if="!loading"
        :src="user.profile_photo || defaultAvatar(user.name)" 
        :alt="`${user.name} avatar`" 
        loading="lazy"
      />
      <div v-else class="avatar-shimmer" aria-hidden="true"></div>
      <div class="user-status" :class="{ 'online': user.is_active }" :aria-label="user.is_active ? 'Online' : 'Offline'" role="status"></div>
      <div class="avatar-glow" aria-hidden="true"></div>
    </div>
    <div class="user-info">
      <div class="user-name" tabindex="0">
        <span v-if="!loading">{{ user.name }}</span>
        <div v-else class="text-shimmer" aria-hidden="true"></div>
      </div>
      <div class="user-email" tabindex="0">
        <span v-if="!loading">{{ user.email }}</span>
        <div v-else class="text-shimmer-small" aria-hidden="true"></div>
      </div>
      <div class="user-role">
        <span v-if="!loading" class="badge bg-primary" :aria-label="`Role: ${user.role?.display_name || 'No Role'}`">
          {{ user.role?.display_name || 'No Role' }}
        </span>
        <div v-else class="badge-shimmer" aria-hidden="true"></div>
      </div>
    </div>
    <div class="user-actions" role="group" aria-label="User actions">
      <button 
        class="action-btn edit" 
        @click="$emit('edit', user)" 
        title="Edit User" 
        aria-label="Edit user"
        type="button"
      >
        <i class="bi bi-pencil" aria-hidden="true"></i>
      </button>
      <button 
        class="action-btn delete" 
        @click="$emit('delete', user)" 
        title="Delete User" 
        aria-label="Delete user"
        type="button"
      >
        <i class="bi bi-trash" aria-hidden="true"></i>
      </button>
      <button 
        class="action-btn status" 
        @click="$emit('status-toggle', user)" 
        :title="user.is_active ? 'Set user offline' : 'Set user online'" 
        :aria-label="user.is_active ? 'Set user offline' : 'Set user online'"
        type="button"
      >
        <i :class="user.is_active ? 'bi bi-toggle-on' : 'bi bi-toggle-off'" aria-hidden="true"></i>
      </button>
    </div>
    <div class="card-glow" aria-hidden="true"></div>
  </div>
</template>

<script>
export default {
  name: 'UserCard',
  props: {
    user: { type: Object, required: true },
    loading: { type: Boolean, default: false }
  },
  methods: {
    defaultAvatar(name) {
      return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=0D8ABC&color=fff&size=64`;
    }
  }
};
</script>

<style scoped>
.user-card {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 12px -2px rgba(109,40,217,0.08);
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 18px;
  transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
  position: relative;
  overflow: hidden;
  cursor: pointer;
  outline-offset: 2px;
}

.user-card:focus-visible {
  outline: 3px solid #6D28D9;
}

.user-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(109,40,217,0.05), transparent);
  transition: left 0.6s;
  pointer-events: none;
}

.user-card:hover::before {
  left: 100%;
}

.user-card:hover {
  box-shadow: 0 8px 32px -8px rgba(109,40,217,0.18);
  transform: translateY(-4px) scale(1.02);
}

.user-card:active {
  transform: translateY(-2px) scale(1.01);
  transition: transform 0.1s cubic-bezier(0.22, 1, 0.36, 1);
}

.user-avatar {
  position: relative;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
  transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}

.user-card:hover .user-avatar {
  transform: scale(1.1);
}

.user-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border: 2px solid #6D28D9;
  transition: all 0.3s ease;
}

.user-card:hover .user-avatar img {
  border-color: #9333EA;
  box-shadow: 0 0 20px rgba(109, 40, 217, 0.3);
}

.user-status {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #ef4444;
  border: 2px solid white;
  transition: all 0.3s ease;
}

.user-status.online {
  background: #10b981;
  animation: status-pulse 2s ease-in-out infinite;
}

@keyframes status-pulse {
  0%, 100% { 
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    transform: scale(1);
  }
  50% { 
    box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
    transform: scale(1.2);
  }
}

.avatar-glow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  border-radius: 50%;
  opacity: 0;
  background: radial-gradient(circle, rgba(109, 40, 217, 0.2) 0%, transparent 70%);
  transition: opacity 0.3s ease;
  pointer-events: none;
}

.user-card:hover .avatar-glow {
  opacity: 1;
}

.user-info {
  flex: 1;
  transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}

.user-card:hover .user-info {
  transform: translateX(4px);
}

.user-name {
  font-weight: 700;
  font-size: 1.1rem;
  margin-bottom: 4px;
  transition: color 0.3s ease;
}

.user-card:hover .user-name {
  color: #6D28D9;
}

.user-email {
  color: #6b7280;
  font-size: 0.98rem;
  margin-bottom: 8px;
  transition: color 0.3s ease;
}

.user-card:hover .user-email {
  color: #374151;
}

.user-role .badge {
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

.user-card:hover .user-role .badge {
  transform: scale(1.05);
  box-shadow: 0 2px 8px rgba(109, 40, 217, 0.2);
}

.user-actions {
  display: flex;
  gap: 8px;
  opacity: 0.7;
  transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}

.user-card:hover .user-actions {
  opacity: 1;
  transform: translateX(-4px);
}

.action-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.2rem;
  padding: 8px;
  border-radius: 8px;
  transition: all 0.2s cubic-bezier(0.22, 1, 0.36, 1);
  position: relative;
  overflow: hidden;
}

.action-btn::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(109, 40, 217, 0.1);
  transform: translate(-50%, -50%);
  transition: width 0.3s, height 0.3s;
}

.action-btn:hover::before {
  width: 100%;
  height: 100%;
}

.action-btn.edit { color: #2563eb; }
.action-btn.edit:hover { 
  background: #dbeafe;
  transform: scale(1.1);
}

.action-btn.delete { color: #ef4444; }
.action-btn.delete:hover { 
  background: #fef2f2;
  transform: scale(1.1);
}

.action-btn.status { color: #10b981; }
.action-btn.status:hover { 
  background: #d1fae5;
  transform: scale(1.1);
}

/* Card Glow Effect */
.card-glow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  border-radius: 16px;
  opacity: 0;
  background: radial-gradient(circle at center, rgba(109, 40, 217, 0.1) 0%, transparent 70%);
  transition: opacity 0.3s ease;
  pointer-events: none;
}

.user-card:hover .card-glow {
  opacity: 1;
}

/* Loading Shimmer Effects */
.avatar-shimmer {
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  border-radius: 50%;
  animation: shimmer 1.5s infinite;
}

.text-shimmer {
  width: 120px;
  height: 20px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  border-radius: 4px;
  animation: shimmer 1.5s infinite;
}

.text-shimmer-small {
  width: 80px;
  height: 16px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  border-radius: 4px;
  animation: shimmer 1.5s infinite;
}

.badge-shimmer {
  width: 60px;
  height: 20px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  border-radius: 12px;
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

/* Entrance Animation */
.user-card {
  animation: card-entrance 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
  animation-delay: calc(var(--card-index, 0) * 0.1s);
}

@keyframes card-entrance {
  0% {
    opacity: 0;
    transform: translateY(20px) scale(0.9);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Elastic Hover Effect */
.user-card:hover {
  animation: elastic-hover 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}

@keyframes elastic-hover {
  0% { transform: translateY(0) scale(1); }
  50% { transform: translateY(-8px) scale(1.03); }
  100% { transform: translateY(-4px) scale(1.02); }
}
</style>
