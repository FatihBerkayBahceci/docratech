<!--
  Project: Enterprise Health Sortable List (Brandkit Revamp)
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <section
    class="w-full bg-bg-primary dark:bg-bg-dark border border-border dark:border-border-dark rounded-3xl shadow-brand-md overflow-hidden sortable-list-root"
  >
    <!-- Header -->
    <div
      class="flex items-center justify-between px-6 py-4 border-b border-border dark:border-border-dark bg-bg-header dark:bg-bg-header-dark select-none"
    >
      <h3
        v-if="title"
        class="font-semibold text-lg text-brand dark:text-brand-dark truncate"
      >
        {{ title }}
      </h3>
      <div class="flex gap-3">
        <slot name="actions" />
      </div>
    </div>

    <!-- List -->
    <div
      class="relative p-2 min-h-[160px] sortable-list-scroll"
      tabindex="0"
      role="list"
      aria-label="Sıralanabilir liste"
    >
      <transition-group
        name="sort-fade"
        tag="div"
        role="list"
      >
        <div
          v-for="(item, index) in items"
          :key="item.id"
          class="flex items-center gap-3 bg-bg-primary dark:bg-bg-dark border border-border dark:border-border-dark rounded-2xl px-5 py-4 mb-3 shadow-sm group transition-all duration-150 cursor-grab select-none sortable-list-item-root"
          :class="{
            'opacity-50 pointer-events-none rotate-1 shadow-lg scale-[0.98]': draggingIndex === index,
            'hover:shadow-lg hover:-translate-y-[3px] active:scale-95': true
          }"
          draggable="true"
          @dragstart="handleDragStart(index, $event)"
          @dragend="handleDragEnd"
          @dragover="handleDragOver(index, $event)"
          @drop="handleDrop(index, $event)"
          tabindex="0"
          role="listitem"
          :aria-label="`Sıralanabilir öğe: ${item.label}`"
        >
          <div
            class="flex items-center justify-center w-7 text-brand-400 dark:text-brand-300 cursor-grab group-hover:text-accent transition-colors rounded-lg sortable-list-item-handle"
            aria-hidden="true"
          >
            <Icon name="grip-vertical" size="sm" />
          </div>
          <div class="flex-1 min-w-0">
            <slot name="item" :item="item" :index="index">
              <div class="flex items-center gap-3">
                <Icon
                  v-if="item.icon"
                  :name="item.icon"
                  size="sm"
                  class="text-brand-400 dark:text-brand-300"
                />
                <span
                  class="text-base font-medium text-brand dark:text-brand-dark truncate"
                >
                  {{ item.label }}
                </span>
              </div>
            </slot>
          </div>
          <div
            class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity sortable-list-item-actions"
          >
            <slot name="item-actions" :item="item" :index="index">
              <AppButton
                variant="ghost"
                size="sm"
                class="p-1 rounded"
                @click="$emit('item-edit', item, index)"
                aria-label="Öğeyi düzenle"
              >
                <Icon name="edit" size="sm" />
              </AppButton>
              <AppButton
                variant="ghost"
                size="sm"
                class="p-1 rounded"
                @click="$emit('item-delete', item, index)"
                aria-label="Öğeyi sil"
              >
                <Icon name="trash" size="sm" />
              </AppButton>
            </slot>
          </div>
        </div>
      </transition-group>

      <!-- Drop indicator -->
      <transition name="fade-drop">
        <div
          v-if="isDragging && dropIndex !== null"
          class="absolute left-4 right-4 h-[4px] bg-accent rounded-xl z-20 pointer-events-none fade-drop-indicator"
          :style="{ top: dropIndex * itemHeight + 14 + 'px' }"
          aria-hidden="true"
        ></div>
      </transition>
    </div>

    <!-- Empty state -->
    <transition name="fade">
      <div
        v-if="items.length === 0"
        class="flex items-center justify-center py-10 select-none"
      >
        <EmptyState
          title="Liste boş"
          description="Sıralanabilir öğe bulunamadı."
          icon="list"
        />
      </div>
    </transition>
  </section>
</template>

<script setup>
// Project: Enterprise Health Sortable List (Brandkit Revamp)
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref } from 'vue'
import Icon from '../visual/Icon.vue'
import AppButton from '../button/AppButton.vue'
import EmptyState from '../empty/EmptyState.vue'

const props = defineProps({
  title: { type: String, default: null },
  items: { type: Array, default: () => [] },
  itemHeight: { type: Number, default: 60 }
})
const emit = defineEmits(['update:items', 'item-move', 'item-edit', 'item-delete'])

const isDragging = ref(false)
const draggingIndex = ref(null)
const dropIndex = ref(null)

function handleDragStart(index, event) {
  isDragging.value = true
  draggingIndex.value = index
  event.dataTransfer.effectAllowed = 'move'
  event.dataTransfer.setData('text/html', '')
}
function handleDragEnd() {
  isDragging.value = false
  draggingIndex.value = null
  dropIndex.value = null
}
function handleDragOver(index, event) {
  event.preventDefault()
  event.dataTransfer.dropEffect = 'move'
  if (draggingIndex.value !== index) dropIndex.value = index
}
function handleDrop(index, event) {
  event.preventDefault()
  if (draggingIndex.value === null || draggingIndex.value === index) return
  const newItems = [...props.items]
  const draggedItem = newItems[draggingIndex.value]
  newItems.splice(draggingIndex.value, 1)
  newItems.splice(index, 0, draggedItem)
  emit('update:items', newItems)
  emit('item-move', { item: draggedItem, fromIndex: draggingIndex.value, toIndex: index })
  handleDragEnd()
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
  --shadow-brand-md: 0 6px 20px 0 #9D38CF40;
  --shadow-brand-lg: 0 12px 36px 0 #9D38CF30;
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
.shadow-brand-md {
  box-shadow: var(--shadow-brand-md);
}
.shadow-brand-lg {
  box-shadow: var(--shadow-brand-lg);
}

/* Scrollbars */
.sortable-list-scroll::-webkit-scrollbar {
  width: 6px;
}
.sortable-list-scroll::-webkit-scrollbar-thumb {
  background-color: var(--border);
  border-radius: 3px;
  opacity: 0.5;
}
.dark .sortable-list-scroll::-webkit-scrollbar-thumb {
  background-color: var(--border-dark);
  opacity: 0.7;
}

/* Animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.18s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.fade-enter-to,
.fade-leave-from {
  opacity: 1;
}
.sort-fade-enter-active,
.sort-fade-leave-active {
  transition: all 0.2s cubic-bezier(0.55, 0, 0.16, 1);
}
.sort-fade-enter-from,
.sort-fade-leave-to {
  opacity: 0;
  transform: translateY(15px) scale(0.97);
}
.sort-fade-enter-to,
.sort-fade-leave-from {
  opacity: 1;
  transform: none;
}
.fade-drop-enter-active,
.fade-drop-leave-active {
  transition: opacity 0.16s;
}
.fade-drop-enter-from,
.fade-drop-leave-to {
  opacity: 0;
}
.fade-drop-enter-to,
.fade-drop-leave-from {
  opacity: 1;
}

/* Responsive */
@media (max-width: 640px) {
  .sortable-list-root {
    border-radius: 1.5rem;
  }
  .px-6 {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }
  .py-4 {
    padding-top: 1rem !important;
    padding-bottom: 1rem !important;
  }
  .sortable-list-item-root {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  .sortable-list-item-actions {
    width: 100%;
    justify-content: flex-end;
    opacity: 1 !important;
  }
}
</style>
