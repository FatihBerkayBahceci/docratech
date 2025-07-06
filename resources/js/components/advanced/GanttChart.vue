<!--
  Project: Enterprise Health Gantt Chart (Brandkit Revamp)
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <section class="w-full rounded-3xl border border-brand-dark/50 bg-bg-primary dark:bg-bg-dark shadow-xl overflow-hidden">
    <!-- Header & Controls -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 px-8 py-5 border-b border-brand-dark/40 bg-gradient-to-r from-brand-dark/70 to-brand/10 dark:bg-gradient-to-r dark:from-brand-dark dark:to-brand/20">
      <h3 v-if="title" class="font-semibold text-2xl text-brand tracking-tight">{{ title }}</h3>
      <div class="flex gap-2">
        <AppButton variant="outline" size="sm" @click="zoomIn" aria-label="Yakınlaştır">
          <Icon name="zoom-in" size="sm" />
        </AppButton>
        <AppButton variant="outline" size="sm" @click="zoomOut" aria-label="Uzaklaştır">
          <Icon name="zoom-out" size="sm" />
        </AppButton>
        <AppButton variant="outline" size="sm" @click="fitToView" aria-label="Fit to View">
          <Icon name="maximize" size="sm" />
        </AppButton>
      </div>
    </div>

    <!-- Gantt Grid -->
    <div class="relative overflow-auto max-h-[600px] transition-shadow duration-300 gantt-scroll" ref="ganttContainer">
      <!-- Timeline Header -->
      <div class="flex sticky top-0 z-20 bg-bg-header dark:bg-bg-header-dark border-b border-brand-dark/30 shadow-sm">
        <div class="w-56 min-w-[14rem] py-4 px-5 font-bold text-brand-dark dark:text-accent bg-brand/5 dark:bg-brand-dark border-r border-brand-dark/30">
          Görev
        </div>
        <div class="flex flex-1">
          <div
            v-for="date in timelineDates"
            :key="date.toISOString()"
            class="flex flex-col justify-center items-center min-w-[86px] md:min-w-[112px] px-2 py-2 border-r border-brand-dark/15 dark:border-brand-dark/35 transition-colors duration-200"
            :class="{
              'bg-brand/20 dark:bg-brand-dark/60 text-brand font-semibold shadow-brand-md': isToday(date)
            }"
          >
            <span class="text-xs tracking-wide">{{ formatDate(date) }}</span>
          </div>
        </div>
      </div>

      <!-- Tasks Rows -->
      <div>
        <div
          v-for="task in tasks"
          :key="task.id"
          class="flex relative border-b border-brand-dark/10 min-h-[56px] group hover:bg-accent/5 dark:hover:bg-accent/10 transition-colors duration-200"
        >
          <!-- Task Info -->
          <div class="w-56 min-w-[14rem] flex flex-col gap-1 px-5 py-4 bg-bg-card dark:bg-bg-dark border-r border-brand-dark/25 z-10 rounded-none">
            <span class="font-semibold text-base text-brand truncate">{{ task.name }}</span>
            <span v-if="task.assignee" class="flex items-center gap-1 text-xs text-accent">
              <Avatar :src="task.assignee.avatar" :name="task.assignee.name" size="xs" />
              <span>{{ task.assignee.name }}</span>
            </span>
          </div>
          <!-- Gantt Task Bar -->
          <div class="relative flex-1 h-full">
            <transition name="fade-scale">
              <button
                v-if="true"
                class="absolute flex items-center h-8 py-1 px-4 gap-2 rounded-2xl shadow-brand-sm border-2 outline-none transition-all duration-200 select-none focus:ring-2 focus:ring-accent/60 active:scale-[0.97] cursor-pointer z-20"
                :class="[getTaskBarClass(task), 'hover:shadow-brand-xl hover:scale-[1.03]']"
                :style="getTaskBarStyle(task)"
                @click="selectTask(task)"
                @dblclick="editTask(task)"
                :aria-label="`${task.name} görev çubuğu`"
              >
                <span class="truncate text-xs font-semibold">{{ task.name }}</span>
                <div v-if="task.progress !== undefined" class="relative w-16 h-2 rounded-full bg-accent/10 mt-0.5">
                  <div class="absolute left-0 top-0 h-full rounded-full bg-gradient-to-r from-accent to-brand" :style="{ width: task.progress + '%' }"></div>
                </div>
                <!-- Resize handles -->
                <span
                  v-if="editable"
                  class="gantt-handle-left"
                  @mousedown="startResize(task, 'start')"
                  tabindex="0"
                  :aria-label="`${task.name} başlangıç tarihini değiştir`"
                ></span>
                <span
                  v-if="editable"
                  class="gantt-handle-right"
                  @mousedown="startResize(task, 'end')"
                  tabindex="0"
                  :aria-label="`${task.name} bitiş tarihini değiştir`"
                ></span>
              </button>
            </transition>
          </div>
        </div>
      </div>

      <!-- Today Line -->
      <transition name="fade-slide">
        <div
          v-if="showTodayLine"
          class="absolute top-0 bottom-0 w-1.5 bg-gradient-to-b from-brand to-accent z-30 pointer-events-none animate-pulse shadow-brand-xl"
          :style="getTodayLineStyle()"
          aria-label="Bugün çizgisi"
        ></div>
      </transition>
    </div>

    <!-- Task Edit Modal -->
    <AppDialog v-model="showTaskModal" title="Görev Detayları" size="lg" class="gantt-modal">
      <div v-if="selectedTask" class="flex flex-col gap-5 p-2">
        <FormGroup label="Görev Adı">
          <InputText v-model="selectedTask.name" />
        </FormGroup>
        <div class="flex gap-3 flex-wrap">
          <FormGroup label="Başlangıç Tarihi" class="flex-1 min-w-[150px]">
            <DatePicker v-model="selectedTask.start" />
          </FormGroup>
          <FormGroup label="Bitiş Tarihi" class="flex-1 min-w-[150px]">
            <DatePicker v-model="selectedTask.end" />
          </FormGroup>
        </div>
        <FormGroup label="İlerleme (%)">
          <NumberInput v-model="selectedTask.progress" :min="0" :max="100" :step="5" />
        </FormGroup>
        <FormGroup label="Durum">
          <Select v-model="selectedTask.status" :options="statusOptions" />
        </FormGroup>
      </div>
      <template #footer>
        <AppButton variant="outline" @click="showTaskModal = false">İptal</AppButton>
        <AppButton variant="primary" @click="saveTask">Kaydet</AppButton>
      </template>
    </AppDialog>
  </section>
