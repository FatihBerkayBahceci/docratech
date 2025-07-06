<!--
Proje: Enterprise Sağlık Platformu
Bileşen: Advanced DatePicker
Developer: DocraTech Team - Fatih Berkay Bahceci
Açıklama: Marka uyumlu, modern ve animasyonlu DatePicker bileşeni.
-->
<template>
  <div class="relative w-full" :class="{'opacity-50 pointer-events-none': disabled}">
    <div class="relative">
      <input
        ref="inputRef"
        :value="displayValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        class="w-full py-3 px-4 rounded-xl bg-docratech-background text-docratech-text border border-docratech-secondary cursor-pointer transition-all duration-300 hover:border-docratech-primaryLight focus:border-docratech-primaryLight focus:ring-2 focus:ring-docratech-primaryLight focus:outline-none"
        @click="toggleCalendar"
        @focus="handleFocus"
        @blur="handleBlur"
        @keydown="handleKeydown"
      />
      <Icon
        name="calendar"
        class="absolute right-4 top-1/2 -translate-y-1/2 transition-transform duration-300 text-docratech-primaryLight"
        :class="{'rotate-180': isOpen}"
      />
    </div>

    <Transition name="fade-scale">
      <div
        v-if="isOpen"
        ref="calendarRef"
        class="absolute z-50 mt-2 w-full bg-docratech-accent rounded-2xl shadow-lg border border-docratech-secondary overflow-hidden"
        :class="dropdownPlacement"
      >
        <Calendar
          :value="selectedDate"
          :events="events"
          :min-date="minDate"
          :max-date="maxDate"
          :show-today="showToday"
          :animated="animated"
          @update:value="selectDate"
          @date-select="handleDateSelect"
        />
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { computed, ref, watch, nextTick, onMounted, onUnmounted } from 'vue'
import Icon from './Icon.vue'
import Calendar from './Calendar.vue'

const props = defineProps({
  value: Date,
  placeholder: { type: String, default: 'Tarih Seçiniz...' },
  format: { type: String, default: 'DD/MM/YYYY' },
  disabled: Boolean,
  readonly: Boolean,
  events: { type: Array, default: () => [] },
  minDate: Date,
  maxDate: Date,
  showToday: { type: Boolean, default: true },
  animated: { type: Boolean, default: true },
  placement: { type: String, default: 'bottom' }
})

const emit = defineEmits(['update:value', 'change', 'focus', 'blur'])

const inputRef = ref(null)
const calendarRef = ref(null)
const isOpen = ref(false)
const selectedDate = ref(props.value)

const displayValue = computed(() => {
  if (!selectedDate.value) return ''
  const date = selectedDate.value
  const day = date.getDate().toString().padStart(2, '0')
  const month = (date.getMonth() + 1).toString().padStart(2, '0')
  const year = date.getFullYear()
  return props.format.replace('DD', day).replace('MM', month).replace('YYYY', year)
})

const dropdownPlacement = computed(() =>
  props.placement === 'top' ? 'bottom-full mb-2' : 'top-full mt-2'
)

const toggleCalendar = () => {
  if (props.disabled || props.readonly) return
  isOpen.value = !isOpen.value
}

const handleFocus = () => {
  if (!props.disabled && !props.readonly) isOpen.value = true
  emit('focus')
}

const handleBlur = (event) => {
  setTimeout(() => {
    if (!calendarRef.value?.contains(event.relatedTarget)) isOpen.value = false
  }, 100)
  emit('blur')
}

const handleKeydown = (event) => {
  if (event.key === 'Escape') {
    isOpen.value = false
    inputRef.value?.blur()
  } else if (event.key === 'Enter') {
    event.preventDefault()
    toggleCalendar()
  }
}

const selectDate = (date) => {
  selectedDate.value = date
  emit('update:value', date)
  emit('change', date)
  nextTick(() => {
    isOpen.value = false
    inputRef.value?.blur()
  })
}

const handleDateSelect = (date) => selectDate(date)

const handleClickOutside = (event) => {
  if (!inputRef.value?.contains(event.target) && !calendarRef.value?.contains(event.target))
    isOpen.value = false
}

watch(() => props.value, (newValue) => { selectedDate.value = newValue })

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>

<style scoped>
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: all 0.2s ease;
}

.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

input::placeholder {
  color: theme('colors.docratech.textMuted');
  opacity: 0.6;
}
</style>
