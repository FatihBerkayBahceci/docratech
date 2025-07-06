<!--
  Project: Enterprise Health Infinite Scroll (Brandkit Revamp)
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <section
    class="w-full h-full overflow-y-auto rounded-3xl border border-brand-dark/40 bg-bg-primary dark:bg-bg-dark shadow-brand-md infinite-scroll-root"
    ref="containerRef"
    tabindex="0"
    aria-label="Sonsuz kaydırma alanı"
  >
    <!-- Content -->
    <div class="min-h-full">
      <slot :items="displayedItems" />
    </div>

    <!-- Loading State -->
    <transition name="fade-scale">
      <div
        v-if="loading"
        class="flex items-center justify-center gap-3 py-12 text-brand dark:text-accent font-semibold"
        aria-live="polite"
      >
        <LoadingSpinner size="md" />
        <span class="animate-pulse text-lg">Yükleniyor...</span>
      </div>
    </transition>

    <!-- Load More Trigger -->
    <transition name="fade-scale">
      <div
        v-if="hasMore && !loading"
        class="flex items-center justify-center py-5"
        ref="triggerRef"
      >
        <slot name="load-more">
          <AppButton
            variant="gradient"
            size="sm"
            class="rounded-xl font-semibold px-5 shadow-brand-sm hover:scale-105 transition-transform"
            @click="loadMore"
          >
            Daha Fazla Yükle
          </AppButton>
        </slot>
      </div>
    </transition>

    <!-- End State -->
    <transition name="fade">
      <div v-if="!hasMore && items.length > 0" class="py-12 text-center">
        <slot name="end">
          <div class="flex items-center justify-center gap-2 text-accent text-base font-medium animate-fade-in-slow">
            <Icon name="check-circle" size="sm" />
            <span>Tüm öğeler yüklendi</span>
          </div>
        </slot>
      </div>
    </transition>

    <!-- Error State -->
    <transition name="fade-scale">
      <div v-if="error" class="py-12 text-center">
        <slot name="error" :error="error" :retry="retry">
          <div class="flex flex-col items-center gap-2 text-red-600 dark:text-red-400 text-base font-semibold animate-shake">
            <Icon name="alert-triangle" size="sm" />
            <span>{{ error }}</span>
            <AppButton
              variant="outline"
              size="sm"
              class="rounded-xl font-semibold hover:scale-105 transition-transform"
              @click="retry"
            >
              Tekrar Dene
            </AppButton>
          </div>
        </slot>
      </div>
    </transition>
  </section>
</template>

<script setup>
// Project: Enterprise Health Infinite Scroll (Brandkit Revamp)
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import LoadingSpinner from '../loading/LoadingSpinner.vue'
import AppButton from '../button/AppButton.vue'
import Icon from '../visual/Icon.vue'

const props = defineProps({
  items: { type: Array, default: () => [] },
  pageSize: { type: Number, default: 20 },
  threshold: { type: Number, default: 100 },
  loading: { type: Boolean, default: false },
  hasMore: { type: Boolean, default: true },
  error: { type: String, default: null },
  autoLoad: { type: Boolean, default: true }
})
const emit = defineEmits(['load-more', 'retry'])

const containerRef = ref(null)
const triggerRef = ref(null)
const currentPage = ref(1)
const observer = ref(null)

const displayedItems = computed(() =>
  props.items.slice(0, currentPage.value * props.pageSize)
)

function loadMore() {
  if (props.loading || !props.hasMore) return
  currentPage.value++
  emit('load-more', { page: currentPage.value, pageSize: props.pageSize })
}

function retry() { emit('retry') }

function setupIntersectionObserver() {
  if (!props.autoLoad || !triggerRef.value) return
  observer.value = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (entry.isIntersecting && props.hasMore && !props.loading) {
          loadMore()
        }
      }
    },
    { rootMargin: `${props.threshold}px` }
  )
  observer.value.observe(triggerRef.value)
}
function cleanupObserver() {
  observer.value?.disconnect()
  observer.value = null
}

