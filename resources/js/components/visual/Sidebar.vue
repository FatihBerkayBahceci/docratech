<!--
  Project: DocraTech Medical Website Platform
  Developer: DocraTech Team | Fatih Berkay Bahceci
-->

<template>
  <aside
    :class="[
      'h-screen flex flex-col overflow-hidden transition-all duration-300 ease-in-out shadow-xl z-40',
      sticky ? 'sticky top-0' : '',
      variantClass,
      isCollapsed ? 'w-[72px]' : 'w-[260px]'
    ]"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
  >
    <!-- Modern DocraTech Header -->
    <div class="flex items-center justify-center py-6 px-4 border-b border-docratech-border">
      <slot name="header">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-docratech-primary to-docratech-primaryAccent flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2L2 7v10c0 5.55 3.84 9.74 9 11 5.16-1.26 9-5.45 9-11V7l-10-5z"/>
            </svg>
          </div>
          <transition name="fade">
            <div v-if="title && !isCollapsed" class="flex flex-col">
              <span class="font-bold text-lg text-docratech-primary">{{ title }}</span>
              <span class="text-xs text-docratech-textSecondary">Medical Platform</span>
            </div>
          </transition>
        </div>
      </slot>
    </div>

    <!-- Nav - No scrolling, content expands naturally -->
    <nav class="flex-1 px-2 py-4">
      <div class="flex flex-col gap-2">
        <slot />
      </div>
    </nav>

    <!-- Footer -->
    <div v-if="$slots.footer" class="px-4 py-4 border-t border-docratech-border text-docratech-textSecondary text-sm">
      <slot name="footer" />
    </div>

    <!-- Toggle Button -->
    <button
      v-if="collapsible"
      type="button"
      @click="toggleCollapsed"
      :aria-label="isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
      class="absolute top-4 -right-5 w-10 h-10 rounded-full bg-docratech-primary text-white flex items-center justify-center shadow-md hover:bg-docratech-primaryAccent transition-colors"
    >
      <Icon :name="isCollapsed ? 'chevron-right' : 'chevron-left'" size="sm" :animated="animated" />
    </button>
  </aside>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import Icon from './Icon.vue'

const props = defineProps({
  collapsed: { type: Boolean, default: false },
  collapsible: { type: Boolean, default: true },
  logo: { type: String, default: null },
  logoAlt: { type: String, default: 'Logo' },
  title: { type: String, default: null },
  animated: { type: Boolean, default: true },
  variant: {
    type: String,
    default: 'light',
    validator: (val) => ['gradient', 'dark', 'light'].includes(val)
  },
  sticky: { type: Boolean, default: false }
})

const emit = defineEmits(['update:collapsed', 'collapse', 'expand'])

const isCollapsed = ref(props.collapsed)
const isHovered = ref(false)

watch(() => props.collapsed, (val) => {
  isCollapsed.value = val
})

const variantClass = computed(() => {
  switch (props.variant) {
    case 'dark':
      return 'bg-docratech-darkPurple text-white border-r border-docratech-border'
    case 'gradient':
      return 'bg-gradient-to-b from-docratech-primary to-docratech-primaryAccent text-white'
    case 'light':
    default:
      return 'bg-docratech-surface text-docratech-text border-r border-docratech-border'
  }
})

const toggleCollapsed = () => {
  isCollapsed.value = !isCollapsed.value
  emit('update:collapsed', isCollapsed.value)
  isCollapsed.value ? emit('collapse') : emit('expand')
}

const handleMouseEnter = () => {
  if (props.animated) isHovered.value = true
}
const handleMouseLeave = () => {
  if (props.animated) isHovered.value = false
}
</script>

<style scoped>
/* Scrollbar styling */
.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: var(--color-border);
  border-radius: 3px;
}

/* Smooth fade for title collapse */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