</template>

<script setup>
// Project: Enterprise Health Gantt Chart (Brandkit Revamp)
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, onMounted } from 'vue'
import Icon from '../visual/Icon.vue'
import AppButton from '../button/AppButton.vue'
import AppDialog from '../modal/AppDialog.vue'
import Avatar from '../media/Avatar.vue'
import FormGroup from '../form/FormGroup.vue'
import InputText from '../form/InputText.vue'
import DatePicker from '../visual/DatePicker.vue'
import NumberInput from '../form/NumberInput.vue'
import Select from '../form/Select.vue'

const props = defineProps({
  title: String,
  tasks: { type: Array, default: () => [] },
  startDate: { type: Date, default: () => new Date() },
  endDate: { type: Date, default: () => new Date(Date.now() + 30 * 24 * 60 * 60 * 1000) },
  rowHeight: { type: Number, default: 56 },
  cellWidth: { type: Number, default: 100 },
  editable: { type: Boolean, default: false },
  showTodayLine: { type: Boolean, default: true }
})

const emit = defineEmits(['task-update', 'task-select'])

const ganttContainer = ref(null)
const showTaskModal = ref(false)
const selectedTask = ref(null)
const zoomLevel = ref(1)

const statusOptions = [
  { value: 'not-started', label: 'Başlanmadı' },
  { value: 'in-progress', label: 'Devam Ediyor' },
  { value: 'completed', label: 'Tamamlandı' },
  { value: 'on-hold', label: 'Beklemede' }
]

const timelineDates = computed(() => {
  const dates = []
  const current = new Date(props.startDate)
  const end = new Date(props.endDate)
  while (current <= end) {
    dates.push(new Date(current))
    current.setDate(current.getDate() + 1)
  }
  return dates
})

// Brandkit renk kodları
const getTaskBarClass = (task) => {
  return [
    'transition-all',
    task.status === 'not-started' && 'bg-gradient-to-r from-brand/70 to-accent/60 border-brand',
    task.status === 'in-progress' && 'bg-gradient-to-r from-accent/80 to-brand/70 border-accent',
    task.status === 'completed' && 'bg-gradient-to-r from-emerald-500 to-brand border-emerald-500',
    task.status === 'on-hold' && 'bg-gradient-to-r from-red-400 via-accent to-brand border-red-400',
    task.progress === 100 && 'bg-gradient-to-r from-emerald-600 to-accent border-emerald-600'
  ].filter(Boolean).join(' ')
}

const getTaskBarStyle = (task) => {
  const start = new Date(task.start)
  const end = new Date(task.end)
  const projectStart = new Date(props.startDate)
  const startOffset = Math.floor((start - projectStart) / (1000 * 60 * 60 * 24))
  const duration = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1
  return {
    left: `${startOffset * props.cellWidth * zoomLevel.value}px`,
    width: `${duration * props.cellWidth * zoomLevel.value}px`
  }
}

