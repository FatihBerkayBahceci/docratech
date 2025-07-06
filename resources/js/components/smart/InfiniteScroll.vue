<!--
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Enterprise InfiniteScroll - Tailwind + Micro Animasyon + Robust Observer
-->
<template>
  <div class="relative w-full" ref="container">
    <div class="animate-fadein">
      <slot></slot>
    </div>

    <!-- Loading state (animated) -->
    <div
      v-if="loading"
      class="flex flex-col items-center justify-center py-8 gap-3 w-full"
      aria-live="polite"
    >
      <LoadingSpinner variant="blob" size="medium" />
      <span class="text-sm text-gray-500 font-semibold animate-pulse">Daha fazla yükleniyor...</span>
      <!-- Skeleton Loader -->
      <slot name="skeleton">
        <div class="w-full h-16 rounded-xl bg-gradient-to-r from-[#f3f4f6] via-[#ede9fe] to-[#f3f4f6] animate-shimmer mt-2"></div>
      </slot>
    </div>

    <!-- End state -->
    <div
      v-if="!hasMore && !loading"
      class="flex flex-col items-center justify-center py-8 gap-2 text-brand-green-600 animate-fadein"
    >
      <slot name="end">
        <svg class="w-8 h-8 text-brand-green-600" fill="none" viewBox="0 0 32 32">
          <circle cx="16" cy="16" r="15" stroke="#10b981" stroke-width="2" />
          <path d="M10 17l4 4 8-8" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="font-semibold text-sm text-green-600 dark:text-green-300">Tüm veriler yüklendi</span>
      </slot>
    </div>

    <!-- Intersection Sentinel -->
    <div
      v-if="hasMore && !loading"
      ref="sentinel"
      :style="{ height: `${distance}px` }"
      class="w-full pointer-events-none"
      aria-hidden="true"
    ></div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import LoadingSpinner from '../loading/LoadingSpinner.vue'

const props = defineProps({
  loading: Boolean,
  hasMore: {
    type: Boolean,
    default: true
  },
  threshold: {
    type: Number,
    default: 0.1
  },
  distance: {
    type: Number,
    default: 100
  }
})

const emit = defineEmits(['load-more'])
const container = ref(null)
const sentinel = ref(null)
let observer = null

const initObserver = () => {
  if (!sentinel.value) return
  destroyObserver()
  observer = new window.IntersectionObserver(
    (entries) => {
      const entry = entries[0]
      if (entry.isIntersecting && props.hasMore && !props.loading) {
        emit('load-more')
      }
    },
    {
      root: null,
      rootMargin: `${props.distance}px`,
      threshold: props.threshold
    }
  )
  observer.observe(sentinel.value)
}

const destroyObserver = () => {
  if (observer) observer.disconnect()
  observer = null
}

onMounted(() => {
  setTimeout(initObserver, 120)
})

onBeforeUnmount(() => {
  destroyObserver()
})

watch(() => [props.loading, props.hasMore], () => {
  setTimeout(initObserver, 120)
})
</script>

<style scoped>
@keyframes fadein {
  0% { opacity: 0; transform: translateY(12px);}
  100% { opacity: 1; transform: none;}
}
.animate-fadein {
  animation: fadein 0.5s cubic-bezier(.22,1,.36,1);
}
@keyframes shimmer {
  0% { background-position: -800px 0 }
  100% { background-position: 800px 0 }
}
.animate-shimmer {
  background-size: 200% 100%;
  background-position: -800px 0;
  animation: shimmer 2s linear infinite;
}
</style>
