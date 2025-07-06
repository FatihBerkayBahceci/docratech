<!--
  Project: Enterprise Health Autocomplete
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    class="relative w-full"
    ref="autocompleteRef"
    role="combobox"
    :aria-expanded="isOpen"
    aria-haspopup="listbox"
    :aria-owns="dropdownId"
    :aria-controls="dropdownId"
  >
    <!-- Input + Iconlar -->
    <div class="relative flex items-center">
      <input
        ref="inputRef"
        v-model="searchQuery"
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :aria-autocomplete="'list'"
        :aria-controls="dropdownId"
        :aria-activedescendant="isOpen && filteredOptions[selectedIndex] ? `ac-option-${filteredOptions[selectedIndex].value}` : undefined"
        class="w-full px-4 py-3 pr-12 rounded-2xl bg-[#181124] border-2 border-[#2A183D] text-white placeholder-[#9D38CF88] font-medium text-base shadow-sm transition-all duration-200 outline-none focus-visible:ring-2 focus-visible:ring-[#9D38CF] focus:border-[#9D38CF] disabled:bg-[#231541] disabled:text-[#9ca3af]"
        @input="onInput"
        @focus="handleFocus"
        @blur="handleBlur"
        @keydown="handleKeydown"
        autocomplete="off"
        :tabindex="disabled ? -1 : 0"
        :aria-disabled="disabled"
        :aria-readonly="readonly"
      />

      <!-- Suffix: loading, clear, chevron -->
      <span class="absolute right-3 flex items-center gap-1">
        <Icon
          v-if="loading"
          name="loader"
          size="sm"
          class="animate-spin text-[#9D38CF]"
          aria-label="Loading"
        />
        <button
          v-else-if="searchQuery && !disabled"
          type="button"
          class="group rounded-full bg-[#231541]/40 hover:bg-[#9D38CF33] transition-colors p-1 focus-visible:ring-2 focus-visible:ring-[#9D38CF]"
          aria-label="Clear"
          @click="clearInput"
          tabindex="0"
        >
          <Icon name="x" size="sm" class="text-white group-hover:text-[#9D38CF] transition" />
        </button>
        <Icon
          v-else
          name="chevron-down"
          size="sm"
          class="text-[#9D38CF] transition-transform duration-300"
          :class="{ 'rotate-180': isOpen }"
          aria-hidden="true"
        />
      </span>
    </div>

    <!-- Dropdown (animated, listbox role) -->
    <Transition name="fade-slide">
      <div
        v-if="isOpen && filteredOptions.length > 0"
        :id="dropdownId"
        class="absolute top-full left-0 w-full mt-2 rounded-2xl bg-[#181124] border-2 border-[#9D38CF55] shadow-xl max-h-72 overflow-y-auto z-50 py-2 backdrop-blur-md animate-fadein"
        role="listbox"
      >
        <div
          v-for="(option, index) in filteredOptions"
          :key="option.value"
          :id="`ac-option-${option.value}`"
          class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-all rounded-xl text-white select-none"
          :class="{
            'bg-gradient-to-r from-[#5A1188]/70 to-[#9D38CF]/80 text-white shadow-lg scale-[1.02]': selectedIndex === index,
            'hover:bg-[#2A183D]/60': selectedIndex !== index
          }"
          @click="selectOption(option)"
          @mouseenter="selectedIndex = index"
          :aria-selected="selectedIndex === index"
          role="option"
        >
          <slot name="option" :option="option">
            <Icon v-if="option.icon" :name="option.icon" size="sm" />
            <span class="flex-1 font-medium">{{ option.label }}</span>
            <span v-if="option.description" class="text-xs text-[#C9C6D5] font-normal">{{ option.description }}</span>
          </slot>
        </div>
      </div>
    </Transition>
    <!-- No results -->
    <Transition name="fade-slide">
      <div
        v-if="isOpen && filteredOptions.length === 0 && searchQuery"
        class="absolute top-full left-0 w-full mt-2 rounded-2xl bg-[#181124] border-2 border-[#2A183D] shadow-xl z-50 py-4 flex items-center justify-center gap-2 text-[#9D38CF] font-semibold text-base animate-fadein"
        role="status"
      >
        <Icon name="search" size="sm" />
        <span>No results found</span>
      </div>
    </Transition>
  </div>
</template>

