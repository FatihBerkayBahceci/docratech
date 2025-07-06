<!--
  Project: Corporate Healthcare Platform UI Library
  Component: TimeRangePicker
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div ref="timeRangeRef" class="relative w-full select-none">
    <div
      class="flex items-center justify-between px-4 py-3 bg-[#181124] border-2 border-[#5A1188] rounded-2xl cursor-pointer transition-all duration-200 font-semibold group shadow-md hover:border-[#9D38CF] focus-within:ring-4 focus-within:ring-[#9D38CF33]"
      :class="{
        'opacity-60 pointer-events-none': disabled,
        'ring-4 ring-[#9D38CF33] border-[#9D38CF]': isOpen && !disabled
      }"
      tabindex="0"
      @click="toggleDropdown"
      @keydown.space.prevent="toggleDropdown"
      @keydown.enter.prevent="toggleDropdown"
      aria-haspopup="listbox"
      :aria-expanded="isOpen"
    >
      <div class="flex items-center gap-2 flex-1">
        <Icon name="clock" size="sm" />
        <span v-if="displayText" class="text-white text-base font-medium transition-colors">{{ displayText }}</span>
        <span v-else class="text-[#9C90B1] text-base font-normal">{{ placeholder }}</span>
      </div>
      <Icon
        name="chevron-down"
        size="sm"
        class="transition-transform duration-200 text-[#B1A9C7]"
        :class="{ 'rotate-180': isOpen }"
      />
    </div>
    <Transition name="dropdown">
      <div
        v-if="isOpen"
        class="absolute left-0 right-0 mt-2 bg-[#231935] border-2 border-[#5A1188] rounded-2xl shadow-2xl z-50 min-w-[340px] max-w-full animate-fadeIn overflow-hidden"
        @keydown.esc="isOpen = false"
      >
        <div class="p-5 border-b border-[#33224d]">
          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
              <label class="text-xs text-[#B1A9C7] font-semibold tracking-wide uppercase">Start Time</label>
              <input
                ref="startInput"
                v-model="startTime"
                type="time"
                class="w-full px-3 py-2 rounded-lg border-2 border-[#6D488D] bg-[#181124] text-white font-medium focus:border-[#9D38CF] focus:ring-4 focus:ring-[#9D38CF22] transition-all duration-200 outline-none"
                :min="min"
                :max="max"
                :disabled="disabled"
                @change="handleStartTimeChange"
                aria-label="Start time"
              />
            </div>
            <div class="flex flex-col gap-1">
              <label class="text-xs text-[#B1A9C7] font-semibold tracking-wide uppercase">End Time</label>
              <input
                ref="endInput"
                v-model="endTime"
                type="time"
                class="w-full px-3 py-2 rounded-lg border-2 border-[#6D488D] bg-[#181124] text-white font-medium focus:border-[#9D38CF] focus:ring-4 focus:ring-[#9D38CF22] transition-all duration-200 outline-none"
                :min="startTime"
                :max="max"
                :disabled="disabled"
                @change="handleEndTimeChange"
                aria-label="End time"
              />
            </div>
          </div>
          <div v-if="timeError" class="text-[#ef4444] mt-2 text-xs font-medium animate-shake">
            {{ timeError }}
          </div>
        </div>
        <div class="p-5 border-b border-[#33224d]">
          <h4 class="text-xs text-[#B1A9C7] font-semibold uppercase mb-2">Quick Selection</h4>
          <div class="grid grid-cols-2 gap-2">
            <button
              v-for="preset in presets"
              :key="preset.key"
              type="button"
              class="px-3 py-2 rounded-lg border-2 border-[#6D488D] bg-[#181124] text-[#9D38CF] font-medium transition-all duration-150 hover:bg-gradient-to-r hover:from-[#5A1188] hover:to-[#9D38CF] hover:text-white hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#9D38CF] aria-selected:font-bold"
              @click="applyPreset(preset)"
            >
              {{ preset.label }}
            </button>
          </div>
        </div>
        <div class="flex items-center justify-end gap-2 p-5">
          <AppButton
            variant="secondary"
            size="small"
            @click="clearRange"
            :class="{'opacity-80': !startTime && !endTime}"
          >Clear</AppButton>
          <AppButton
            variant="primary"
            size="small"
            @click="applyRange"
            :disabled="!startTime || !endTime || !!timeError"
          >Apply</AppButton>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import Icon from '../visual/Icon.vue'
import AppButton from '../button/AppButton.vue'

