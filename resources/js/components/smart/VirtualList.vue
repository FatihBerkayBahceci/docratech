<!--
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Enterprise VirtualList - TailwindCSS, Micro Animasyon, Dark Mode, Boş/Skeleton State
-->
<template>
  <div
    class="relative bg-white dark:bg-[#181124] rounded-2xl shadow-md border border-[#e5e7eb] dark:border-[#2A183D] overflow-y-auto overflow-x-hidden transition-all duration-300"
    :style="{ height: containerHeight }"
    ref="container"
    @scroll="handleScroll"
    tabindex="0"
    aria-label="Liste"
  >
    <div :style="{ height: `${totalHeight}px` }"></div>
    <div
      class="absolute left-0 right-0 top-0"
      :style="{ transform: `translateY(${offsetY}px)` }"
    >
      <div
        v-for="(item, index) in visibleItems"
        :key="item.id || index"
        class="flex items-center px-4 border-b border-[#f3f4f6] dark:border-[#2A183D] transition-colors duration-200 group animate-list-item"
        :style="{ height: `${itemHeight}px` }"
        @mouseenter="hoverIndex = startIndex + index"
        @mouseleave="hoverIndex = null"
        :class="{'bg-[#f9fafb] dark:bg-[#2A183D]': hoverIndex === startIndex + index}"
      >
        <slot :item="item" :index="startIndex + index">
          <span class="truncate text-sm text-gray-800 dark:text-gray-200">{{ item }}</span>
        </slot>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-if="!loading && items.length === 0"
      class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500"
    >
      <slot name="empty">
        <svg class="w-10 h-10 mb-2" fill="none" viewBox="0 0 40 40">
          <rect width="40" height="40" rx="10" fill="#ede9fe" />
          <path d="M13 27l4-6 6 8 8-12" stroke="#9D38CF" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span class="font-medium text-base animate-fadein">Hiç veri bulunamadı</span>
      </slot>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="absolute bottom-5 right-5 z-10">
      <slot name="loading">
        <LoadingSpinner variant="blob" size="small" />
      </slot>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import LoadingSpinner from '../loading/LoadingSpinner.vue'

const props = defineProps({
  items: { type: Array, required: true },
  itemHeight: { type: Number, default: 50 },
  containerHeight: { type: String, default: '400px' },
  overscan: { type: Number, default: 5 },
  loading: Boolean
})

const emit = defineEmits(['scroll'])
const container = ref(null)
const hoverIndex = ref(null)
const scrollTop = ref(0)
const containerHeightPx = ref(400)

const updateContainerHeight = () => {
  if (container.value) {
    containerHeightPx.value = container.value.clientHeight
  }
}
onMounted(() => {
  updateContainerHeight()
  window.addEventListener('resize', updateContainerHeight)
})
onBeforeUnmount(() => {
  window.removeEventListener('resize', updateContainerHeight)
})

watch(() => props.containerHeight, updateContainerHeight)
watch(() => props.items, () => setTimeout(updateContainerHeight, 100))

const totalHeight = computed(() => props.items.length * props.itemHeight)
const startIndex = computed(() =>
  Math.max(0, Math.floor(scrollTop.value / props.itemHeight) - props.overscan)
)
const visibleCount = computed(() =>
  Math.ceil(containerHeightPx.value / props.itemHeight)
)
const endIndex = computed(() =>
  Math.min(props.items.length, Math.floor(scrollTop.value / props.itemHeight) + visibleCount.value + props.overscan)
)
const visibleItems = computed(() =>
  props.items.slice(startIndex.value, endIndex.value)
)
const offsetY = computed(() => startIndex.value * props.itemHeight)

function handleScroll(event) {
  scrollTop.value = event.target.scrollTop
  emit('scroll', {
    scrollTop: scrollTop.value,
    startIndex: startIndex.value,
    endIndex: endIndex.value
  })
}

function scrollToIndex(index) {
  if (!container.value) return
  container.value.scrollTop = index * props.itemHeight
}
function scrollToItem(item) {
  const index = props.items.findIndex(i => i.id === item.id)
  if (index !== -1) scrollToIndex(index)
}
defineExpose({ scrollToIndex, scrollToItem })
</script>

<style scoped>
@keyframes list-item-in {
  0% { opacity: 0; transform: translateX(-10px);}
  100% { opacity: 1; transform: translateX(0);}
}
.animate-list-item {
  animation: list-item-in 0.16s cubic-bezier(.22,1,.36,1);
}
@keyframes fadein {
  0% { opacity: 0;}
  100% { opacity: 1;}
}
.animate-fadein {
  animation: fadein 0.4s cubic-bezier(.22,1,.36,1);
}
.virtual-list::-webkit-scrollbar {
  width: 8px;
}
.virtual-list::-webkit-scrollbar-track {
  background: #f3f4f6;
  border-radius: 4px;
}
.virtual-list::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 4px;
  transition: background 0.2s;
}
.virtual-list::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}
.dark .virtual-list::-webkit-scrollbar-track {
  background: #2A183D;
}
.dark .virtual-list::-webkit-scrollbar-thumb {
  background: #6D28D9;
}
</style>
