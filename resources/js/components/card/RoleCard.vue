<!--
  Project: Enterprise Health RoleCard
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <article class="role-card" role="region" :aria-label="`Role: ${role.display_name}`">
    <div class="role-icon" aria-hidden="true">
      <i class="bi bi-shield-check"></i>
    </div>
    <div class="role-info">
      <h3 class="role-name" tabindex="0">{{ role.display_name }}</h3>
      <p class="role-description" tabindex="0">{{ role.description || 'No description available' }}</p>
      <div class="role-meta" aria-label="Role metadata">
        <span class="role-users" aria-live="polite" aria-atomic="true">{{ role.users_count || 0 }} users</span>
        <span class="role-permissions" aria-live="polite" aria-atomic="true">{{ role.permissions_count || 0 }} permissions</span>
      </div>
    </div>
    <nav class="role-actions" aria-label="Role actions">
      <button
        class="action-btn edit"
        @click="$emit('edit', role)"
        title="Edit Role"
        aria-label="Edit Role"
        type="button"
      >
        <i class="bi bi-pencil-square"></i>
      </button>
      <button
        class="action-btn permissions"
        @click="$emit('permissions', role)"
        title="Manage Permissions"
        aria-label="Manage Permissions"
        type="button"
      >
        <i class="bi bi-key"></i>
      </button>
      <button
        class="action-btn delete"
        @click="$emit('delete', role)"
        title="Delete Role"
        aria-label="Delete Role"
        type="button"
      >
        <i class="bi bi-trash"></i>
      </button>
    </nav>
  </article>
</template>

<script>
export default {
  name: 'RoleCard',
  props: {
    role: { type: Object, required: true }
  }
};
</script>

<style scoped>
.role-card {
  background: var(--color-surface, #fff);
  border-radius: 16px;
  box-shadow: 0 4px 12px -2px rgba(109,40,217,0.08);
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 18px;
  transition: box-shadow 0.2s ease, transform 0.2s ease;
  user-select: none;
}
.role-card:hover,
.role-card:focus-within {
  box-shadow: 0 8px 32px -8px rgba(109,40,217,0.18);
  transform: translateY(-2px) scale(1.02);
  outline: none;
}
.role-icon {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  background: linear-gradient(135deg, #6D28D9 0%, #9333EA 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: #fff;
  flex-shrink: 0;
}
.role-info {
  flex: 1;
  min-width: 0;
}
.role-name {
  font-weight: 700;
  font-size: 1.1rem;
  margin-bottom: 4px;
  color: var(--color-text-primary, #111827);
}
.role-description {
  color: var(--color-text-secondary, #6b7280);
  font-size: 0.95rem;
  margin-bottom: 8px;
  line-height: 1.4;
}
.role-meta {
  display: flex;
  gap: 16px;
  font-size: 0.85rem;
  color: var(--color-text-muted, #9ca3af);
  user-select: text;
}
.role-users, .role-permissions {
  white-space: nowrap;
}
.role-actions {
  display: flex;
  gap: 8px;
  flex-shrink: 0;
}
.action-btn {
  background: transparent;
  border: none;
  cursor: pointer;
  font-size: 1.2rem;
  padding: 8px;
  border-radius: 8px;
  transition: background-color 0.2s ease;
  color: var(--color-text-primary, #374151);
}
.action-btn:focus-visible {
  outline: 2px solid var(--color-primary, #6D28D9);
  outline-offset: 2px;
}
.action-btn.edit {
  color: #2563eb;
}
.action-btn.edit:hover,
.action-btn.edit:focus-visible {
  background: #dbeafe;
}
.action-btn.permissions {
  color: #10b981;
}
.action-btn.permissions:hover,
.action-btn.permissions:focus-visible {
  background: #d1fae5;
}
.action-btn.delete {
  color: #ef4444;
}
.action-btn.delete:hover,
.action-btn.delete:focus-visible {
  background: #fef2f2;
}

/* Responsive */
@media (max-width: 640px) {
  .role-card {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
  }
  .role-icon {
    width: 48px;
    height: 48px;
    font-size: 1.25rem;
  }
  .role-actions {
    width: 100%;
    justify-content: flex-start;
    gap: 12px;
  }
}
</style>
