<!--
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Component: AppBreadcrumb (Enterprise UX)
-->

<template>
  <nav class="app-breadcrumb" aria-label="Breadcrumb">
    <ol class="app-breadcrumb-list" role="list">
      <li 
        v-for="(item, index) in items" 
        :key="item.id || index"
        class="app-breadcrumb-item"
        :class="{ 'app-breadcrumb-item-active': index === items.length - 1 }"
      >
        <!-- Only non-last, clickable items -->
        <a
          v-if="item.href && index !== items.length - 1"
          :href="item.href"
          class="app-breadcrumb-link focusable"
          @click.prevent="handleClick(item, index)"
          tabindex="0"
          @keydown.enter="handleClick(item, index)"
          :aria-current="index === items.length - 1 ? 'page' : null"
        >
          <Icon v-if="item.icon" :name="item.icon" size="sm" class="app-breadcrumb-icon" />
          <span>{{ item.label }}</span>
        </a>
        <!-- Last or non-clickable item (span) -->
        <span v-else class="app-breadcrumb-text" aria-current="page">
          <Icon v-if="item.icon" :name="item.icon" size="sm" class="app-breadcrumb-icon" />
          <span>{{ item.label }}</span>
        </span>
        <!-- Separator, except after last -->
        <Icon 
          v-if="index < items.length - 1" 
          name="chevron-right" 
          size="sm" 
          class="app-breadcrumb-separator"
          aria-hidden="true"
        />
      </li>
    </ol>
  </nav>
</template>

<script>
import Icon from '../visual/Icon.vue'

export default {
  name: 'AppBreadcrumb',
  components: { Icon },
  props: {
    items: {
      type: Array,
      default: () => []
    }
  },
  emits: ['item-click'],
  setup(props, { emit }) {
    const handleClick = (item, index) => {
      emit('item-click', { item, index })
    }
    return { handleClick }
  }
}
</script>

<style scoped>
.app-breadcrumb {
  display: flex;
  align-items: center;
  font-family: 'Inter', sans-serif;
  padding: 0.3rem 0;
  user-select: none;
}

.app-breadcrumb-list {
  display: flex;
  align-items: center;
  list-style: none;
  margin: 0;
  padding: 0;
  flex-wrap: wrap;
}

.app-breadcrumb-item {
  display: flex;
  align-items: center;
  gap: 0.47rem;
  position: relative;
  transition: color 0.2s;
}

.app-breadcrumb-link {
  display: flex;
  align-items: center;
  gap: 0.23rem;
  color: #9D38CF;
  background: none;
  border: none;
  font-size: 0.96em;
  font-weight: 500;
  border-radius: 8px;
  cursor: pointer;
  padding: 0.13em 0.42em;
  outline: none;
  transition: background 0.15s, color 0.17s;
  text-decoration: none;
}
.app-breadcrumb-link:hover, .app-breadcrumb-link:focus {
  color: #5A1188;
  background: linear-gradient(90deg, #ede9fe 0%, #f3e8ff 100%);
  text-decoration: underline;
}

.focusable:focus {
  outline: 2px solid #9D38CF;
  outline-offset: 2px;
}

.app-breadcrumb-text {
  display: flex;
  align-items: center;
  gap: 0.23rem;
  color: #2A183D;
  font-size: 0.98em;
  font-weight: 600;
  background: none;
  border-radius: 8px;
  padding: 0.09em 0.34em;
  transition: color 0.18s;
}

.dark .app-breadcrumb-text {
  color: #f9fafb;
}

.app-breadcrumb-icon {
  flex-shrink: 0;
  opacity: 0.86;
  margin-right: 2px;
}

.app-breadcrumb-separator {
  color: #b993d6;
  margin: 0 0.18rem;
  font-weight: 500;
  opacity: 0.75;
  transition: color 0.2s;
  pointer-events: none;
}

.app-breadcrumb-item-active .app-breadcrumb-text {
  color: #9D38CF;
  font-weight: 700;
  background: linear-gradient(90deg, #ede9fe 0%, #f3e8ff 100%);
  animation: fadeInBreadcrumb 0.34s cubic-bezier(.22,1,.36,1);
}

@keyframes fadeInBreadcrumb {
  0% { opacity: 0; transform: translateY(12px);}
  100% { opacity: 1; transform: translateY(0);}
}

@media (max-width: 700px) {
  .app-breadcrumb-list { gap: 0.09rem;}
  .app-breadcrumb-text, .app-breadcrumb-link { font-size: 0.92em;}
}
</style>
