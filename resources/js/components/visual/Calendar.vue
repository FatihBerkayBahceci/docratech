<!--
  Project: Kurumsal Sağlık Platformu Frontend
  Component: Calendar (Enterprise-Level, Brandkit Uyumlu, Animasyonlu)
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div :class="calendarClasses" class="calendar" role="application" aria-label="Takvim">
    <!-- Calendar Header -->
    <header class="calendar-header flex items-center justify-between px-6 py-4 border-b border-docratech-secondary dark:border-docratech-border">
      <button
        type="button"
        class="calendar-nav-btn rounded-lg p-2 transition-shadow focus:outline-none focus:ring-2 focus:ring-docratech-primaryLight disabled:opacity-40 disabled:cursor-not-allowed"
        @click="previousMonth"
        :disabled="!canGoPrevious"
        aria-label="Previous month"
      >
        <Icon name="chevron-left" size="sm" class="text-docratech-primaryLight" />
      </button>
      <h2
        class="calendar-title text-lg font-semibold text-white select-none"
        aria-live="polite"
        aria-atomic="true"
      >
        {{ currentMonthYear }}
      </h2>
      <button
        type="button"
        class="calendar-nav-btn rounded-lg p-2 transition-shadow focus:outline-none focus:ring-2 focus:ring-docratech-primaryLight disabled:opacity-40 disabled:cursor-not-allowed"
        @click="nextMonth"
        :disabled="!canGoNext"
        aria-label="Next month"
      >
        <Icon name="chevron-right" size="sm" class="text-docratech-primaryLight" />
      </button>
    </header>

    <!-- Calendar Grid -->
    <section class="calendar-grid p-6 grid grid-rows-[auto_auto_1fr] gap-2">
      <!-- Weekday Headers -->
      <div class="calendar-weekdays grid grid-cols-7 text-xs font-semibold text-docratech-primaryLight select-none">
        <div v-for="day in weekDays" :key="day" class="text-center">
          {{ day }}
        </div>
      </div>

      <!-- Calendar Days -->
      <div class="calendar-days grid grid-cols-7 gap-1" role="grid" aria-rowcount="6" aria-colcount="7">
        <button
          v-for="(day, index) in calendarDays"
          :key="index"
          :class="getDayClasses(day)"
          :disabled="isDateDisabled(day.date)"
          @click="selectDate(day)"
          role="gridcell"
          :aria-selected="day.isSelected ? 'true' : 'false'"
          :tabindex="day.isSelected ? 0 : -1"
          :aria-label="`${
            day.isToday ? 'Bugün, ' : ''
          }${day.date.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}${
            day.events.length ? ', Etkinlikler var' : ''
          }`"
          @keydown.enter.prevent="selectDate(day)"
          @keydown.space.prevent="selectDate(day)"
          type="button"
        >
          <span class="calendar-day-number select-none">{{ day.day }}</span>
          <div v-if="day.events && day.events.length" class="calendar-day-events flex justify-center mt-1 space-x-0.5" aria-hidden="true">
            <span
              v-for="(event, eIndex) in day.events.slice(0, 3)"
              :key="eIndex"
              class="calendar-event-dot rounded-full"
              :style="{ backgroundColor: event.color || theme('colors.docratech.primaryLight') }"
            />
            <span v-if="day.events.length > 3" class="calendar-event-more text-xs text-docratech-primaryLight">+{{ day.events.length - 3 }}</span>
          </div>
        </button>
      </div>
    </section>

    <!-- Calendar Footer -->
    <footer v-if="showToday" class="calendar-footer flex justify-center border-t border-docratech-secondary dark:border-docratech-border p-4">
      <AppButton variant="outline" size="sm" @click="goToToday" aria-label="Bugüne git">
        Today
      </AppButton>
    </footer>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import Icon from './Icon.vue'
import AppButton from '../button/AppButton.vue'

const props = defineProps({
  value: Date,
  events: { type: Array, default: () => [] },
  minDate: Date,
  maxDate: Date,
  showToday: { type: Boolean, default: true },
  animated: { type: Boolean, default: true },
})

const emit = defineEmits(['update:value', 'date-select', 'month-change'])

const currentDate = ref(props.value ? new Date(props.value) : new Date())
const selectedDate = ref(props.value ? new Date(props.value) : null)
const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

const currentMonthYear = computed(() =>
  currentDate.value.toLocaleDateString(undefined, { month: 'long', year: 'numeric' }),
)

const calendarClasses = computed(() => [
  'calendar',
  { 'calendar-animated': props.animated },
])

const canGoPrevious = computed(() => {
  if (!props.minDate) return true
  const prevMonth = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1)
  return prevMonth >= props.minDate
})

const canGoNext = computed(() => {
  if (!props.maxDate) return true
  const nextMonth = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1)
  return nextMonth <= props.maxDate
})

const calendarDays = computed(() => {
  const year = currentDate.value.getFullYear()
  const month = currentDate.value.getMonth()
  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)
  const startDate = new Date(firstDay)
  startDate.setDate(startDate.getDate() - firstDay.getDay())

  const days = []
  const current = new Date(startDate)

  // We want to display 6 full weeks (42 days) for consistent UI
  for (let i = 0; i < 42; i++) {
    const dayEvents = props.events.filter(event => {
      const eventDate = new Date(event.date)
      return eventDate.toDateString() === current.toDateString()
    })

    days.push({
      date: new Date(current),
      day: current.getDate(),
      isCurrentMonth: current.getMonth() === month,
      isToday: current.toDateString() === new Date().toDateString(),
      isSelected: selectedDate.value && current.toDateString() === selectedDate.value.toDateString(),
      events: dayEvents,
    })
    current.setDate(current.getDate() + 1)
  }

  return days
})

const isDateDisabled = (date) => {
  if (props.minDate && date < props.minDate) return true
  if (props.maxDate && date > props.maxDate) return true
  return false
}

const selectDate = (day) => {
  if (isDateDisabled(day.date)) return
  selectedDate.value = new Date(day.date)
  emit('update:value', day.date)
  emit('date-select', day.date)
}

const previousMonth = () => {
  if (!canGoPrevious.value) return
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1)
  emit('month-change', currentDate.value)
}

const nextMonth = () => {
  if (!canGoNext.value) return
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1)
  emit('month-change', currentDate.value)
}

const goToToday = () => {
  const today = new Date()
  currentDate.value = new Date(today.getFullYear(), today.getMonth(), 1)
  selectedDate.value = today
  emit('update:value', today)
  emit('date-select', today)
}

watch(() => props.value, (newVal) => {
  if (newVal) {
    selectedDate.value = new Date(newVal)
    currentDate.value = new Date(newVal.getFullYear(), newVal.getMonth(), 1)
  }
})
</script>

<style scoped>
.calendar {
  --calendar-transition: cubic-bezier(0.4, 0, 0.2, 1);
  --calendar-primary: theme('colors.docratech.primaryLight');
  --calendar-today: theme('colors.emerald.400');
  --calendar-selected: theme('colors.docratech.secondary');
  font-family: 'Inter', sans-serif;
  background: theme('colors.docratech.background');
  border-radius: 1.5rem;
  box-shadow: 0 8px 30px rgba(157, 56, 207, 0.45);
  user-select: none;
  max-width: 400px;
  margin: 0 auto;
  color: theme('colors.docratech.textMuted');
}

.calendar-header {
  background: transparent;
}

.calendar-nav-btn {
  color: var(--calendar-primary);
  background: transparent;
  border: none;
  cursor: pointer;
  transition: box-shadow 0.25s ease;
}

.calendar-nav-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.calendar-nav-btn:hover:not(:disabled),
.calendar-nav-btn:focus-visible:not(:disabled) {
  box-shadow: 0 0 12px var(--calendar-primary);
  outline: none;
  border-radius: 0.5rem;
}

.calendar-title {
  color: white;
}

.calendar-grid {
  padding-top: 0.75rem;
}

.calendar-weekdays {
  color: var(--calendar-primary);
}

.calendar-weekday {
  text-transform: uppercase;
  font-weight: 600;
  padding: 0.5rem 0;
  user-select: none;
}

.calendar-days {
  margin-top: 0.25rem;
}

.calendar-day {
  background: transparent;
  border-radius: 0.5rem;
  padding: 0.5rem 0.25rem;
  font-weight: 600;
  color: #9ca3af;
  cursor: pointer;
  transition: all 0.3s var(--calendar-transition);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  user-select: none;
  border: 2px solid transparent;
}

.calendar-day:hover:not(.calendar-day-disabled) {
  background: #5a118866;
  color: white;
  transform: scale(1.05);
  box-shadow: 0 0 6px var(--calendar-primary);
  z-index: 10;
}

.calendar-day-current-month {
  color: white;
}

.calendar-day-other-month {
  opacity: 0.4;
  color: #6d488d;
  cursor: default;
}

.calendar-day-today {
  border: 2px solid var(--calendar-today);
  color: var(--calendar-today);
  font-weight: 700;
}

.calendar-day-selected {
  background: var(--calendar-selected);
  color: white;
  box-shadow: 0 0 10px var(--calendar-selected);
}

.calendar-day-disabled {
  cursor: not-allowed;
  opacity: 0.3;
}

.calendar-day-disabled:hover {
  background: transparent;
  transform: none;
  box-shadow: none;
}

.calendar-day-number {
  font-size: 1rem;
  line-height: 1;
  user-select: none;
}

.calendar-day-events {
  display: flex;
  margin-top: 4px;
  gap: 3px;
  justify-content: center;
}

.calendar-event-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}

.calendar-event-more {
  font-size: 0.625rem;
  color: var(--calendar-primary);
  user-select: none;
  align-self: center;
  margin-left: 4px;
}

.calendar-footer {
  border-top: 1px solid theme('colors.docratech.secondary');
  padding: 1rem 0;
  background: transparent;
}

.calendar-animated .calendar-day {
  animation: calendar-day-fade-in 0.3s var(--calendar-transition) both;
}

.calendar-day:nth-child(1) {
  animation-delay: 0.05s;
}
.calendar-day:nth-child(2) {
  animation-delay: 0.1s;
}
.calendar-day:nth-child(3) {
  animation-delay: 0.15s;
}
.calendar-day:nth-child(4) {
  animation-delay: 0.2s;
}
.calendar-day:nth-child(5) {
  animation-delay: 0.25s;
}
.calendar-day:nth-child(6) {
  animation-delay: 0.3s;
}
.calendar-day:nth-child(7) {
  animation-delay: 0.35s;
}

@keyframes calendar-day-fade-in {
  0% {
    opacity: 0;
    transform: scale(0.85);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
