<!--
  Project: Enterprise Health Kanban Board (Brandkit Revamp)
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <section
    class="flex flex-col h-full w-full bg-bg-primary dark:bg-bg-dark border border-border dark:border-border-dark rounded-3xl overflow-hidden shadow-brand-md"
  >
    <!-- Header -->
    <div
      class="flex items-center justify-between px-8 py-5 border-b border-border dark:border-border-dark bg-bg-header dark:bg-bg-header-dark"
    >
      <h2
        v-if="title"
        class="font-extrabold text-2xl text-brand dark:text-brand-dark tracking-tight select-none"
      >
        {{ title }}
      </h2>
      <div class="flex gap-3">
        <slot name="actions" />
      </div>
    </div>

    <!-- Columns -->
    <div
      ref="boardRef"
      class="flex-1 flex gap-6 px-6 py-6 overflow-x-auto kanban-scroll"
      tabindex="0"
      aria-label="Kanban Board Columns"
    >
      <div
        v-for="column in columns"
        :key="column.id"
        class="flex flex-col bg-bg-header dark:bg-bg-header-dark border border-border dark:border-border-dark rounded-2xl min-w-[300px] max-w-xs w-full relative transition-all duration-150 shadow-sm group"
        :class="{
          'ring-2 ring-brand/70 dark:ring-accent scale-[1.03] shadow-brand-xl':
            dragOverColumn === column.id
        }"
        @dragover.prevent="handleDragOver(column.id)"
        @dragleave="handleDragLeave"
        @drop="handleDrop(column.id)"
      >
        <!-- Column header -->
        <div
          class="flex items-center justify-between px-5 py-4 border-b border-border dark:border-border-dark bg-bg-primary dark:bg-bg-dark rounded-t-2xl select-none"
        >
          <h3
            class="font-semibold text-brand-dark dark:text-brand tracking-normal text-base truncate"
          >
            {{ column.title }}
          </h3>
          <span
            class="flex items-center justify-center bg-bg-primary dark:bg-bg-dark text-brand-400 dark:text-brand-300 text-xs font-semibold rounded-full min-w-[2.25rem] h-7 px-3 select-none"
          >
            {{ getColumnItems(column.id).length }}
          </span>
        </div>

        <!-- Items -->
        <div
          class="flex-1 flex flex-col gap-5 p-5 overflow-y-auto kanban-column-scroll min-h-[48px]"
        >
          <transition-group
            name="kanban-fade-list"
            tag="div"
          >
            <div
              v-for="item in getColumnItems(column.id)"
              :key="item.id"
              class="relative bg-bg-primary dark:bg-bg-dark border border-border dark:border-border-dark rounded-2xl p-5 shadow-sm transition-all duration-200 cursor-grab select-none
                hover:shadow-lg hover:-translate-y-1 active:scale-[0.97] group"
              :class="{
                'opacity-50 pointer-events-none rotate-1': draggingItem?.id === item.id
              }"
              draggable="true"
              @dragstart="handleDragStart(item, $event)"
              @dragend="handleDragEnd"
              aria-grabbed="true"
              tabindex="0"
              role="listitem"
              :aria-label="`Görev: ${item.title}`"
            >
              <!-- Header/Actions -->
              <div class="flex items-start justify-between mb-2">
                <h4
                  class="font-semibold text-base text-brand dark:text-brand-dark leading-snug truncate"
                >
                  {{ item.title }}
                </h4>
                <div
                  class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-2"
                >
                  <slot name="item-actions" :item="item">
                    <AppButton
                      variant="ghost"
                      size="sm"
                      class="hover:scale-110 transition-transform p-1 rounded"
                      @click="$emit('edit-item', item)"
                      aria-label="Görevi düzenle"
                    >
                      <Icon name="edit" size="sm" />
                    </AppButton>
                  </slot>
                </div>
              </div>

              <!-- Description -->
              <p
                v-if="item.description"
                class="text-sm text-brand-400 dark:text-brand-300 leading-relaxed mb-3 line-clamp-2"
              >
                {{ item.description }}
              </p>

              <!-- Assignee & Due Date & Priority -->
              <div class="flex items-center gap-4 flex-wrap">
                <div
                  v-if="item.assignee"
                  class="flex items-center gap-2 text-sm text-brand-400 dark:text-brand-300"
                >
                  <Avatar
                    :src="item.assignee.avatar"
                    :name="item.assignee.name"
                    size="xs"
                  />
                  <span class="truncate max-w-[100px]" :title="item.assignee.name">{{
                    item.assignee.name
                  }}</span>
                </div>
                <div
                  v-if="item.dueDate"
                  class="flex items-center gap-1 text-sm text-brand-400 dark:text-brand-300"
                >
                  <Icon name="calendar" size="sm" />
                  <span>{{ formatDate(item.dueDate) }}</span>
                </div>
                <div v-if="item.priority" class="flex items-center">
                  <AppBadge
                    :variant="getPriorityVariant(item.priority)"
                    :text="item.priority"
                    size="sm"
                  />
                </div>
              </div>
            </div>
          </transition-group>
        </div>

        <!-- Column footer -->
        <div
          class="px-5 py-4 border-t border-border dark:border-border-dark bg-bg-primary dark:bg-bg-dark rounded-b-2xl"
        >
          <AppButton
            variant="ghost"
            size="sm"
            class="w-full hover:scale-105 transition-transform font-semibold rounded-lg"
            @click="$emit('add-item', column.id)"
            aria-label="Yeni öğe ekle"
          >
            <Icon name="plus" size="sm" />
            <span class="ml-2">Yeni Öğe Ekle</span>
          </AppButton>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
