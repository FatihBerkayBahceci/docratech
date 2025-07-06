<!--
  Project: Enterprise Health Data Table
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    class="data-table bg-white dark:bg-brand-950 rounded-2xl shadow-sm border border-brand-100 dark:border-brand-900 overflow-hidden transition-all duration-300 ease-in-out"
    :class="{ 'data-table-loading opacity-70 pointer-events-none': loading }"
  >
    <!-- Header -->
    <div
      v-if="searchable || $slots.header"
      class="data-table-header flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-b border-brand-100 dark:border-brand-900 bg-brand-50 dark:bg-brand-900 gap-4"
    >
      <div class="flex-1 max-w-md w-full">
        <slot name="search">
          <SearchInput
            v-if="searchable"
            v-model="searchQuery"
            placeholder="Tabloda ara..."
            @input="handleSearch"
            aria-label="Tabloda arama"
          />
        </slot>
      </div>
      <div class="flex gap-2 items-center">
        <slot name="actions" />
      </div>
    </div>

    <!-- Table Wrapper -->
    <div class="data-table-container relative overflow-x-auto">
      <table class="w-full border-collapse animate-table-in">
        <thead class="bg-brand-50 dark:bg-brand-900 border-b border-brand-100 dark:border-brand-900">
          <tr>
            <th v-if="selectable" class="w-12 text-center">
              <Checkbox
                :model-value="isAllSelected"
                @update:model-value="toggleSelectAll"
                aria-label="Tümünü seç"
              />
            </th>
            <th
              v-for="column in visibleColumns"
              :key="column.key"
              class="px-4 py-3 text-left font-semibold text-brand-900 dark:text-brand-100 cursor-pointer select-none"
              :class="{
                'cursor-default': column.sortable === false,
                'text-brand-600 dark:text-brand-300': sortColumn === column.key
              }"
              @click="handleSort(column)"
              role="columnheader"
              :aria-sort="sortColumn === column.key ? (sortDirection === 'asc' ? 'ascending' : 'descending') : 'none'"
              tabindex="0"
              @keydown.enter.prevent="handleSort(column)"
              @keydown.space.prevent="handleSort(column)"
            >
              <div class="flex items-center gap-2">
                <slot :name="`header-${column.key}`" :column="column">
                  {{ column.label }}
                </slot>
                <i
                  v-if="column.sortable !== false"
                  :class="['bi', getSortIcon(column.key), 'text-sm text-brand-500 transition-colors']"
                  aria-hidden="true"
                />
              </div>
            </th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(row, index) in paginatedData"
            :key="row.id || index"
            class="border-b border-brand-100 dark:border-brand-900 hover:bg-brand-50 dark:hover:bg-brand-900 transition-colors cursor-pointer"
            :class="{ 'bg-brand-100 dark:bg-brand-800': selectedRows.includes(row) }"
            @click="handleRowClick(row)"
            tabindex="0"
            @keydown.enter.prevent="handleRowClick(row)"
            @keydown.space.prevent="handleRowClick(row)"
            role="row"
            :aria-selected="selectedRows.includes(row) ? 'true' : 'false'"
          >
            <td v-if="selectable" class="text-center">
              <Checkbox
                :model-value="selectedRows.includes(row)"
                @update:model-value="toggleRowSelection(row)"
                @click.stop
                :aria-label="`Seç: ${getCellValue(row, visibleColumns[0])}`"
              />
            </td>
            <td
              v-for="column in visibleColumns"
              :key="column.key"
              class="px-4 py-3 text-brand-900 dark:text-brand-100 truncate max-w-[200px]"
              role="cell"
            >
              <slot :name="`cell-${column.key}`" :row="row" :column="column">
                {{ getCellValue(row, column) }}
              </slot>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Loading Overlay -->
      <div
        v-if="loading"
        class="absolute inset-0 bg-white/70 dark:bg-brand-900/70 flex flex-col items-center justify-center gap-4 z-10"
        aria-live="assertive"
        role="status"
      >
        <LoadingSpinner />
      </div>

      <!-- Empty State -->
      <div v-if="!loading && filteredData.length === 0" class="py-10 text-center text-brand-500 dark:text-brand-400">
        <slot name="empty">
          <EmptyState
            title="Veri bulunamadı"
            description="Arama kriterlerinize uygun veri bulunamadı."
            icon="bi-search"
          />
        </slot>
      </div>
    </div>

    <!-- Footer -->
    <div
      v-if="pagination || $slots.footer"
      class="data-table-footer flex flex-col sm:flex-row justify-between items-center gap-3 px-6 py-4 border-t border-brand-100 dark:border-brand-900 bg-brand-50 dark:bg-brand-900"
    >
      <div class="text-sm text-brand-600 dark:text-brand-400 font-medium">
        <slot name="info">
          <span v-if="pagination">{{ startItem }}-{{ endItem }} / {{ filteredData.length }} öğe</span>
        </slot>
      </div>
      <div>
        <slot name="pagination">
          <Pagination
            v-if="pagination"
            :current-page="currentPage"
            :total-pages="totalPages"
            :total-items="filteredData.length"
            :per-page="perPage"
            @update:current-page="currentPage = $event"
          />
        </slot>
      </div>
    </div>
  </div>
</template>

<script setup>
defineOptions({ name: 'DataTable' })
// Project: Enterprise Health Data Table
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, watch } from 'vue'
import SearchInput from '../form/SearchInput.vue'
import Checkbox from '../form/Checkbox.vue'
import LoadingSpinner from '../loading/LoadingSpinner.vue'
import EmptyState from '../empty/EmptyState.vue'
import Pagination from '../navigation/Pagination.vue'

