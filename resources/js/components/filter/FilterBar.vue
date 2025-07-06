<!--
  Project: Enterprise Health FilterBar
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <form
    class="w-full flex flex-wrap items-center gap-3 bg-[#2A183D] rounded-2xl shadow-xl px-6 py-4 mb-8 animate-fadein"
    role="search"
    @submit.prevent="emitFilter"
    autocomplete="off"
  >
    <i class="bi bi-search text-[#9D38CF] text-xl mr-1 pointer-events-none" aria-hidden="true"></i>
    <div class="relative flex-1 min-w-[140px] max-w-xs">
      <input
        ref="inputRef"
        v-model="search"
        type="text"
        class="filter-input w-full bg-transparent text-white placeholder-[#9D38CF88] outline-none border-0 font-medium transition-shadow focus-visible:ring-2 focus-visible:ring-[#9D38CF] rounded-xl px-3 py-2 pr-8"
        :placeholder="searchPlaceholder"
        aria-label="Search"
        @input="onInput"
        @keydown.enter="emitFilter"
      />
      <!-- Clear icon -->
      <button
        v-if="search"
        type="button"
        class="absolute right-1.5 top-1/2 -translate-y-1/2 p-1 rounded-full bg-[#9D38CF33] hover:bg-[#9D38CF66] focus-visible:ring-2 focus-visible:ring-[#9D38CF] transition-all"
        aria-label="Clear search"
        @click="clearSearch"
        tabindex="0"
      >
        <i class="bi bi-x-lg text-white text-xs"></i>
      </button>
    </div>
    <!-- Slot: Ek filtre alanları (ör: dropdown, chip, datepicker, vs) -->
    <slot />

    <button
      type="submit"
      class="btn-gradient px-5 py-2 rounded-2xl font-semibold text-base shadow-sm transition-all duration-200 hover:scale-[1.04] focus-visible:ring-4 focus-visible:ring-[#9D38CF]/40 flex items-center gap-2"
      aria-label="Filter"
      :disabled="loading"
    >
      <span v-if="!loading">Filter</span>
      <svg v-else class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-30" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8v4l3.5-3.5L12 0v4a8 8 0 100 16v-4l-3.5 3.5L12 24v-4a8 8 0 01-8-8z"/>
      </svg>
    </button>
    <button
      type="button"
      class="btn-outline px-5 py-2 rounded-2xl font-semibold text-base shadow-none border border-[#9D38CF55] text-[#9D38CF] hover:bg-[#9D38CF11] transition-all duration-200 focus-visible:ring-2 focus-visible:ring-[#9D38CF]/40"
      @click="emitReset"
      aria-label="Reset"
      :disabled="loading"
    >
      Reset
    </button>
  </form>
</template>

<script setup>
// Project: Enterprise Health FilterBar
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, watch, nextTick } from 'vue'

const props = defineProps({
  searchPlaceholder: { type: String, default: 'Search...' },
  loading: { type: Boolean, default: false }
})
const emit = defineEmits(['filter', 'reset'])

// Main search value
const search = ref('')

// Ek slotlu filtrelerin değerleri için ref örneği (dilerseniz parent’ta prop olarak yönetebilirsiniz)
const filters = ref({})

// Debounce için timer
let debounceTimeout = null

// Input referansı (auto-focus için)
const inputRef = ref(null)
nextTick(() => { inputRef.value?.focus() })

// Input değiştiğinde debounced filter
function onInput() {
  if (debounceTimeout) clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(() => {
    emitFilter()
  }, 300)
}

// Filter emit — search ve ek slot filtreleriyle birlikte gönder
function emitFilter() {
  emit('filter', { search: search.value, ...filters.value })
}

// Clear input
function clearSearch() {
  search.value = ''
  emitFilter()
}

// Reset tüm filtreler
function emitReset() {
  search.value = ''
  filters.value = {}
  emit('reset')
  emitFilter()
}

// Parent’ta slot ile gelen input/checkbox gibi ek alanlardan emit için örnek handler:
// <input @input="$emit('filter-change', { key: 'status', value: $event.target.value })">
function onFilterChange({ key, value }) {
  filters.value[key] = value
  emitFilter()
}
</script>

<style scoped>
@keyframes fadein {
  from { opacity: 0; transform: translateY(24px);}
  to   { opacity: 1; transform: translateY(0);}
}
.animate-fadein { animation: fadein 0.5s cubic-bezier(.22,1,.36,1) both; }

.btn-gradient {
  background: linear-gradient(92deg, #5A1188 0%, #9D38CF 100%);
  color: #FFF;
  box-shadow: 0 2px 14px #5A118844;
  border: none;
  outline: none;
}
.btn-gradient:hover,
.btn-gradient:focus-visible {
  background: linear-gradient(92deg, #6D488D 0%, #9D38CF 100%);
  box-shadow: 0 0 0 3px #9D38CF55;
}
.btn-outline {
  background: transparent;
  border-width: 2px;
}
.filter-input::-webkit-input-placeholder { color: #9D38CF88;}
.filter-input:-ms-input-placeholder { color: #9D38CF88;}
.filter-input::placeholder { color: #9D38CF88;}
/* Responsive: mobile uyum */
@media (max-width: 640px) {
  form { padding: 12px 8px; border-radius: 16px;}
  .filter-input { min-width: 90px; font-size: 0.95rem;}
  .btn-gradient, .btn-outline { padding: 0.6em 1.2em; font-size: 0.97rem;}
}
</style>
