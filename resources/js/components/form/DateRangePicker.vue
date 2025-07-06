<!--
  Project: Kurumsal Sağlık Platformu UI Library
  Component: DateRangePicker
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    ref="dateRangeRef"
    class="relative w-full"
    :class="{ 'opacity-60 pointer-events-none': disabled }"
  >
    <!-- Trigger -->
    <button
      type="button"
      class="flex items-center justify-between w-full px-4 py-3 bg-[var(--color-bg-card)] border-2 border-[var(--color-border)] rounded-2xl font-medium transition-all duration-300
      focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-accent)] group
      hover:border-[var(--color-primary)] hover:shadow-[0_4px_24px_rgba(157,56,207,0.12)]
      "
      :aria-expanded="isOpen"
      :aria-haspopup="true"
      @click="toggleDropdown"
      :disabled="disabled"
      tabindex="0"
    >
      <div class="flex items-center gap-2 truncate">
        <Icon name="calendar" size="sm" class="text-[var(--color-accent)] transition-colors" />
        <span
          v-if="displayText"
          class="text-base font-medium text-[var(--color-text)] truncate transition-colors"
        >
          {{ displayText }}
        </span>
        <span
          v-else
          class="text-base text-[var(--color-placeholder)] truncate"
        >
          {{ placeholder }}
        </span>
      </div>
      <Icon
        name="chevron-down"
        size="sm"
        class="ml-2 text-[var(--color-desc)] transition-transform duration-300"
        :class="{ 'rotate-180': isOpen }"
        aria-hidden="true"
      />
    </button>

    <!-- Dropdown -->
    <transition name="dropdown-fade">
      <section
        v-if="isOpen"
        class="absolute z-50 top-full left-0 right-0 mt-2 bg-[var(--color-bg-card)] border-2 border-[var(--color-border)] rounded-2xl shadow-2xl min-w-[320px] max-w-full
          animate-dropdown-in
        "
        role="dialog"
        aria-modal="true"
        @keydown.esc="isOpen = false"
      >
        <!-- Date Inputs -->
        <header class="p-6 border-b border-[var(--color-border-light)]">
          <div class="grid grid-cols-2 gap-4 sm:grid-cols-1">
            <div class="flex flex-col gap-2">
              <label class="text-xs font-semibold text-[var(--color-desc)] uppercase tracking-wider">Start Date</label>
              <input
                ref="startInput"
                v-model="startDate"
                type="date"
                class="date-range-input"
                :max="endDate || undefined"
                @change="handleStartDateChange"
                :aria-label="'Start date'"
              />
            </div>
            <div class="flex flex-col gap-2">
              <label class="text-xs font-semibold text-[var(--color-desc)] uppercase tracking-wider">End Date</label>
              <input
                ref="endInput"
                v-model="endDate"
                type="date"
                class="date-range-input"
                :min="startDate || undefined"
                @change="handleEndDateChange"
                :aria-label="'End date'"
              />
            </div>
          </div>
        </header>
        <!-- Presets -->
        <section class="p-6 border-b border-[var(--color-border-light)]">
          <h4 class="mb-2 text-sm font-semibold text-[var(--color-text)]">Quick Selection</h4>
          <div class="grid grid-cols-2 sm:grid-cols-1 gap-3">
            <button
              v-for="preset in presets"
              :key="preset.key"
              type="button"
              class="px-3 py-2 rounded-xl bg-[var(--color-bg-card)] border-2 border-[var(--color-border)] text-xs font-medium text-[var(--color-text)] transition
                hover:bg-gradient-to-r hover:from-[var(--color-primary)] hover:to-[var(--color-accent)]
                hover:text-white hover:border-[var(--color-accent)]
                focus-visible:ring-2 focus-visible:ring-[var(--color-accent)]
                active:scale-95
              "
              @click="applyPreset(preset)"
              :tabindex="isOpen ? 0 : -1"
            >
              {{ preset.label }}
            </button>
          </div>
        </section>
        <!-- Actions -->
        <footer class="flex justify-end items-center gap-3 p-6">
          <AppButton
            variant="secondary"
            size="small"
            @click="clearRange"
            :tabindex="isOpen ? 0 : -1"
          >Clear</AppButton>
          <AppButton
            variant="primary"
            size="small"
            @click="applyRange"
            :tabindex="isOpen ? 0 : -1"
          >Apply</AppButton>
        </footer>
      </section>
    </transition>
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import Icon from '../visual/Icon.vue'
import AppButton from '../button/AppButton.vue'