const props = defineProps({
  data: { type: Array, required: true },
  columns: {
    type: Array,
    required: true,
    validator: columns => columns.every(col => col.key && col.label)
  },
  sortable: { type: Boolean, default: true },
  searchable: { type: Boolean, default: true },
  pagination: { type: Boolean, default: true },
  loading: Boolean,
  selectable: Boolean,
  perPage: { type: Number, default: 10 }
})

const emit = defineEmits(['search', 'sort', 'row-click', 'selection-change'])

const searchQuery = ref('')
const sortColumn = ref('')
const sortDirection = ref('asc')
const currentPage = ref(1)
const selectedRows = ref([])

const visibleColumns = computed(() => props.columns.filter(col => col.visible !== false))

const filteredData = computed(() => {
  let data = [...props.data]

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    data = data.filter(row =>
      visibleColumns.value.some(column => {
        const val = getCellValue(row, column)
        return String(val).toLowerCase().includes(query)
      })
    )
  }

  if (sortColumn.value && props.sortable) {
    data.sort((a, b) => {
      const aVal = getCellValue(a, { key: sortColumn.value })
      const bVal = getCellValue(b, { key: sortColumn.value })

      if (aVal < bVal) return sortDirection.value === 'asc' ? -1 : 1
      if (aVal > bVal) return sortDirection.value === 'asc' ? 1 : -1
      return 0
    })
  }

  return data
})

const paginatedData = computed(() => {
  if (!props.pagination) return filteredData.value

  const start = (currentPage.value - 1) * props.perPage
  const end = start + props.perPage
  return filteredData.value.slice(start, end)
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / props.perPage))
const startItem = computed(() => (currentPage.value - 1) * props.perPage + 1)
const endItem = computed(() => Math.min(currentPage.value * props.perPage, filteredData.value.length))
const isAllSelected = computed(() =>
  paginatedData.value.length > 0 && paginatedData.value.every(row => selectedRows.value.includes(row))
)

function handleSearch() {
  currentPage.value = 1
  emit('search', searchQuery.value)
}

function handleSort(column) {
  if (column.sortable === false) return

  if (sortColumn.value === column.key) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortColumn.value = column.key
    sortDirection.value = 'asc'
  }

  emit('sort', { column: sortColumn.value, direction: sortDirection.value })
}

function getSortIcon(columnKey) {
  if (sortColumn.value !== columnKey) return 'bi-arrow-down-up'
  return sortDirection.value === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'
}

function getCellValue(row, column) {
  if (typeof column === 'string') column = { key: column }
  const value = row[column.key]
  if (column.formatter) return column.formatter(value, row)
  return value
}

function handleRowClick(row) {
  emit('row-click', row)
}

function toggleSelectAll() {
  if (isAllSelected.value) {
    selectedRows.value = selectedRows.value.filter(row => !paginatedData.value.includes(row))
  } else {
    selectedRows.value = [...new Set([...selectedRows.value, ...paginatedData.value])]
  }
  emit('selection-change', selectedRows.value)
}

function toggleRowSelection(row) {
  const index = selectedRows.value.indexOf(row)
  if (index > -1) selectedRows.value.splice(index, 1)
  else selectedRows.value.push(row)
  emit('selection-change', selectedRows.value)
}

watch(() => props.data, () => {
  currentPage.value = 1
})
</script>

<style scoped>
.data-table {
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(109,40,217,0.04);
  border: 1px solid #e5e7eb;
  overflow: hidden;
  transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}
.data-table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 24px;
  border-bottom: 1px solid #e5e7eb;
  background: #f9fafb;
}
.data-table-search {
  flex: 1;
  max-width: 300px;
}
.data-table-actions {
  display: flex;
  gap: 8px;
}
.data-table-container {
  position: relative;
  overflow-x: auto;
}
.data-table-element {
  width: 100%;
  border-collapse: collapse;
  animation: table-in 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}
@keyframes table-in {
  0% { opacity: 0; transform: translateY(10px); }
  100% { opacity: 1; transform: translateY(0); }
}
.data-table-thead {
  background: #f9fafb;
}
.data-table-tr {
  transition: all 0.2s;
}
.data-table-tr:hover {
  background: #f3f4f6;
}
.data-table-tr-selected {
  background: #ede9fe;
}
.data-table-th {
  padding: 12px 16px;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
  position: relative;
}
.data-table-th-sortable {
  cursor: pointer;
  user-select: none;
}
.data-table-th-sortable:hover {
  background: #f3f4f6;
}
.data-table-th-content {
  display: flex;
  align-items: center;
  gap: 8px;
}
.data-table-sort-icon {
  font-size: 0.9em;
  color: #9ca3af;
  transition: color 0.2s;
}
.data-table-th-sorted .data-table-sort-icon {
  color: #6D28D9;
}
.data-table-td {
  padding: 12px 16px;
  border-bottom: 1px solid #f3f4f6;
  color: #374151;
}
.data-table-th-checkbox,
.data-table-td-checkbox {
  width: 48px;
  text-align: center;
}
.data-table-loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255,255,255,0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}
.data-table-empty {
  padding: 48px 24px;
  text-align: center;
}
.data-table-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 24px;
  border-top: 1px solid #e5e7eb;
  background: #f9fafb;
}
.data-table-info {
  color: #6b7280;
  font-size: 0.9em;
}
.data-table-loading {
  opacity: 0.7;
  pointer-events: none;
}
@media (max-width: 768px) {
  .data-table-header {
    flex-direction: column;
    gap: 12px;
    align-items: stretch;
  }
  .data-table-search {
    max-width: none;
  }
  .data-table-footer {
    flex-direction: column;
    gap: 12px;
  }
}
</style>