<script setup>
// Project: Enterprise Health Autocomplete
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import Icon from '../visual/Icon.vue'

const props = defineProps({
  modelValue: [String, Number, Object],
  options: { type: Array, default: () => [] },
  placeholder: { type: String, default: 'Select...' },
  type: { type: String, default: 'text' },
  disabled: { type: Boolean, default: false },
  readonly: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
  minChars: { type: Number, default: 1 },
  maxResults: { type: Number, default: 10 }
})
const emit = defineEmits(['update:modelValue', 'change', 'search'])

const isOpen = ref(false)
const searchQuery = ref('')
const selectedIndex = ref(0)
const autocompleteRef = ref(null)
const inputRef = ref(null)
const dropdownId = `autocomplete-dropdown-${Math.random().toString(36).substr(2, 6)}`

// Debounce timer for search
let debounceTimeout = null

const filteredOptions = computed(() => {
  if (!searchQuery.value || searchQuery.value.length < props.minChars) {
    return props.options.slice(0, props.maxResults)
  }
  const query = searchQuery.value.toLowerCase()
  const filtered = props.options.filter(option =>
    option.label.toLowerCase().includes(query) ||
    option.value?.toString().toLowerCase().includes(query) ||
    option.description?.toLowerCase().includes(query) ||
    (option.keywords && option.keywords.some(kw => kw.toLowerCase().includes(query)))
  )
  return filtered.slice(0, props.maxResults)
})

function onInput() {
  if (debounceTimeout) clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(() => {
    emit('search', searchQuery.value)
    if (searchQuery.value.length >= props.minChars) {
      isOpen.value = true
      selectedIndex.value = 0
    } else {
      isOpen.value = false
    }
  }, 200)
}

function handleFocus() {
  if (!props.disabled && !props.readonly) {
    isOpen.value = filteredOptions.value.length > 0
    selectedIndex.value = 0
  }
}
function handleBlur() {
  setTimeout(() => { isOpen.value = false }, 120)
}
function handleKeydown(event) {
  if (!isOpen.value) return
  if (event.key === 'ArrowDown') {
    event.preventDefault()
    selectedIndex.value = Math.min(selectedIndex.value + 1, filteredOptions.value.length - 1)
  }
  if (event.key === 'ArrowUp') {
    event.preventDefault()
    selectedIndex.value = Math.max(selectedIndex.value - 1, 0)
  }
  if (event.key === 'Enter') {
    event.preventDefault()
    if (filteredOptions.value[selectedIndex.value]) {
      selectOption(filteredOptions.value[selectedIndex.value])
    }
  }
  if (event.key === 'Escape') {
    event.preventDefault()
    isOpen.value = false
  }
}

function selectOption(option) {
  searchQuery.value = option.label
  emit('update:modelValue', option.value)
  emit('change', option)
  isOpen.value = false
}

function clearInput() {
  searchQuery.value = ''
  emit('update:modelValue', null)
  emit('change', null)
  isOpen.value = false
}

// Click outside to close dropdown
function handleClickOutside(event) {
  if (autocompleteRef.value && !autocompleteRef.value.contains(event.target)) {
    isOpen.value = false
  }
}

// Initial value set
function initializeValue() {
  if (props.modelValue) {
    const option = props.options.find(opt => opt.value === props.modelValue)
    if (option) searchQuery.value = option.label
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  initializeValue()
})
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
watch(() => props.modelValue, initializeValue)
</script>

<style scoped>
/* Animations */
@keyframes fadein {
  from { opacity: 0; transform: translateY(20px);}
  to   { opacity: 1; transform: translateY(0);}
}
.animate-fadein { animation: fadein 0.38s cubic-bezier(.52,1.52,.5,1) both; }
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.27s cubic-bezier(.52,1.52,.5,1);
}
.fade-slide-enter-from, .fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(.97);
}

/* Mobile */
@media (max-width: 640px) {
  .px-4, .py-3 { padding-left: 1rem !important; padding-right: 1rem !important; padding-top: 0.85rem !important; padding-bottom: 0.85rem !important;}
  .max-h-72 { max-height: 15rem !important; }
}

/* Custom scrollbar for dropdown */
.max-h-72::-webkit-scrollbar { width: 7px; }
.max-h-72::-webkit-scrollbar-thumb { background: #231541; border-radius: 7px;}
.max-h-72::-webkit-scrollbar-track { background: transparent; }
</style>
