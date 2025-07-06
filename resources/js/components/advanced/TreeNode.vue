<!--
  Project: Enterprise Health Tree Node
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div class="relative">
    <!-- Node Content -->
    <div
      class="flex items-center gap-2 py-2 px-3 rounded-2xl transition-all group select-none cursor-pointer border border-transparent
                hover:bg-brand-50 dark:hover:bg-brand-900 focus-visible:ring-2 focus-visible:ring-brand-400 outline-none"
      :class="{
        'bg-brand-50 dark:bg-brand-900 text-brand dark:text-brand-dark ring-2 ring-brand-300': isSelected,
        'bg-yellow-50/90 dark:bg-yellow-900/60 text-yellow-900 dark:text-yellow-300 ring-2 ring-yellow-400': isHighlighted,
        'opacity-50 pointer-events-none rotate-1': isDragging
      }"
      :style="{ paddingLeft: `${level * 1.5 + 1}rem` }"
      @click="handleClick"
      @dblclick="handleDoubleClick"
      tabindex="0"
      @keydown.enter.prevent="handleClick"
      @keydown.space.prevent="handleClick"
      :aria-selected="isSelected.toString()"
      :aria-label="node.label"
      :draggable="draggable"
      @dragstart="handleDragStart"
      @dragover.prevent="handleDragOver"
      @drop.prevent="handleDrop"
      role="treeitem"
    >
      <!-- Chevron (expand/collapse) -->
      <div class="w-5 flex items-center justify-center shrink-0">
        <Icon
          v-if="hasChildren"
          :name="isExpanded ? 'chevron-down' : 'chevron-right'"
          size="sm"
          class="text-brand-300 hover:text-brand-500 transition-colors cursor-pointer"
          @click.stop="toggleNode"
          role="button"
          :aria-expanded="isExpanded.toString()"
          :aria-label="isExpanded ? 'Kapat' : 'Aç'"
          tabindex="0"
          @keydown.enter.stop.prevent="toggleNode"
          @keydown.space.stop.prevent="toggleNode"
        />
      </div>

      <!-- File/Folder Icon -->
      <div class="w-5 flex items-center justify-center shrink-0">
        <Icon :name="getNodeIcon()" size="sm" :class="getNodeIconClass()" />
      </div>

      <!-- Info -->
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 font-medium">
          <span v-if="selectable" class="flex items-center">
            <input
              type="checkbox"
              :checked="isSelected"
              @click.stop="selectNode"
              class="accent-brand-600 rounded border-brand-300 h-4 w-4 transition"
              :aria-label="`${node.label} seç`"
              tabindex="-1"
            />
          </span>
          <span class="truncate" :title="node.label">{{ node.label }}</span>
        </div>
        <transition name="fade-desc" mode="out-in">
          <div
            v-if="node.description"
            class="text-xs text-brand-400 dark:text-brand-300 mt-1 select-text"
          >
            {{ node.description }}
          </div>
        </transition>
      </div>

      <!-- Actions Button -->
      <div
        class="flex items-center gap-1 ml-1 opacity-0 group-hover:opacity-100 transition-opacity select-none"
      >
        <slot name="actions" :node="node">
          <AppButton
            v-if="node.actions"
            variant="ghost"
            size="sm"
            @click.stop="showActions = !showActions"
            aria-haspopup="menu"
            :aria-expanded="showActions.toString()"
            aria-label="İşlemler"
          >
            <Icon name="more-vertical" size="sm" />
          </AppButton>
        </slot>
      </div>
    </div>

    <!-- Actions Dropdown -->
    <transition name="fade-dropdown">
      <div
        v-if="showActions && node.actions"
        class="absolute z-40 min-w-[140px] right-2 top-full mt-1 rounded-2xl bg-white dark:bg-brand-950 border border-border dark:border-border-dark shadow-lg py-1"
        role="menu"
      >
        <button
          v-for="action in node.actions"
          :key="action.key"
          class="flex items-center w-full gap-2 px-4 py-2 text-sm text-brand-800 dark:text-brand-100 hover:bg-brand-50 dark:hover:bg-brand-900 transition rounded-lg"
          @click="handleAction(action)"
          role="menuitem"
          tabindex="0"
        >
          <Icon :name="action.icon" size="sm" />
          <span>{{ action.label }}</span>
        </button>
      </div>
    </transition>

    <!-- Children -->
    <div
      v-if="hasChildren && isExpanded"
      class="border-l-2 border-brand-50 dark:border-brand-900 ml-4 pl-1 mt-1"
      role="group"
    >
      <TreeNode
        v-for="child in node.children"
        :key="child.id"
        :node="child"
        :level="level + 1"
        :selected-ids="selectedIds"
        :expanded-ids="expandedIds"
        :search-query="searchQuery"
        :selectable="selectable"
        :multi-select="multiSelect"
        :draggable="draggable"
        @toggle="$emit('toggle', $event)"
        @select="$emit('select', $event)"
        @drag-start="$emit('drag-start', $event)"
        @drag-over="$emit('drag-over', $event)"
        @drop="$emit('drop', $event)"
      />
    </div>
  </div>