// Project: Enterprise Health Kanban Board (Brandkit Revamp)
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref } from 'vue'
import Icon from '../visual/Icon.vue'
import AppButton from '../button/AppButton.vue'
import AppBadge from '../badge/AppBadge.vue'
import Avatar from '../media/Avatar.vue'

const props = defineProps({
  title: String,
  columns: { type: Array, default: () => [] },
  items: { type: Array, default: () => [] }
})
const emit = defineEmits(['item-move', 'edit-item', 'add-item'])

const draggingItem = ref(null)
const dragOverColumn = ref(null)
const boardRef = ref(null)

function getColumnItems(columnId) {
  return props.items.filter(item => item.columnId === columnId)
}

function handleDragStart(item, event) {
  draggingItem.value = item
  event.dataTransfer.effectAllowed = 'move'
  event.dataTransfer.setData('text/html', '')
}
function handleDragEnd() {
  draggingItem.value = null
  dragOverColumn.value = null
}
function handleDragOver(columnId) {
  dragOverColumn.value = columnId
}
function handleDragLeave() {
  dragOverColumn.value = null
}
function handleDrop(columnId) {
  if (draggingItem.value && draggingItem.value.columnId !== columnId) {
    emit('item-move', {
      item: draggingItem.value,
      fromColumn: draggingItem.value.columnId,
      toColumn: columnId
    })
  }
  dragOverColumn.value = null
}

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('tr-TR')
}
function getPriorityVariant(priority) {
  const variants = { low: 'success', medium: 'warning', high: 'danger' }
  return variants[priority] || 'default'
}
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
  --border: #6D488D;
  --border-dark: #9D38CF;
  --shadow-brand-sm: 0 1px 5px 0 #9D38CF40;
  --shadow-brand-md: 0 6px 20px 0 #5A118840;
  --shadow-brand-xl: 0 12px 36px 0 #9D38CF20;
}

.bg-bg-primary {
  background-color: var(--bg-primary);
}
.bg-bg-dark {
  background-color: var(--bg-dark);
}
.bg-bg-header {
  background-color: var(--bg-header);
}
.bg-bg-header-dark {
  background-color: var(--bg-header-dark);
}
.text-brand {
  color: var(--brand);
}
.text-brand-dark {
  color: var(--brand-dark);
}
.text-accent {
  color: var(--accent);
}
.border-border {
  border-color: var(--border);
}
.border-border-dark {
  border-color: var(--border-dark);
}

.shadow-brand-sm {
  box-shadow: var(--shadow-brand-sm);
}
.shadow-brand-md {
  box-shadow: var(--shadow-brand-md);
}
.shadow-brand-xl {
  box-shadow: var(--shadow-brand-xl);
}

/* Scrollbars */
.kanban-scroll::-webkit-scrollbar {
  height: 8px;
}
.kanban-scroll::-webkit-scrollbar-thumb {
  background-color: var(--border);
  border-radius: 4px;
  opacity: 0.6;
}
.dark .kanban-scroll::-webkit-scrollbar-thumb {
  background-color: var(--border-dark);
  opacity: 0.8;
}

.kanban-column-scroll::-webkit-scrollbar {
  width: 6px;
}
.kanban-column-scroll::-webkit-scrollbar-thumb {
  background-color: var(--border);
  border-radius: 3px;
  opacity: 0.45;
}
.dark .kanban-column-scroll::-webkit-scrollbar-thumb {
  background-color: var(--border-dark);
  opacity: 0.6;
}

/* Transition group */
.kanban-fade-list-enter-active,
.kanban-fade-list-leave-active {
  transition: all 0.25s cubic-bezier(0.51, 0, 0.21, 1);
}
.kanban-fade-list-enter-from,
.kanban-fade-list-leave-to {
  opacity: 0;
  transform: translateY(12px) scale(0.98);
}
.kanban-fade-list-enter-to,
.kanban-fade-list-leave-from {
  opacity: 1;
  transform: none;
}

/* Dragging style */
.group .opacity-50.pointer-events-none {
  user-select: none;
  cursor: grabbing;
  transform: rotate(2deg);
  transition: transform 0.15s ease-in-out;
}
</style>