const props = defineProps({
  modelValue: { type: Object, default: () => ({ start: null, end: null }) },
  placeholder: { type: String, default: 'Select time range' },
  disabled: { type: Boolean, default: false },
  min: String,
  max: String,
  format: { type: String, default: '24h' },
  presets: {
    type: Array,
    default: () => [
      { key: 'morning', label: 'Morning (08:00-12:00)', getValue: () => ({ start: '08:00', end: '12:00' }) },
      { key: 'afternoon', label: 'Afternoon (12:00-17:00)', getValue: () => ({ start: '12:00', end: '17:00' }) },
      { key: 'evening', label: 'Evening (17:00-21:00)', getValue: () => ({ start: '17:00', end: '21:00' }) },
      { key: 'night', label: 'Night (21:00-08:00)', getValue: () => ({ start: '21:00', end: '08:00' }) },
      { key: 'business', label: 'Business Hours (09:00-18:00)', getValue: () => ({ start: '09:00', end: '18:00' }) },
      { key: 'fullDay', label: 'Full Day (00:00-23:59)', getValue: () => ({ start: '00:00', end: '23:59' }) }
    ]
  }
})
const emit = defineEmits(['update:modelValue', 'change'])

const isOpen = ref(false)
const startTime = ref('')
const endTime = ref('')
const timeRangeRef = ref(null)
const timeError = ref('')

// Display as 24h or 12h
const displayText = computed(() => {
  if (!props.modelValue.start || !props.modelValue.end) return ''
  const start = formatTime(props.modelValue.start)
  const end = formatTime(props.modelValue.end)
  return `${start} - ${end}`
})

function formatTime(time) {
  if (!time) return ''
  if (props.format === '12h') {
    const [h, m] = time.split(':')
    const hour = parseInt(h)
    const ampm = hour >= 12 ? 'PM' : 'AM'
    const displayHour = hour % 12 || 12
    return `${displayHour}:${m} ${ampm}`
  }
  return time
}

function toggleDropdown() {
  if (props.disabled) return
  isOpen.value = !isOpen.value
  if (isOpen.value) setTimeout(() => {
    timeRangeRef.value?.querySelector('input[type="time"]')?.focus()
  }, 120)
}

function handleStartTimeChange() {
  // If end time is before start time, show UX error
  if (startTime.value && endTime.value && !isTimeInRange(startTime.value, endTime.value)) {
    timeError.value = 'End time cannot be before start time.'
  } else {
    timeError.value = ''
  }
  // UX: If end time is empty or invalid, set it to startTime
  if (startTime.value && (!endTime.value || endTime.value < startTime.value)) {
    endTime.value = startTime.value
  }
}

function handleEndTimeChange() {
  // If end time is before start time, show UX error
  if (startTime.value && endTime.value && !isTimeInRange(startTime.value, endTime.value)) {
    timeError.value = 'End time cannot be before start time.'
  } else {
    timeError.value = ''
  }
}

function isTimeInRange(start, end) {
  // Support night shift (21:00-08:00 etc)
  if (start === end) return true
  if (start < end) return true
  // If end < start, treat as overnight (valid for night preset)
  return false
}

function applyPreset(preset) {
  const range = preset.getValue()
  startTime.value = range.start
  endTime.value = range.end
  timeError.value = ''
}

function applyRange() {
  if (timeError.value) return
  emit('update:modelValue', {
    start: startTime.value || null,
    end: endTime.value || null
  })
  emit('change', {
    start: startTime.value || null,
    end: endTime.value || null
  })
  isOpen.value = false
}

function clearRange() {
  startTime.value = ''
  endTime.value = ''
  timeError.value = ''
  emit('update:modelValue', { start: null, end: null })
  emit('change', { start: null, end: null })
}

function handleClickOutside(event) {
  if (timeRangeRef.value && !timeRangeRef.value.contains(event.target)) {
    isOpen.value = false
    timeError.value = ''
  }
}

function onEnter(el) {
  el.style.transform = 'translateY(-10px) scale(0.97)'
  el.style.opacity = '0'
  requestAnimationFrame(() => {
    el.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)'
    el.style.transform = 'translateY(0) scale(1)'
    el.style.opacity = '1'
  })
}
function onLeave(el) {
  el.style.transition = 'all 0.22s cubic-bezier(0.4, 0, 0.2, 1)'
  el.style.transform = 'translateY(-10px) scale(0.97)'
  el.style.opacity = '0'
}

// Initial sync
function initializeValues() {
  if (props.modelValue.start) startTime.value = props.modelValue.start
  if (props.modelValue.end) endTime.value = props.modelValue.end
}
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  initializeValues()
})
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
watch(() => props.modelValue, () => {
  initializeValues()
}, { deep: true })
</script>

<style scoped>
@keyframes fadeIn {
  0% { opacity: 0; transform: translateY(-10px) scale(0.97);}
  100% { opacity: 1; transform: translateY(0) scale(1);}
}
.animate-fadeIn { animation: fadeIn 0.35s cubic-bezier(.22,1,.36,1) both; }
@keyframes shake {
  0%,100% { transform: translateX(0);}
  18%,54%,88% { transform: translateX(-2px);}
  36%,72% { transform: translateX(2px);}
}
.animate-shake { animation: shake 0.28s cubic-bezier(.36,.07,.19,.97) both; }
.dropdown-enter-active, .dropdown-leave-active {
  transition: all 0.32s cubic-bezier(0.22, 1, 0.36, 1);
}
.dropdown-enter-from, .dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.97);
}
</style>