const getTodayLineStyle = () => {
  const today = new Date()
  const projectStart = new Date(props.startDate)
  const offset = Math.floor((today - projectStart) / (1000 * 60 * 60 * 24))
  return { left: `${offset * props.cellWidth * zoomLevel.value}px` }
}

const formatDate = (date) => date.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit' })
const isToday = (date) => date.toDateString() === new Date().toDateString()

const selectTask = (task) => emit('task-select', task)
const editTask = (task) => {
  if (props.editable) {
    selectedTask.value = { ...task }
    showTaskModal.value = true
  }
}
const saveTask = () => {
  if (selectedTask.value) {
    emit('task-update', selectedTask.value)
    showTaskModal.value = false
  }
}

const zoomIn = () => { zoomLevel.value = Math.min(zoomLevel.value * 1.2, 3) }
const zoomOut = () => { zoomLevel.value = Math.max(zoomLevel.value / 1.2, 0.5) }
const fitToView = () => {
  if (ganttContainer.value) {
    const containerWidth = ganttContainer.value.offsetWidth
    const timelineWidth = timelineDates.value.length * props.cellWidth
    zoomLevel.value = Math.max(0.5, Math.min(3, containerWidth / timelineWidth))
  }
}

const startResize = (task, handle) => {
  // Resize için mikro animasyon ve feedback eklenebilir
}
onMounted(fitToView)
</script>

<style scoped>
:root {
  --brand: #5A1188;
  --accent: #9D38CF;
  --brand-dark: #2A183D;
  --bg-primary: #181124;
  --bg-dark: #211733;
  --bg-header: #251A38;
  --bg-header-dark: #1B1530;
  --bg-card: #2A183D;
  --emerald-500: #10B981;
  --emerald-600: #059669;
  --red-400: #f87171;
  --shadow-brand: 0 12px 32px 0 #9D38CF18;
}

.bg-brand { background: var(--brand);}
.bg-accent { background: var(--accent);}
.bg-brand-dark { background: var(--brand-dark);}
.bg-bg-primary { background: var(--bg-primary);}
.bg-bg-dark { background: var(--bg-dark);}
.bg-bg-header { background: var(--bg-header);}
.bg-bg-header-dark { background: var(--bg-header-dark);}
.bg-bg-card { background: var(--bg-card);}
.text-brand { color: var(--brand);}
.text-accent { color: var(--accent);}
.text-brand-dark { color: var(--brand-dark);}
.shadow-brand-md { box-shadow: 0 6px 18px 0 #9D38CF26;}
.shadow-brand-xl { box-shadow: 0 18px 40px 0 #9D38CF13;}
.shadow-brand-sm { box-shadow: 0 2px 8px 0 #5A118812;}

.gantt-scroll::-webkit-scrollbar { height: 12px; background: #130b22; border-radius: 8px;}
.gantt-scroll::-webkit-scrollbar-thumb { background: #5A118855; border-radius: 8px;}
.gantt-scroll { scrollbar-width: thin; scrollbar-color: #9D38CF55 #181124;}

.gantt-handle-left,
.gantt-handle-right {
  position: absolute;
  top: 0;
  width: 10px;
  height: 100%;
  background: linear-gradient(120deg, #9D38CF 10%, #5A1188 80%);
  border-radius: 5px;
  cursor: col-resize;
  transition: box-shadow 0.2s, background 0.22s;
  z-index: 10;
  opacity: 0.55;
  border: 1px solid #5A118870;
}
.gantt-handle-left:hover,
.gantt-handle-right:hover {
  background: #9D38CF;
  box-shadow: 0 0 0 2px #9D38CF80;
  opacity: 1;
}
.gantt-handle-left { left: -10px; }
.gantt-handle-right { right: -10px; }

/* Animasyonlar */
.fade-scale-enter-active, .fade-scale-leave-active {
  transition: opacity 0.28s cubic-bezier(.45,0,.1,1), transform 0.27s cubic-bezier(.45,0,.1,1);
}
.fade-scale-enter-from, .fade-scale-leave-to { opacity: 0; transform: scale(0.96);}
.fade-scale-leave-from, .fade-scale-enter-to { opacity: 1; transform: scale(1);}

.fade-slide-enter-active, .fade-slide-leave-active {
  transition: opacity 0.25s, transform 0.24s;
}
.fade-slide-enter-from, .fade-slide-leave-to { opacity: 0; transform: translateY(-10px);}
.fade-slide-enter-to, .fade-slide-leave-from { opacity: 1; transform: none;}
</style>
