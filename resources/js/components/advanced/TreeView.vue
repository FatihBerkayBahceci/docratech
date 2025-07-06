<!--
  Project: Enterprise Health TreeView
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    class="tree-view rounded-2xl shadow-sm border border-brand-100 dark:border-brand-800 bg-white dark:bg-brand-950 overflow-hidden"
    role="tree"
    tabindex="0"
    aria-label="Ağaç görünümü"
  >
    <div
      v-if="title"
      class="tree-view-header flex items-center px-6 py-4 border-b border-brand-50 dark:border-brand-800 bg-brand-50 dark:bg-brand-900"
    >
      <h3 class="tree-view-title text-lg font-semibold text-brand-800 dark:text-brand-100 truncate">
        {{ title }}
      </h3>
    </div>

    <div
      class="tree-view-content max-h-[480px] overflow-y-auto py-2"
      role="group"
      aria-multiselectable="true"
    >
      <transition-group name="fade-tree" tag="div">
        <div
          v-for="node in nodes"
          :key="node.id"
          class="tree-view-node border-b border-brand-50 dark:border-brand-900 last:border-b-0"
        >
          <TreeNode
            :node="node"
            :level="0"
            :selected-ids="selectedIds"
            :expanded-ids="expandedIds"
            :search-query="searchQuery"
            :selectable="selectable"
            :multi-select="multiSelect"
            :draggable="draggable"
            @toggle="toggleNode"
            @select="selectNode"
            @drag-start="handleDragStart"
            @drag-over="handleDragOver"
            @drop="handleDrop"
          />
        </div>
      </transition-group>
    </div>

    <transition name="fade-tree">
      <div
        v-if="nodes.length === 0"
        class="tree-view-empty py-10 flex items-center justify-center"
      >
        <EmptyState
          title="Veri bulunamadı"
          description="Gösterilecek veri bulunamadı."
          icon="folder"
        />
      </div>
    </transition>
  </div>
</template>

<script setup>
// Project: Enterprise Health TreeView
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref } from 'vue'
import TreeNode from './TreeNode.vue'
import EmptyState from '../empty/EmptyState.vue'

const props = defineProps({
  title: { type: String, default: null },
  nodes: { type: Array, default: () => [] },
  selectable: { type: Boolean, default: false },
  multiSelect: { type: Boolean, default: false },
  draggable: { type: Boolean, default: false },
  searchQuery: { type: String, default: '' }
})
const emit = defineEmits(['node-toggle', 'node-select', 'node-drag'])

const selectedIds = ref([])
const expandedIds = ref([])

function toggleNode(nodeId) {
  const index = expandedIds.value.indexOf(nodeId)
  if (index > -1) expandedIds.value.splice(index, 1)
  else expandedIds.value.push(nodeId)
  emit('node-toggle', nodeId)
}

function selectNode(nodeId) {
  if (!props.selectable) return
  if (props.multiSelect) {
    const index = selectedIds.value.indexOf(nodeId)
    if (index > -1) selectedIds.value.splice(index, 1)
    else selectedIds.value.push(nodeId)
  } else {
    selectedIds.value = [nodeId]
  }
  emit('node-select', selectedIds.value)
}

function handleDragStart(event, node) {
  if (!props.draggable) return
  event.dataTransfer.setData('text/plain', JSON.stringify(node))
  event.dataTransfer.effectAllowed = 'move'
}

function handleDragOver(event) {
  if (!props.draggable) return
  event.preventDefault()
  event.dataTransfer.dropEffect = 'move'
}

function handleDrop(event, targetNode) {
  if (!props.draggable) return
  event.preventDefault()
  try {
    const draggedNode = JSON.parse(event.dataTransfer.getData('text/plain'))
    emit('node-drag', { draggedNode, targetNode })
  } catch {
    // silently fail
  }
}
</script>

<style scoped>
/* Project: Enterprise Health TreeView */
/* Developer: DocraTech Team - Fatih Berkay Bahceci */

.fade-tree-enter-active,
.fade-tree-leave-active {
  transition: all 0.25s;
}
.fade-tree-enter-from,
.fade-tree-leave-to {
  opacity: 0;
  transform: translateY(16px) scale(0.98);
}
.fade-tree-enter-to,
.fade-tree-leave-from {
  opacity: 1;
  transform: none;
}

/* Custom Scrollbar */
.tree-view-content::-webkit-scrollbar {
  width: 6px;
}
.tree-view-content::-webkit-scrollbar-thumb {
  background: #e5e7eb;
  border-radius: 4px;
}
.dark .tree-view-content::-webkit-scrollbar-thumb {
  background: #374151;
}

/* Responsive */
@media (max-width: 640px) {
  .tree-view-header {
    padding: 1rem !important;
  }
  .px-6 {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }
}
</style>
