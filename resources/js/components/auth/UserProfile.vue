<template>
  <div class="user-dropdown" ref="dropdownRef">
    <button
      type="button"
      class="user-dropdown-trigger"
      @click="toggleDropdown"
      :aria-expanded="isOpen"
      aria-haspopup="true"
      @keydown.enter.prevent="toggleDropdown"
      @keydown.space.prevent="toggleDropdown"
      ref="triggerButton"
    >
      <Avatar
        :src="user?.avatar"
        :alt="user?.name"
        size="sm"
        class="user-avatar"
      />
      <div class="user-info">
        <span class="user-name">{{ user?.name || 'User' }}</span>
        <span class="user-role">{{ user?.role || 'Guest' }}</span>
      </div>
      <Icon
        name="chevron-down"
        size="sm"
        class="dropdown-icon"
        :class="{ 'dropdown-icon-rotated': isOpen }"
      />
    </button>

    <Transition name="dropdown">
      <div
        v-if="isOpen"
        id="user-menu"
        class="user-dropdown-menu"
        role="menu"
        tabindex="-1"
        @keydown.prevent.stop="handleKeydown"
      >
        <div class="dropdown-header">
          <Avatar
            :src="user?.avatar"
            :alt="user?.name"
            size="md"
            class="header-avatar"
          />
          <div class="header-info">
            <div class="header-name">{{ user?.name || 'User' }}</div>
            <div class="header-email">{{ user?.email || 'user@example.com' }}</div>
          </div>
        </div>

        <div class="dropdown-divider"></div>

        <div class="dropdown-items">
          <router-link
            to="/profile"
            class="dropdown-item"
            role="menuitem"
            tabindex="0"
            @click="closeDropdown"
          >
            <Icon name="user" size="sm" />
            <span>Profile</span>
          </router-link>

          <router-link
            to="/settings"
            class="dropdown-item"
            role="menuitem"
            tabindex="0"
            @click="closeDropdown"
          >
            <Icon name="settings" size="sm" />
            <span>Settings</span>
          </router-link>

          <div class="dropdown-divider"></div>

          <button
            type="button"
            class="dropdown-item dropdown-item-danger"
            role="menuitem"
            tabindex="0"
            @click="handleLogout"
          >
            <Icon name="log-out" size="sm" />
            <span>Sign Out</span>
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Avatar from '@/components/media/Avatar.vue'
import Icon from '@/components/visual/Icon.vue'

export default {
  name: 'UserDropdown',
  components: {
    Avatar,
    Icon
  },
  setup() {
    const router = useRouter()
    const authStore = useAuthStore()
    const dropdownRef = ref(null)
    const triggerButton = ref(null)
    const isOpen = ref(false)
    const focusIndex = ref(-1)

    const user = computed(() => authStore.user)

    const toggleDropdown = () => {
      if (isOpen.value) closeDropdown()
      else openDropdown()
    }

    const openDropdown = () => {
      isOpen.value = true
      focusIndex.value = -1
      nextTick(() => {
        focusFirstItem()
      })
    }

    const closeDropdown = () => {
      isOpen.value = false
      focusIndex.value = -1
      nextTick(() => {
        if (triggerButton.value) triggerButton.value.focus()
      })
    }

    const focusFirstItem = () => {
      const items = getMenuItems()
      if (items.length > 0) {
        focusIndex.value = 0
        items[0].focus()
      }
    }

    const getMenuItems = () => {
      if (!dropdownRef.value) return []
      return [...dropdownRef.value.querySelectorAll('.dropdown-item')]
    }

    const handleKeydown = (event) => {
      if (!isOpen.value) return
      const items = getMenuItems()
      if (items.length === 0) return

      switch(event.key) {
        case 'ArrowDown':
          event.preventDefault()
          focusIndex.value = (focusIndex.value + 1) % items.length
          items[focusIndex.value].focus()
          break
        case 'ArrowUp':
          event.preventDefault()
          focusIndex.value = (focusIndex.value - 1 + items.length) % items.length
          items[focusIndex.value].focus()
          break
        case 'Tab':
          if (event.shiftKey && focusIndex.value === 0) {
            event.preventDefault()
            focusIndex.value = items.length - 1
            items[focusIndex.value].focus()
          } else if (!event.shiftKey && focusIndex.value === items.length - 1) {
            event.preventDefault()
            focusIndex.value = 0
            items[focusIndex.value].focus()
          }
          break
        case 'Escape':
          closeDropdown()
          break
      }
    }

    const handleLogout = async () => {
      try {
        await authStore.logout()
        router.push('/login')
      } catch (error) {
        console.error('Logout failed:', error)
      }
    }

    const handleClickOutside = (event) => {
      if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        closeDropdown()
      }
    }

    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
    })

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
    })

    return {
      dropdownRef,
      triggerButton,
      isOpen,
      user,
      toggleDropdown,
      closeDropdown,
      handleLogout,
      handleKeydown
    }
  }
}
</script>

<style scoped>
.user-dropdown {
  position: relative;
}

.user-dropdown-trigger {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;
  background: transparent;
  border: none;
  border-radius: var(--border-radius);
  cursor: pointer;
  transition: var(--transition);
  color: var(--color-text);
}

.user-dropdown-trigger:hover {
  background: var(--color-background-hover);
}

.user-info {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  min-width: 0;
}

.user-name {
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-medium);
  color: var(--color-text);
  line-height: 1.2;
}

.user-role {
  font-size: var(--font-size-xs);
  color: var(--color-text-secondary);
  line-height: 1.2;
}

.dropdown-icon {
  transition: transform 0.2s ease;
  color: var(--color-text-secondary);
}

.dropdown-icon-rotated {
  transform: rotate(180deg);
}

.user-dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  min-width: 240px;
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow-lg);
  z-index: var(--z-index-dropdown);
  margin-top: 0.5rem;
  overflow: hidden;
}

.dropdown-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
  background: var(--color-surface);
  border-bottom: 1px solid var(--color-border-light);
}

.header-info {
  flex: 1;
  min-width: 0;
}

.header-name {
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-semibold);
  color: var(--color-text);
  margin-bottom: 0.25rem;
}

.header-email {
  font-size: var(--font-size-xs);
  color: var(--color-text-secondary);
}

.dropdown-divider {
  height: 1px;
  background: var(--color-border-light);
  margin: 0.5rem 0;
}

.dropdown-items {
  padding: 0.5rem 0;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem 1rem;
  background: transparent;
  border: none;
  color: var(--color-text);
  text-decoration: none;
  font-size: var(--font-size-sm);
  cursor: pointer;
  transition: var(--transition);
}

.dropdown-item:hover,
.dropdown-item:focus {
  background: var(--color-background-hover);
  color: var(--color-primary);
  outline: none;
}

.dropdown-item-danger {
  color: var(--color-danger);
}

.dropdown-item-danger:hover,
.dropdown-item-danger:focus {
  background: var(--color-danger-alpha);
  color: var(--color-danger);
}

/* Dropdown animations */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px) scale(0.95);
}

/* Responsive */
@media (max-width: 640px) {
  .user-info {
    display: none;
  }

  .user-dropdown-trigger {
    padding: 0.25rem;
  }

  .user-dropdown-menu {
    min-width: 200px;
    right: -0.5rem;
  }
}
</style>