</template>

<script setup>
// Project: Enterprise Health Tree Node
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed } from 'vue'
import Icon from '../visual/Icon.vue'
import AppButton from '../button/AppButton.vue'

const props = defineProps({
  node: { type: Object, required: true },
  level: { type: Number, default: 0 },
  selectedIds: { type: Array, default: () => [] },
  expandedIds: { type: Array, default: () => [] },
  searchQuery: { type: String, default: '' },
  selectable: { type: Boolean, default: false },
  multiSelect: { type: Boolean, default: false },
  draggable: { type: Boolean, default: false }
})
const emit = defineEmits(['toggle', 'select', 'drag-start', 'drag-over', 'drop'])

const showActions = ref(false)
const isDragging = ref(false)

const hasChildren = computed(() => props.node.children && props.node.children.length > 0)
const isExpanded = computed(() => props.expandedIds.includes(props.node.id))
const isSelected = computed(() => props.selectedIds.includes(props.node.id))
const isHighlighted = computed(() =>
  props.searchQuery && props.node.label.toLowerCase().includes(props.searchQuery.toLowerCase())
)

function getNodeIcon() {
  if (props.node.icon) return props.node.icon
  if (hasChildren.value) return isExpanded.value ? 'folder-open' : 'folder'
  return 'file'
}
function getNodeIconClass() {
  return [
    'transition-colors',
    props.node.type === 'folder' ? 'text-yellow-400 dark:text-yellow-300' : '',
    props.node.type === 'file' ? 'text-brand-400 dark:text-brand-300' : ''
  ]
}
function handleClick() {
  if (props.selectable) emit('select', props.node.id)
}
function handleDoubleClick() {
  if (hasChildren.value) toggleNode()
}
function toggleNode() {
  emit('toggle', props.node.id)
}
function selectNode() {
  emit('select', props.node.id)
}
function handleAction(action) {
  showActions.value = false
  if (action.handler) action.handler(props.node)
}
function handleDragStart(event) {
  if (!props.draggable) return
  isDragging.value = true
  emit('drag-start', event, props.node)
}
function handleDragOver(event) {
  if (!props.draggable) return
  emit('drag-over', event, props.node)
}
function handleDrop(event) {
  if (!props.draggable) return
  isDragging.value = false
  emit('drop', event, props.node)
}
</script>

<style scoped>
/* Project: Enterprise Health Tree Node */
/* Developer: DocraTech Team - Fatih Berkay Bahceci */

.fade-dropdown-enter-active,
.fade-dropdown-leave-active {
  transition: opacity 0.18s;
}
.fade-dropdown-enter-from,
.fade-dropdown-leave-to {
  opacity: 0;
}
.fade-dropdown-enter-to,
.fade-dropdown-leave-from {
  opacity: 1;
}

.fade-desc-enter-active,
.fade-desc-leave-active {
  transition: opacity 0.25s;
}
.fade-desc-enter-from,
.fade-desc-leave-to {
  opacity: 0;
}
.fade-desc-enter-to,
.fade-desc-leave-from {
  opacity: 1;
}

/* Scrollbar for children if overflow */
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}
.dark ::-webkit-scrollbar-thumb {
  background: #374151;
}

/* Responsive tweaks */
@media (max-width: 640px) {
  .py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
  }
  .px-3 {
    padding-left: 0.75rem !important;
    padding-right: 0.75rem !important;
  }
  .rounded-2xl {
    border-radius: 0.75rem !important;
  }
}
</style>
