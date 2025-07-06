<!--
  Project: Enterprise Health VirtualList
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    ref="containerRef"
    class="virtual-list relative border border-brand-100 dark:border-brand-800 bg-white dark:bg-brand-950 rounded-2xl overflow-hidden shadow-sm"
    :style="{ height: containerHeight + 'px' }"
    tabindex="0"
    aria-label="Veri Listesi"
    @keydown.down.prevent="scrollBy(1)"
    @keydown.up.prevent="scrollBy(-1)"
  >
    <div
      class="virtual-list-content"
      :style="{ height: totalHeight + 'px' }"
    >
      <transition-group
        name="list-fade"
        tag="div"
        class="virtual-list-items"
        :style="{ transform: `translateY(${offsetY}px)` }"
      >
        <div
          v-for="(item, i) in visibleItems"
          :key="item.id || getItemIndex(item)"
          class="virtual-list-item group focus:outline-none focus:ring-2 focus:ring-brand-400 dark:focus:ring-brand-600 transition-all duration-200 ease-out"
          :style="{ height: itemHeight + 'px' }"
          tabindex="0"
          @focus="emit('item-visible', { item, index: getItemIndex(item), isVisible: true })"
          role="listitem"
        >
          <slot
            name="item"
            :item="item"
            :index="getItemIndex(item)"
          >
            <div class="virtual-list-item-default flex items-center gap-3">
              <div v-if="item.icon" class="text-brand-500 dark:text-brand-300">
                <component :is="item.icon" class="w-5 h-5" />
              </div>
              <span>{{ item.label || item }}</span>
            </div>
          </slot>
        </div>
      </transition-group>
    </div>

    <!-- Loading indicator -->
    <transition name="list-fade">
      <div
        v-if="loading"
        class="virtual-list-loading flex items-center justify-center gap-2 absolute bottom-0 left-0 right-0 bg-white/90 dark:bg-brand-900/90 py-4 text-brand-400 dark:text-brand-300 text-sm font-medium select-none"
        aria-live="polite"
        aria-busy="true"
      >
        <LoadingSpinner size="md" />
        <span>Yükleniyor...</span>
      </div>
    </transition>

    <!-- Empty state -->
    <transition name="list-fade">
      <div v-if="items.length === 0 && !loading" class="virtual-list-empty flex items-center justify-center h-full p-8">
        <slot name="empty">
          <EmptyState
            title="Veri bulunamadı"
            description="Gösterilecek veri bulunamadı."
            icon="list"
          />
        </slot>
      </div>
    </transition>
  </div>
</template>

<script setup>
// Project: Enterprise Health VirtualList
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import LoadingSpinner from '../loading/LoadingSpinner.vue'
import EmptyState from '../empty/EmptyState.vue'

const props = defineProps({
  items: { type: Array, default: () => [] },
  itemHeight: { type: Number, default: 60 },
  containerHeight: { type: Number, default: 400 },
  overscan: { type: Number, default: 5 },
  loading: { type: Boolean, default: false }
})

const emit = defineEmits(['scroll', 'item-visible'])

const containerRef = ref(null)
const scrollTop = ref(0)

const totalHeight = computed(() => props.items.length * props.itemHeight)

const startIndex = computed(() =>
  Math.max(0, Math.floor(scrollTop.value / props.itemHeight) - props.overscan)
)
const visibleCount = computed(() => Math.ceil(props.containerHeight / props.itemHeight))
const endIndex = computed(() =>
  Math.min(props.items.length - 1, Math.floor(scrollTop.value / props.itemHeight) + visibleCount.value + props.overscan)
)

const visibleItems = computed(() =>
  props.items.slice(startIndex.value, endIndex.value + 1)
)
const offsetY = computed(() => startIndex.value * props.itemHeight)

const getItemIndex = (item) => props.items.indexOf(item)

function handleScroll(event) {
  scrollTop.value = event.target.scrollTop
  emit('scroll', {
    scrollTop: scrollTop.value,
    scrollHeight: totalHeight.value,
    clientHeight: props.containerHeight
  })
}

function scrollBy(dir = 1) {
  if (!containerRef.value) return
  const curr = containerRef.value.scrollTop
  containerRef.value.scrollTop = Math.max(0, curr + dir * props.itemHeight)
}

function scrollToIndex(index) {
  if (!containerRef.value) return
  containerRef.value.scrollTop = index * props.itemHeight
}

function scrollToItem(item) {
  const index = props.items.indexOf(item)
  if (index !== -1) scrollToIndex(index)
}

watch(visibleItems, (newVisibleItems) => {
  newVisibleItems.forEach((item, localIndex) => {
    const globalIndex = startIndex.value + localIndex
    emit('item-visible', { item, index: globalIndex, isVisible: true })
  })
})

onMounted(() => {
  if (containerRef.value) {
    containerRef.value.addEventListener('scroll', handleScroll)
  }
})

onUnmounted(() => {
  if (containerRef.value) {
    containerRef.value.removeEventListener('scroll', handleScroll)
  }
})

defineExpose({ scrollToIndex, scrollToItem })
</script>

<style scoped>
/* Project: Enterprise Health VirtualList */
/* Developer: DocraTech Team - Fatih Berkay Bahceci */

.list-fade-enter-active,
.list-fade-leave-active {
  transition: all 0.2s cubic-bezier(.43,.13,.23,0.96);
}
.list-fade-enter-from,
.list-fade-leave-to {
  opacity: 0;
  transform: translateY(10px) scale(0.98);
}
.list-fade-enter-to,
.list-fade-leave-from {
  opacity: 1;
  transform: none;
}

.virtual-list {
  background: #fff;
}
.dark .virtual-list {
  background: #1f2937;
}
.virtual-list-content {
  position: relative;
  width: 100%;
}
.virtual-list-items {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
}
.virtual-list-item {
  border-bottom: 1px solid #f3f4f6;
  padding: 0.75rem 1.25rem;
  background: transparent;
  transition: background 0.15s, box-shadow 0.2s;
  outline: none;
  cursor: pointer;
}
.dark .virtual-list-item {
  border-bottom-color: #374151;
}
.virtual-list-item:last-child {
  border-bottom: none;
}
.virtual-list-item:hover,
.virtual-list-item:focus {
  background: #eff6ff;
  box-shadow: 0 2px 6px 0 rgb(59 130 246 / 8%);
}
.dark .virtual-list-item:hover,
.dark .virtual-list-item:focus {
  background: #1e3a8a;
}
.virtual-list-item-default {
  font-size: 0.9375rem;
  color: #111827;
}
.dark .virtual-list-item-default {
  color: #f9fafb;
}

.virtual-list-loading {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 1.25rem 0;
  background: rgba(255, 255, 255, 0.93);
  backdrop-filter: blur(6px);
  color: #6b7280;
  font-size: 0.92rem;
  z-index: 10;
}
.dark .virtual-list-loading {
  background: rgba(31, 41, 55, 0.93);
  color: #9ca3af;
}

.virtual-list-empty {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  min-height: 220px;
  padding: 2rem;
}

/* Scrollbar */
.virtual-list::-webkit-scrollbar {
  width: 6px;
}
.virtual-list::-webkit-scrollbar-thumb {
  background: #e5e7eb;
  border-radius: 3px;
}
.dark .virtual-list::-webkit-scrollbar-thumb {
  background: #4b5563;
}
.virtual-list::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
.dark .virtual-list::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}

/* Responsive */
@media (max-width: 640px) {
  .virtual-list-item {
    padding: 1rem 0.75rem;
  }
}
</style>