function handleScroll() {
  if (!props.autoLoad || props.loading || !props.hasMore) return
  const c = containerRef.value
  if (!c) return
  const { scrollTop, scrollHeight, clientHeight } = c
  if (scrollTop + clientHeight >= scrollHeight - props.threshold) loadMore()
}

onMounted(() => {
  if (props.autoLoad) {
    setupIntersectionObserver()
    containerRef.value?.addEventListener('scroll', handleScroll)
  }
})
onUnmounted(() => {
  cleanupObserver()
  containerRef.value?.removeEventListener('scroll', handleScroll)
})
watch(() => props.items.length, (newLength, oldLength) => {
  if (newLength < oldLength) currentPage.value = 1
})
watch(() => props.error, (err) => {
  if (err) currentPage.value = Math.max(1, currentPage.value - 1)
})

</script>

<style scoped>
:root {
  --brand: #5A1188;
  --accent: #9D38CF;
  --brand-dark: #2A183D;
  --bg-primary: #181124;
  --bg-dark: #211733;
  --shadow-brand: 0 8px 24px 0 #9D38CF19;
}
.bg-brand { background: var(--brand);}
.bg-accent { background: var(--accent);}
.bg-brand-dark { background: var(--brand-dark);}
.bg-bg-primary { background: var(--bg-primary);}
.bg-bg-dark { background: var(--bg-dark);}
.text-brand { color: var(--brand);}
.text-accent { color: var(--accent);}
.text-brand-dark { color: var(--brand-dark);}
.shadow-brand-md { box-shadow: 0 8px 24px 0 #9D38CF19;}
.shadow-brand-xl { box-shadow: 0 18px 56px 0 #9D38CF15;}
.shadow-brand-sm { box-shadow: 0 2px 8px 0 #5A118814;}

.infinite-scroll-root {
  min-height: 200px;
  background: linear-gradient(120deg, #181124 60%, #2A183D 100%);
  border-radius: 1.5rem;
  transition: box-shadow 0.19s, border-color 0.19s, background 0.24s;
  box-shadow: var(--shadow-brand);
  font-family: 'Inter', sans-serif;
}
.dark .infinite-scroll-root { background: linear-gradient(120deg, #181124 70%, #2A183D 100%); }

.fade-scale-enter-active, .fade-scale-leave-active {
  transition: opacity .22s cubic-bezier(.42,0,.2,1), transform .22s cubic-bezier(.42,0,.2,1);
}
.fade-scale-enter-from, .fade-scale-leave-to { opacity: 0; transform: scale(0.97);}
.fade-scale-leave-from, .fade-scale-enter-to { opacity: 1; transform: scale(1);}
.fade-enter-active, .fade-leave-active { transition: opacity .19s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.fade-enter-to, .fade-leave-from { opacity: 1; }

.animate-fade-in-slow {
  animation: fadeIn 1.2s cubic-bezier(.42,0,.58,1) both;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(12px);}
  to { opacity: 1; transform: none;}
}
@keyframes shake {
  0% { transform: translateX(0);}
  18% { transform: translateX(-6px);}
  36% { transform: translateX(6px);}
  54% { transform: translateX(-4px);}
  72% { transform: translateX(4px);}
  90% { transform: translateX(-2px);}
  100% { transform: translateX(0);}
}
.animate-shake {
  animation: shake 0.37s cubic-bezier(.36,.07,.19,.97) both;
}

/* Custom scrollbar */
.infinite-scroll-root::-webkit-scrollbar { width: 7px; }
.infinite-scroll-root::-webkit-scrollbar-track { background: #1a1025; border-radius: 7px; }
.infinite-scroll-root::-webkit-scrollbar-thumb { background: #5A118855; border-radius: 7px; }
.infinite-scroll-root::-webkit-scrollbar-thumb:hover { background: #9D38CF66; }
.infinite-scroll-root { scrollbar-width: thin; scrollbar-color: #9D38CF55 #181124; }

@media (max-width: 640px) {
  .infinite-scroll-root { font-size: 15px; }
  .py-12 { padding-top: 2.3rem !important; padding-bottom: 2.3rem !important; }
  .py-5 { padding-top: 1.1rem !important; padding-bottom: 1.1rem !important; }
}
</style>
