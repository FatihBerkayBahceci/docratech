<!--
Proje: Enterprise Sağlık Platformu
Bileşen: Advanced Menu
Developer: DocraTech Team - Fatih Berkay Bahceci
Açıklama: Marka uyumlu, mikro animasyonlu ve enterprise seviyesinde gelişmiş Menü bileşeni.
-->
<template>
  <nav :class="menuClasses">
    <slot />
  </nav>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: { type: String, default: 'vertical' },
  size: { type: String, default: 'md' }
})

const menuClasses = computed(() => [
  'flex',
  props.variant === 'horizontal' ? 'flex-row gap-4' : 'flex-col gap-2',
  props.size === 'sm' ? 'text-sm' : props.size === 'lg' ? 'text-lg' : 'text-base'
])
</script>

<style scoped>
nav {
  font-family: Inter, sans-serif;
}

:deep(.menu-item) {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  border-radius: 16px;
  color: #FFF;
  background: #2A183D;
  transition: all 0.3s ease;
  cursor: pointer;
}

:deep(.menu-item:hover) {
  background: #5A1188;
  transform: translateX(5px);
  box-shadow: 0 5px 15px rgba(90,17,136,0.3);
}

:deep(.menu-item.active) {
  background: linear-gradient(90deg, #5A1188, #9D38CF);
  box-shadow: 0 5px 15px rgba(157,56,207,0.5);
}

:deep(.menu-item.disabled) {
  opacity: 0.4;
  cursor: not-allowed;
  pointer-events: none;
}

:deep(.menu-group-title) {
  padding: 0.5rem 1rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #9D38CF;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-top: 1rem;
}

:deep(.menu-divider) {
  height: 1px;
  background: #6D488D;
  margin: 0.5rem 0;
}
</style>