const props = defineProps({
  modelValue: { type: Object, default: () => ({ start: null, end: null }) },
  placeholder: { type: String, default: 'Select date range' },
  disabled: { type: Boolean, default: false },
  format: { type: String, default: 'DD.MM.YYYY' },
  presets: {
    type: Array,
    default: () => [
      { key: 'today', label: 'Today', getValue: () => ({ start: new Date(), end: new Date() }) },
      { key: 'yesterday', label: 'Yesterday', getValue: () => ({ start: new Date(Date.now() - 86400000), end: new Date(Date.now() - 86400000) }) },
      { key: 'last7days', label: 'Last 7 Days', getValue: () => ({ start: new Date(Date.now() - 7 * 86400000), end: new Date() }) },
      { key: 'last30days', label: 'Last 30 Days', getValue: () => ({ start: new Date(Date.now() - 30 * 86400000), end: new Date() }) },
      {
        key: 'thisMonth', label: 'This Month', getValue: () => {
          const now = new Date()
          const start = new Date(now.getFullYear(), now.getMonth(), 1)
          const end = new Date(now.getFullYear(), now.getMonth() + 1, 0)
          return { start, end }
        }
      },
      {
        key: 'lastMonth', label: 'Last Month', getValue: () => {
          const now = new Date()
          const start = new Date(now.getFullYear(), now.getMonth() - 1, 1)
          const end = new Date(now.getFullYear(), now.getMonth(), 0)
          return { start, end }
        }
      }
    ]
  }
})
const emit = defineEmits(['update:modelValue', 'change'])

const isOpen = ref(false)
const startDate = ref('')
const endDate = ref('')
const dateRangeRef = ref(null)

const displayText = computed(() => {
  if (!props.modelValue.start || !props.modelValue.end) return ''
  const start = formatDate(props.modelValue.start)
  const end = formatDate(props.modelValue.end)
  return start === end ? start : `${start} - ${end}`
})

function formatDate(date) {
  if (!date) return ''
  const d = new Date(date)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  return `${day}.${month}.${year}`
}

function formatDateForInput(date) {
  if (!date) return ''
  const d = new Date(date)
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

function toggleDropdown() {
  if (props.disabled) return
  isOpen.value = !isOpen.value
  if (isOpen.value) setTimeout(() => {
    dateRangeRef.value?.querySelector('input')?.focus()
  }, 200)
}

function handleStartDateChange() {
  if (startDate.value && endDate.value && startDate.value > endDate.value) {
    endDate.value = startDate.value
  }
}

function handleEndDateChange() {
  // Do nothing; handled by :min binding
}

function applyPreset(preset) {
  const range = preset.getValue()
  startDate.value = formatDateForInput(range.start)
  endDate.value = formatDateForInput(range.end)
}

function applyRange() {
  const range = {
    start: startDate.value ? new Date(startDate.value) : null,
    end: endDate.value ? new Date(endDate.value) : null
  }
  emit('update:modelValue', range)
  emit('change', range)
  isOpen.value = false
}

function clearRange() {
  startDate.value = ''
  endDate.value = ''
  const range = { start: null, end: null }
  emit('update:modelValue', range)
  emit('change', range)
}

function handleClickOutside(event) {
  if (dateRangeRef.value && !dateRangeRef.value.contains(event.target)) {
    isOpen.value = false
  }
}

function initializeValues() {
  if (props.modelValue.start) {
    startDate.value = formatDateForInput(props.modelValue.start)
  } else {
    startDate.value = ''
  }
  if (props.modelValue.end) {
    endDate.value = formatDateForInput(props.modelValue.end)
  } else {
    endDate.value = ''
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  initializeValues()
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

watch(() => props.modelValue, initializeValues, { deep: true })
</script>

<style scoped>
/* Brandkit renk paleti */
:root {
  --color-primary: #5A1188;
  --color-accent: #9D38CF;
  --color-bg-card: #181124;
  --color-bg-checked: #2A183D;
  --color-border: #6D488D;
  --color-border-light: #31214d;
  --color-text: #fff;
  --color-placeholder: #B1A9C7;
  --color-desc: #9D38CF;
}

/* Input için özel stil */
.date-range-input {
  @apply px-4 py-3 bg-[var(--color-bg-card)] border-2 border-[var(--color-border)] rounded-xl text-base text-[var(--color-text)] font-medium transition-all duration-300 outline-none;
  box-shadow: none;
}
.date-range-input:focus {
  border-color: var(--color-accent);
  box-shadow: 0 0 0 4px rgba(157,56,207,0.16);
  background: #221736;
}

/* Dropdown animasyonu */
@keyframes dropdown-in {
  from { opacity: 0; transform: translateY(-16px) scale(0.96);}
  to   { opacity: 1; transform: translateY(0) scale(1);}
}
.animate-dropdown-in { animation: dropdown-in 0.32s cubic-bezier(.22,1,.36,1); }

.dropdown-fade-enter-active,
.dropdown-fade-leave-active {
  transition: all 0.32s cubic-bezier(.22,1,.36,1);
}
.dropdown-fade-enter-from,
.dropdown-fade-leave-to {
  opacity: 0;
  transform: translateY(-12px) scale(0.96);
}

/* Responsive design */
@media (max-width: 640px) {
  .min-w-\[320px\] { min-width: unset !important; }
  .p-6 { padding: 1rem !important; }
  .grid-cols-2 { grid-template-columns: 1fr !important; }
}
</style>
